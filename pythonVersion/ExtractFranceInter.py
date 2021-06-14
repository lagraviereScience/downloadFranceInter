import os

import PageLoader
from PageLoader import PageLoader
from PageLoader import download
from datetime import datetime


class ExtractFranceInter(PageLoader):

    def getMaxPage(self):
        maxPages = self._myDom.xpath("//*[contains(@class, 'pager-item last')]")[0].getchildren()[0].get("href")[-3:]
        maxPages = int(maxPages)
        return maxPages

    def extractor(self, pageId):
        if pageId is not None:
            className = "replay-button"
            xpathRequest = "//*[contains(normalize-space(@class), '" + className + "')]"
            toDownload = self._myDom.xpath(xpathRequest)
            d = dict()

            for child in toDownload:
                currentYear = None
                fileName = None
                url = None
                if child.get("data-url") is not None:
                    if "cdn.radiofrance.fr" in child.get("data-url"):
                        # Old podcasts
                        url = child.get("data-url")
                        fileName = datetime.utcfromtimestamp(int(child.get("data-start-time"))).strftime('%Y.%m.%d')
                        #print("old!detected! \n")
                        #print(child.get("data-diffusion-path"))
                        #date('m/d/


                        #fileName = os.path.basename(child.get("data-diffusion-path"))
                        #fileName.replace("le-jeu-des-1000-eu-", "")

                        #if fileName is not None and fileName != "":
                        #    fileName = fileName.split("-")
                        #    if type(fileName) is list and len(fileName) == 3:
                        #        newYear = int(fileName[2])
                        #        if newYear != currentYear:
                        #            currentYear = newYear
                        #            #$this->monthCorrespondence[$fileName[1]]
                        #        fileName = fileName[2] + "." + self._monthCorrespondence[fileName[1]] + "." + fileName[0] + ".mp3"

                    else:
                        # New podcasts
                        # print(child.get("data-url"))
                        #print("new detected \n")
                        #print(child.get("data-url"))
                        fileName = datetime.utcfromtimestamp(int(child.get("data-start-time"))).strftime('%Y.%m.%d')
                        url = child.get("data-url")
                        #filename = child.get("data-url").replace("-", ".")
                        #filename = filename.split(".")
                        #filename = filename[6] + "." + filename[5] + "." + filename[4] + ".mp3"
                        url = child.get("data-url")

                    if fileName is not None:
                        fileName = fileName + ".mp3"
                        print(download(url, fileName))