<?php
##################################################
################emailinc.php######################
##################################################
//function for outgoing emails
function registration_email ($email_addr,$subject, $message)
{
	if (!isset($_SESSION["SESSION"])) require ( "./sessioninitinc.php");
	global $server_name;
	global $send_emails_to_users;
	
	// If any lines are larger than 120 characters, we will use wordwrap()
	$message = wordwrap($message, 120);
	// add additional headers...
	$headers = "From: pradministrator@".$server_name."\r\n" .
	   "Reply-To: pradministrator@".$server_name."\r\n" .
	   "X-Mailer: PHP/".phpversion();
	// Send the email...
	
	if($send_emails_to_users == "on"){ mail($email_addr, $subject, $message, $headers); }
	elseif($send_emails_to_users == "off"){ } //do nothing
}//registration_email ($message)
?>