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
    mtg_cards.retrieve('http://mtgjson.com/json/AllCards-x.json',
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

    # Create a list containing the processed card information.
    card_database = []    
    card_map = []
    for i in card_dict:
        card = []
        temp_dict = {}
        card.append(card_dict[i]['name'].encode('ascii', 'xmlcharrefreplace'))
        temp_dict['name'] = unidecode(card_dict[i]['name'])
        temp_dict['html'] = card_dict[i]['name'].encode('ascii', 'xmlcharrefreplace')
        card_map.append(temp_dict)
        try:
            color_str = ''
            for j in card_dict[i]['colors']:
                color_str += j + ', '
            card.append(color_str[:-2])
        except:
            card.append('Colorless')
        card.append(string_encode(card_dict[i]['type']))
        try:
            card.append(string_encode(card_dict[i]['manaCost']))
        except:
            card.append('')
        try:
            card.append(string_encode(card_dict[i]['text']))
        except:
            card.append('')
        try:
            card.append(card_dict[i]['power'])
        except:
            card.append('')

        try:
            card.append(card_dict[i]['toughness'])
        except:
            card.append('')

        for j in ('Modern', 'Standard', 'Legacy', 'Vintage'):
            try:
                if card_dict[i]['legalities'][j] == 'Legal':
                    card.append('1')
                else:
                    card.append('0')
            except:
                card.append('0')
        try:
            image_name = ('http://mtgimage.com/card/' +
                          card_dict[i]['imageName'] + '.jpg')
            card.append(image_name)
        except:
            card.append('')

        card_database.append(card)

    # Sort the card information based on color and then name.
    card_database = sorted(card_database, key=itemgetter(1, 0))

    # Write the tuple of tuples to database.csv
    with open(os.path.join(WORKING_PATH, 'data', "database.csv"), 'wb+')\
            as csv_file:
        csv_writer = csv.writer(csv_file, delimiter=',')
        for i in card_database:
            csv_writer.writerow(i)

    with open(os.path.join(WORKING_PATH, 'data', "card_map.json"), 'wb+')\
            as cm:
        json.dump({'data': card_map}, cm)

if __name__ == "__main__":
    main()
