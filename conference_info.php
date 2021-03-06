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
	require("./include/findconferenceinc.php");
	require("./include/errorreportinc.php");

	whereUgo(0);
	whereUgo(1);
	whereUgo(8);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
<link rel="stylesheet" rev="stylesheet" href="./scripts/print.inc.css" type="text/css" media="print" />
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="conference_info">

	<div id="page_title">Conference Info</div>
	<div id="spacer"></div>
	<div id="conference_info_content">
		<div class="dataTypeGroup">
			<fieldset>
				<legend>
					Conference: 
					<?php 
						if(strlen($cvalues["find_conf_name"]) >= 25) { echo "<div id=\"LargeConferenceName\">" . strtoupper($cvalues["find_conf_name"]) . "</div>"; }
						else {echo strtoupper($cvalues["find_conf_name"]); }
					?>
				</legend>
					<div class="field">
						<label for="">Conference Alias: </label>
						<div class="text"><div class="red"><?=$cvalues["find_conf_alias"]?></div></div>
					</div>				
					<div class="field">
						<label for="">Where is the conference held?: </label>
						<div class="text"><div class="red"><?=$cvalues["find_conf_place"]?></div></div>
					</div>
					<div class="field">
						<label for="">When is the conference held?: </label>
						<div class="text"><div class="red"><?=$cvalues["date_conference_held"]?></div></div>
					</div>
					<div class="field">
						<label for="">Contact E-mail: </label>
						<div class="text"><div class="red"><?=$cvalues["find_conf_contact_email"]?></div></div>
					</div>
					<div class="field">
						<label for="">Contact Phone Number: </label>
						<div class="text"><div class="red"><?=$cvalues["find_conf_contact_phone"]?></div></div>
					</div>
					<div class="field">
						<label for="">Conference Website: </label>
						<div class="text"><div class="red"><?=$cvalues["find_conf_website"]?></div></div>
					</div>
					<div class="field">
						<label for="">Deadline date: </label>
						<div class="text"><div class="red"><?=$cvalues["g_find_conf_deadline"]?></div></div>
					</div>
					<div class="field">
						<label for="">Abstracts Submittion Deadline: </label>
						<div class="text"><div class="red"><?=$cvalues["g_find_conf_abstracts_deadline"]?></div></div>
					</div>
					<div class="field">
						<label for="">Manuscripts Submittion Deadline: </label>
						<div class="text"><div class="red"><?=$cvalues["g_find_conf_manuscripts_deadline"]?></div></div>
					</div>
					<div class="field">
						<label for="">Camera-Ready Submittion Deadline: </label>
						<div class="text"><div class="red"><?=$cvalues["g_find_conf_camera_ready_deadline"]?></div></div>
					</div>
					<div class="field">
						<label for="">Preferencies Submittion Deadline: </label>
						<div class="text"><div class="red"><?=$cvalues["g_find_conf_preferencies_deadline"]?></div></div>
					</div>
					<div class="field">
						<label for="">Reviews Submittion Deadline: </label>
						<div class="text"><div class="red"><?=$cvalues["g_find_conf_reviews_deadline"]?></div></div>
					</div>
					<div class="field">
						<label for="">Comments: </label>
						<div class="text"><div class="red"><pre class="comments"><?=$cvalues["find_conf_comments"]?></pre></div></div>
					</div>
			</fieldset>
			</div>
			
			<br>
			<? if($flg == ""){ ?>
					<fieldset>
						<legend>Conference Chairmen List</legend>
						<? //this session has the conference id 
							show_conference_participants("chairman",$cvalues["find_conf_id"],"no_remove_option");
						?>
					</fieldset>
					<br>
					<fieldset>
						<legend>Reviewers List</legend>
						<? //this session has the conference id 
							show_conference_participants("reviewer",$cvalues["find_conf_id"],"no_remove_option");
						?>
					</fieldset>
			<? }//end if ?>
		<center><div class="print_button" onClick="javascript:if (window.print) window.print();" title="print page"></div></center>
	</div><!--conference_info_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--conference_info-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>