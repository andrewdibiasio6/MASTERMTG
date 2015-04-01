# -*- coding: utf-8 -*-
"""
Monthly MTG Card Update

Updated on 2015-02-05 @ 2:15pm

Python 2.7.2

@author: Michael Mammosser

Description: A python script that retrieves a json file from
http://mtgjson.com/json/AllCards-x.json and processes it into a csv.

"""

# Imports
import os
import urllib
import json
import csv
from operator import itemgetter
import sys
from unidecode import unidecode

# Path(s)
WORKING_PATH = os.path.dirname(os.path.abspath(__file__))


def process_json():
    """
    A function that opens and saves json from mtgjson.com. Then
    reads in the json and converts it to a python dictionary.
    Return:
        A python dictionary containing magic cards.
    """
    mtg_cards = urllib.URLopener()
    mtg_cards.retrieve('http://mtgjson.com/json/AllSets-x.json',
                       os.path.join(WORKING_PATH, 'data', 'cards.json'))

    with open(os.path.join(WORKING_PATH, 'data', 'cards.json'), 'r+')\
            as cards:
        return json.loads(cards.read())


def string_encode(str_to_encode):
    """
    A function that converts a unicode string into ascii.

    Args:
        -"str_to_encode (unicode str)": An UTF-8 encoded string.

    Return:
        An ascii string.
    """
    if str_to_encode is None:
        return ""
    else:
        try:
            temp_str = str_to_encode.encode('ascii', 'ignore')
            temp_str = ' '.join(temp_str.split())
            return temp_str
        except Exception as e:
            print e
            print "Replace failed: " + temp_str
            if isinstance(str_to_encode, str):
                return str_to_encode
            else:
                try:
                    return str(str_to_encode)
                except:
                    return ""


def main():
    """
    A function that processes the card dictionary into a tuple of
    tuples to be written to a csv file.
    """
    # Allow  python to handle utf-8.
    reload(sys)
    sys.setdefaultencoding("utf-8")

    # Retrieve cards as a python dictionary.
    card_dict = process_json()

    # Create a dictionary containing the processed card information.
    card_database = {}
    for i in card_dict:
        for j in card_dict[i]['cards']:
            card_name = j['name'].encode('ascii', 'xmlcharrefreplace')
            card = [card_name]
            card.append(unidecode(j['name']).lower())
            card.append(j['name'])
            try:
                color_str = ''
                for k in j['colors']:
                    color_str += k + ', '
                card.append(color_str[:-2])
            except:
                card.append('Colorless')
            card.append(string_encode(j['type']))
            card.append(string_encode(j['rarity']))
            try:
                card.append(string_encode(j['manaCost']))
            except:
                card.append('')
            try:
                card.append(string_encode(j['text']))
            except:
                card.append('')
            try:
                card.append(j['power'])
            except:
                card.append('')
            try:
                card.append(j['toughness'])
            except:
                card.append('')

            for k in ('Modern', 'Standard', 'Legacy', 'Vintage'):
                try:
                    if j['legalities'][k] == 'Legal':
                        card.append('1')
                    else:
                        card.append('0')
                except:
                    card.append('0')
            try:
                image_name = ('http://gatherer.wizards.com/Handlers/Image.\
ashx?multiverseid=' + str(j['multiverseid']) + '&type=card')
                card.append(image_name)
            except:
                card.append('')

            card_database[card_name] = card

    # Swap dictionary for list to order it by color.
    cards = []
    for i in card_database:

        cards.append(card_database[i])

    # Sort the card information based on color and then name.
    cards = sorted(cards, key=itemgetter(3, 0))

    #Create image map.
    card_image_map = []
    for i in cards:
        card_image_map.append([i[1], i[14]])
    card_image_map = sorted(card_image_map, key=itemgetter(0))

    with open(os.path.join(WORKING_PATH, 'data', 'image_map.json'), 'w+')\
            as outfile:
        json.dump(card_image_map, outfile)

    # Write the tuple of tuples to database.csv
    with open(os.path.join(WORKING_PATH, 'data', "cards.csv"), 'wb+')\
            as csv_file:
        csv_writer = csv.writer(csv_file, delimiter=',')
        for i in cards:
            csv_writer.writerow(i)


if __name__ == "__main__":
    main()
