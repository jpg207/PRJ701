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

# ADD all base URLS to array on first load (TO BE COMPLETED)
url = "https://pricespy.co.nz/category.php?m=s321383620"
base = "https://pricespy.co.nz"
page = "https://pricespy.co.nz/category.php"
default = "null"
urls = [url]
stored = []
nevervisit = []
visited = []
itemsstorage = {}

mainregex = r'^https://pricespy\.co\.nz\/product\.php\?p=[0-9]{1,}$'
subregex = r"\?(p)="
#cutregex = r'(Suggest change - Beta)'
#cutregex2 = r'(-&gt;)'

#.*?[\W]{2,}.*?|
detailregex = r'(.*?)[\W]{2,}([\w].*)'
pageregex = r'^https://pricespy\.co\.nz\/category\.php\?[a-zA-Z]=[A-Za-z0-9]{1,}&s=[0-9]{1,}$'


def PrintException():
    exc_type, exc_obj, tb = sys.exc_info()
    f = tb.tb_frame
    lineno = tb.tb_lineno
    filename = f.f_code.co_filename
    linecache.checkcache(filename)
    line = linecache.getline(filename, lineno, f.f_globals)
    print 'EXCEPTION IN ({}, LINE {} "{}"): {}'.format(filename, lineno, line.strip(), exc_obj)


count = 0
while urls:
    #try:
        htmltext = urllib.urlopen(urls[0]).read()
        soup = BeautifulSoup(htmltext, "lxml")
        count += 1
        urls.pop(0)
        print len(urls)

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
count =0
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
            #product = re.sub(cutregex, "", product.get_text())
            product = product.replace("Suggest change - Beta","")
            product = product.replace("->", "-")
            #print product
            product = product.strip()
            #product = product.split("u'")
            #product = product[len(product)-1]
            #product = re.sub(cutregex2, "-", product.get_text())
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
        print "Links remaining " + str(len(stored)-count)
        #for item in itemsstorage:
        #    print  item
        #    for itemdet in itemsstorage[item]:
        #        print "        " + itemdet + " " + itemsstorage[item][itemdet]
        #    print "\n ++++++++++++++++++++++++ \n"
        #break
    except Exception:
        PrintException()

for item in itemsstorage:
    db = MySQLdb.connect("localhost", "root","","compcreator")
    cursor = db.cursor()
    #print item + "\n" + itemsstorage[item]['Price'] + "\n" + itemsstorage[item]['Link']
    sql = "INSERT INTO component(CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s')" % (item, itemsstorage[item]['Price'], itemsstorage[item]['Link'])

    sql2 = "INSERT INTO gpu(`Cooling`, `Numberoffans`, `Semipassive`, `Factoryoverclocked`, `Graphicsprocessor`, `Lowprofile`, `Nonreferencecooler`, `PCIExpressversion`, `DisplayPort`, `NumberofDisplayPortoutputs`, `DVI`, `NumberofDVIoutputs`, `HDMI`, `NumberofHDMIoutputs`, `VGAoutputs`, `Maximumresolution`, `Numberofsupportedmonitors`, `Length`, `Numberofslots`, `DirectX`, `HDR`, `OpenGL`, `Vulkan`, `Supportformultiplegraphicscards`, `Memorybandwidth`, `Memorycapacity`, `Memoryinterface`, `Memoryspeed`, `Memorytype`, `GPUBoost`, `Processorspeed`, `Supplementarypowerconnector`, `Manufacturerwarranty`, `Releaseyear`, `Productpage`, `GPURating`, `CompID`) VALUES ('%s' ,'%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 1111, LAST_INSERT_ID())" % ( itemsstorage[item].setdefault('Cooling', default), itemsstorage[item].setdefault('Numberoffans', default), itemsstorage[item].setdefault('Semi-passive', default), itemsstorage[item].setdefault('Factoryoverclocked', default), itemsstorage[item].setdefault('Graphicsprocessor', default), itemsstorage[item].setdefault('Lowprofile', default), itemsstorage[item].setdefault('Non-referencecooler', default), itemsstorage[item].setdefault('PCIExpressversion', default), itemsstorage[item].setdefault('DisplayPort', default),  itemsstorage[item].setdefault('NumberofDisplayPortoutputs', default), itemsstorage[item].setdefault('DVI', default), itemsstorage[item].setdefault('NumberofDVIoutputs', default), itemsstorage[item].setdefault('HDMI', default), itemsstorage[item].setdefault('NumberofHDMIoutputs', default), itemsstorage[item].setdefault('VGAoutputs', default), itemsstorage[item].setdefault('Maximumresolution', default), itemsstorage[item].setdefault('Numberofsupportedmonitors', default), itemsstorage[item].setdefault('Length', default), itemsstorage[item].setdefault('Numberofslots', default), itemsstorage[item].setdefault('DirectX', default), itemsstorage[item].setdefault('HDR', default), itemsstorage[item].setdefault('OpenGL', default), itemsstorage[item].setdefault('Vulkan', default), itemsstorage[item].setdefault('Supportformultiplegraphicscards', default), itemsstorage[item].setdefault('Memorybandwidth', default), itemsstorage[item].setdefault('Memorycapacity', default), itemsstorage[item].setdefault('Memoryinterface', default), itemsstorage[item].setdefault('Memoryspeed', default), itemsstorage[item].setdefault('Memorytype', default), itemsstorage[item].setdefault('GPUBoost', default), itemsstorage[item].setdefault('Processorspeed', default), itemsstorage[item].setdefault('Supplementarypowerconnector', default), itemsstorage[item].setdefault('Manufacturerwarranty', default), itemsstorage[item].setdefault('Releaseyear', default), itemsstorage[item].setdefault('Productpage', default))

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
       print "Error occured on item: " + item
       for itemdet in itemsstorage[item]:
           print "        " + itemdet + " +++++++++ " + itemsstorage[item][itemdet]
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
