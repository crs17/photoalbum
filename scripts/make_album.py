#!/usr/bin/env python
# -*- coding: latin-1 -*-

import sys
import os
import sqlite3

usage = '''
Stand in the images sub-dir and do:

> make_album.py <folder> <name>
'''

image_formats = ['.jpg', '.JPG']
size = 800
thumb_size = 300
image_db = '../../db/images.db'

target_dir_template = './%s/'

if not len(sys.argv) == 3:
    print usage
    exit()

folder = sys.argv[1]
name = sys.argv[2]


def get_exif(fn):
    """
    Maerke              |Vaerdi                                                    
    --------------------+----------------------------------------------------------
    Manufacturer        |Canon                                                     
    Model               |Canon EOS 550D                                            
    Orientation         |right - top                                               
    x-Resolution        |72,00                                                     
    y-Resolution        |72,00                                                     
    Resolution Unit     |Inch                                                      
    Date and Time       |2012:01:15 14:10:40                                       
    Artist              |                                                          
    YCbCr Positioning   |co-sited                                                  
    Copyright           |[None] (Photographer) -  (Editor)                         
    Compression         |JPEG compression                                          
    x-Resolution        |72,00                                                     
    y-Resolution        |72,00                                                     
    Resolution Unit     |Inch                                                      
    Exposure Time       |1/60 sec.                                                 
    FNumber             |f/4,5                                                     
    Exposure Program    |Normal program                                            
    ISO Speed Ratings   |100                                                       
    Exif Version        |Exif Version 2.21                                         
    Date and Time (origi|2012:01:15 14:10:40                                       
    Date and Time (digit|2012:01:15 14:10:40                                       
    Components Configura|Y Cb Cr -                                                 
    Shutter speed       |6,00 EV (APEX: 8, 1/64 sec.)                              
    Aperture            |4,38 EV (f/4,6)                                           
    Exposure Bias       |0,00 EV                                                   
    Metering Mode       |Pattern                                                   
    Flash               |Flash did not fire, compulsory flash mode.                
    Focal Length        |35,0 mm                                                   
    Maker Note          |7518 bytes undefined data                                 
    User Comment        |                                                          
    SubsecTime          |09                                                        
    SubSecTimeOriginal  |09                                                        
    SubSecTimeDigitized |09                                                        
    FlashPixVersion     |FlashPix Version 1.0                                      
    Color Space         |sRGB                                                      
    PixelXDimension     |5184                                                      
    PixelYDimension     |3456                                                      
    Focal Plane x-Resolu|5728,18                                                   
    Focal Plane y-Resolu|5808,40                                                   
    Focal Plane Resoluti|Inch                                                      
    Custom Rendered     |Normal process                                            
    Exposure Mode       |Auto exposure                                             
    White Balance       |Auto white balance                                        
    Scene Capture Type  |Standard                                                  
    InteroperabilityInde|R98                                                       
    InteroperabilityVers|0100                                                      
    --------------------+----------------------------------------------------------
    EXIF-data indeholder et miniatureportræt (17237 byte).
    
    """
    import subprocess

    proc = subprocess.Popen(['exif', fn], 
                            stdout=subprocess.PIPE)
    result = proc.communicate()[0]

    def extract(key):
        values = [l[l.find('|') + 1:-1].strip() 
                  for l in result.split('\n') if l.startswith(key)]
        if values:
            return values[0]
   
    keys = ['Model', 'Orientation', 'Date and Time', 'Exposure Time', 'FNumber',
            'ISO Speed Ratings', 'Aperture', 'Flash', 'Shutter speed', 'Focal Length']

    values = [extract(key) for key in keys]
    res = dict(zip(keys, values))

    return res


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
timestamp DATETIME,
camera text,
orientation text,
exposure_time text,
fnumber text,
ISO text,
aperture text,
flash text,
shutter_speed text,
focel_length text,
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
    cmd = 'convert -resize %d \"%s\" \"%s\"' %(size, src_fn, trg_fn)
    os.system(cmd)
    # convert thumbnail image
    cmd = 'convert -resize %d \"%s\" \"%s\"' %(thumb_size, src_fn,
                                                thumb_trg_fn)
    os.system(cmd)
    
    info = get_exif(trg_fn)

    # store image information in DB
    db.execute(
        'INSERT INTO images (path, thumb_path, album_id, timestamp, camera, orientation, exposure_time, fnumber, ISO, aperture, flash, shutter_speed, focel_length) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        (trg_fn,
         thumb_trg_fn,
         album_id,
         info['Date and Time'],
         info['Model'],
         info['Orientation'],
         info['Exposure Time'],
         info['FNumber'],
         info['ISO Speed Ratings'],
         info['Aperture'],
         info['Flash'],
         info['Shutter speed'],
         info['Focal Length']))

connection.commit()

# Check how many images have been stored
db.execute('SELECT id FROM images WHERE album_id = ?', (str(album_id)))
res = db.fetchall()
print '%d images now stored in the DB in the album \"%s\" ' % (len(res), name)

db.close()
