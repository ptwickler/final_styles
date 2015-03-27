<?php
session_start();

ini_set('display_errors', 1);

error_reporting(E_ALL);


if(!isset($_SESSION)) {
    session_start();
}

ob_start();
require_once('FirePHPCore/FirePHP.class.php');


require_once('functions.php');

print '<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Crystals, Charms, and Coffee
    </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="final.css">
</head>

<body>
<div class="banner"><img class="banner" src="img/banner.jpg"></div>



    <form class="login" name=""Sign In" action="login.php" method="POST">
        <label for="username">Name</label><br>
        <input class="login" type="text" size="20" name="username"/><br>
        <label for="password" class ="login">Password</label><br>
        <input class="login" name="password" size="20"><br>
        <input type="submit" value="Sign In">
    </form>

    <form class="login" action="index.php?out=1" method="GET">
        <input type="text" name="out" value="1" hidden>
        <input type="submit" value="Sign Out">
    </form>';

if (isset($_SESSION['out_cart'])) {
    $items = $_SESSION['out_cart'];
}

else {

    /* $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php";

     header("Location: ".$url) or die("Didn't work");*/


}



$out_table = build_out_cart($items,$products);

echo '<!DOCTYPE html>
        <html lang="en">
          <head>
              <title>
                  Checkout!
              </title>
              <meta charset = "utf-8">
              <link rel="stylesheet" href="final.css">
          </head>
          <body>
<div class="cart_display">
          <h2>Your Cart:</h2>
          <hr>
          <br>
          <br>
            <table><tbody><th>Item</th><th>Quantity</th><th>Price</th>' . $out_table .  '</div></body></html>
<form name = "purchase" action="checkout.php?mail=1">
  <input type="text" hidden name="mail" value="1">
  <input type="submit" value="complete purchase">
</form>
</div>
<div>
  <a href="index.php">Continue Shopping!</a>

</div>';


if (isset($_GET['mail']) && $_GET['mail'] == 1) {
    confirm_email($_SESSION['username'],$products);

}




'</body>
</html>';