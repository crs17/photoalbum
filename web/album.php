<?php

require_once("auth.php");
?>

<html>
<head>
</head>
<body>
<!-- Get the album info -->
<?php
$id =$_GET['a'];

require('./db.php');
$name = get_album_name($id);
$photos = get_photos($id);
?>

<!-- Headline -->
<h1><?php echo $name ?></h1>

<!-- pictures -->
	<?php
foreach ($photos as $photo) 
{
	echo "<a href=photo.php?a=$id&p=$photo[id]>";
	echo "<img src='./images/$photo[thumb_path]'>";
	echo "</a>";
}
?>

<!-- footer -->
<div>
<?php
echo "<a href='index.php'> Tilbage til forsiden </a>";
?>
</div>
</body>
</html>

