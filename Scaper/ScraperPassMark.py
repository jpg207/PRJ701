import linecache
import sys
import urllib
import re
from bs4 import BeautifulSoup
import MySQLdb

default = 0

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

def ScraperMainTask(url, id, mainregex):
    itemsstorage = {}
    dualregex = r'(Dual)'
    removeat = r'@.*'
    removedash = r'-'

    try:
        htmltext = urllib.urlopen(url).read()#Reads the URL and saves its text
        soup = BeautifulSoup(htmltext, "lxml")# Sets up BeautifulSoup
        for row in soup.find_all('tr', id=re.compile(mainregex)):
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
    except Exception:
        PrintException()
    #PrintResults(itemsstorage)
    return itemsstorage

def ScraperUpload(DetailsUpload):
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
    url = "https://www.cpubenchmark.net/cpu_list.php"
    mainregex = r'(cpu[\d]*)'
    print "Starting CPU scrap"
    itemsstorage = ScraperMainTask(url, "CPU", mainregex)
    count = len(itemsstorage)
    for item in itemsstorage:
        ScraperUpload("""UPDATE cpu INNER JOIN component ON cpu.CompID = component.CompID SET CPURating= %s WHERE component.CompName LIKE '%s'""" % (itemsstorage[item]["Rank"], "%" + itemsstorage[item]["Name"] + "%"))
        print str(count) + " CPU uploads remaining"
        count = count - 1

def GPU():
    url = "https://www.videocardbenchmark.net/gpu_list.php"
    mainregex = r'(gpu[\d]*)'
    print "Starting GPU scrap"
    itemsstorage = ScraperMainTask(url, "GPU", mainregex)
    count = len(itemsstorage)
    for item in itemsstorage:
        ScraperUpload("""UPDATE gpu INNER JOIN component ON gpu.CompID = component.CompID SET GPURating= %s WHERE component.CompName LIKE '%s'""" % (itemsstorage[item]["Rank"], "%" + itemsstorage[item]["Name"] + "%"))
        print str(count) + " GPU uploads remaining"
        count = count - 1

CPU()
GPU()

print "All tasks completed"
