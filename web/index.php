<html>

<body>

<!-- Contact DB -->
<?php
$db = new PDO("sqlite:../db/images.db");
?>

<h1>Billeder</h1>

<table>
<?php

$result = $db->query('SELECT * FROM albums');
foreach($result as $row)
{  
	print "<tr><td>".$row['id']."</td>";
	print "<td><a href=\"album.php?a=".$row['id']."\">".$row['name']."</a></td>";
}
  ?>
  
</table>

<?php
echo "<br>";
echo phpversion();
?>
</body>
</html>
