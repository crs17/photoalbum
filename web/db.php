<?php

$db = new PDO("sqlite:../db/images.db");

$album_name = $db->prepare('SELECT `name` FROM `albums` WHERE id=?');
$album_name->setFetchMode(PDO::FETCH_COLUMN, 0);

function get_album_name($id){	
   global $db, $album_name;

   $album_name->execute(array($id));
   $name = $album_name->fetch();

   return $name;
}



$photos = $db->prepare('SELECT * FROM `images` WHERE album_id=? ORDER BY `id`');

function get_photos($id){
   global $db, $photos;
   $photos->execute(array($id));
   return $photos;
}


$photo_ids = $db->prepare('SELECT `id` FROM `images` WHERE album_id=? ORDER BY `id`');

function get_photo_ids($id){
   global $db, $photo_ids;
   $photo_ids->execute(array($id));
   return $photo_ids->fetchAll(PDO::FETCH_COLUMN, 0);
}

$photo = $db->prepare('SELECT * FROM `images` WHERE id=?');

function get_photo($id){
   global $db, $photo;
   $photo->execute(array($id));
   return $photo->fetch(PDO::FETCH_ASSOC);
}

?>
