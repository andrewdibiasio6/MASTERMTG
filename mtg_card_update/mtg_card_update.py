# -*- coding: utf-8 -*-
"""
Monthly MTG Card Update

Updated on 2015-02-05 @ 2:15pm

Python 2.7.2

@author: Michael Mammosser

Description:

Tables:
    Target:
        - mtg_cards (MYSQL)
    Source:
        - http://mtgjson.com/json/AllCards-x.json
"""

# Libraries
import os
import urllib
import json
import csv
from operator import itemgetter
import mysql.connector

# Path(s)
WORKING_PATH = os.path.dirname(os.path.abspath(__file__))


def update_database(card_database):
    """
    """
    target_connection = mysql.connector.connect(user='root',
                                                password='Tesla80000!',
                                                host='127.0.0.1',
                                                database='test')

    target_cursor = target_connection.cursor()

    sql = """
    delete FROM mtg_cards
    """

    target_cursor.execute(sql)
    target_connection.commit()

    sql = """
        INSERT INTO mtg_cards
        (
            name,
            colors,
            type,
            mana_cost,
            text,
            power,
            toughness,
            modern,
            standard,
            legacy,
            vintage,
            image
        )
        VALUES
        (
            %s,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s,
            %s
        )
        """
    i = 0
    count = len(card_database)
    while i < count:
        if i+1000 >= count:
            target_cursor.executemany(sql, card_database[i:count])
        else:
            target_cursor.executemany(sql, card_database[i:i+1000])
        target_connection.commit()
        i += 1000

    target_connection.close()


def retrieve_json():
    """
    """
    mtg_cards = urllib.URLopener()
    mtg_cards.retrieve("http://mtgjson.com/json/AllCards-x.json",
                       os.path.join(WORKING_PATH, 'data', "cards.json"))


def process_json():
    """
    """
    with open(os.path.join(WORKING_PATH, 'data', "cards.json"), 'r') as cards:
        return json.loads(cards.read())


def main():
    """
    """
    retrieve_json()
    card_dict = process_json()

    card_database = []
    for i in card_dict:
        card = []
        card.append(card_dict[i]['name'].encode('ascii', errors="ignore"))
        try:
            color_str = ''
            for j in card_dict[i]['colors']:
                color_str += j + ', '
            card.append(color_str[:-2])
        except:
            card.append('Colorless')
        card.append(card_dict[i]['type'].encode('ascii', errors="ignore"))
        try:
            card.append(card_dict[i]['manaCost'].encode('ascii',
                        errors="ignore"))
        except:
            card.append('')
        try:
            card.append(card_dict[i]['text'].encode('ascii', errors="ignore"))
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

    card_database = sorted(card_database, key=itemgetter(1, 0))

    with open(os.path.join(WORKING_PATH, 'data', "database.csv"), 'wb+')\
            as csv_file:
        csv_writer = csv.writer(csv_file, delimiter=',')
        for i in card_database:
            csv_writer.writerow(i)

    update_database(card_database)

if __name__ == "__main__":
    main()
