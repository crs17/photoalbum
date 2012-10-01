<?php
$path = '../Usered';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once("user.php");
$USER = new User();

$target = $_SERVER['REQUEST_URI'];

if (!$USER->authenticated) {?>
<title>Please log in</title>
<meta charset="utf-8"/>
<script type="text/javascript" src="js/sha1.js"></script>
<script type="text/javascript" src="js/user.js"></script>
<link rel="stylesheet" type="text/css" href="style.css"></link>

<form class="controlbox" name="log in" id="login" action=<?php print "$target"?> method="POST">
      <input type="hidden" name="op" value="login"/>
      <input type="hidden" name="sha1" value=""/>
      <table>
          <tr><td>user name </td><td><input type="text" name="username" value="" /></td></tr>
          <tr><td>password </td><td><input type="password" name="password1" value="" /></td></tr>
      </table>
      <input type="button" value="log in" onclick="User.processLogin()"/>
</form>
   
<?php  exit(); } ?>
