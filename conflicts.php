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
	//require("./include/errorreportinc.php");

	global $csrf_password_generator;
	
	whereUgo(0);
	whereUgo(8);
	whereUgo(5);
	
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
				
<div class="conflicts">
	<div id="page_title">Reviewers</div>
	<div id="spacer"></div>

		<? 
			//if the length of a papers' title is more than 25 characters, then use 2 lines,
			//else use only one.
			if ( (isset($_SESSION["paper_title"])) && (strlen($_SESSION["paper_title"]) > 30)) { echo "<div id=\"ppaperInfo2\">"; }
			else {echo "<div id=\"ppaperInfo\">"; } 
		?>
			<div id="ppaperTitle">
				Paper: <a href="./paper_info.php?paperid=<? if(isset($_SESSION["paper_id"])){ echo $_SESSION["paper_id"];} ?>" target="_parent" title="Paper Info."><? if(isset($_SESSION["paper_title"])){ echo $_SESSION["paper_title"];} ?></a>
				<!--Paper: <i><? if(isset($_SESSION["paper_title"])){ echo $_SESSION["paper_title"];}?></i>-->
			</div>
			
		<? 
			//if the length of a papers' title is more than 25 characters, then use 2 lines,
			//else use only one.
			if ( (isset($_SESSION["paper_title"])) && (strlen($_SESSION["paper_title"]) > 30)) { echo "<div id=\"ppaper_search_form2\" title=\"Select Paper.\">"; }
			else {echo "<div id=\"ppaper_search_form\" title=\"Select Paper.\">"; } 
		?>
			<form id="spaperform" name="spaperform" method="post" action= "./include/functionsinc.php?type=41">
				<?php  paper_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "conflicts") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--ppaper_search_form-->
		</div><!--ppaperInfo-->

	<div id="conflicts_content">
		
		<div id="instructions">
			Select a paper from the list above, and for that paper check all the reviewers that you 
			have conflicts with.
			<br>
			Your name would not be included in the list, if you are a reviewer in this conferencen
			
			<?php 
				if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";} 
				elseif($coptions1D["ACR"] == 0){echo "<br><br><span class=\"red\">Authors are not allowed to enter conflicts with reviewers.</span>";}
			?>
            
		</div>

	<?php
	if(isset($_SESSION["paper_id"]) && isset($_SESSION["temp_token"]))
	{
		echo "<form id=\"ecifrm\" name=\"ecifrm\" method=\"post\" action=\"./include/functionsinc.php?type=42\">";

		echo "<fieldset>";
			echo "<legend>Conflicts with Reviewers for Paper: ";
			if(strlen($_SESSION["paper_title"]) > 30){ echo "\n\t\t\t<br>";}			
			echo "<a href=\"paper_info.php?paperid=" . $_SESSION["paper_id"] . "\" title=\"Click for paper info.\" class=\"simple\">" . $_SESSION["paper_title"] . "</a>";
		echo"</legend>";

		echo "<div class=\"messages\">";
				VariousMessages($flg);
		echo "</div>";

		echo "<div class=\"field\">
				<label for=\"\">Paper Authors: </label>
				<div class=\"text\"><div class=\"red\">" . $_SESSION["authors"] . "</div></div>
			</div>";
		echo "<br>";

		//function doesn't return the authors name if he is a reviewer of this conference
		$temp_value = display_reviewers_for_conflicts();

		echo "<input type=\"hidden\" name=\"csrf\" id=\"csrf\" value=\"" . hash('sha256', "conflicts") . $csrf_password_generator . "\" />";
		if($temp_value != 0)
		{
			echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\"></div></div>";
		}//if

		echo "</fieldset>";
		
		echo "</form>";
	}//if
	else
	{
		//do nothing
	}//else
	?>

	</div><!--conflicts_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--conflicts-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>

</body>
</html>
