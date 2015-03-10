# -*- coding: utf-8 -*-
"""
File: winning_update.py
91.462 Project: Mastering Magic
Michael Mammosser, Computer Science Major @ UMass Lowell
Contact: michael_mammosser@student.uml.edu
Copyright (c) 2015 by Michael Mammosser.  All rights reserved.
Updated on February 26, 2015.

A simple php script to update `mtg_decks` database
"""
# Imports
import urllib2
from BeautifulSoup import BeautifulSoup
import re
import csv
import os

# Path(s)
WORKING_PATH = os.path.dirname(os.path.abspath(__file__))

# Regex to remove html tags and unicode characters.
detag_re = re.compile(r'<[^>]+>')
html_re = re.compile(r'&[^\s]*;')

deck_list = []

# Parse winning-decks page.
url = "http://magic.wizards.com/en/articles/winning-decks"
parse = BeautifulSoup(urllib2.urlopen(url).read())
decks = parse.findAll('h4')

# Loop through all of the decks on the winning-decks page.
for i in decks:
    count_list = []
    card_list = []
    
    # Use regex to clean html.
    deck = detag_re.sub('', str(i))
    deck = html_re.sub('', deck).replace(',', '')
    division_str = deck.replace(' ', '_').lower()
    print division_str
    
    # Try to parse page, if error escape.
    try:
        
        # Find the deck container.
        div = parse.findAll('div', {'id': division_str})
        div = div[0].findAll('div', {'class': 'sorted-by-overview-container \
sortedContainer'})
        card_counts = div[0].findAll('span', {'class': 'card-count'})

        c = 0
        # Retrieve card counts and count the total number of cards.
        for j in card_counts:
            card_count = detag_re.sub('', str(j))
            count_list.append(card_count)
            c = c + int(card_count)

        card_names = div[0].findAll('span', {'class': 'card-name'})

        # Retrieve card names.
        for j in card_names:
            card_name = detag_re.sub('', str(j))
            card_name = card_name.encode('ascii', 'xmlcharrefreplace')
            card_name = card_name.replace('&eacute;','e')
            card_list.append(card_name)

        # Append deck and card info to main list.
        for j in xrange(0, len(card_list)):
            deck_list.append([deck, card_list[j], count_list[j]])
    except:
        print division_str

# Write the tuple of tuples to database.csv
with open(os.path.join(WORKING_PATH, 'data', "winning.csv"), 'wb+')\
        as csv_file:
    csv_writer = csv.writer(csv_file, delimiter=',')
    for i in deck_list:
        csv_writer.writerow(i)
