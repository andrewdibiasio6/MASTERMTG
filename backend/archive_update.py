# -*- coding: utf-8 -*-
"""
Created on Thu Feb 26 15:51:12 2015

@author: mmammosser
"""
# Imports
import urllib2
from BeautifulSoup import BeautifulSoup
import re
import csv
import os

# Path(s)
WORKING_PATH = os.path.dirname(os.path.abspath(__file__))

detag_re = re.compile(r'<[^>]+>')
html_re = re.compile(r'&[^\s]*;')
last_number_re = re.compile(r'(\d+)(?!.*\d)')
first_numer_re = re.compile(r'\d')


def removeNonAscii(s):
    return "".join(filter(lambda x: ord(x) < 128, s))

i = 369
active = True
missing_page = 0
card_list = []
while active:
    url = "http://archive.wizards.com/Magic/Magazine/Article.aspx?x=mtg/daily/deck/" + str(i)
    deck = []
    card_count = 0
    site_count = 0
    last_end = 0
    try:
        parse = BeautifulSoup(urllib2.urlopen(url).read())
        title = parse.findAll('div', {'class': 'main'})[0]
        title = detag_re.sub('', str(title))
        title = html_re.sub('', title).replace(',', '')
        title = removeNonAscii(' '.join(title.split()))
        print title + ' - ' + str(i)
        cardgroup = parse.findAll('table', {'class': 'cardgroup'})
        raw_table = detag_re.sub('', str(cardgroup[0]))
        raw_table = html_re.sub('', raw_table).replace(',', '')
        raw_table = removeNonAscii(raw_table)
        raw_table = ''.join(raw_table.split())
        cards = cardgroup[0].findAll('a', {'class': 'nodec'})
        sideboard = raw_table.find('Sideboard')
        site_count = last_number_re.search(raw_table[:raw_table.find('cards')]).group(0)
        if sideboard == -1:
            sideboard = len(raw_table)

        for j in cards:
            card = detag_re.sub('', str(j))
            card = html_re.sub('', card).replace(',', '')
            card = removeNonAscii(card)
            start = first_numer_re.search(raw_table[last_end:]).start()
            end = last_end + start + raw_table[(last_end + start):].find(''.join(card.split()))
            if end < sideboard:
                count = last_number_re.search(raw_table[last_end:end]).group(0)

                if [title, card, count] in deck:
                    if card_count < 60:
                        index = deck.index([title, card, count])
                        old_value = deck[index]
                        old_value[2] = old_value[2] + count
                        deck[index] = old_value
                    else:
                        break
                else:
                    deck.append([title, card, count])
                
                card_count += int(count)
                last_end = end
                
            else:
                break
        missing_page = 0

    except:
        if missing_page > 25:
            active = False
        missing_page += 1
    
    if card_count < 75:
        if int(card_count) != int(site_count):
            print str(card_count) + '/' + str(site_count)
            print deck
            print
        else:
            for j in deck:
                card_list.append(j)
    i += 1


with open(os.path.join(WORKING_PATH, 'data', "archive.csv"), 'wb+')\
        as csv_file:
    csv_writer = csv.writer(csv_file, delimiter=',')
    for i in card_list:
        csv_writer.writerow(i)



#deck_list = []
#parse = BeautifulSoup(urllib2.urlopen(url).read())
#decks = parse.findAll('h4')
#for i in decks:
#    count_list = []
#    card_list = []
#    deck = detag_re.sub('', str(i))
#    deck = html_re.sub('', deck).replace(',', '')
#    division_str = deck.replace(' ', '_').lower()
#    print division_str
#    try:
#        div = parse.findAll('div', {'id': division_str})
#        div = div[0].findAll('div', {'class': 'sorted-by-overview-container \
#sortedContainer'})
#        card_counts = div[0].findAll('span', {'class': 'card-count'})
#
#        c = 0
#        for j in card_counts:
#            card_count = detag_re.sub('', str(j))
#            count_list.append(card_count)
#            c = c + int(card_count)
#
#        card_names = div[0].findAll('span', {'class': 'card-name'})
#
#        for j in card_names:
#            card_name = detag_re.sub('', str(j))
#            card_list.append(card_name)
#
#        for j in xrange(0, len(card_list)):
#            deck_list.append([deck, card_list[j], count_list[j]])
#    except:
#        print division_str
#
## Write the tuple of tuples to database.csv
