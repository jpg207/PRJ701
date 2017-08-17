import urllib

urls = ["https://pricespy.co.nz/","https://google.co.nz/"]

i=0

for url in urls:
    htmlfile = urllib.urlopen(url)
    htmltext = htmlfile.read()
    print htmltext
    print "++++++++++++++++++++++++++++++++++++++"
    i+=1
