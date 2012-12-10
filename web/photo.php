<?php

require_once("auth.php");
?>


<?php
$aid =$_GET['a'];
$pid =$_GET['p'];

require('./db.php');

if (array_key_exists("comment", $_POST)){
  set_comment($pid, $_POST["comment"], $USER->username);}

if (array_key_exists("frontpic", $_POST)){
  set_frontpage_pid($aid, $_POST['frontpic']);
}


$photo = get_photo($pid);
$photo_ids = get_photo_ids($aid);

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="css/style.css"></link>
  </head>
  
  <body class="photo_page">
    <div id='photo'>
      <!-- Image -->
      <?php print "<img src='./images/$photo[path]' id='big_photo'>"; ?>

      <!-- Navigation -->
      <p>
      <table width="800px">
	<tr><td>
	    <?php
	       if ($photo_ids[0] != $pid){
	       $last = $pid - 1;
	       print "<a href='photo.php?a=$aid&p=$last'>Tilbage</a>";
	       }
	       ?>
	  </td>
	  <td align="right">
	    <?php
	       if (end($photo_ids) != $pid){
	       $next = $pid + 1;
	       print "<a href='photo.php?a=$aid&p=$next'>Frem</a>";
	       }
	       ?>
	  </td>
	</tr>
      </table>
      </p>

      <!-- Comments -->
      <!--  Editable, if superuser -->
      <?php
	$comment = get_comment($pid); 
        if ($USER->role==="superuser"){ ?>
	  <p>
	  <form name="update_comment" class="form-inline" method="POST">
	     <textarea name="comment" row="3"><?php print $comment['comment'];?></textarea><br>
	  <label class="checkbox">
	  <input type="checkbox" name="frontpic" value=<?php
	  print "$pid";
	  $frontpic_id = get_frontpage_pid($aid);
	  if ($frontpic_id==$pid)
	    {print ' checked';}
	  ?>> Forside billede
	  </label>
	  <input type="submit" value="Opdater" class="btn btn-info">
	  </form>
	  </p>
       <!--  Otherwise just display comments-->
      <?php } else{  print $comment[comment];}

      if (count($comment)>1){
	print "<p class='comment-usertag'><small>$comment[username] ($comment[timestamp])</small></p>";}
      ?>
      <br>
      <!-- Links -->
      <p>
      <br><a href=<?php print "album.php?a=$aid"?>>Tilbage til album</a>
      <br><a href='/'>Tilbage til forside</a>
      </p>
    </div>
     <div id="techs">
      <?php
         print "$photo[timestamp]<br>";
         print "$photo[camera]<br>";
         print "$photo[orientation]<br>";
         print "$photo[exposure_time]<br>";
         print "$photo[fnumber]<br>";
         print "$photo[ISO]<br>";
         print "$photo[aperture]<br>";
         print "$photo[flash]<br>";
         print "$photo[shutter_speed]<br>";
         print "$photo[focel_length]<br>";
       ?>
    </div>
</body>

