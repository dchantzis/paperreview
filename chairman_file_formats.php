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
	$csrf_password_generator = hash('sha256', "chairman_file_formats") . $csrf_password_generator;

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

<div class="chairman_file_formats">
	<div id="page_title">File Formats</div>
	<div id="spacer"></div>

	<div id="chairman_file_formats_content">
		<div id="instructions">
			Select which of the following available file formats, will be supported by this conference.
			<?php if($coptions1D["CIA"] == 0){echo "<br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";}  ?>
        </div>
		
		<fieldset>
			<legend>Select Supported File Formats: </legend>
			<div class="messages"><?php VariousMessages($flg); ?></div>
			<div class="notes">Fields marked with <span class="required">*</span> are required.</div>

			<div class="dataTypeGroup">
			File Formats
			<form id="ffsfrm01" name="ffsfrm01" method="post" action="./include/functionsinc.php?type=44">
				<div class="notes">Choose a file format to add to the supported formats list below.</div>
					<div class="field">
						<input type="hidden" id="conference_id" name="conference_id" value="<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"];} ?>">
						<input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
                        <label for="format_id">File Format: <span class="required" title="this field is required">*</span></label>
						<div class="text"><? unselected_fileformats_combo_box(); ?><input type="submit" value="ADD"></div>
					</div>
			</form>
			</div>
		</fieldset>
		<br>		
		<fieldset>
			<legend>Supported File Formats List</legend>
			<?php show_conference_file_formats("chairman");?>
		</fieldset>
	</div><!--chairman_file_formats_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--chairman_file_formats-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>