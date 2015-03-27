<?php
date_default_timezone_set ( 'America/New_York' );
session_start();
ini_set('display_errors', 1);

error_reporting(E_ALL);

ob_start();
require_once('FirePHPCore/FirePHP.class.php');
if (!$firephp) {
    ob_start();

    $firephp = FirePHP::getInstance(true);
}

#----------------------#
# Product List         #
#----------------------#

$products = array(
    'quartzorb' => array(
        'img' => 'quartzorb',
        'name' => 'Quartz Orb',
        'material' => 'quartz',
        'price' => '30.00',
        'weight' => '2lbs'
    ),

    'amethyst'=> array(
        'img' => 'amethyst',
        'name' => 'Amethyst',
        'material' => 'amethyst',
        'price' => '10.00',
        'weight'=> '.5lbs'
    ),

    'catseye'=> array(
        'img' => 'catseye',
        'name' => 'Cats Eye',
        'material' => 'cats eye',
        'price' => '3.00',
        'weight' => '.02lbs'
    ),

    'wizard' => array(
        'img' => 'wizard',
        'name' => 'Wizard',
        'material' => 'pewter and quartz',
        'price' => '40.00',
        'weight' => '1lb'
    ),

    'dragon' => array(
        'img' => 'dragon',
        'name' => 'Dragon',
        'material' => 'pewter and amethyst',
        'price' => '50.00',
        'weight' => '3lbs'
    ),

    'elf' => array(
        'img'=> 'elf',
        'name' => 'Elf',
        'material' => 'pewter',
        'price' => '20.00',
        'weight' => '2lbs'
    )
);


// Grabs the items out of the cart and gets their relevant details from the array in products.php which it then pushes
// into the "out cart" which will be used to create the shopping cart page.
/*function add_to_cart($products,$item,$quantity,$cart) {


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
}*/


#----------------------#
# Functions  checkout  #
#----------------------#
// finish the out_cart indexing to pull in the items.n
function confirm_email($user,$products) {
   // $items = $products;
    $message = "<html><head></head><body><br><br><br><br><br><br><br>" . $user.", thank you for buying this stuff.<br>Your Purchases:";

    $user_list = file('accounts.txt');
    for($i=0; $i < count($user_list);$i++) {
        $line = explode(",",$user_list[$i]);
        for ($c = 0; $c < count($line); $c++) {
            $user_match = preg_match('/^' . $user . '$/', $line[$c], $matches);

            if ($matches) {
                $user_email = $line[1]; //This is the index of the user info that stores the email address.
                $to = $user_email;

                $email_subject = $user . "-- Your Purchase from Crystals, Charms, and Coffee " . date("F d, Y h:i a");

                //$message = '';
                $total = 0;

                foreach($_SESSION['out_cart'] as $key=>$value) {
                    $product = $products[$key];

                    $message .= '<table><tbody><tr><td class="checkout_name">' . $product['name'] . '</td><td class="checkout_quantity">' . $_SESSION['out_cart'][$key]['quantity'] . '</td><td class="checkout_price">$' . $product['price'] * intval($_SESSION['out_cart'][$key]['quantity']) .'.00</td></tr>';
                    $total += $product['price'] * intval($_SESSION['out_cart'][$key]['quantity']);
                }

                $message .= '</tbody></table><div class="total_price"> Your Total: $' .$total . '.00</div></body></html>';

                $headers  = "From: peter.twickler@gmail.com" . "\r\n";
                $headers .= 'MIME-Version: 1.0' . "\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

                $mail = mail($to, $email_subject, $message,$headers);


                if ($mail) {
                    echo "Thank you for your purchase, ". $user . ". An email with your purchase receipt has been sent to your email address.<br><br>
                    Your friends at Crystals, Charms, and Coffees";
                }

                else echo "I'm sorry, something went wrong and we could not send your receipt to the email address on file.";

            }
        }
    }

}

function build_out_cart($cart=NULL,$products){

    $out_cart = '';
    $total = 0;

if ($cart) {
    foreach ($cart as $key => $value) {
        $product = $products[$key];

        $out_cart .= '<tr><td class="checkout_name">' . $product['name'] . '</td><td class="checkout_quantity">' . $cart[$key]['quantity'] . '</td><td class="checkout_price">$' . $product['price'] * intval($cart[$key]['quantity']) . '.00</td></tr>';
        $total += $product['price'] * intval($cart[$key]['quantity']);

    }

    $out_cart .= '</tbody></table><div class="total_price"> Your Total: $' . $total . '.00</div>';
    return $out_cart;
}
}




#----------------------#
# Functions  index     #
#----------------------#

/*
 * @param $product
 * pulls the "properties" of the product arrays into a string to build the html for the display of the products
 *
 *  $item is the product being processed and $products is the array of products in products.php.s
 */
function display($item,$products){


// Pushes the properties of the items into the html to display them. I use a hidden, read-only input called "item"
    // in the form to get the "machine" name of the item for use as an index later. I couldn't figure out a better way
    // to do this.
    $product_display =  '<form  method="GET" action="addtocart.php">
                         <div class = "product_display">
                         <input class="disp_name" type="text" value = "'.$products[$item]["name"] .'" name="prod_name" readonly>
                         <div class ="prod_img" ><img src = "./img/' . $products[$item]["img"].'.jpg"></div>
                         <div class = "prod_weight">'.$products[$item]["weight"] . '</div>
                         <div class = "prod_price">$'.$products[$item]["price"].'</div>

                         <input type="text" size="5" name="quantity">
                         <input class="add_to_cart"  type="submit" value="Add to Cart" >
                         <input type="text" name ="item" value="'.$item.'" readonly hidden="true">
                         </form>

    </div>';

    return $product_display;

};


#----------------------#
# Functions  login     #
#----------------------#

// This function inserts a new "account" into the accounts.txt file. This is how I keep track of login credentials.
// Basically, it implodes the values into a string and then writes it to the file accounts.txt. NOTE: The accounts.txt
// file must be primed with a blank line at the end of the file to avoid writing the new data on the end of the same
// line as the previous user's data. I know there's a lot of more clever stuff I could do to handle that
// automatically (or at least more robustly) but I didn't think it was worth it for this "simple" implementation.
function new_user($user,$pass,$email) {


    $n_user = $user;

    $n_pass = $pass;
    $n_email = $email;

    $users_list = fopen('/Library/WebServer/Documents/final2_back_01/accounts.txt','a+');

    $user_values = array($n_user,$n_pass,$n_email);

    $user_in = implode(",",$user_values);

    $user_in_line = PHP_EOL . $user_in;

    fwrite($users_list,$user_in_line);

    fclose($users_list);
}


function register_display($session) {

if (isset($session['valid']['name']) && $session['valid']['name'] == 'name_error' ) {

       $register_display =  '<form name="register" action="index.php?new_user=1" method="POST">
             <label for="name">Enter your name</label>
             <input type="text" size="20" name="username"><span class="form_error">Please enter a valid username.</span>
             <label for="email">Enter your email address</label>
             <input type="text" size="20" name="email">
             <label for="password">Enter a password</label>
             <input type="text" size="20" name="password">
             <input type="submit" value="Click to register!">
           </form>';

    }

elseif(isset($session['valid']['email']) && $session['valid']['email'] == 'email_error' ) {
    $register_display =  '<form name="register" action="index.php?new_user=1" method="POST">
             <label for="name">Enter your name</label>
             <input type="text" size="20" name="username">
             <label for="email">Enter your email address</label>
             <input type="text" size="20" name="email"><span class="form_error">Please enter a valid email address.</span>
             <label for="password">Enter a password</label>
             <input type="text" size="20" name="password">
             <input type="submit" value="Click to register!">
           </form>';

}

elseif(isset($session['valid']['password']) && $session['valid']['password'] == 'password_error' ) {
    $register_display =  '<form name="register" action="index.php?new_user=1" method="POST">
             <label for="name">Enter your name</label>
             <input type="text" size="20" name="username">
             <label for="email">Enter your email address</label>
             <input type="text" size="20" name="email"
             <label for="password">Enter a password</label>
             <input type="text" size="20" name="password"><span class="form_error">Please enter a valid password.</span>
             <input type="submit" value="Click to register!">
           </form>';

}
    else {
        $register_display = '<form name="register" action="index.php?new_user=1" method="POST">
             <label for="name">Enter your name</label>
             <input type="text" size="20" name="username">
             <label for="email">Enter your email address</label>
             <input type="text" size="20" name="email">
             <label for="password">Enter a password</label>
             <input type="text" size="20" name="password">
             <input type="submit" value="Click to register!">
           </form>';
    }
    return $register_display;
}

/*
 * @param Takes $_POST['username'] and $_POST['password']
 */
// If the user has submitted the login form, iterate through the records in accounts.txt.
// The nested for loops iterate first through the file, line by line, and then the first nested for loop
// iterates through the line looking first for the username submitted. If it finds the username,
// it then iterates through the same line again looking for the password. If both are found, the user is
// logged in. If the password isn't found, it suggests you try again. If the user isn't found, it displays
// the registration form.
function user_cred($username,$pw,$query=array()) {
    $user_info = $query;


    // Form validation and processing. If the new_user variable is set, test the form inputs and then process.
    if(isset($_GET['new_user']) && $_GET['new_user'] ==1){


        $name_test = $user_info['username'];


       if ($name_test != null && $name_test != '') {
           $user_name = $name_test;
       }

       // I have set $user_info to the query (POST) and so now I pass that along instead of the $_POST. I hope to
       // avoid confusion by doing so. Whether or not the $_POST['email'] property is set is the test for distinguishing
       // the regular non-validation path from the form validation path.
       elseif (($name_test == '' || $name_test == null) && isset($_POST['email'])) {
           $_SESSION['valid']['name'] = 'name_error';
          $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php?register_new=1";

           header("Location: " . $url) or die("didn't redirect from login");


        }
        $user_email = $_POST['email'];
        if ($user_email && $user_email != null) {
            $email_check = filter_var($user_email, FILTER_VALIDATE_EMAIL);
        }

        if($user_email == null || $email_check != true){
            $_SESSION['valid']['email'] = 'email_error';
            $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php?register_new=1";

            header("Location: " . $url) or die("didn't redirect from login");

        }

        $user_pw = $_POST['password'];
        if ($user_pw == null or !isset($user_pw)){
            $_SESSION['valid']['password'] = 'password_error';
            $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php?register_new=1";

            header("Location: " . $url) or die("didn't redirect from login");
        }

        new_user($user_name,$user_email,$user_pw);
        ob_clean();
        $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php";

        header("Location: " . $url) or die("didn't redirect from login");
    }

    $username = $_POST['username'];
    $pw = $_POST['password'];

    $g = 0; // Counter to limit the display of the "register here" verbiage.

    $user_list = file('/Library/WebServer/Documents/final2_back_01/accounts.txt');

    for ($i = 0; $i < count($user_list); $i++){
        $line = explode(",",$user_list[$i]);


        for ($c = 0; $c < count($line); $c++) {
            $user_match =  preg_match('/^' . $username . '$/', $line[$c], $matches);



            if ($matches) {

                for ($p = 0; $p < count($line); $p++) {

                   $pw_match =  preg_match('/^' . $pw . '$/', $line[$p], $match);


                    if ($match) {
                        $_SESSION['sign_in'] = 1;
                        $_SESSION['username'] = $username;
                        $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php";
                        ob_clean();
                        header("Location: " . $url) or die("didn't redirect from login");


                    } elseif (!$match) {
                        echo "Wrong Password, jerko";
                    }
                }
            } elseif (!$matches) {
                if ($g == 1) break;
                echo '<div>You do not seem to be registered. Click <a href="index.php?register_new=1">here</a> to register.</div>';
                $g++; // Increments counter to control the number of times the above verbiage and link are displayed.

            }
        }
    }
    //TODO: THis is probably not needed. Remove once verified not needed or remove this TODO
    return $_POST;
}
$firephp->log($_SESSION, 'session');