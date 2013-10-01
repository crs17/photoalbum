<!DOCTYPE HTML>
<?php require_once("auth.php");?>

<html>
<head>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link rel="stylesheet" type="text/css" href="css/style.css"></link>
  <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
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

for ($i = 0; $i < count($result); ++$i)
  {
    # Find the time span of the photos in the album
    $oldest = DateTime::createFromFormat ('Y' , '3000', $timezone);
    $newest = DateTime::createFromFormat ('Y' , '1000', $timezone);

    $aphotos = get_photos($result[$i]['id']);
    foreach($aphotos as $photo)
      {
	$ts = DateTime::createFromFormat('Y:m:d G:i:s', $photo['timestamp'], $timezone);
	
	if ($ts > $newest)
	  {$newest = $ts;}
	if ($ts < $oldest)
	  {$oldest = $ts;}
      }

    $result[$i]["newest"] = $newest->format('Y/m/d');
    $result[$i]["oldest"] = $oldest->format('Y/m/d');
    $result[$i]["number"] = count($aphotos);;
  }



function albumsort($a, $b)
{
  return strtotime($a["oldest"]) > strtotime($b["oldest"]);
}

usort($result, "albumsort");


foreach($result as $row)
  {

    $album_date = $row["newest"];
    if ($row["newest"] != $row["oldest"])
      {$album_date = $row["oldest"]." - ".$row["newest"];}

    $frontpic = get_photo(get_frontpage_pid($row['id']));

    print "<tr><td class='td_frontpage'>  <a href=\"album.php?a=".$row['id']."\">";
    print "<img src=\"./images/$frontpic[thumb_path]\" width='150px'>";
    print "<b>".$row['name']." (".$row["number"]." billeder)</b></a></td>";

    print "<td class='td_frontpage'>";

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



    print "</td><td class='td_frontpage'>$album_date</td></tr>\n";
  }
?>
  </tbody>
</table>

<br>
<!-- Log out option -->
<form name="log out" id="logout" action="index.php" method="POST">
  <input type="hidden" name="op" value="logout"/>
  <input type="hidden" name="username" value="<?php echo $_SESSION["username"]; ?>" />
  <p>Du er logget ind som <?php echo $_SESSION["username"]; ?>
  (<input type="submit" class="btn btn-link" value="log ud"/>)</P
</form>

</body>
</html>
