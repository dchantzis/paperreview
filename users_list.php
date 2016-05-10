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
	require("./include/errorreportinc.php");
	
	whereUgo(0);
	whereUgo(10);
	whereUgo(1);
	
	reset ($_POST); 
	//limitPerPage contains the number of users to be displayied
	$limitPerPage = "";
	if (isset($_POST["limitPerPage"])) 
	{
		$limitPerPage = $_POST["limitPerPage"];
		$_SESSION["limitPerPage"] = $_POST["limitPerPage"];
	}elseif (isset($_SESSION["limitPerPage"]))
	{
		$limitPerPage = $_SESSION["limitPerPage"];
	}else { $limitPerPage = 20; /*default users per page */}
	
	reset ($_GET); 
	$search_lname = "";
	if (isset($_GET["search_lname"]))
	{
		$search_lname = $_GET["search_lname"];
	}//
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="scripts/prvalidations.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);*/
</style>
</head>

<body class="users_list">
<div id="wrapper">
	<div id="users_list_content">
	<?php 	display_users($limitPerPage,$search_lname); ?>
	</div><!--content-->
</div><!--wrapper-->
</body>
</html>
