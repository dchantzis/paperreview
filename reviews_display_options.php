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
<script type="text/javascript" src="./scripts/navigation.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="reviews_display_options">
	<div id="page_title">Reviews Display Options</div>
	<div id="spacer"></div>
	<div id="reviews_display_options_content">

	<div id="instructions">Select a way to view all the papers reviews.</div>
				
		<ul class="rdo_types">
			<li><a href="./display_reviews_ch.php" class="simple">Display all reviews</a></li>
			<li><a href="./display_incomplete_reviews.php" class="simple">Display reviews status</a></li>
			<li><a href="./accept_papers.php" class="simple">Accept papers for conference</a></li>
		</ul>

	</div><!--view_assignments_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--view_assignments-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>