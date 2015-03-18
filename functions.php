<?php
date_default_timezone_set ( 'America/New_York' );

ini_set('display_errors', 1);

error_reporting(E_ALL);


#----------------------#
# Functions  checkout  #
#----------------------#
// finish the out_cart indexing to pull in the items.n
function confirm_email($user) {
    $message = "<html><head><head><body><br><br><br><br><br><br><br>" . $user.", thank you for buying this stuff.<br>Your Purchases:";


    include('/Library/WebServer/Documents/final2_back_01/products.php');
    $user_list = file('accounts.txt');
    for($i=0; $i < count($user_list);$i++) {
        $line = explode(",",$user_list[$i]);
        for ($c = 0; $c < count($line); $c++) {
            $user_match = preg_match('/^' . $user . '$/', $line[$c], $matches);

            if ($matches) {
                $user_email = $line[2]; //This is the index of the user info that stores the email address.
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



                $mail = mail($to, $email_subject, $message, "From: peter.twickler@gmail.com");

                if ($mail) {
                    echo "Thank you for your purchase, ". $user . ". An email with your purhcase receipt has been sent to your email address.<br><br>
                    Your friends at Crystals, Charms, and Coffees";
                }

                else echo "I'm sorry, something went wrong and we could not send your receipt to the email address on file.";

            }
        }
    }


    $p_items = '';
    /*foreach($_SESSION['out_cart'] as $key=>$value) {
        foreach ($value as $item=>$property) {

            if ($item == 'name') {
                $p_items .= $property;
                echo $p_items;
            }


        }
    }
*/
    /*    $to = "support@example.com";  //for testing purposes, this should be YOUR email address.
        $from = $_GET['email'];
        $email_subject = $_GET['subject'];

    #now mail
        mail($to, $email_subject, $message, "From: ".$from);*/

}

function build_out_cart($cart,$products){

    $out_cart = '';
    $total = 0;


    foreach($cart as $key=>$value) {
        $product = $products[$key];

        $out_cart .= '<tr><td class="checkout_name">' . $product['name'] . '</td><td class="checkout_quantity">' . $cart[$key]['quantity'] . '</td><td class="checkout_price">$' . $product['price'] * intval($cart[$key]['quantity']) .'.00</td></tr>';
        $total += $product['price'] * intval($cart[$key]['quantity']);

    }

    $out_cart .= '</tbody></table><div class="total_price"> Your Total: $' .$total . '.00';
    return $out_cart;

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

    $user_in_line = $user_in . PHP_EOL;

    fwrite($users_list,$user_in_line);

    fclose($users_list);
}


function register_display() {
    print '<form name="register" action="login.php?new_use=1" method="POST">
             <label for="name">Enter your name</label>
             <input type="text" size="20" name="name">
             <label for="email">Enter your email address</label>
             <input type="text" size="20" name="email">
             <label for="password">Enter a password</label>
             <input type="text" size="20" name="password">
             <input type="submit" value="Click to register!">


           </form>';
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
function user_cred($username,$pw) {
    $username = $username;
    $pw = $pw;

    $g = 0; // Counter to limit the display of the "register here" verbiage.

    $user_list = file('/Library/WebServer/Documents/final2_back_01/accounts.txt');

    for ($i = 0; $i < count($user_list); $i++){
        $line = explode(",",$user_list[$i]);

        for ($c = 0; $c < count($line); $c++) {
            $user_match = preg_match('/^' . $username . '$/', $line[$c], $matches);

            if ($matches) {

                for ($p = 0; $p < count($line);$p++) {

                    $pw_match = preg_match('/^' . $pw . '$/', $line[$p], $match);

                    if ($match) {
                        $_SESSION['sign_in'] = 1;
                        $_SESSION['username'] = $username;
                        $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php";
                        ob_clean();
                        header("Location: " . $url) or die("didn't redirect from login");

                        echo "logging in no worries";
                    }

                    elseif (!$match) {
                        echo "Wrong Password, jerko";
                    }
                }
            }

            elseif(!$matches){
                if ($g==1) break;
                echo '<html><div>You do not seem to be registered. Click <a href="login.php?register_new=1">here</a> to register.</div></html>';
                $g++; // Increments counter to control the number of times the above verbiage and link are displayed.

            }

        }
    }

}