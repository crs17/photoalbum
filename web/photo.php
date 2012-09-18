<html>
<head>
<?php
$aid =$_GET['a'];
$pid =$_GET['p'];

require('./db.php');


$photo = get_photo($pid);
$photo_ids = get_photo_ids($aid);
?>
</head>

<body>
<?php
echo "<img src='./images/$photo[path]'><br>";

if ($photo_ids[0] != $pid){
   $last = $pid - 1;
   echo "<a href='photo?a=$aid&p=$last'>Tilbage</a>";
}
	
if (end($photo_ids) != $pid){
   $next = $pid + 1;
   echo "<a href='photo?a=$aid&p=$next'>Frem</a>";
}


echo "<br><a href='album?a=$aid'>Tilbage til album</a>";
echo "<br><a href='/'>Tilbage til forside</a>";
?>

</body>

