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

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "create_announcement") . $csrf_password_generator; 

	whereUgo(0);
	whereUgo(8);
	whereUgo(3);

	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
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

<div class="announcements">

	<div id="page_title">Announcements</div>
	<div id="spacer"></div>
	<div id="announcements_content">
		
		<div id="instructions">
			Create an announcements for this conference using the form below.
			<br>
			Note: Announcements can not be updated.
		</div>
		
		<fieldset>
			<legend>New Announcement</legend>
			<div class="messages"><?php VariousMessages($flg); ?></div>
			<div class="notes">Fields marked with <span class="required">*</span> are required.</div>
			<form id="cafrm" name="cafrm" method="post" action="./include/functionsinc.php?type=24">
			<input type="hidden" name="post_date" id="post_date" value="<?php echo date("Y-m-d") . " " . date("H:i:s") ?>">
			<input type="hidden" name="user_id" id="user_id" value="<?=$_SESSION["logged_user_id"]?>">
			<input type="hidden" name="conference_id" id="conference_id" value="<?=$_SESSION["conf_id"]?>">
            <input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
			<div class="dataTypeGroup">
				<div class="field">
						<label for="message">Message: <span class="required" title="this field is required">*</span></label>
						<textarea class="text" cols="35" rows="18" name="message" id="message" wrap="hard" title="enter message"></textarea>
						<div class="notes">(maximum of 2000 characters)</div>
					</div>
					
					<div class="field">
						<label for="">This announcement regards: <span class="required" title="this field is required">*</span></label>
							<ul class="checkboxes">
								<li><input type="checkbox" id="regardschairmen" name="regardschairmen"> Chairmen</li>
								<li><input type="checkbox" id="regardsreviewers" name="regardsreviewers"> Reviewers</li>
								<li><input type="checkbox" id="regardsauthors" name="regardsauthors"> Authors</li>
							</ul>
							<div class="notes">(select at least one)</div>
					</div><!--fields-->
					<div class="field"><div class="submit"><input type="submit" title="Submit form" value="Submit"></div></div>
			</div><!--dataTypeGroup-->
			</form>
			</fieldset>
	</div><!--announcements_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--announcements-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
<?php
empty_announcement_sessions();
?>
</body>
</html>


