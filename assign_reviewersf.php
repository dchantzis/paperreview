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
	//regenerate session id if PHP version is lower than 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}

	require("./include/layoutfragmentsinc.php");
	require("./include/layoutinc.php"); 
	require("./include/functionsinc.php");
	require("./include/errorreportinc.php");

	global $csrf_password_generator;

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

<div class="assign_reviewersf">
	<div id="page_title">Assign Papers To Reviewers</div>
	<div id="spacer"></div>
	<div id="assign_reviewersf_content">
		<div id="instructions">
			Select the names of the reviewers for each paper.
			<br />
			Each paper can have up to <font size="+1"><b><?=$coptions2D["chairman"]["NORPC"]?></b></font> reviewers.
            <br />
            In the Reviewers combo boxes, each available selection is in the format: <br /> "Interest Level for this paper - Reviewer Name - (No of assigned papers) - Conflict with paper author(s)".
			<?php if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";}  ?>
        </div>
        
        <form id="arfrm" name="arfrm" method="post" action="./include/functionsinc.php?type=46">
			<fieldset>
			<legend>Assign Reviewers to Papers</legend>
            <div class="messages"><?php VariousMessages($flg); ?></div>	
			<?php 
				$temp_value = display_reviewers_for_papers(); 
				if($temp_value != 0)
				{
					echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\"></div></div>";
				}//if
			?>
            <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "assign_reviewersf") . $csrf_password_generator?>" />
			</fieldset>
		</form>
		
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
