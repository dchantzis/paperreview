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

	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::Paper Reviews::.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<script type="text/javascript" src="./scripts/content_toggle.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>
</head>

<body class="login_choose_conference">
<div id="wrapper">
<div id="masthead">PAPER <div class="red">REVIEW</div></div>
	
	<div id="user">
		<div id="userData">
		<?php echo "USER ~ " . strtoupper($_SESSION["logged_user_fname"]) . "\t" . strtoupper($_SESSION["logged_user_lname"]); ?>
		</div>
		<div id="userOptions">
			<ul>
				<li><a href="./include/functionsinc.php?type=2" title="logout">logout</a></li>
			</ul>
		</div>
	</div>
	
	<div id="topbar"></div>	
	<div id="leftbar"></div>
	<div id="rightbar"></div>
	
	<div id="logo">logo</div>
	
	<div id="content">
		<div id="instructions">
			Select a conference from the list below.
		</div>

		<?php display_login_conferences(); ?>
		<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
	</div><!--content-->
</div><!--wrapper-->
<?php
/*
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	reset($_SESSION);
	while (list($key, $val) = each ($_SESSION))
	{
		echo $key . " = " . $_SESSION[$key] . "<br>";
	}//
*/
?>
</body>
</html>