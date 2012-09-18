#!/usr/bin/env python

import sys
import os
import sqlite3

usage = '''
Stand in the images sub-dir and do:

> make_album.py <folder> <name>
'''

image_formats = ['.jpg']
size = 800
thumb_size = 300
image_db = '../../db/images.db'

target_dir_template = './%s/'

if not len(sys.argv) == 3:
    print usage
    exit()

folder = sys.argv[1]
name = sys.argv[2]

# connect to the DB
connection = sqlite3.connect(image_db)
db = connection.cursor()

# Make tables, if not already there
db.execute('''CREATE TABLE IF NOT EXISTS albums
(id INTEGER PRIMARY KEY AUTOINCREMENT,
name text)''')

db.execute('''CREATE TABLE IF NOT EXISTS images  
(id INTEGER PRIMARY KEY AUTOINCREMENT,
path text,
thumb_path text,
album_id INTEGER,
FOREIGN KEY(album_id) REFERENCES albums(id))''')

# prepare the target dir
target_dir = target_dir_template % name
if os.path.exists(target_dir):
    raise Exception('Target dir already exists')
os.mkdir(target_dir)

# Store the album name
db.execute('SELECT id FROM albums WHERE name = ?', (name, ))
res = db.fetchone()
if res is None:
    db.execute('INSERT INTO albums (name) VALUES (?)', (name, ))
    album_id = db.lastrowid
else:
    album_id = res[0]
    print 'Album already exists'
connection.commit()

# make list of the pictures
files = os.listdir(folder)
images = [f for f in files if os.path.splitext(f)[1] in image_formats]

images = images[0:4]

# convert all images
for image in images:
    # generate folder names
    src_fn = os.path.join(folder, image)
    trg_fn = os.path.join(target_dir, image)
    thumb_trg_fn = os.path.join(target_dir, 'thumb' + image)
    # convert normal image
    cmd = '/usr/bin/convert -resize %d %s %s' %(size, src_fn, trg_fn)
    os.system(cmd)
    # convert thumbnail image
    cmd = '/usr/bin/convert -resize %d %s %s' %(thumb_size, src_fn,
                                                thumb_trg_fn)
    os.system(cmd)
    # store image information in DB
    db.execute(
        'INSERT INTO images (path, thumb_path, album_id) VALUES (?, ?, ?)',
        (trg_fn, thumb_trg_fn, album_id))

connection.commit()

# Check how many images have been stored
db.execute('SELECT id FROM images WHERE album_id = ?', (str(album_id)))
res = db.fetchall()
print '%d images now stored in the DB in the album \"%s\" ' % (len(res), name)

db.close()
