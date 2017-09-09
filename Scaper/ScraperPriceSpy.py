import linecache
import sys
import urllib
import re
from bs4 import BeautifulSoup
import MySQLdb

default = 0
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

def PrintResults(itemsstorage):
    for item in itemsstorage:
        print  item
        for itemdet in itemsstorage[item]:
            print "        " + itemdet + "  D:  " + itemsstorage[item][itemdet]
        print "\n ++++++++++++++++++++++++ \n"

def ScraperMainTask(urls, id):
    stored = [] #Array of products to scrape
    nevervisit = [] #Array of invalid URLs
    visited = [] #Array of visited links
    itemsstorage = {} #Dictionary of scraped products
    #Regular expretion statements
    mainregex = r'^https://pricespy\.co\.nz\/product\.php\?p=[0-9]{1,}$'
    subregex = r"\?(p)="
    cutregex = r"(List [\w]* \([\d]* [\w]*, from .*\))"
    detailregex = r'(.*?)[\W]{2,}([\w].*)'
    removebrackets = r'\([^)]*\)'
    removecomma = r","
    removeapostrophe = r"'"
    removedash = r"-"
    pageregex = r'^https://pricespy\.co\.nz\/category\.php\?[a-zA-Z]=[A-Za-z0-9]{1,}&s=[0-9]{1,}$'

    count = 0
    while urls: #Cycles through each URL passed in
        try:
            htmltext = urllib.urlopen(urls[0]).read()#Reads the URL and saves its text
            soup = BeautifulSoup(htmltext, "lxml")# Sets up BeautifulSoup
            count += 1
            urls.pop(0)#Removes current url from list of URLs pased in
            for tag in soup.find_all('a', href=True):#Finds any link in on the current page with a active link
                link = base + tag['href']#Merges the base URL with the one from the link
                category = page + tag['href']#Merges category URL with one from link
                if link not in visited and re.search(mainregex, link, re.IGNORECASE | re.VERBOSE):
                    #If the link is not in the visted array and matches the regular exprestion for being a product link
                    #and then addes the link to the appropriate arrays
                    visited.append(link)
                    link = re.sub(subregex, "?e=", link)
                    stored.append(link)
                elif link not in visited and re.search(pageregex, category, re.IGNORECASE | re.VERBOSE):
                    #If the link is not in the visted array and matches the regular exprestion for being a category link
                    #and then addes the link to the appropriate arrays
                    visited.append(link)
                    urls.append(category)
                elif link not in stored:
                    #Link must be invalid
                    nevervisit.append(link)
        except Exception:
            PrintException()
    count = 0
    for link in stored: #Loop through each product link in stored
        itemsdetails = {} #Sets itemsdetails to nothing
        try:
            htmltext = urllib.urlopen(link).read() #Read url
            soup = BeautifulSoup(htmltext, "lxml")
            count += 1
            producttitle = soup.find("h1", class_="intro_header").get_text() #Get product title
            producttitle = producttitle.strip() #Strip off white space
            productcost = soup.find("span", class_="price").get_text() #Get product price
            productcost = productcost.lstrip('$') #Strip price of $
            productcost = re.sub(removecomma, '', productcost)
            products = soup.find_all("tr", class_="erow") #Get all product details
            for product in products: #Loop through all product details
                product = product.get_text() #Get detail text
                #Remove unwanted information from text
                product = product.replace("Suggest change - Beta","")
                product = re.sub(cutregex , "", product)
                product = product.replace("->", "-")
                product = re.sub(removeapostrophe, '', product)
                product = re.sub(removebrackets, '', product)
                product = re.sub(removedash, '', product)
                #if (re.match(matchGB, product)):
                #    product = re.sub(removeGB, '', product)
                #product = re.sub(removecomma, '', product)
                product = product.strip()
                product = re.search(detailregex, product) #Split product detail and detail title
                if product:
                    productdetail = product.group(1).replace(" ", "")
                    productdetailvalue = product.group(2).strip()
                    if productdetailvalue == "Contribute": #Null detail if only value is Contribute
                        productdetailvalue = default
                    else:
                        #Add all details to the dictionary
                        productdetailvalue = productdetailvalue.encode('utf-8')
                    itemsdetails[productdetail] = productdetailvalue
            itemsdetails["Link"] = link.encode('utf-8')
            itemsdetails["Price"] = productcost.encode('utf-8')
            itemsstorage[producttitle] = itemsdetails #Store item details in Item storage for upload
            #for i in itemsdetails:
            #    print i, itemsdetails[i]
            print str(len(stored) - count) + " " + id + " links remaining" #Print number of links remaining
            #break
        except Exception:
            PrintException()
    return itemsstorage

def ScraperUpload(ComponentUpload, DetailsUpload):
    db = MySQLdb.connect("localhost", "root","","compcreator", charset="utf8")
    cursor = db.cursor()
    try:
        cursor.execute(ComponentUpload)
        db.commit()
        cursor.execute(DetailsUpload)
        db.commit()
    except MySQLdb.Error, e:
       # Rollback in case there is any error
       print "MySQL failiure: " + str(e)
       db.rollback()

def ScraperDelete(delete):
    try:
        db = MySQLdb.connect("localhost", "root","","compcreator", charset="utf8")
        cursor = db.cursor()
        cursor.execute("""SELECT CompID FROM %s""" % (delete))
        results = cursor.fetchall()
        for row in results:
            cursor.execute("""DELETE FROM component WHERE CompID = %s""" % (row))
        db.commit()
        print "Table " + delete + " has been cleared of data!!"
    except MySQLdb.Error, e:
       # Rollback in case there is any error
       print "MySQL failiure: " + str(e)
       db.rollback()

def CPU():
    print "Starting CPU scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321418940"]
    itemsstorage = ScraperMainTask(urls, "CPU")
    count = len(itemsstorage)
    ScraperDelete("cpu")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `cpu`(`ClockFrequency`, `ProductPage`, `L3Cache`, `TurboBoostCore`, `CPUType`, `BoxVersion`, `NumberOfThreads`, `64bitProcessor`, `L2Cache`, `Socket`, `NumberOfCores`, `GraphicsProcessor`, `ReleaseYear`, `ThermalDesignPower`, `IntegratedGraphics`, `Virtualization`, `CPURating`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 999999, LAST_INSERT_ID())""" %(itemsstorage[item].setdefault('Clockfrequency', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('L3cache', default), itemsstorage[item].setdefault('TurboBoost/Core', default), itemsstorage[item].setdefault('CPUtype', default), itemsstorage[item].setdefault('Boxversion', default), itemsstorage[item].setdefault('Numberofthreads', default), itemsstorage[item].setdefault('64-bitprocessor', default), itemsstorage[item].setdefault('L2cache', default), itemsstorage[item].setdefault('Socket', default), itemsstorage[item].setdefault('Numberofcores', default), itemsstorage[item].setdefault('Graphicsprocessor', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('ThermalDesignPower', default), itemsstorage[item].setdefault('Integratedgraphics', default), itemsstorage[item].setdefault('Virtualization', default)   ))
        print str(count) + " uploads to DB remaining"

def GPU():
    print "Starting GPU scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321383620"]
    itemsstorage = ScraperMainTask(urls, "GPU")
    count = len(itemsstorage)
    ScraperDelete("gpu")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `gpu`(`Cooling`, `NumberOfFans`, `SemiPassive`, `FactoryOverclocked`, `GraphicsProcessor`, `LowProfile`, `NonreferenceCooler`, `PCIExpressVersion`, `Displayport`, `NumberOfDisplayportoutputs`, `DVI`, `NumberOfDVIOutputs`, `HDMI`, `NumberOfHdmiOutputs`, `VGAOutputs`, `MaximumResolution`, `NumberOfSupportedMonitors`, `Length`, `NumberOfSlots`, `DirectX`, `HDR`, `OpenGL`, `Vulkan`, `SupportForMultipleGraphicsCards`, `MemoryBandwidth`, `MemoryCapacity`, `MemoryInterface`, `MemorySpeed`, `MemoryType`, `GPUBoost`, `ProcessorSpeed`, `SupplementaryPowerConnector`, `ManufacturerWarranty`, `ReleaseYear`, `ProductPage`, `GPURating`, `CompID`) VALUES ('%s' ,'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 999999, LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Cooling', default), itemsstorage[item].setdefault('Numberoffans', default), itemsstorage[item].setdefault('Semi-passive', default), itemsstorage[item].setdefault('Factoryoverclocked', default), itemsstorage[item].setdefault('Graphicsprocessor', default), itemsstorage[item].setdefault('Lowprofile', default), itemsstorage[item].setdefault('Non-referencecooler', default), itemsstorage[item].setdefault('PCIExpressversion', default), itemsstorage[item].setdefault('DisplayPort', default),  itemsstorage[item].setdefault('NumberofDisplayPortoutputs', default), itemsstorage[item].setdefault('DVI', default), itemsstorage[item].setdefault('NumberofDVIoutputs', default), itemsstorage[item].setdefault('HDMI', default), itemsstorage[item].setdefault('NumberofHDMIoutputs', default), itemsstorage[item].setdefault('VGAoutputs', default), itemsstorage[item].setdefault('Maximumresolution', default), itemsstorage[item].setdefault('Numberofsupportedmonitors', default), itemsstorage[item].setdefault('Length', default), itemsstorage[item].setdefault('Numberofslots', default), itemsstorage[item].setdefault('DirectX', default), itemsstorage[item].setdefault('HDR', default), itemsstorage[item].setdefault('OpenGL', default), itemsstorage[item].setdefault('Vulkan', default), itemsstorage[item].setdefault('Supportformultiplegraphicscards', default), itemsstorage[item].setdefault('Memorybandwidth', default), itemsstorage[item].setdefault('Memorycapacity', default), itemsstorage[item].setdefault('Memoryinterface', default), itemsstorage[item].setdefault('Memoryspeed', default), itemsstorage[item].setdefault('Memorytype', default), itemsstorage[item].setdefault('GPUBoost', default), itemsstorage[item].setdefault('Processorspeed', default), itemsstorage[item].setdefault('Supplementarypowerconnector', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Productpage', default)))
        print str(count) + " uploads to DB remaining"

def RAM():
    print "Starting RAM scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321420922", "https://pricespy.co.nz/category.php?m=s321421236"]
    itemsstorage = ScraperMainTask(urls, "RAM")
    count = len(itemsstorage)
    ScraperDelete("memory")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `memory`(`NumberOfModules`, `MemorySpeed`, `MemoryCapacity`, `ECC`, `ReleaseYear`, `PricePerGigabyte`, `ManufacturerWarranty`, `MemoryCapacityPerModule`, `CASLatency`, `TypeOfMemory`, `ProductPage`, `Voltage`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Numberofmodules', default), itemsstorage[item].setdefault('Memoryspeed', default), itemsstorage[item].setdefault('Memorycapacity', default), itemsstorage[item].setdefault('ECC', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Price/GB', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('Memorycapacitypermodule', default), itemsstorage[item].setdefault('CASLatency', default), itemsstorage[item].setdefault('Typeofmemory', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('Voltage', default)))
        print str(count) + " uploads to DB remaining"

def MOBO():
    print "Starting MOBO scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321421551"]
    itemsstorage = ScraperMainTask(urls, "MOBO")
    count = len(itemsstorage)
    ScraperDelete("motherboard")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `motherboard`(`Width`, `Cooling`, `FormFactor`, `PCISlots`, `NumberOfHdmiOutputs`, `EthernetConnection`, `Socket`, `HDMI`, `HeadPhoneOutput`, `TypeOfMemory`, `USB2`, `mSATA`, `Bluetooth`, `ECCSupport`, `PCIExpressx8`, `TypeOfRaid`, `PCIExpressx1`, `PCIExpressx4`, `ManufacturerWarranty`, `PCIExpressVersion`, `SoundCard`, `PCIExpressx16`, `MemorySpeeds`, `ProductPage`, `ChassisFanConnectors`, `PCIExpressMini`, `BluetoothVersion`, `MemorySlots`, `MiniPCI`, `DVI`, `SupportForMultipleGraphicsCards`, `SupportForIIntegratedGraphicsInCPU`, `SATAExpress`, `MicrophoneInput`, `NumberOfEthernetConnections`, `PowerFanConnector`, `NumberOfDisplayportOutputs`, `Chipset`, `SATA3Gbs`, `MaximumAmountOfMemory`, `Displayport`, `USB`, `SoundCardChip`, `RaidController`, `Thunderbolt`, `64bitProcessor`, `U2`, `Depth`, `ReleaseYear`, `M2`, `VGAOutputs`, `WirelessNetwork`, `SATA6Gbs`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Width', default),  itemsstorage[item].setdefault('Cooling', default), itemsstorage[item].setdefault('Formfactor', default), itemsstorage[item].setdefault('PCIslots', default), itemsstorage[item].setdefault('NumberofHDMIoutputs', default), itemsstorage[item].setdefault('Ethernetconnection', default), itemsstorage[item].setdefault('Socket', default), itemsstorage[item].setdefault('HDMI', default), itemsstorage[item].setdefault('Headphoneoutput', default), itemsstorage[item].setdefault('Typeofmemory', default), itemsstorage[item].setdefault('USB2.0', default), itemsstorage[item].setdefault('mSATA', default), itemsstorage[item].setdefault('Bluetooth', default), itemsstorage[item].setdefault('ECCsupport', default), itemsstorage[item].setdefault('PCIExpressx8', default), itemsstorage[item].setdefault('TypeofRAID', default), itemsstorage[item].setdefault('PCIExpressx1', default), itemsstorage[item].setdefault('PCIExpressx4', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('PCIExpressversion', default), itemsstorage[item].setdefault('Soundcard', default), itemsstorage[item].setdefault('PCIExpressx16', default), itemsstorage[item].setdefault('Memoryspeeds', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('Chassisfanconnectors', default), itemsstorage[item].setdefault('PCIExpressMini', default), itemsstorage[item].setdefault('Bluetoothversion', default), itemsstorage[item].setdefault('Memoryslots', default), itemsstorage[item].setdefault('Mini-PCI', default), itemsstorage[item].setdefault('DVI', default), itemsstorage[item].setdefault('Supportformultiplegraphicscards', default), itemsstorage[item].setdefault('SupportforintegratedgraphicsinCPU', default), itemsstorage[item].setdefault('SATAExpress', default), itemsstorage[item].setdefault('Microphoneinput', default), itemsstorage[item].setdefault('NumberofEthernetconnections', default), itemsstorage[item].setdefault('Powerfanconnector', default), itemsstorage[item].setdefault('NumberofDisplayPortoutputs', default), itemsstorage[item].setdefault('Chipset', default), itemsstorage[item].setdefault('SATA3Gb/s', default), itemsstorage[item].setdefault('Maximumamountofmemory', default), itemsstorage[item].setdefault('DisplayPort', default), itemsstorage[item].setdefault('USB', default), itemsstorage[item].setdefault('Soundcardchip', default), itemsstorage[item].setdefault('RAIDcontroller', default), itemsstorage[item].setdefault('Thunderbolt', default), itemsstorage[item].setdefault('64-bitprocessor', default), itemsstorage[item].setdefault('U.2', default), itemsstorage[item].setdefault('Depth', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('M.2', default),  itemsstorage[item].setdefault('VGAoutputs', default), itemsstorage[item].setdefault('Wirelessnetwork', default), itemsstorage[item].setdefault('SATA6Gb/s', default)))
        print str(count) + " uploads to DB remaining"

def PSU():
    print "Starting PSU scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321422467"]
    itemsstorage = ScraperMainTask(urls, "PSU")
    count = len(itemsstorage)
    ScraperDelete("psu")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `psu`(`Capacity`, `ReleaseYear`, `FanSize`, `Modular`, `CableSocks`, `PowerConnectorsForSata`, `PowerConnectionsForPciExpress`, `TemperatureControlledFan`, `ManufacturerWarranty`, `ProductPage`, `Efficiency`, `NumberOfFans`, `Semipassive`, `Format`, `80PlusCertification`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Capacity', default), itemsstorage[item].setdefault('Releaseyear', default) ,itemsstorage[item].setdefault('Fansize', default) ,itemsstorage[item].setdefault('Modular', default) ,itemsstorage[item].setdefault('Cablesocks', default) ,itemsstorage[item].setdefault('PowerconnectorsforSATA', default) ,itemsstorage[item].setdefault('PowerconnectionsforPCI-Express', default) ,itemsstorage[item].setdefault('Temperature-controlledfan', default) ,itemsstorage[item].setdefault('Manufacturerwarranty', default) ,itemsstorage[item].setdefault('Productpage', default) ,itemsstorage[item].setdefault('Efficiency', default) ,itemsstorage[item].setdefault('Numberoffans', default) ,itemsstorage[item].setdefault('Semi-passive', default) ,itemsstorage[item].setdefault('Format', default) ,itemsstorage[item].setdefault('80-pluscertification', default)))
        print str(count) + " uploads to DB remaining"

def SSD():
    print "Starting SSD scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321423261"]
    itemsstorage = ScraperMainTask(urls, "SSD")
    count = len(itemsstorage)
    ScraperDelete("ssd")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `ssd`(`MaximumReadSpeed`, `ControllerChip`, `PricePerGigabyte`, `ManufacturerWarranty`, `Interface`, `FormFactor`, `TypeOfFlashMemory`, `Connection`, `ProductPage`, `Weight`, `MaximumWriteSpeed`, `Size`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Maximumreadspeed', default), itemsstorage[item].setdefault('Controllerchip', default) ,itemsstorage[item].setdefault('Price/GB', default) ,itemsstorage[item].setdefault('Manufacturerwarranty', default) ,itemsstorage[item].setdefault('Interface', default) ,itemsstorage[item].setdefault('Formfactor', default) ,itemsstorage[item].setdefault('Typeofflashmemory', default) ,itemsstorage[item].setdefault('Connection', default) ,itemsstorage[item].setdefault('Productpage', default) ,itemsstorage[item].setdefault('Weight', default) ,itemsstorage[item].setdefault('Maximumwritespeed', default) ,itemsstorage[item].setdefault('Size', default)))
        print str(count) + " uploads to DB remaining"

def HDD():
    print "Starting HDD scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321423383"]
    itemsstorage = ScraperMainTask(urls, "HDD")
    count = len(itemsstorage)
    ScraperDelete("hdd")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `hdd`(`InternalTransferRate`, `ProductPage`, `HybridDisk`, `PriceperTeraByte`, `FormFactor`, `ManufacturerWarranty`, `Interface`, `CacheSize`, `Connection`, `ReleaseYear`, `RotationalSpeed`, `NoiseLevel`, `HardDriveSize`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Internaltransferrate', default), itemsstorage[item].setdefault('Productpage', default),itemsstorage[item].setdefault('Hybriddisk', default),itemsstorage[item].setdefault('Price/TB', default),itemsstorage[item].setdefault('Formfactor', default),itemsstorage[item].setdefault('Manufacturerwarranty', default),itemsstorage[item].setdefault('Interface', default),itemsstorage[item].setdefault('Cachesize', default),itemsstorage[item].setdefault('Connection', default),itemsstorage[item].setdefault('Releaseyear', default),itemsstorage[item].setdefault('Rotationalspeed', default),itemsstorage[item].setdefault('Noiselevel', default),itemsstorage[item].setdefault('Harddrivesize', default)))
        print str(count) + " uploads to DB remaining"

def CASE():
    print "Starting CASE scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321423946"]
    itemsstorage = ScraperMainTask(urls, "CASE")
    count = len(itemsstorage)
    ScraperDelete("systemcase")
    for item in itemsstorage:
        count = count - 1
        ScraperUpload ("""INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')""" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """INSERT INTO `systemcase`(`TypeOfChassis`, `Material`, `Dimensions`, `ProductPage`, `NumberOfCardSlots`, `35DriveBays`, `ScrewlessDesign`, `Format`, `PositionOfThePowerSupply`, `Volume`, `SupportedMotherboards`, `Colour`, `RoomForExpansion`, `ReleaseYear`, `MaximumLengthOfVideoCard`, `HeightOfExpansionSlots`, `ActiveCooling`, `525DriveBays`, `Weight`, `WaterCooling`, `FanSpacesTotal`, `MaximumMotherboardSize`, `25DriveBays`, `MaxCPUcoolerheight`, `BuiltInWatercooling`, `FrontConnections`, `CompID`) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', LAST_INSERT_ID())""" % ( itemsstorage[item].setdefault('Typeofchassis', default), itemsstorage[item].setdefault('Material', default), itemsstorage[item].setdefault('Dimensions', default), itemsstorage[item].setdefault('Productpage', default), itemsstorage[item].setdefault('Numberofcardslots', default), itemsstorage[item].setdefault('3.5drivebays', default), itemsstorage[item].setdefault('Screwlessdesign', default), itemsstorage[item].setdefault('Format', default), itemsstorage[item].setdefault('Positionofthepowersupply', default), itemsstorage[item].setdefault('Volume', default), itemsstorage[item].setdefault('Supportedmotherboards', default), itemsstorage[item].setdefault('Colour', default), itemsstorage[item].setdefault('Roomforexpansion', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Maximumlengthofvideocard', default), itemsstorage[item].setdefault('Heightofexpansionslots', default), itemsstorage[item].setdefault('Activecooling', default), itemsstorage[item].setdefault('5.25drivebays', default), itemsstorage[item].setdefault('Weight', default), itemsstorage[item].setdefault('Watercooling', default), itemsstorage[item].setdefault('Fanspacestotal', default), itemsstorage[item].setdefault('Maximummotherboardsize', default), itemsstorage[item].setdefault('2.5drivebays', default), itemsstorage[item].setdefault('MaxCPUcoolerheight', default), itemsstorage[item].setdefault('Built-inwatercooling', default), itemsstorage[item].setdefault('Frontconnections', default)))
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
