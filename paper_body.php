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

<div class="paper_body">

	<div id="page_title">Paper Body</div>
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
			<form id="spaperform" name="spaperform" method="post" action= "./include/functionsinc.php?type=39">
				<? paper_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "paper_body") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--ppaper_search_form-->
		</div><!--ppaperInfo-->

	<div id="paper_body_content">

		<div id="instructions">
			Select a paper from the list above to attach a paper body to it.
			<br>
			Do the same to update the body of a paper.
			<?php 
				if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed." . "</span>";} 
				else
				{
					if($coptions1D["ASM"] == 0){echo "<br><br><span class=\"red\">" . "Authors are not allowed to submit their manuscripts." . "</span>";} 
					if($coptions1D["AUM"] == 0){echo "<br><br><span class=\"red\">" . "Authors are not allowed to update their manuscripts." . "</span>";}
					if($coptions1D["ASCRP"] == 0){echo "<br><br><span class=\"red\">" . "Authors are not allowed to submit their camera-ready papers." . "</span>";}
					if($coptions1D["AUCRP"] == 0){echo "<br><br><span class=\"red\">" . "Authors are not allowed to update their camera-ready papers." . "</span>";}
				}
			?>
		</div>

		<div id="insertform">
		<form id="pifrm" name="pifrm" method="post" action="./include/functionsinc.php?type=40" enctype="multipart/form-data">
		<br>
		<fieldset>
			<legend>Paper Body</legend>
			<div class="messages"><?php VariousMessages($flg); ?></div>
			<div class="field"><div class="notes">Fields marked with <span class="required">*</span> are required.</div></div>
			<input type="hidden" name="paper_id" id="paper_id" value="<? if(isset($_SESSION["paper_id"])){ echo $_SESSION["paper_id"];} ?>">
            <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "paper_body") . $csrf_password_generator?>" />
			<div class="field">
				<label for="title">Paper Title: </label>
				<div class="text"><? if(isset($_SESSION["title"])){ echo $_SESSION["title"];} ?></div>
			</div>
			<div class="field">
				<label for="paper_body">Paper Abstract: <span class="required" title="this field is required">*</span></label>
				<input type="file" class="text" name="paper_body" id="paper_body" value="<? if(isset($_SESSION["paper_body"])){ echo $_SESSION["paper_body"];} ?>" maxlength="" size="24" title="Browse file." />

				<?php 
					if(isset($_SESSION["paper_id"]))
					{ 
						$paper_type = show_uploaded_paper_body($_SESSION["paper_id"],0);
					} 
				 	else
					 {
						$paper_type["manuscript"] = "disabled";
						$paper_type["camera_ready"] = "disabled";
					}					
				?>	
				<br>
				<?php show_conference_file_formats("author");?>
			</div>
			<div class="field">
				<label for="paper_type">Paper Type: <span class="required" title="this field is required">*</span></label>
				Manuscript <input type="radio" class="text" name="paper_type" id="paper_type" value="manuscript" <?=$paper_type["manuscript"]?> /><!-- this session is filled from show_uploaded_paper_body-->
				Camera Ready <input type="radio" class="text" name="paper_type" id="paper_type" value="camera_ready" <?=$paper_type["camera_ready"]?> /><!-- this session is filled from show_uploaded_paper_body-->
				<div class="notes">
				<?php
					if($paper_type["manuscript"] == "disabled")
					{
						echo "You are not allowed to upload the manuscript of this paper.";
						echo "<br>";
					}
					if ($paper_type["camera_ready"] == "disabled")
					{
						echo "You are not allowed to upload the camera-ready version of this paper.";
					}
				?>
				</div>
				<div class="notes">
					(<div class="red">CAUTION</div>: If you have already uploaded a paper of the same type, then the previous one would be deleted.)
				</div>
			</div>
			<input type="hidden" name="date_of_submition" id="date_of_submition" value="<?php echo date("Y-m-d") . " " . date("H:i:s") ?>">
			<input type="hidden" name="upload_type" id="upload_type" value="<?=$paper_upload_type?>"><!--global variable-->
			<?php
				if($paper_type["manuscript"] == "disabled" && $paper_type["camera_ready"] == "disabled")
				{
					echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\" disabled></div></div>";
				}//if
				else
				{
					echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\"></div></div>";
				}//else
			?>
		</fieldset>
		</form>
		</div><!--insertform-->

	</div><!--papers_content-->
<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--papers-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>
