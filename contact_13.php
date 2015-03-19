<?php
date_default_timezone_set('America/New_York');
function mail_message($data_array, $template_file, $deadline_str, $myname, $myemail) {

  #get template contents, and replace variables with data
  $email_message = file_get_contents($template_file);
  $email_message = str_replace("#DEADLINE#", $deadline_str, $email_message);
  $email_message = str_replace("#WHOAMI#", $data_array['whoami'], $email_message);
  $email_message = str_replace("#DATE#", date("F d, Y h:i a"), $email_message);
  $email_message = str_replace("#NAME#", $myname, $email_message);
  $email_message = str_replace("#EMAIL#", $myemail, $email_message);
  $email_message = str_replace("#IP#", $_SERVER['HTTP_X_FORWARDED_FOR'], $email_message);
  $email_message = str_replace("#AGENT#", $_SERVER['HTTP_USER_AGENT'], $email_message);
  $email_message = str_replace("#SUBJECT#", $data_array['subject'], $email_message);
  $email_message = str_replace("#MESSAGE#", $data_array['message'], $email_message);
  $email_message = str_replace("#FOUND#", $data_array['found'], $email_message);

  #include whether or not to contact the customer with offers in the future
  $contact = "";
  if (isset($data_array['update1'])) {
    $contact = $contact." Please email updates about your products.<br/>";
  }
  if (isset($data_array['update2'])) {
    $contact = $contact." Please email updates about products from third-party partners.<br/>";
  }
  $email_message = str_replace("#CONTACT#", $contact, $email_message);


  #construct the email headers
  $to = "peter.twickler@gmail.com";  //for testing purposes, this should be YOUR email address.
  $from = $data_array['email'];
  $email_subject = "CONTACT #".time().": ".$data_array['subject'];

  $headers  = "From: " . $from . "\r\n";
  $headers .= 'MIME-Version: 1.0' . "\n";
  $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";   #now mail
  mail($to, $email_subject, $email_message, $headers);

}


$customer_name = $_SESSION['name'];
if (!($customer_name)) {
  $customer_name = $_GET['name'];
}
$customer_email = $_SESSION['email'];
if (!($customer_email)) {
  $customer_email = $_GET['email'];
}


#Remember, if you place any output before a header() call, you'll get an error.
#We used the superglobal $_GET here 
if (!($customer_name && $customer_email && $_GET['whoami']
  && $_GET['subject'] && $_GET['message'])) {

  #with the header() function, no output can come before it.
  #echo "Please make sure you've filled in all required information.";

  $query_string = $_SERVER['QUERY_STRING'];
  #add a flag called "error" to tell contact_form.php that something needs fixed
  $url = "http://".$_SERVER['HTTP_HOST']."/contact_form.php?".$query_string."&error=1";
  header("Location: ".$url);
  exit();  //stop the rest of the program from happening

}

#we want a deadline 2 days after the message date.
$deadline_array = getdate();
$deadline_day = $deadline_array['mday'] + 2;

$deadline_stamp = mktime($deadline_array['hours'],$deadline_array['minutes'],$deadline_array['seconds'],
  $deadline_array['mon'],$deadline_day,$deadline_array['year']);
$deadline_str = date("F d, Y", $deadline_stamp);


if (isset($_GET['remember'])) {
  #the customer wants us to remember him/her for next time

  $_SESSION['name'] =$customer_name;
  $_SESSION['email']=$customer_email;
}



//DOCUMENT_ROOT is the file path leading up to the template name.
mail_message($_GET, $_SERVER['DOCUMENT_ROOT']."/email_template.txt", $deadline_str, $customer_name, $customer_email);

include($_SERVER['DOCUMENT_ROOT']."/lab13/template_top.inc");

extract($_GET, EXTR_PREFIX_SAME, "get");

echo "<h3>Thank you!</h3>";
echo "We'll get back to you by ".$deadline_str.".<br/>";
echo "Here is a copy of your request:<br/><br/>";
echo "CONTACT #".time().":<br/>";
echo "Message Date: ".date("F d, Y h:i a")."<br/>";
echo "Name: ".$customer_name."<br/>";
echo "Email: ".$customer_email."<br/>";
echo "Type of Request: ".$whoami."<br/>";
echo "Subject: ".$subject."<br/>";
echo "Message: ".$message."<br/>";
echo "How you heard about us: ".$found."<br/>";

for ($i = 1; $i <= 2; $i++) {
  $element_name = "update".$i;
  echo $element_name.": ";
  echo $$element_name;
  echo "<br/>";
}

echo "You are currently working on ".$_SERVER['HTTP_USER_AGENT'];
echo "<br/>The IP address of the computer you're working on is ".$_SERVER['HTTP_X_FORWARDED_FOR'];

?>
<br/><br/><a href="download.php"><b>Download our PDF brochure!</b></a>
<?


include($_SERVER['DOCUMENT_ROOT']."/lab13/template_bottom.inc");


?>
