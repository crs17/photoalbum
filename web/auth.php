<?php
$path = '../Usered';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once("user.php");
$USER = new User();

$target = $_SERVER['REQUEST_URI'];

if (!$USER->authenticated) {?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1">
    <title>Please log in</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/style.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="/js/sha1.js"></script>
    <script type="text/javascript" src="/js/user.js"></script>
  </head>
  <body>
    <img id="login-image" src="static_images/vagthund.JPG" alt="Vagthund">
    <form class="form-inline" name="log in" id="login" action='<?php print "$target"?>' method="POST">
      <input type="hidden" name="op" value="login">
      <input type="hidden" name="sha1" value="">
      
      <input type="text" class="input-small" placeholder="Brugernavn" name="username">
      <input type="password" class="input-small" placeholder="Kodeord" name="password1">
      <input type="submit" class="btn btn-success" value="log ind" onclick="User.processLogin()">
    </form>
  </body>
 </html>

   
<?php  exit(); } ?>
