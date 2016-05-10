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

	require("./include/layoutfragmentsinc.php");
	require("./include/layoutinc.php"); 
	require("./include/functionsinc.php");  
	require("./include/errorreportinc.php");	
	
	if ($_SESSION["user_updated"] != TRUE || !isset($_SESSION["user_updated"]))
	{
		header("Location: ./index.php");
		exit;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="scripts/prvalidations.js"></script>
<script type="text/javascript" src="./scripts/navigation.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="update_user_profile_complete">

	<div id="page_title">User Profile</div>
	<div id="spacer"></div>
	<div id="update_user_profile_complete_content">
			<fieldset>
				<legend>Update Complete</legend>
				<div class="notes">A notifing message has been sent to your e-mail address.</div>
				<div class="notes">We suggest you to <a href="javascript:if (window.print) window.print();" class="simple">print this page</a> <input type="image" src="./images/layout/print.gif" width="15" height="20" align="absmiddle" border="0" onClick="javascript:if (window.print) window.print();">for future reference.</div>
				<div class="dataTypeGroup">
					User LogIn Info
					<div class="notes">You are required to remember the following to login.</div>
					<div class="field">
						<label for="username">Your username:</label>
						<div class="text"><div class="red"><?=$_SESSION["logged_user_email"]?></div></div>
					</div>
					<div class="field">
						<label for="password">Your password:</label>
						<div class="text"><div class="red"><div class="red">*encrypted*</div></div></div>
					</div>
				</div><!-- user login info -->
				
				<div class="dataTypeGroup">
					Change Password Info
					<div class="notes">If you forget your password you would be asked the following.</div>
					<div class="field">
						<label for="security_question">Security question:</label>
						<div class="text"><div class="red"><?=$_SESSION["security_question"]?></div></div>
						<br><br>
						<label for="security_answer">Your answer:</label>
						<div class="text"><div class="red"><?=$_SESSION["security_answer"]?></div></div>
					</div>
					<div class="field">
						<label for="birthday">Date of birth:</label>
						<div class="text"><div class="red"><?=$_SESSION["birthday"]?></div></div>
					</div>
				</div><!-- change password info -->				
			</fieldset>
	</div><!--update_user_profile_complete_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--update_user_profile_complete-->
		
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
<?php
empty_upated_user_info_sessions();
?>
</body>
</html>
