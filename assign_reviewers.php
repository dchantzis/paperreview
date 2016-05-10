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

	whereUgo(0);
	whereUgo(8);
	whereUgo(3);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}

	if(isset($_GET["paperid"]))
	{
		$get_var_type["paperid"] = "([^0-9]+)";
		$validated_vars = checkGetVariable(1,0,$get_var_type);
		$paper_id = $validated_vars["paperid"];
	}//if
	else if (isset($_SESSION["paper_id"]))//this is for when this page is reloaded with the error flag $_GET["flg"]
	{
		$paper_id = $_SESSION["paper_id"];
	}//else if

	if (isset($_GET["update_reviewer_assignment"])) 
	{ 
		$_SESSION["update_reviewer_assignment"] = "no";
		//if the sessions regarding a paper interest levels are filled, empty them
		empty_assign_reviewers_sessions();
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="./scripts/prvalidations.js"></script>
<script language="JavaScript" type="text/javascript" src="./scripts/navigation.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="assign_reviewers">

	<div id="page_title">Assign Papers To Reviewers</div>
	<div id="spacer"></div>

		<div id="assign_reviewers_content">
			<div id="instructions">
				Check the names of the reviewer for this paper.
				<br>
				Each paper has to have up to <font size="+1"><b><?=$coptions2D["chairman"]["NORPC"]?></b></font> reviewers.
				<?php if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";}  ?>
            </div>			
				<?php
					if($_SESSION["update_reviewer_assignment"] == "no"){
						echo "<form id=\"arifrm\" name=\"arifrm\" method=\"post\" action=\"./include/functionsinc.php?type=37\">";
						$submit_button = "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\"></div></div>";
					}
					elseif ($_SESSION["update_reviewer_assignment"] == "yes"){
						echo "<form id=\"arufrm\" name=\"arufrm\" method=\"post\" action=\"./include/functionsinc.php?type=38\">";
						$submit_button = "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Update\"></div></div>";
					}//else
				?>
				<fieldset>
					<?php $_SESSION["temp_paper_id"] = $paper_id; ?>
					<?php  $string_to_echo = show_candidate_reviewers_of_paper($paper_id); ?>
					<legend>Assign Reviewers to Paper: <a href="paper_info.php?paperid=<?=$_SESSION["temp_paper_id"]?>" title="Click for paper info." class="simple"><?=$_SESSION["temp_paper_title"]?></a></legend>
					<div class="messages"><?php VariousMessages($flg); ?></div>
					
					<?php  echo $string_to_echo; ?>
				<?=$submit_button?>
			</fieldset>
            <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "assign_reviewers") . $csrf_password_generator?>" />
		</form>
	</div><!--paper_interest_level_content-->

	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--paper_interest_level-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>
