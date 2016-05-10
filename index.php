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
	//regenerate session id if PHP version is lower thatn 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}
	
	require("./include/functionsinc.php");
	
	##########################################################
	//NOTE: THE USER IS NOT SUPPOSED TO EVER VIEW THIS PAGE.
	// IF HE DOES, THEN REALLY SOMEKIND OF HUGE ERROR OCCURED
	##########################################################

	if( 
		($_SESSION["user_logged_in"] != TRUE) || 
		(!isset($_SESSION["logged_user_password"])) ||
		( $_SESSION["logged_user_password"] == "" )
	){
		header("Location: login.php");
		exit;
	}//if
	elseif (
		($_SESSION["user_logged_in"] == TRUE) || (isset($_SESSION["logged_user_password"])) ||( $_SESSION["logged_user_password"] != "" )
		&&	(
			((!isset($_SESSION["administrator"])) || ($_SESSION["administrator"] != TRUE)) ||
			((!isset($_SESSION["chairman"])) || ($_SESSION["chairman"] != TRUE)) ||
			((!isset($_SESSION["reviewer"])) || ($_SESSION["reviewer"] != TRUE)) ||
			((!isset($_SESSION["author"])) || ($_SESSION["author"] != TRUE))
			)
		)
	{
		header("Location: ulounge.php");
		exit;
	}//else if

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if($_SESSION["administrator"] == FALSE){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>


<body class="index">

<div id="wrapper">
	<div id="content">
		<div id="masthead">PAPER <div class="red">REVIEW</div></div>
		<div id="logo">logo</div>

		<h1><span class="red">ERROR</span></h1>

		<div id="instructions">
        	Sorry for the inconvenience.<br />
			Some kind of error occurred. What would you like to do?
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