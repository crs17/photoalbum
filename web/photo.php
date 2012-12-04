<?php

require_once("auth.php");
?>


<?php
$aid =$_GET['a'];
$pid =$_GET['p'];

require('./db.php');

if (array_key_exists("comment", $_POST)){
   set_comment($pid, $_POST["comment"], $USER->username);
}


$photo = get_photo($pid);
$photo_ids = get_photo_ids($aid);

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <link rel="stylesheet" type="text/css" href="style.css"></link>
  </head>
  
  <body class="photo_page">
    <div id='photo'>
      <!-- Image -->
      <?php
	 print "<img src='./images/$photo[path]'>";
	 ?>
      <!-- Navigation -->
      <table width="800px">
	<tr><td>
	    <?php
	       if ($photo_ids[0] != $pid){
	       $last = $pid - 1;
	       print "<a href='photo?a=$aid&p=$last'>Tilbage</a>";
	       }
	       ?>
	  </td>
	  <td align="right">
	    <?php
	       if (end($photo_ids) != $pid){
	       $next = $pid + 1;
	       print "<a href='photo?a=$aid&p=$next'>Frem</a>";
	       }
	       ?>
	  </td>
	</tr>
      </table>
      <!-- Comments -->
      <!--  Editable, if superuser -->
      <?php
	$comment = get_comment($pid); 
        if (count($comment)>1){
           print "$comment[username] ($comment[timestamp]):<br>";}
        if ($USER->role==="superuser"){ ?>
         <form name="update_comment" method="POST">
         <textarea name="comment" cols="100"><?php print $comment[comment];?> </textarea>
         <br><br>
	   <input type="submit" value="Opdater">
         </form>
       <!--  Otherwise just display comments-->
      <?php } else{  print $comment[comment];}?>

      <!-- Links -->
      <br><a href=<?php print "album?a=$aid"?>>Tilbage til album</a>
      <br><a href='/'>Tilbage til forside</a>
    </div>
</body>

