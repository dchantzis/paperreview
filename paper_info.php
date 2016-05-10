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
	require("./include/findpaperinc.php");
	require("./include/errorreportinc.php");

	whereUgo(0);
	whereUgo(1);
	
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

<div class="paper_info">

	<div id="page_title">paper info</div>
	<div id="spacer"></div>
	<div id="paper_info_content">
		<div class="dataTypeGroup">
			<fieldset>
				<legend>
					Paper: 
					<?php 
						if(strlen($pvalues["find_paper_title"]) >= 25) { echo "<div id=\"LargePaperName\">" . strtoupper($pvalues["find_paper_title"]) . "</div>"; }
						else {echo strtoupper($pvalues["find_paper_title"]); }
					?>
				</legend>
					<?php //Download papers
					if( 
						( isset($_SESSION["chairman"]) && ($_SESSION["chairman"] == TRUE) ) || 
						( isset($_SESSION["administrator"]) && ($_SESSION["administrator"] == TRUE) )
					){
						echo "<div class=\"field\">";
							echo "<div id=\"download_paper\">";
							echo "<label for=\"\">Download Paper: </label>";
							$paper_type = show_uploaded_paper_body($pvalues["find_paper_id"],0);
							echo "</div>";
						echo "</div>"; 
					}//if 			
					?>
				<div class="field">
					<label for="user">Submitted by: </label>
					<div class="text"><div class="red"><?=$pvalues["find_user_fname"] . " " . $pvalues["find_user_lname"]?></div></div>
				</div>
					<div class="field">
						<label for="authors">Authors: </label>
						<div class="text"><div class="red"><?=$pvalues["find_paper_authors"]?></div></div>
					</div>
					<!--
					<div class="field">
						<label for="subject">Subject: </label>
						<div class="text"><div class="red"><? //echo $pvalues["find_paper_subject"];?></div></div>
					</div>
					-->
					<div class="field">
						<label for="abstract">Paper Abstract: </label>
						<div id="paper_abstract"><pre><?=stripslashes($pvalues["find_paper_abstract"])?></pre></div>
					</div>	
			</fieldset>
			</div>
			<center><div class="print_button" onClick="javascript:if (window.print) window.print();" title="print page"></div></center>
	</div><!--paper_info_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--paper_info-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>