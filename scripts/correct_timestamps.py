#!/usr/bin/env python
# -*- coding: latin-1 -*-

import subprocess
import os
import sqlite3

image_dir = '../web/images'
image_db = '../db/images.db'


connection = sqlite3.connect(image_db)
connection.text_factory = str
db = connection.cursor()

def get_exif(fn):
    proc = subprocess.Popen(['exif', fn], 
                            stdout=subprocess.PIPE)
    result = proc.communicate()[0]

    key = 'Date and Time'

    values = [l[l.find('|') + 1:-1].strip() 
              for l in result.split('\n') if l.startswith(key)]
    
    return values


def correct(old_date, new_date):
    db.execute('SELECT id FROM images WHERE timestamp = ?', [str(old_date)])
    res = db.fetchall()
    if len(res) != 1:
        print 'DID NOT FIND IMAGE'
        #exit()
    db.execute('UPDATE images SET timestamp = ? WHERE timestamp = ?',
               [str(new_date),
                str(old_date)])
    #db.close()
    connection.commit()

    #exit()


dirs  = os.listdir(image_dir)

for d in dirs:
    d = os.path.join(image_dir, d)
    fs = os.listdir(d)
    for f in fs:
        if 'thumb' in f:
            continue
        dates = get_exif(os.path.join(d, f))
        if len(set(dates)) != 1 and len(dates) == 2:
            print d, f, dates[0], dates[1]
            correct(dates[1], dates[0])

db.close()
