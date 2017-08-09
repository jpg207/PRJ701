import urllib

urls = ["https://pricespy.co.nz/","https://google.co.nz/"]

i=0

while i< len(urls):
    htmlfile = urllib.urlopen(urls[i])
    htmltext = htmlfile.read()
    print htmltext
    print "++++++++++++++++++++++++++++++++++++++"
    i+=1
