<!DOCTYPE HTML>
<?php require_once("auth.php");?>

<html>
<head>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
  <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
</head>
<body>

<!-- Contact DB -->
<?php require('./db.php');

if (array_key_exists("album_comment", $_POST)){
  set_album_comment($_POST["aid"], $_POST["album_comment"], $USER->username);}

 ?>

<h1>Billeder</h1>

<table class="table">
  <thead>
  <tr><th>Album</th><th>Beskrivelse</th><th>Dato</th><tr>
  </thead>
  <tbody>
<?php
$result = get_albums();


$timezone = new DateTimeZone('Europe/Copenhagen');

foreach($result as $row)
  {
    # Find the time span of the photos in the album
    $oldest = DateTime::createFromFormat ('Y' , '1000', $timezone);
    $newest = DateTime::createFromFormat ('Y' , '3000', $timezone);

    $photos = get_photos("$row[id]");
    foreach($photos as $photo)
      {
	$ts = DateTime::createFromFormat('Y:m:d G:i:s', $photo[timestamp], $timezone);
	
	if ($ts < $newest)
	  {$newest = $ts;}
	if ($ts > $oldest)
	  {$oldest = $ts;}
      }

    $newest_day = $newest->format('Y/m/d');
    $oldest_day = $oldest->format('Y/m/d');

    $album_date = $newest_day;
    if ($newest_day != $oldest_day)
      {$album_date = $oldest_day." - ".$newest_day;}


    $frontpic = get_photo(get_frontpage_pid($row['id']));

    print "<tr><td>  <a href=\"album.php?a=".$row['id']."\">";
    print "<img src='./images/$frontpic[thumb_path]' width='150px'>";
    print $row['name']."</a></td>";

    print "<td>";

    $album_comment = get_album_comment($row['id']); 
    if ($USER->role==="superuser"){ ?>
      <form name="update_album_comment" class="form-inline" method="POST">
      <textarea name="album_comment" row="3"><?php
      print $album_comment;
      ?></textarea><br>
      <input type="hidden" name="aid" value=<?php print "$row[id]";?>>
      <input type="submit" value="Opdater" class="btn btn-info">
      </form>
      <!--  Otherwise just display comments-->
      <?php } else{  print $album_comment;}



    print "</td><td>$album_date</td></tr>\n";
  }
?>
  </tbody>
</table>

<br>
<!-- Log out option -->
<form class="controlbox" name="log out" id="logout" action="index.php" method="POST">
  <input type="hidden" name="op" value="logout"/>
  <input type="hidden" name="username" value="<?php echo $_SESSION["username"]; ?>" />
  <p>Du er logget ind som <?php echo $_SESSION["username"]; ?>
  (<input type="submit" class="btn btn-link" value="log ud"/>)</P
</form>

</body>
</html>
