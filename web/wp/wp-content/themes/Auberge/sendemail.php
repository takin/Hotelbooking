<?php
$name = stripslashes($_POST['name']);
$mailFrom = $_POST['emailFrom'];
$subject = stripslashes($_POST['subject']);
$numreserv = stripslashes($_POST['numreserv']);
$sitename = stripslashes($_POST['sitename']);
$message = stripslashes($_POST['message']);
$adminemail = ($_POST['adminemail']);
$date = date('d/m/Y');

/* Admin part */

/* Put admin email here */

//$subjectemail     = $sitename.' - '.__('Customer Service','auberge').' - '.$numreserv;

$subjectemail     = "$sitename - $numreserv";

//$headers = "From: $name <$mailFrom>\r\n";
//$headers .= "MIME-Version: 1.0\r\n";
//$headers .= "Content-type: text/html; charset=UTF-8\r\n";

$headers  = "Content-Type: text/plain;". "charset=UTF-8; format=flowed\n"."MIME-Version: 1.0\n"."Content-Transfer-Encoding: 8bit\n"."X-Mailer: PHP\n";
$headers  .= "From: Contact Page Request <$adminemail>\n";
//$headers .= "Reply-To: ". strip_tags($adminemail) . "\r\n";
//$headers .= "MIME-Version: 1.0\r\n";
//$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

$fullmsg = 'Name: '. $name .'

Email: '. $mailFrom .'

Subject: '. $subject.'

Reservation Number: '. $numreserv.'

Message: '. $message .'

Date: '. $date;

/* send mail to admin */
mail($adminemail, $subjectemail, $fullmsg, $headers);
?>