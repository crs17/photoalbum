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


$photos = $db->prepare('SELECT * FROM `images` WHERE album_id=?');

function get_photos($id){
   global $db, $photos;
   $photos->execute(array($id));
   return $photos;
}
?>
