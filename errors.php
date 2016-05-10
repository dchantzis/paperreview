<?php 
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	header ("Content-type: text/html; charset=utf-8");
	###################################################################################
	
	session_start(); //start session
	session_regenerate_id(true); //regenerate session id
	//regenerate session id if PHP version is lower than 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}
	
	require("./include/functionsinc.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);*/
</style>
</head>


<body class="index">

<div id="wrapper">
	<div id="content">
		<div id="masthead">PAPER <span class="red">REVIEW</span></div>
		<h1><span class="red">404 ERROR</span></h1>

		<div id="instructions">
        	Sorry for the inconvenience.<br>
			Somekind of error occured. The system administrators would be notified.<br>
            What would you like to do?
        </div> 
		
        <ul class="desperate_actions">
            <li><a href="./login.php" class="simple">Login</a></li>
            <li><a href="./include/functionsinc.php?type=2" class="simple">Logout</a></li> 
            <li><a href="./index.php" class="simple">Go to users lounge</a></li>
        </ul>
   	</div>
</div>

</body>
</html>