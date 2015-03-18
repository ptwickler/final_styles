<?php

session_start();
$item = $_GET['item'];
$quantity = $_GET['quantity'];
$cart = $_SESSION['cart'];
ini_set('display_errors', 1);

error_reporting(E_ALL);


include_once('./products.php');




// Grabs the items out of the cart and gets their relevant details from the array in products.php which it then pushes
// into the "out cart" which will be used to create the shopping cart page.
function add_to_cart($products,$item,$quantity,$cart) {


    $cart = $cart;
    $order_quantity = $quantity;
    $item = $item;
    $products = $products;
    $_SESSION['out_cart'][$item]['name'] =$item;
    $_SESSION['out_cart'][$item]['quantity'] = $order_quantity;
}


if($_SESSION['sign_in'] == 1) {
    add_to_cart($products, $item, $quantity, $cart);
    $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php";
    header("Location: ".$url) or die("Didn't work");
}

else {

    $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php?signed=0";
    header("Location: ".$url) or die("Didn't work");
}



