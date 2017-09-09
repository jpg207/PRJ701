import urllib

urls = ["https://www.videocardbenchmark.net/gpu_list.php"]

i=0

for url in urls:
    htmlfile = urllib.urlopen(url)
    htmltext = htmlfile.read()
    print htmltext
    print "++++++++++++++++++++++++++++++++++++++"
    i+=1
