<?php




/*ini_set('display_errors', 1);

error_reporting(E_ALL);*/

ob_start();


require_once('functions.php');

if (isset($_GET['out']) && $_GET['out']==1){
    session_start();

    // I'll be honest. I had to poach the code in this if statement. I couldn't get the session ID to regenerate any
    // other way. It would wipe out the data in the session, but it wouldn't renew the session ID. I was having some
    // other problems and wanted to get this piece nailed down before I went on to look for another cause.
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
// END POACHED CODE

    // Finally, destroy the session.
    session_destroy();

    //Add in a page reload so that the session_destroy() will take effect*/

    $url = "http://" . $_SERVER['HTTP_HOST'] . "/final2_back_01/index.php";

    header("Location: ".$url) or die("Didn't work");
}

if(!isset($_SESSION)) {
    session_start();
}

ob_start();
require_once('FirePHPCore/FirePHP.class.php');

if (!$firephp) {
    ob_start();

    $firephp = FirePHP::getInstance(true);
}

$_SESSION['cart'] = array();

// The valid property of the session will be used to store values for form validation. If it's set, don't change it,
// but if it's not set, set it.
if (!isset($_SESSION['valid'])) {
    $_SESSION['valid'] = array();
}


if(isset($_SESSION['out_cart'])) {
    $item = $_SESSION['out_cart'];
}

include_once($_SERVER['DOCUMENT_ROOT'] ."/final2_back_01/template_top.inc");


// This array stores the machine names of the products. It needs to be appended if you want to add another
// product to the store. The products.php file must also be appended to contain the new item's properties.
$current_products = array('amethyst','quartzorb','wizard','catseye','dragon','elf');


if (isset($_POST['username']) && isset($_POST['password'])) {

    user_cred($_POST['username'],$_POST['password'],$_POST);

}

if (isset($_GET['register_new']) && $_GET['register_new'] == 1) {




 $register_display = register_display($_SESSION);




    echo $register_display;

}

/*if (isset($_GET['new_user']) && $_GET['new_user']  == 1){

    if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email]'])) {



        new_user($_POST['username'], $_POST['password'], $_POST['email]']);

    }
}*/


$disp = '';
for ($i =0; $i < count($current_products); $i++){
    echo display($current_products[$i],$products);
}


echo "</div><!--end div.wrapper-->";


$firephp->log($_SESSION, 'session');

echo '</body>
</html>';





