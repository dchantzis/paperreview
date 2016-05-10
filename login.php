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
	//require("./include/errorreportinc.php"); //DON'T USE HERE

	$flg = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
	
	############## CHECK IF COOKIES ARE ENABLED ON THE BROWSER ##############
	error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);
	// Check if cookie has been set or not
	if ( (!isset($_GET['s'])) && ($_GET['s'] != '0') )
	{
		// Set cookie
		setcookie ('test', 'test', time() + 60);
		// Reload page
		if (isset($_GET["flg"])) { header ("Location: login.php?s=0&flg=" . $flg); }
		else {	header ("Location: login.php?s=0"); }
	} 
	else {
		// Check if cookie exists
		if (!empty($_COOKIE['test'])) {}//"Cookies are enabled on your browser"
		else if(!isset($_COOKIE['test']))
		{ 
			echo "<div class=\"cookies_notification\">";
			echo "<a href=\"./browsererrors.php?e=" . hash('sha256', "cookies") . "\" class=\"simple\" title=\"Click for instructions.\">Cookies are <b>NOT</b> enabled on your browser</a>";
			echo "</div>";
		}
	}
	######################################################################
	
	if (
		( isset($_SESSION["user_logged_in"]) && $_SESSION["user_logged_in"] == TRUE) || isset($_SESSION["password"]) ||
		(isset($_SESSION["administrator"]) && $_SESSION["administrator"] != "") ||
		(isset($_SESSION["chairman"]) && $_SESSION["chairman"] != "") ||
		(isset($_SESSION["reviewer"]) && $_SESSION["reviewer"] != "") ||
		(isset($_SESSION["author"]) && $_SESSION["author"] != "")
		)
	{
		header("Location: ulounge.php");
		exit;
	}//if

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>.::Paper Review::. - Login</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    
    <noscript>
        <meta http-equiv="refresh" content="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>" />
    </noscript>
    
    <style type="text/css" media="screen">
        @import url(./scripts/allstyles.inc.css);
    </style>
</head>


<body class="login">
<div id="wrapper">	
	<div id="content">
		<div id="masthead">PAPER <div class="red">REVIEW</div></div>
		<div id="logo">logo</div>
		
		<div id="instructions">
			Enter your e-mail into "E-mail" and password into the "Password" fields respectively, then click "Sign in".
		</div>
		<div id="errors">
			<?php VariousMessages($flg); ?>
		</div>
		<div id="form">
		<form id="lifrm" name="lifrm" method="post" action="./include/functionsinc.php?type=1">
			<fieldset>
				<legend>Log In</legend>				
				<div class="field">
					<label for="email">E-mail: </label>
					<input type="text" class="text" name="email" id="email" maxlength="50" size="24" title="Type your e-mail." />
				</div>
				<div class="field">
					<label for="password">Password: </label>
					<input type="password" class="text" name="password" id="password" maxlength="35" size="24" title="Type your password." />
				</div>
				<div class="field">
					<div class="submit"><input type="submit" title="Click to LogIn" value="Enter"/></div>
				</div>
			</fieldset>
		</form>
		</div>
	
	
		<div id="userOptions">
			<ul>
				<li id='register'><a href="./user_registration.php" title="Register to the system">Register</a></li>
				<li id='forgotpassword'><a href="./change_password.php" title="Change your password">Forgot Password?</a></li>
			</ul>
		</div><!--userOptions-->
	</div><!--content-->
<?php
	$active_c_ar = load_all_conferences("active");
	$expired_c_ar = load_all_conferences("inactive");
	
	display_sorted_conferences($active_c_ar,$expired_c_ar,0);	
?>	
</div><!--wrapper-->
</body>
</html>
