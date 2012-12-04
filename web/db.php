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

<!-- comments database -->

<?php


function connect_comment_db(){
   $comment_file = "../db/comments.db";

   $build = false;
   if (!file_exists($comment_file)) {
	$build = true; }
   
   $comment_db = new PDO("sqlite:" . $comment_file);
   if ($build){
      $comment_db->beginTransaction();
      $create = "CREATE TABLE comments (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, comment TEXT, photo_id INTEGER, timestamp DATETIME);";
      $comment_db->exec($create);
      $comment_db->commit();
   }
   return $comment_db;
}

function get_comment($photo_id){

   $comment_db = connect_comment_db();

   $comment = $comment_db->prepare('SELECT * FROM `comments` WHERE photo_id=?');
   $comment->execute(array($photo_id));
   return $comment->fetch(PDO::FETCH_ASSOC);
}

function set_comment($photo_id, $comment, $username){
   $comment_db = connect_comment_db();
   // delete old comment
   $del_cmd = $comment_db->prepare("DELETE FROM comments WHERE photo_id=:photo_id");
   $del_cmd->bindParam(':photo_id', $photo_id);
   $del_cmd->execute();

   // Insert new comment
   $stmt = $comment_db->prepare("INSERT INTO comments (username, comment, photo_id, timestamp) VALUES (:username, :comment, :photo_id, DATETIME('NOW', 'localtime'));");
   $stmt->bindParam(':username', $username);
   $stmt->bindParam(':comment', $comment);
   $stmt->bindParam(':photo_id', $photo_id);

   $stmt->execute();

   return;
}

?>
