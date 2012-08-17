#!/usr/bin/env python

import sys
import os

usage = '''
Stand in the images sub-dir and do:

> make_album.py <folder> <name>
'''

image_formats = ['.jpg']
size = 800
thumb_size = 300


target_dir_template = './%s/'


if not len(sys.argv) == 3:
    print usage
    exit()

folder = sys.argv[1]
name = sys.argv[2]

# prepare the target dir
target_dir = target_dir_template % name
if os.path.exists(target_dir):
    raise Exception('Target dir already exists')
os.mkdir(target_dir)

# make list of the pictures
files = os.listdir(folder)
images = [f for f in files if os.path.splitext(f)[1] in image_formats]

images = images[0:3]

# convert all images
for image in images:
    src_fn = os.path.join(folder, image)
    trg_fn = os.path.join(target_dir, image)
    cmd = '/usr/bin/convert -size %d %s %s' %(size, src_fn, trg_fn)
    print cmd
    os.system(cmd)
