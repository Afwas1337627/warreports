#! /usr/bin/python

import os
import sqlite3
import csv
import re
from pprint import pprint


def get_files():
    """
    returns the filenames of .csv files
    in the ./reports folder
    :return:
    """
    this_files = []
    for file in os.listdir(reports_folder):
        if file.endswith(".csv"):
            print(file)
            this_files.append(file)
    return this_files


def initialize_database():
    """
    Empty the database each run
    :return:
    """
    query = """DROP TABLE IF EXISTS members;"""
    c.execute(query)
    query = """CREATE TABLE members(
        id INTEGER PRIMARY KEY,
        member_id INTEGER NOT NULL,
        member_name TEXT,
        member_level INTEGER,
        faction VARCHAR(3),
        points INTEGER,
        joins INTEGER,
        clears INTEGER,
        UNIQUE(member_id));"""
    c.execute(query)
    query = """DROP TABLE IF EXISTS wars;"""
    c.execute(query)
    query = """CREATE TABLE wars(
        id INTEGER PRIMARY KEY,
        member_id INTEGER,
        war_id INTEGER,
        UNIQUE(member_id, war_id));"""
    c.execute(query)


def process_files(this_file):
    """
    File is read and it's contents are places in an OrderedDict
    :param this_file: file to process
    :return: OrderedDict
    """
    member_dicts = []
    with open(reports_folder+'/'+file, newline='') as csvfile:
        fieldnames = ['war_id', 'territory', 'team', 'faction', 'member_id', \
                      'member_name', 'member_level', 'points', 'joins', 'clears']
        csvreader = csv.DictReader(csvfile, fieldnames=fieldnames)
        for row in csvreader:
            if len(row) < 2:
                continue

            # Remove wars that are not in the curated list by Proxima
            # Wars that are discarded are likely fake walls
            if row['war_id'] not in filtered_wars:
                # pprint(row['war_id'])
                continue
            # pprint(row)
            member_dicts.append(row)
    return member_dicts


def process_members():
    """
    Store all lines in the database
    :return:
    """
    i = 0
    for member in members:
        if i % 100 == 0:
            # Sort of progress bar
            # print(i)
            pass

        # Check if this row is already stored
        query = """SELECT count(*) FROM wars 
        WHERE member_id = ? AND war_id = ?;"""
        c.execute(query, (member['member_id'], member['war_id']))
        in_database = c.fetchone()
        if in_database[0] > 0:
            # already in database; apparent duplicate file
            # pprint(member)
            continue

        # Insert this line in the wars database
        # That will later be used to check for duplicate lines
        query = """INSERT INTO wars(member_id, war_id) 
        VALUES(?, ?) ON CONFLICT (member_id, war_id) DO NOTHING;"""
        c.execute(query, (member['member_id'], member['war_id']))

        # The meat of filling the database. This line is added now
        query = """INSERT INTO members(
                        member_id, member_name, member_level,
                        faction, points, joins, clears)
                   VALUES(?, ?, ?, ?, ?, ?, ?)
                   ON CONFLICT(member_id) DO UPDATE SET
                        member_name = excluded.member_name,
                        member_level = excluded.member_level,
                        faction = excluded.faction,
                        points = points + ?,
                        joins = joins + ?,
                        clears = clears + ?;"""
        c.execute(query, (member['member_id'], member['member_name'],
                          member['member_level'], member['faction'],
                          member['points'], member['joins'], member['clears'],
                          member['points'], member['joins'], member['clears']))
        i += 1
    print("Stored {} rows".format(i))


if __name__ == '__main__':
    reports_folder = 'reports'
    files = get_files()

    filtered_wars = []
    for row in open('reports_filtered.txt'):
        regex = r'(\d{4,5})$'
        match = re.search(regex, row)
        if match is None:
            continue
        if match:
            filtered_war = match.group(1)
            filtered_wars.append(filtered_war)
    # pprint(filtered_wars)
    conn = sqlite3.connect('members.sqlite')
    c = conn.cursor()
    initialize_database()
    conn.commit()

    members = []
    for file in files:
        mem = process_files(file)
        members.extend(mem)
    # print(len(members))
    process_members()
    conn.commit()
    c.close()
    conn.close()
