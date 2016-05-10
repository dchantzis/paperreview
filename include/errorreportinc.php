<?php
##################################################
################errorreportinc.php############
##################################################

//INCLUDES THE FOLLOWING FUNCTIONS
/*
errorReporter($type, $msg, $file, $line, $context),
*/
//NOTE: THESE FUNCTIONS ARE USED FOR DEBUGGING THE SYSTEM. THE SYSTEM WORK AND RESPONSE FLOW WILL NOT BE CHANGED

//DEFINE THE USERS ACTION LOG FILE
$errorLog = "/wamp/www/PR/errors/bebug_error_log.log"; //for localhost
//$errorLog = dirname($_SERVER['SCRIPT_FILENAME']) . "/errors/bebug_error_log.log";

function errorReporter($type, $msg, $file, $line, $context)
{

	global $errorLog;	

	// construct the error string
	$errorString = "DATE: " . date("d-m-Y H:i:s", mktime()) ."\r\n";
	$errorString .= "TYPE: " .  $type . "\r\n";
	$errorString .= "FILE: " . $file . "(line: " . $line . ")" . "\r\n";
	$errorString .= "ERROR: " . $msg . "\n";
	$errorString .= "\r\n\r\n";

	// write the error string to the specified log file
	$fp = fopen($errorLog, "a+");
	fwrite($fp, $errorString);
	fclose($fp);

}//errorReporter($type, $msg, $file, $line, $context)


if($custom_debugger == "on")
{
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE); //6143
	// define a custom handler
	set_error_handler("errorReporter");	
}//if($custom_debugger == "on")

?>