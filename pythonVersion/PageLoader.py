import lxml.html
from lxml import etree
import requests


class PageLoader:
    def __init__(self, urlParam):
        self.urlSource = urlParam
        self._readUrlSource()
        self._myDom = None
        self._prepareHtml()

        self._monthCorrespondence = {"janvier": "01", "fevrier": "02", "mars": "03", "avril": "04", "mai": "05",
                                     "juin": "06", "juillet": "07", "aout": "08", "septembre": "09", "octobre": "10",
                                     "novembre": "11", "decembre": "12"}

    def _readUrlSource(self):
        self._response = requests.get(self.urlSource)

    def _prepareHtml(self):
        self._myDom = lxml.html.fromstring(self._response.text)


def download(source, destination):
    message = "Wrong parameters for downloading file"
    if source is not None and destination is not None:
        try:
            r = requests.get(source, allow_redirects=True)
            open(destination, 'wb').write(r.content)
            message = destination + " - File downloaded successfully.\n"
        except requests.exceptions.MissingSchema as e:
            # send_somewhere(traceback.format_exception(*sys.exc_info()))
            message = "File downloading failed.\n"
            print(e)
    return message

