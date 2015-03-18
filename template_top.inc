<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Crystals, Charms, and Coffee
    </title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="final.css">
</head>

<body>
<div class="wrapper">



<?php
include_once('products.php');
print '<div class="cart_display"><form name="checkout_button" action="checkout.php" method="POST">';
if (isset($_SESSION['sign_in']) && $_SESSION['sign_in'] == 1) {
    echo "Your Cart:<br>";
    if (isset($_SESSION['out_cart'])) {
        foreach ($_SESSION['out_cart'] as $key => $value) {

            $product = $products[$key];

            print '<input class="checkout_list" name="checkout_button" type="text" readonly value="' . $product['name'] . ': ' . $value["quantity"] . '"> <br>';


        }
        print '<input type="submit" value="Complete Checkout" ></form>
</div><!--end div.cart_display-->';
    }
}
elseif (!isset($_SESSION['sign_in'])) {
    print  '<input name="checkout_button" type="text" readonly ><span class="require_auth">Sign in to Purchase Items</span><br></form>';

}?>
<div class="login">
    <form class="login" value="Sign In" action="login.php?log=1" method="POST">
        <label for="username">Name</label><br>
        <input class="login" type="text" size="20" name="username"/><br>
        <label for="password" class ="login">Password</label><br>
        <input type="password" class="login" name="password" size="20"><br>
        <input type="submit" value="Sign In">
    </form>
</div><!--end div.login-->
<div class="sign_out">
    <form class="login" action="index.php?out=1" method="GET">
        <input type="text" name="out" value="1" hidden>
        <input type="submit" value="Sign Out">
    </form>
</div><!--end div.sign_out-->
    <div class="banner"><img class="banner" src="img/banner.jpg"></div><!--end div.banner-->



