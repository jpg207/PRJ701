import urllib
import re

urls = ["https://pricespy.co.nz/","https://google.co.nz/"]

i=0

regex = '<title>(.+?)</title>'
pattern = re.compile(regex)

while i< len(urls):
    htmlfile = urllib.urlopen(urls[i])
    htmltext = htmlfile.read()
    titles = re.findall(pattern, htmltext)
    print titles
    print "++++++++++++++++++++++++++++++++++++++"
    i+=1
