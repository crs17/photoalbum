<?php

require_once("auth.php");


?>
<html>

<body>

<!-- Contact DB -->
<?php
$db = new PDO("sqlite:../db/images.db");
?>

<h1>Billeder</h1>

<table>
<?php
print $USER->username;

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


?>
<!-- Log out option -->
<form class="controlbox" name="log out" id="logout" action="index.php" method="POST">
  <input type="hidden" name="op" value="logout"/>
  <input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" />
  <p>You are logged in as <?php echo $_SESSION["username"]; ?></p>
  <input type="submit" value="log out"/>
</form>

</body>
</html>
