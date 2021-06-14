import lxml.html
from lxml import etree
import requests
import PageLoader
from PageLoader import PageLoader
from ExtractFranceInter import ExtractFranceInter
from tkinter import *
import tkinter as tk
import tkinter.ttk as ttk
from ttkthemes import ThemedStyle


if __name__ == '__main__':
    URL = 'https://www.franceinter.fr/emissions/le-jeu-des-1000-euros'
    p1 = ExtractFranceInter(URL)
    # page = requests.get(URL)
    # content = str(page.content)
    # tree = etree.HTML(content)
    # r = tree.xpath("//*[contains(@class, 'pager-item last')]")
    # maxPages = r[0].getchildren()[0].get("href")[-3:]
    # maxPages = int(maxPages)
    # print("native" + str(maxPages) + " calculated : " + str())

    # if p1.getMaxPage() is not None:
    #    for id in range(1, p1.getMaxPage()):
    #        URL = 'https://www.franceinter.fr/emissions/le-jeu-des-1000-euros?p=' + str(id)
    #        toExtract = ExtractFranceInter(URL)
    #        toExtract.extractor(id)

    #window = Tk()

    #window.title("Radio Downloader")
    #window.geometry('350x200')
    #window.mainloop()

    app = tk.Tk()
    app['background'] = '#3c3f41'
    app.geometry("1152x784")
    app.title("Changing Themes")
    # Setting Theme
    style = ThemedStyle(app)
    style.set_theme("black")

    # Button Widgets
    Def_Btn = tk.Button(app, text='Default Button')
    Def_Btn.pack()
    Themed_Btn = ttk.Button(app, text='Themed button')
    Themed_Btn.pack()

    # Scrollbar Widgets
    Def_Scrollbar = ttk.Scrollbar(app)
    Def_Scrollbar.pack(side='right', fill='y')
    Themed_Scrollbar = ttk.Scrollbar(app, orient='horizontal')
    Themed_Scrollbar.pack(side='top', fill='x')

    # Entry Widgets
    Def_Entry = ttk.Entry(app)
    Def_Entry.pack()
    Themed_Entry = ttk.Entry(app)
    Themed_Entry.pack()

    app.mainloop()
    exit(0)
