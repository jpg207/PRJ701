from threading import Thread
import urllib
import re
import MySQLdb

gmap = {}

def th(ur):
    regex = '<title>(.+?)</title>'
    pattern = re.compile(regex)
    htmltext = urllib.urlopen(ur).read()
    results = re.findall(pattern, htmltext)
    try:
        gmap[ur] = results[0]
    except:
        print "ERROR"

urls = "https://pricespy.co.nz/ https://google.co.nz/".split()
threadlist = []

for u in urls:
    t = Thread(target=th,args=(u,))
    t.start()
    threadlist.append(t)

for b in threadlist    :
    b.join


for key in gmap.keys():
    print key,gmap[key]



db = MySQLdb.connect("localhost", "root","","compcreator")
cursor = db.cursor()

sql = "INSERT INTO component(CompName, CompPrice, CompLink) values ('Test',10,'Test')"
try:
   # Execute the SQL command
   cursor.execute(sql)
   # Commit your changes in the database
   db.commit()
except:
   # Rollback in case there is any error
   db.rollback()
