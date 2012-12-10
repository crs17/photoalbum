<?php

$db = new PDO("sqlite:../db/images.db");


$albums = $db->prepare('SELECT * FROM `albums` ORDER BY `id`');

function get_albums(){
   global $db, $albums;
   $albums->execute();
   return $albums;
}


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

$photo_db = $db->prepare('SELECT * FROM `images` WHERE id=?');

function get_photo($id){
   global $db, $photo_db;
   $photo_db->execute(array($id));
   return $photo_db->fetch(PDO::FETCH_ASSOC);
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




<!-- album comments database -->

<?php
function connect_album_comment_db(){
   $album_comment_file = "../db/album_comments.db";

   $build = false;
   if (!file_exists($album_comment_file)) {
	$build = true; }
   
   $album_comment_db = new PDO("sqlite:" . $album_comment_file);
   if ($build){
      $album_comment_db->beginTransaction();
      $create = "CREATE TABLE album_comments (id INTEGER PRIMARY KEY AUTOINCREMENT, username TEXT, comment TEXT, album_id INTEGER UNIQUE, frontpage_photo_id INTEGER, timestamp DATETIME);";
      $album_comment_db->exec($create);
      $album_comment_db->commit();
   }
   return $album_comment_db;
}

function get_album_comment($album_id){

   $album_comment_db = connect_album_comment_db();

   $comment = $album_comment_db->prepare('SELECT * FROM `album_comments` WHERE album_id=?');
   $comment->execute(array($album_id));
   $res = $comment->fetch(PDO::FETCH_ASSOC);
   $c = $res['comment'];
   return $c;
   
}

function set_album_comment($album_id, $comment, $username){
   $album_comment_db = connect_album_comment_db();

   // Try to update
   $stmt = $album_comment_db->prepare("UPDATE album_comments SET comment=:comment WHERE album_id=:album_id;");

   $stmt->bindParam(':album_id', $album_id);
   $stmt->bindParam(':comment', $comment);
   $stmt->execute();

   $changes = $stmt->rowCount();

   // Insert new comment
   if ($changes==0)
     {
       $insert = $album_comment_db->prepare("INSERT INTO album_comments (album_id, comment) VALUES (:album_id, :comment);");
     
       $insert->bindParam(':album_id', $aid);
       $insert->bindParam(':comment', $comment);
       $insert->execute();
     }
   $changes = $stmt->rowCount();

   return;
}

function get_frontpage_pid($aid){
   $album_comment_db = connect_album_comment_db();

   $frontpic = $album_comment_db->prepare('SELECT `frontpage_photo_id` FROM `album_comments` WHERE album_id=?');

   $frontpic->execute(array($aid));
   $res = $frontpic->fetch(PDO::FETCH_ASSOC);
   $fpid = $res['frontpage_photo_id'];
   return $fpid;
}


function set_frontpage_pid($aid, $pid){
   $album_comment_db = connect_album_comment_db();

   // Try to update
   $stmt = $album_comment_db->prepare("UPDATE album_comments SET frontpage_photo_id= :frontpage_photo_id WHERE album_id=:album_id;");

   $stmt->bindParam(':album_id', $aid);
   $stmt->bindParam(':frontpage_photo_id', $pid);
   $stmt->execute();

   $changes = $stmt->rowCount();

   // Insert new comment
   if ($changes==0)
     {
       $insert = $album_comment_db->prepare("INSERT INTO album_comments (album_id, frontpage_photo_id) VALUES (:album_id, :frontpage_photo_id);");
     
       $insert->bindParam(':album_id', $aid);
       $insert->bindParam(':frontpage_photo_id', $pid);
       $insert->execute();
     }
   $changes = $stmt->rowCount();
  
}

?>