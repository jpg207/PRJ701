import linecache
import sys
import urllib
import re
from bs4 import BeautifulSoup
import MySQLdb
reload(sys)
sys.setdefaultencoding('utf8')

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
            print "        " + itemdet #+ "  D:  " + str(itemsstorage[item][itemdet])
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
    linkregex = re.compile(r'Product Page')
    removebrackets = r'\([^)]*\)'
    removecomma = r","
    removeapostrophe = r"'"
    removedash = r"-"
    idsort = r"\d+$"
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
                if not linkregex.search(product):
                    product = product.replace("->", "")
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
                        productdetailvalue = None
                    else:
                        #Add all details to the dictionary
                        productdetailvalue = productdetailvalue.encode('utf-8')
                    itemsdetails[productdetail] = productdetailvalue
            itemsdetails["Link"] = link.encode('utf-8')
            CompID = re.search(idsort, itemsdetails["Link"])
            itemsdetails["CompID"] = CompID.group()
            itemsdetails["Price"] = productcost.encode('utf-8')
            itemsstorage[producttitle] = itemsdetails #Store item details in Item storage for upload
            #for i in itemsdetails:
            #    print i, itemsdetails[i]
            print str(len(stored) - count) + " " + id + " links remaining" #Print number of links remaining
            #break
        except Exception:
            PrintException()
    #PrintResults(itemsstorage)
    return itemsstorage

def ScraperCreateStatement(itemsstorage, item):
    feildsquery = ""
    detailsquery = ""
    for detail in itemsstorage[item]:
        if detail != "Link" and detail != "Price":
            processeddetail = re.sub(r'[^a-zA-Z0-9]', '', detail)
            if feildsquery == "":
                try:
                    feildsquery = "`" + processeddetail + "`"
                    detailsquery = "'" + str(itemsstorage[item][detail]) + "'"
                except Exception as e:
                    print "Nothing"
                detailsquery = "'" + str(itemsstorage[item][detail]) + "'"
            else:
                try:
                    feildsquery = feildsquery + ", `" + processeddetail + "`"
                    detailsquery = detailsquery + ", '" + str(itemsstorage[item][detail])  + "'"
                except Exception as e:
                    print "Nothing"
    return feildsquery, detailsquery

def ScraperUpload(ComponentUpload, DetailsUpload):
    db = MySQLdb.connect("localhost", "root","","compcreator", charset="utf8")
    cursor = db.cursor()
    try:
        #print ComponentUpload
        cursor.execute(ComponentUpload)
        db.commit()
        #print DetailsUpload
        cursor.execute(DetailsUpload)
        db.commit()
    except MySQLdb.Error, e:
       # Rollback in case there is any error
       print "MySQL failiure: " + str(e)
       db.rollback()

def RemoveLegacyItems():
    try:
        db = MySQLdb.connect("localhost", "root","","compcreator", charset="utf8")
        cursor = db.cursor()
        cursor.execute("""DELETE FROM component WHERE CompDate < NOW() - INTERVAL 1 DAY""")
        print "Legacy data has been cleared!!"
    except MySQLdb.Error, e:
       # Rollback in case there is any error
       print "MySQL failiure: " + str(e)
       db.rollback()

def ScraperPassMarkTask(passmarkurl, id, passmarkregex):
    itemsstorage = {}
    dualregex = r'(Dual)'
    removeat = r'@.*'
    removedash = r'-'

    try:
        htmltext = urllib.urlopen(passmarkurl).read()#Reads the URL and saves its text
        soup = BeautifulSoup(htmltext, "lxml")# Sets up BeautifulSoup
        for row in soup.find_all('tr', id=re.compile(passmarkregex)):
            column = 1
            ItemRow = {}
            for cell in row.find_all('td'):
                arraytitle = 0
                celltext = cell.get_text()
                if column == 1:
                    arraytitle = "Name"
                    celltext = re.sub(removeat, '', celltext)
                    celltext = re.sub(removedash, ' ', celltext)
                elif column == 2:
                    arraytitle = "Score"
                elif column == 3:
                    arraytitle = "Rank"
                elif column == 5:
                    arraytitle = "Price"
                if arraytitle != 0:
                    ItemRow[arraytitle] = celltext.encode('utf-8')
                column += 1
            if ItemRow['Price'] != "NA":
                itemsstorage[ItemRow["Name"]] = ItemRow
            break
    except Exception:
        PrintException()
    PrintResults(itemsstorage)
    return itemsstorage

def ScraperUploadPassMark(DetailsUpload):
    db = MySQLdb.connect("localhost", "root","","compcreator", charset="utf8")
    cursor = db.cursor()
    try:
        cursor.execute(DetailsUpload)
        db.commit()
    except MySQLdb.Error, e:
       # Rollback in case there is any error
       print "MySQL failiure: " + str(e)
       db.rollback()

def CPU():
    print "Starting CPU scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321418940"]
    itemsstorage = ScraperMainTask(urls, "CPU")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']), """REPLACE  INTO `CPU`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"
    passmarkurl = "https://www.cpubenchmark.net/cpu_list.php"
    passmarkregex = r'(cpu[\d]*)'
    print "Starting CPU rank scrap"
    itemsstorage = ScraperPassMarkTask(passmarkurl, "CPU", passmarkregex)
    count = len(itemsstorage)
    for item in itemsstorage:
        ScraperUploadPassMark("""UPDATE cpu INNER JOIN component ON cpu.CompID = component.CompID SET CPURating= %s WHERE component.CompName LIKE '%s'""" % (itemsstorage[item]["Rank"], "%" + itemsstorage[item]["Name"] + "%"))
    print "CPU uploads complete"

def GPU():
    print "Starting GPU scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321383620"]
    itemsstorage = ScraperMainTask(urls, "GPU")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `GPU`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"
    passmarkurl = "https://www.videocardbenchmark.net/gpu_list.php"
    passmarkregex = r'(gpu[\d]*)'
    print "Starting GPU rank scrap"
    itemsstorage = ScraperPassMarkTask(passmarkurl, "GPU", passmarkregex)
    count = len(itemsstorage)
    for item in itemsstorage:
        ScraperUploadPassMark("""UPDATE gpu INNER JOIN component ON gpu.CompID = component.CompID SET GPURating= %s WHERE component.CompName LIKE '%s'""" % (itemsstorage[item]["Rank"], "%" + itemsstorage[item]["Name"] + "%"))
    print "GPU uploads complete"

def RAM():
    print "Starting RAM scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321420922", "https://pricespy.co.nz/category.php?m=s321421236"]
    itemsstorage = ScraperMainTask(urls, "RAM")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `Memory`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def MOBO():
    print "Starting MOBO scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321421551"]
    itemsstorage = ScraperMainTask(urls, "MOBO")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `MotherBoard`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def PSU():
    print "Starting PSU scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321422467"]
    itemsstorage = ScraperMainTask(urls, "PSU")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `PSU`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def SSD():
    print "Starting SSD scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321751548"]
    itemsstorage = ScraperMainTask(urls, "SSD")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `SSD`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def HDD():
    print "Starting HDD scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321423383"]
    itemsstorage = ScraperMainTask(urls, "HDD")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `HDD`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def CASE():
    print "Starting CASE scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321751584"]
    itemsstorage = ScraperMainTask(urls, "CASE")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `SystemCase`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def ODD():
    print "Starting ODD scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321944433"]
    itemsstorage = ScraperMainTask(urls, "ODD")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `ODD`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"

def AirCooler():
    print "Starting AirCooler scrap"
    urls = ["https://pricespy.co.nz/category.php?m=s321944431"]
    itemsstorage = ScraperMainTask(urls, "AirCooler")
    count = len(itemsstorage)
    for item in itemsstorage:
        count = count - 1
        feildsquery, detailsquery = ScraperCreateStatement(itemsstorage, item)
        ScraperUpload ("""REPLACE  INTO component(CompID, CompName, CompPrice, CompLink) VALUES ('%s', '%s', '%s', '%s')""" % (itemsstorage[item]['CompID'], item, itemsstorage[item]['Price'], itemsstorage[item]['Link']),  """REPLACE  INTO `AirCooler`({}) VALUES ({})""".format(feildsquery, detailsquery))
        print str(count) + " uploads to DB remaining"


CPU()
GPU()
RAM()
MOBO()
PSU()
SSD()
HDD()
CASE()
ODD()
#AirCooler()
RemoveLegacyItems()

print "All tasks completed"
