#from threading import Thread
import linecache
import sys
import urllib
import re
# mport urlparse
from bs4 import BeautifulSoup
import MySQLdb

reload(sys)
sys.setdefaultencoding('utf-8')

default = "null"
base = "https://pricespy.co.nz"
page = "https://pricespy.co.nz/category.php"

def PrintException():
    exc_type, exc_obj, tb = sys.exc_info()
    f = tb.tb_frame
    lineno = tb.tb_lineno
    filename = f.f_code.co_filename
    linecache.checkcache(filename)
    line = linecache.getline(filename, lineno, f.f_globals)
    print 'EXCEPTION IN ({}, LINE {} "{}"): {}'.format(filename, lineno, line.strip(), exc_obj)

def PrintResutls(itemsstorage):
    for item in itemsstorage:
        print  item
        for itemdet in itemsstorage[item]:
            print "        " + itemdet + "  D:  " + itemsstorage[item][itemdet]
        print "\n ++++++++++++++++++++++++ \n"

def ScraperMainTask(urls, id):
    stored = []
    nevervisit = []
    visited = []
    itemsstorage = {}

    mainregex = r'^https://pricespy\.co\.nz\/product\.php\?p=[0-9]{1,}$'
    subregex = r"\?(p)="
    cutregex = r"(List [\w]* \([\d]* [\w]*, from .*\))"
    #cutregex2 = r'(Suggest change - Beta)'
    #cutregex3 = r'(-&gt;)'

    #.*?[\W]{2,}.*?|
    #(.*?[\W]{2,}.*?\)?|.*?)[\W]{2,}([\w].*)

    detailregex = r'(.*?)[\W]{2,}([\w].*)'
    pageregex = r'^https://pricespy\.co\.nz\/category\.php\?[a-zA-Z]=[A-Za-z0-9]{1,}&s=[0-9]{1,}$'


    count = 0
    while urls:
        #try:
            htmltext = urllib.urlopen(urls[0]).read()
            soup = BeautifulSoup(htmltext, "lxml")
            count += 1
            urls.pop(0)
            #print len(urls)

            for tag in soup.find_all('a', href=True):
                # print tag['href']
                link = base + tag['href']
                category = page + tag['href']
                # print link
                # print matches
                if link not in visited and re.search(mainregex, link, re.IGNORECASE | re.VERBOSE):
                    visited.append(link)
                    link = re.sub(subregex, "?e=", link)
                    stored.append(link)
                    # urls.append(link)
                    #print "Match"
                    # print soup.title
                    # print link
                elif link not in visited and re.search(pageregex, category, re.IGNORECASE | re.VERBOSE):
                    visited.append(link)
                    urls.append(category)
                    #print category
                elif link not in stored:
                    nevervisit.append(link)
        #except Exception, e:
            #print "ERROR set 1 " + str(e)

    # print "Initial pass"
    count = 0
    for link in stored:
        itemsdetails = {}
        try:
            #print "Current link: " + link
            # print item
            htmltext = urllib.urlopen(link).read()
            # print htmltext
            soup = BeautifulSoup(htmltext, "lxml")
            # print soup
            count += 1
            producttitle = soup.find("h1", class_="intro_header").get_text()
            producttitle = producttitle.strip()
            productcost = soup.find("span", class_="price").get_text()
            productcost = productcost.lstrip('$')
            products = soup.find_all("tr", class_="erow")
            for product in products:
                #print product.get_text()
                product = product.get_text()
                #product = re.sub(cutregex2, "", product.get_text())
                product = product.replace("Suggest change - Beta","")
                product = re.sub(cutregex , "", product)
                #print product
                product = product.replace("->", "-")
                #print product
                product = product.strip()
                #product = product.split("u'")
                #product = product[len(product)-1]
                #product = re.sub(cutregex3 "-", product.get_text())
                #print product
                #print product
                product = re.search(detailregex, product)
                #print product
                if product:
                    #print product
                    productdetail = product.group(1).replace(" ", "")
                    #print productdetail
                    productdetailvalue = product.group(2).strip()
                    #print productdetailvalue + "\n"
                    if productdetailvalue == "Contribute":
                        productdetailvalue = "null"
                    itemsdetails[productdetail] = productdetailvalue
                    itemsdetails["Link"] = link
                    itemsdetails["Price"] = productcost
                    #print itemsdetails
            itemsstorage[producttitle] = itemsdetails
            print str(len(stored) - count) + " " + id + " links remaining"
            #break
        except Exception:
            PrintException()
    #PrintResutls(itemsstorage)
    return itemsstorage

def ScraperUpload(item, sql, sql2):
    db = MySQLdb.connect("localhost", "root","","compcreator")
    cursor = db.cursor()
    #print item + "\n" + itemsstorage[item]['Price'] + "\n" + itemsstorage[item]['Link']
    try:
       cursor.execute(sql)
       db.commit()
       cursor.execute(sql2)
       db.commit()
    except MySQLdb.Error, e:
       # Rollback in case there is any error
       print "MySQL failiure: " + str(e)
       print sql
       print sql2
       db.rollback()


        # print letters[0]
        #lines = [span.get_text() for span in spans]
        # for line in letters:
        #    print line
        # for item in listitem:

        #    print "++++++++++++"

        # except Exception,e:
        #    print "ERROR set 2 "+str(e)


    # for link in stored:
    #    print link
    # print count



def CPU():
    urls = ["https://pricespy.co.nz/category.php?m=s321418940"]
    itemsstorage = ScraperMainTask(urls, "CPU")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage,
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `cpu`(`Clockfrequency`, `Productpage`, `L3cache`, `TurboBoostCore`, `CPUtype`, `Boxversion`, `Numberofthreads`, `64bitprocessor`, `L2cache`, `Socket`, `Numberofcores`, `Graphicsprocessor`, `Releaseyear`, `ThermalDesignPower`, `Integratedgraphics`, `Virtualization`, `CPURating`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 1111, LAST_INSERT_ID())" %(itemsstorage[item].setdefault('Clockfrequency', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('L3cache', default), itemsstorage[item].setdefault('TurboBoost/Core', default), itemsstorage[item].setdefault('CPUtype', default), itemsstorage[item].setdefault('Boxversion', default), itemsstorage[item].setdefault('Numberofthreads', default), itemsstorage[item].setdefault('64-bitprocessor', default), itemsstorage[item].setdefault('L2cache', default), itemsstorage[item].setdefault('Socket', default), itemsstorage[item].setdefault('Numberofcores', default), itemsstorage[item].setdefault('Graphicsprocessor', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('ThermalDesignPower', default), itemsstorage[item].setdefault('Integratedgraphics', default), itemsstorage[item].setdefault('Virtualization', default)   ))
        print str(count) + " uploads to DB remaining"

def GPU():
    urls = ["https://pricespy.co.nz/category.php?m=s321383620"]
    itemsstorage = ScraperMainTask(urls, "GPU")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `gpu`(`Cooling`, `Numberoffans`, `Semipassive`, `Factoryoverclocked`, `Graphicsprocessor`, `Lowprofile`, `Nonreferencecooler`, `PCIExpressversion`, `DisplayPort`, `NumberofDisplayPortoutputs`, `DVI`, `NumberofDVIoutputs`, `HDMI`, `NumberofHDMIoutputs`, `VGAoutputs`, `Maximumresolution`, `Numberofsupportedmonitors`, `Length`, `Numberofslots`, `DirectX`, `HDR`, `OpenGL`, `Vulkan`, `Supportformultiplegraphicscards`, `Memorybandwidth`, `Memorycapacity`, `Memoryinterface`, `Memoryspeed`, `Memorytype`, `GPUBoost`, `Processorspeed`, `Supplementarypowerconnector`, `Manufacturerwarranty`, `Releaseyear`, `Productpage`, `GPURating`, `CompID`) VALUES ('%s' ,'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 1111, LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Cooling', default), itemsstorage[item].setdefault('Numberoffans', default), itemsstorage[item].setdefault('Semi-passive', default), itemsstorage[item].setdefault('Factoryoverclocked', default), itemsstorage[item].setdefault('Graphicsprocessor', default), itemsstorage[item].setdefault('Lowprofile', default), itemsstorage[item].setdefault('Non-referencecooler', default), itemsstorage[item].setdefault('PCIExpressversion', default), itemsstorage[item].setdefault('DisplayPort', default),  itemsstorage[item].setdefault('NumberofDisplayPortoutputs', default), itemsstorage[item].setdefault('DVI', default), itemsstorage[item].setdefault('NumberofDVIoutputs', default), itemsstorage[item].setdefault('HDMI', default), itemsstorage[item].setdefault('NumberofHDMIoutputs', default), itemsstorage[item].setdefault('VGAoutputs', default), itemsstorage[item].setdefault('Maximumresolution', default), itemsstorage[item].setdefault('Numberofsupportedmonitors', default), itemsstorage[item].setdefault('Length', default), itemsstorage[item].setdefault('Numberofslots', default), itemsstorage[item].setdefault('DirectX', default), itemsstorage[item].setdefault('HDR', default), itemsstorage[item].setdefault('OpenGL', default), itemsstorage[item].setdefault('Vulkan', default), itemsstorage[item].setdefault('Supportformultiplegraphicscards', default), itemsstorage[item].setdefault('Memorybandwidth', default), itemsstorage[item].setdefault('Memorycapacity', default), itemsstorage[item].setdefault('Memoryinterface', default), itemsstorage[item].setdefault('Memoryspeed', default), itemsstorage[item].setdefault('Memorytype', default), itemsstorage[item].setdefault('GPUBoost', default), itemsstorage[item].setdefault('Processorspeed', default), itemsstorage[item].setdefault('Supplementarypowerconnector', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Productpage', default)))
        print str(count) + " uploads to DB remaining"

def RAM():
    urls = ["https://pricespy.co.nz/category.php?m=s321420922", "https://pricespy.co.nz/category.php?m=s321421236"]
    itemsstorage = ScraperMainTask(urls, "RAM")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `memory`(`Numberofmodules`, `Memoryspeed`, `Memorycapacity`, `ECC`, `Releaseyear`, `PriceGB`, `Manufacturerwarranty`, `Memorycapacitypermodule`, `CASLatency`, `Typeofmemory`, `Productpage`, `Voltage`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Numberofmodules', default), itemsstorage[item].setdefault('Memoryspeed', default), itemsstorage[item].setdefault('Memorycapacity', default), itemsstorage[item].setdefault('ECC', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Price/GB', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('Memorycapacitypermodule', default), itemsstorage[item].setdefault('CASLatency', default), itemsstorage[item].setdefault('Typeofmemory', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('Voltage', default)))
        print str(count) + " uploads to DB remaining"

def MOBO():
    urls = ["https://pricespy.co.nz/category.php?m=s321421551"]
    itemsstorage = ScraperMainTask(urls, "MOBO")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `motherboard`(`Width`, `Cooling`, `Formfactor`, `PCIslots`, `NumberofHDMIoutputs`, `Ethernetconnection`, `Socket`, `HDMI`, `Headphoneoutput`, `Typeofmemory`, `USB20`, `mSATA`, `Bluetooth`, `ECCsupport`, `PCIExpressx8`, `TypeofRAID`, `PCIExpressx1`, `PCIExpressx4`, `Manufacturerwarranty`, `PCIExpressversion`, `Soundcard`, `PCIExpressx16`, `Memoryspeeds`, `Productpage`, `Chassisfanconnectors`, `PCIExpressMini`, `Bluetoothversion`, `Memoryslots`, `MiniPCI`, `DVI`, `Supportformultiplegraphicscards`, `SupportforintegratedgraphicsinCPU`, `SATAExpress`, `Microphoneinput`, `NumberofEthernetconnections`, `Powerfanconnector`, `NumberofDisplayPortoutputs`, `Chipset`, `SATA3Gbs`, `Maximumamountofmemory`, `DisplayPort`, `USB`, `Soundcardchip`, `RAIDcontroller`, `Thunderbolt`, `64bitprocessor`, `U2`, `Depth`, `Releaseyear`, `M2`, `VGAoutputs`, `Wirelessnetwork`, `SATA6Gbs`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Width', default),  itemsstorage[item].setdefault('Cooling', default), itemsstorage[item].setdefault('Formfactor', default), itemsstorage[item].setdefault('PCIslots', default), itemsstorage[item].setdefault('NumberofHDMIoutputs', default), itemsstorage[item].setdefault('Ethernetconnection', default), itemsstorage[item].setdefault('Socket', default), itemsstorage[item].setdefault('HDMI', default), itemsstorage[item].setdefault('Headphoneoutput', default), itemsstorage[item].setdefault('Typeofmemory', default), itemsstorage[item].setdefault('USB2.0', default), itemsstorage[item].setdefault('mSATA', default), itemsstorage[item].setdefault('Bluetooth', default), itemsstorage[item].setdefault('ECCsupport', default), itemsstorage[item].setdefault('PCIExpressx8', default), itemsstorage[item].setdefault('TypeofRAID', default), itemsstorage[item].setdefault('PCIExpressx1', default), itemsstorage[item].setdefault('PCIExpressx4', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('PCIExpressversion', default), itemsstorage[item].setdefault('Soundcard', default), itemsstorage[item].setdefault('PCIExpressx16', default), itemsstorage[item].setdefault('Memoryspeeds', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('Chassisfanconnectors', default), itemsstorage[item].setdefault('PCIExpressMini', default), itemsstorage[item].setdefault('Bluetoothversion', default), itemsstorage[item].setdefault('Memoryslots', default), itemsstorage[item].setdefault('Mini-PCI', default), itemsstorage[item].setdefault('DVI', default), itemsstorage[item].setdefault('Supportformultiplegraphicscards', default), itemsstorage[item].setdefault('SupportforintegratedgraphicsinCPU', default), itemsstorage[item].setdefault('SATAExpress', default), itemsstorage[item].setdefault('Microphoneinput', default), itemsstorage[item].setdefault('NumberofEthernetconnections', default), itemsstorage[item].setdefault('Powerfanconnector', default), itemsstorage[item].setdefault('NumberofDisplayPortoutputs', default), itemsstorage[item].setdefault('Chipset', default), itemsstorage[item].setdefault('SATA3Gb/s', default), itemsstorage[item].setdefault('Maximumamountofmemory', default), itemsstorage[item].setdefault('DisplayPort', default), itemsstorage[item].setdefault('USB', default), itemsstorage[item].setdefault('Soundcardchip', default), itemsstorage[item].setdefault('RAIDcontroller', default), itemsstorage[item].setdefault('Thunderbolt', default), itemsstorage[item].setdefault('64-bitprocessor', default), itemsstorage[item].setdefault('U.2', default), itemsstorage[item].setdefault('Depth', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('M.2', default),  itemsstorage[item].setdefault('VGAoutputs', default), itemsstorage[item].setdefault('Wirelessnetwork', default), itemsstorage[item].setdefault('SATA6Gb/s', default)))
        print str(count) + " uploads to DB remaining"

def PSU():
    urls = ["https://pricespy.co.nz/category.php?m=s321422467"]
    itemsstorage = ScraperMainTask(urls, "PSU")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `psu`(`Capacity`, `Releaseyear`, `Fansize`, `Modular`, `Cablesocks`, `PowerconnectorsforSATA`, `PowerconnectionsforPCIExpress`, `Temperaturecontrolledfan`, `Manufacturerwarranty`, `Productpage`, `Efficiency`, `Numberoffans`, `Semipassive`, `Format`, `80pluscertification`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Capacity', default), itemsstorage[item].setdefault('Releaseyear', default) ,itemsstorage[item].setdefault('Fansize', default) ,itemsstorage[item].setdefault('Modular', default) ,itemsstorage[item].setdefault('Cablesocks', default) ,itemsstorage[item].setdefault('PowerconnectorsforSATA', default) ,itemsstorage[item].setdefault('PowerconnectionsforPCI-Express', default) ,itemsstorage[item].setdefault('Temperature-controlledfan', default) ,itemsstorage[item].setdefault('Manufacturerwarranty', default) ,itemsstorage[item].setdefault('Productpage', default) ,itemsstorage[item].setdefault('Efficiency', default) ,itemsstorage[item].setdefault('Numberoffans', default) ,itemsstorage[item].setdefault('Semi-passive', default) ,itemsstorage[item].setdefault('Format', default) ,itemsstorage[item].setdefault('80-pluscertification', default)))
        print str(count) + " uploads to DB remaining"

def SSD():
    urls = ["https://pricespy.co.nz/category.php?m=s321423261"]
    itemsstorage = ScraperMainTask(urls, "SSD")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `ssd`(`Maximumreadspeed`, `Controllerchip`, `PriceGB`, `Manufacturerwarranty`, `Interface`, `Formfactor`, `Typeofflashmemory`, `Connection`, `Productpage`, `Weight`, `Maximumwritespeed`, `Size`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Maximumreadspeed', default), itemsstorage[item].setdefault('Controllerchip', default) ,itemsstorage[item].setdefault('Price/GB', default) ,itemsstorage[item].setdefault('Manufacturerwarranty', default) ,itemsstorage[item].setdefault('Interface', default) ,itemsstorage[item].setdefault('Formfactor', default) ,itemsstorage[item].setdefault('Typeofflashmemory', default) ,itemsstorage[item].setdefault('Connection', default) ,itemsstorage[item].setdefault('Productpage', default) ,itemsstorage[item].setdefault('Weight', default) ,itemsstorage[item].setdefault('Maximumwritespeed', default) ,itemsstorage[item].setdefault('Size', default)))
        print str(count) + " uploads to DB remaining"

def HDD():
    urls = ["https://pricespy.co.nz/category.php?m=s321423383"]
    itemsstorage = ScraperMainTask(urls, "HDD")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `hdd`(`Internaltransferrate`, `Productpage`, `Hybriddisk`, `PriceTB`, `Formfactor`, `Manufacturerwarranty`, `Interface`, `Cachesize`, `Connection`, `Releaseyear`, `Rotationalspeed`, `Noiselevel`, `Harddrivesize`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Internaltransferrate', default), itemsstorage[item].setdefault('Productpage', default),itemsstorage[item].setdefault('Hybriddisk', default),itemsstorage[item].setdefault('Price/TB', default),itemsstorage[item].setdefault('Formfactor', default),itemsstorage[item].setdefault('Manufacturerwarranty', default),itemsstorage[item].setdefault('Interface', default),itemsstorage[item].setdefault('Cachesize', default),itemsstorage[item].setdefault('Connection', default),itemsstorage[item].setdefault('Releaseyear', default),itemsstorage[item].setdefault('Rotationalspeed', default),itemsstorage[item].setdefault('Noiselevel', default),itemsstorage[item].setdefault('Harddrivesize', default)))
        print str(count) + " uploads to DB remaining"

def CASE():
    urls = ["https://pricespy.co.nz/category.php?m=s321423946"]
    itemsstorage = ScraperMainTask(urls, "CASE")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ( itemsstorage, \
        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
        "INSERT INTO `systemcase`(`Typeofchassis`, `Material`, `Dimensions`, `Productpage`, `Numberofcardslots`, `35drivebays`, `Screwlessdesign`, `Format`, `Positionofthepowersupply`, `Volume`, `Supportedmotherboards`, `Colour`, `Roomforexpansion`, `Releaseyear`, `Maximumlengthofvideocard`, `Heightofexpansionslots`, `Activecooling`, `525drivebays`, `Weight`, `Watercooling`, `Fanspacestotal`, `Maximummotherboardsize`, `25drivebays`, `MaxCPUcoolerheight`, `Builtinwatercooling`, `Frontconnections`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Typeofchassis', default), itemsstorage[item].setdefault('Material', default), itemsstorage[item].setdefault('Dimensions', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('Numberofcardslots', default), itemsstorage[item].setdefault('3.5drivebays', default), itemsstorage[item].setdefault('Screwlessdesign', default), itemsstorage[item].setdefault('Format', default), itemsstorage[item].setdefault('Positionofthepowersupply', default), itemsstorage[item].setdefault('Volume', default), itemsstorage[item].setdefault('Supportedmotherboards', default), itemsstorage[item].setdefault('Colour', default), itemsstorage[item].setdefault('Roomforexpansion', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Maximumlengthofvideocard', default), itemsstorage[item].setdefault('Heightofexpansionslots', default), itemsstorage[item].setdefault('Activecooling', default), itemsstorage[item].setdefault('5.25drivebays', default), itemsstorage[item].setdefault('Weight', default), itemsstorage[item].setdefault('Watercooling', default), itemsstorage[item].setdefault('Fanspacestotal', default), itemsstorage[item].setdefault('Maximummotherboardsize', default), itemsstorage[item].setdefault('2.5drivebays', default), itemsstorage[item].setdefault('MaxCPUcoolerheight', default), itemsstorage[item].setdefault('Built-inwatercooling', default), itemsstorage[item].setdefault('Frontconnections', default)))
        print str(count) + " uploads to DB remaining"

#\[value-[0-9]{1,}\]

#def SPARE():
#    urls = [""]
#    itemsstorage = ScraperMainTask(urls, "")
#    for item in itemsstorage:
#        ScraperUpload ( itemsstorage, \
#        "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), \
#        " VALUES (, LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Numberofmodules', default), )


CPU()
GPU()
RAM()
MOBO()
PSU()
SSD()
HDD()
CASE()

print "All tasks completed"
