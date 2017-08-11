#from threading import Thread
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
urls = [url]
stored = []
nevervisit = []
visited = []
itemsstorage = {}

subregex = r"\?(p)="
mainregex = r'^https://pricespy\.co\.nz\/product\.php\?p=[0-9]{7}$'
cutregex = r'(Suggest change - Beta)'
detailregex = r'(.*?)[\W]{2,}([\w].*)'

count = 0
while urls:
    try:
        htmltext = urllib.urlopen(urls[0]).read()
        soup = BeautifulSoup(htmltext, "lxml")
        count += 1
        urls.pop(0)
        # print len(urls)

        for tag in soup.find_all('a', href=True):
            # print tag['href']
            link = base + tag['href']
            # print link
            #link = "https://pricespy.co.nz/product.php?p=3692646"
            # print matches
            if link not in visited and re.search(mainregex, link, re.IGNORECASE | re.VERBOSE):
                visited.append(link)
                link = re.sub(subregex, "?e=", link)
                stored.append(link)
                # urls.append(link)
                # print "Match"
                # print soup.title
                # print link
            elif link not in stored:
                nevervisit.append(link)
                # print "No match"
    except Exception, e:
        print "ERROR set 1 " + str(e)
pass

# print "Initial pass"

for link in stored:
    itemsdetails = {}
    # try:
    # print item
    htmltext = urllib.urlopen(link).read()
    # print htmltext
    soup = BeautifulSoup(htmltext, "lxml")
    # print soup
    count += 1
    producttitle = soup.find("h1", class_="intro_header").get_text()
    products = soup.find_all("tr", class_="erow")
    for product in products:
        product = re.sub(cutregex, "", product.get_text())
        product = product.strip()
        product = product.split("u'")
        product = product[len(product)-1]
        #print product
        product = re.search(detailregex, product)
        #print product
        if product:
            #print product
            productdetail = product.group(1).strip()
            #print productdetail
            productdetailvalue = product.group(2).strip()
            #print productdetailvalue
            itemsdetails[productdetail] = productdetailvalue
            itemsdetails["Link"] = link
            #print itemsdetails
    itemsstorage[producttitle] = itemsdetails
    print "Links remaining " + str(len(stored)-count)
    break
    #for item in itemsstorage:
    #    print  item
    #    for itemdet in itemsstorage[item]:
    #        print "        " + itemdet + " " + itemsstorage[item][itemdet]
    #    print "\n ++++++++++++++++++++++++ \n"

db = MySQLdb.connect("localhost", "root","","compcreator")
cursor = db.cursor()

sql = "SELECT 1 FROM `gpu` LIMIT 1"
try:
   # Execute the SQL command
   cursor.execute(sql)
   # Commit your changes in the database
   db.commit()
except:
   # Rollback in case there is any error
   db.rollback()
   table = "gpu"
   columns = ', '.join(itemsstorage)
   sql = "CREATE TABLE %s ( %s VARCHAR(250));" % (table, columns)
   cursor.execute(sql)



sql = "INSERT INTO component(CompName, CompPrice, CompLink) values ( , , )"
try:
   # Execute the SQL command
   cursor.execute(sql)
   # Commit your changes in the database
   db.commit()
except:
   # Rollback in case there is any error
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
