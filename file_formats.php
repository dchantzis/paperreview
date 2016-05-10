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
	whereUgo(9);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if($_SESSION["administrator"] == FALSE){echo " - " . strtoupper($_SESSION["conf_name"]);}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="scripts/prvalidations.js"></script>
<script language="JavaScript" type="text/JavaScript">
 	function load_hidden_field (selected_id, selected_name){
		selIndex = document.ffffrm.fileformatslist.selectedIndex;
		temp_value = document.ffffrm.fileformatslist.options[selIndex].value;
		document.getElementById("update_ff").value = "Edit: "+selected_name.toUpperCase();
	}
</script>
<script type="text/javascript" src="./scripts/navigation.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="file_formats">
	<div id="page_title">File Formats</div>
	<div id="spacer"></div>
	<div id="file_formats_content">
		<div id="instructions">
			Insert a new file format using the form on the right. <br>
			OR <br>
			Update a file format by selecting it from the list on the left.
		</div>
				<div class="messages"><?php VariousMessages($flg); ?></div>
		<div id="searchform">
			File formats extensions list: <br><br>			
			
			<div id="fileformatextensions">
				<form id="ffffrm" name="ffffrm" method="post" action="./include/functionsinc.php?type=27">
					<?php display_ff_extensions(); ?>
                     <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "find_file_format") . $csrf_password_generator?>" />
					<input id="update_ff" type="submit" value="Edit" style="width: 80px;" title="search for file format" />
					</form>
					
					<form id="efffrm" name="efffrm" method="post" action="./include/functionsinc.php?type=28">
							<input id="insert_new_ff" type="submit" value="Insert new" style="width: 80px;" title="insert new file format" />
					</form>
			</div>

		</div><!--searchform-->	

		<div id="insertform">
		<?php
			if($_SESSION["updatefileformat"] == "no"){
				echo "<form id=\"ffifrm\" name=\"ffifrm\" method=\"post\" action=\"./include/functionsinc.php?type=25\">";
				echo "Insert new file format";
			}
			elseif ($_SESSION["updatefileformat"] == "yes"){
				echo "<form id=\"ffufrm\" name=\"ffufrm\" method=\"post\" action=\"./include/functionsinc.php?type=26\">";
				echo "Update file format";
			}//else
		?>	
		<br><br>
			<fieldset>
				<legend>File format info: </legend>
				<div class="field"><div class="notes">Fields marked with <span class="required">*</span> are required.</div></div>
					<div class="field">
						<label for="extension">File extension: <span class="required" title="this field is required">*</span></label>
						<input type="text" class="text" name="extension" id="extension" maxlength="10" size="18" value="<? if(isset($_SESSION["extension"])){ echo $_SESSION["extension"];} ?>" title="enter file format extension">
						<div class="notes">(maximum of 10 characters)</div>
					</div>
					<div class="field">
						<label for="description">Description: </label>
						<input type="text" class="text" name="description" id="description" maxlength="100" size="18" title="enter file format description" value="<? if(isset($_SESSION["description"])){ echo $_SESSION["description"];} ?>">
						<div class="notes">(maximum of 100 characters)</div>
					</div>
					<div class="field">
						<label for="mime_type">Mime type: <span class="required" title="this field is required">*</span></label>
						<input type="text" class="text" name="mime_type" id="mime_type" maxlength="80" size="18" title="enter mime type" value="<? if(isset($_SESSION["mime_type"])){ echo stripslashes($_SESSION["mime_type"]);} ?>">
						<div class="notes">(maximum of 80 characters)</div>
					</div>
           			<input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "file_formats") . $csrf_password_generator?>" />
				<?php
					if($_SESSION["updatefileformat"] == "no" ){
						//do nothing
						echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit file type\" value=\"Create it\"></div></div>";
					}
					elseif ($_SESSION["updatefileformat"] == "yes"){
						echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Update file type\" value=\"Update it\"></div></div>";
					}//else
				?>
			</fieldset>
			</form>
		</div><!--insertform-->					
	</div><!--file_formats_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--file_formats-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>

</body>
</html>