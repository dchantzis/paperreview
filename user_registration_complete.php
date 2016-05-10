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
	require("./include/layoutinc.php"); 
	require("./include/errorreportinc.php"); 

	whereUgo(7);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::Paper Reviews::. Registrate</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
<link rel="stylesheet" rev="stylesheet" href="./scripts/print.inc.css" type="text/css" media="print" />
</head>

<body class="user_registration_complete">
<div id="wrapper">
<div id="masthead">PAPER <div class="red">REVIEW</div></div>	
	<div id="user">
		<div id="userData">
			<h3>WELCOME <?php echo strtoupper($_SESSION["fname"]) . "\t" . strtoupper($_SESSION["lname"]); ?></h3>
		</div>
	</div>
	<!--image wrapper background bars -->
	<div id="topbar"></div>	
	<div id="leftbar"></div>
	<div id="rightbar"></div>
	
	<div id="logo">logo</div>
	<div id="content">
		<div id="mainColumn">
			<fieldset>
				<legend>Registration Complete</legend>
				<div class="notes">A notifing message has been sent to your e-mail address.</div>
				<div class="notes">We suggest you to <a href="javascript:if (window.print) window.print();" class="simple">print this page</a> <input type="image" src="./images/layout/print.gif" width="15" height="20" align="absmiddle" border="0" onClick="javascript:if (window.print) window.print();">for future reference.</div>
				<div class="notes">You can also change your login information later.</div>
				<div class="dataTypeGroup">
					User LogIn Info
					<div class="notes">You are required to remember the following to login.</div>
					<div class="field">
						<label for="username">Your username:</label>
						<div class="text"><div class="red"><?=$_SESSION["email"]?></div></div>
					</div>
					<div class="field">
						<label for="password">Your password:</label>
						<div class="text"><div class="red"><?=$_SESSION["password"]?></div></div>
					</div>
				</div><!-- user login info -->
				
				<div class="dataTypeGroup">
					Change Password Info
					<div class="notes">If you forget your password you would be asked the following.</div>
					<div class="field">
						<label for="security_question">Security question:</label>
						<div class="text"><div class="red"><?=$_SESSION["security_question"]?></div></div>
						<br>
						<label for="security_answer">Your answer:</label>
						<div class="text"><div class="red"><?=$_SESSION["security_answer"]?></div></div>
					</div>
					<div class="field">
						<label for="birthday">Date of birth:</label>
						<div class="text"><div class="red"><?=$_SESSION["birthday"]?></div></div>
					</div>
				</div><!-- change password info -->
				
			<form id="logoutfrm" name="logoutfrm" method="post" action="./include/functionsinc.php?type=2">
				<div class="submit"><input type="submit" value="Log In" title="Log In"></div>
			</form>
				
			</fieldset>
		</div>
	</div>
</div>

<?php
	session_unset();
	// Clear the session cookie
	unset($_COOKIE[session_name()]);
	// Destroy session data
	session_destroy();
?>
</body>
</html>
