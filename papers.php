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

	//all the possible papers acceptance status's which are set in the sessioninitinc.php file
	global $papers_status_array; 
	global $csrf_password_generator;

	whereUgo(0);
	whereUgo(8);
	whereUgo(5);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
	$form_tag = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<script type="text/javascript" src="./scripts/authors_insert_paper.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge" onload="loadAuthors();">

<?php layout_fragment_start(); ?>

<div class="papers">
	<div id="page_title">Papers</div>
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
			<form id="spaperform" name="spaperform" method="post" action= "./include/functionsinc.php?type=30">
				<? paper_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "papers") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--ppaper_search_form-->
		</div><!--ppaperInfo-->

	<div id="papers_content">

		<div id="instructions">
            Create a paper using the form below.
            <br>
            To update an existing paper, simply select it from the list above.
			<?php 
				if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed." . "</span>";}  
				else
				{
					if($coptions1D["ASA"] == 0){echo "<br><br><span class=\"red\">" . "Authors are not allowed to submit their abstracts." . "</span>";}
					if($coptions1D["AUA"] == 0){echo "<br><br><span class=\"red\">" . "Authors are not allowed to update their abstracts." . "</span>";}
				}
			?>
		</div>
	
		<div id="insertform">
		<?php
		if(isset($_SESSION["updatepaper"]))
		{
			if($_SESSION["updatepaper"] == "no"){
				$form_tag =  "<form id=\"pifrm\" name=\"pifrm\" method=\"post\" action=\"./include/functionsinc.php?type=29\">"; //onsubmit="return checkPasswd(this)">
				echo "Create new paper";
			}
			elseif ($_SESSION["updatepaper"] == "yes"){
				$form_tag =  "<form id=\"pufrm\" name=\"pufrm\" method=\"post\" action=\"./include/functionsinc.php?type=32\">";
				echo "Update paper";
				
				echo "<a href=\"./include/functionsinc.php?type=31\" title=\"Create new paper.\" class=\"inc\">Create New Paper</a>";
			}//else
		}//
		?>
		<br><br>
		<fieldset>
			<legend>Paper Info</legend>
			<div class="messages"><?php VariousMessages($flg); ?></div>
			<div class="field"><div class="notes">Fields marked with <span class="required">*</span> are required.</div></div>
			
			<div class="field">
				<label for="">Authors: <span class="required" title="this field is required">*</span></label>
					<form name="lastfrm" id="lastfrm">
						<div id="author_name_fields">
							<div class="field">
								<label for="authorfname">First Name: </label>
								<input type="text" class="text" id="authorfname" name="authorfname" maxlength="150" size="18" title="enter author first name">
							</div>
							<div class="field">
								<label for="authorlname">Last Name: </label>
								<input type="text" class="text" id="authorlname" name="authorlname" maxlength="150" size="18" title="enter author last name">
							</div>
						</div>
							<ul class="buttons">			
								<li><input type="button" id="add" name="add" value="Add" onclick="addAuthor();"></li>
								<li><input type="button" name="delete" id="delete" value="Remove" onClick="deleteAuthor();"></li>
								<li><input type="button" name="clear" id="clear" value="Clear" onClick="clearAuthors();"></li>
							</ul>
							<select name="authorslist" id="authorslist" size="6" style="width:200px" multiple>
								<option value=""></option>							
							</select>
							<ul class="morebuttons">
									<li><input type="button" id="moveup" name="moveup" value="Up" onclick="moveAuthor('up');"></li>
									<li><input type="button" id="movedown" name="movedown" value="Down" onclick="moveAuthor('down');"></li>								
							</ul>
							<input type="hidden" id="update_authors" name="update_authors" value="<? if(isset($_SESSION["update_authors"])){ echo $_SESSION["update_authors"]; }?>">
							<input type="hidden" id="logged_in_author" name="logged_in_author" value="<? echo $_SESSION["logged_user_fname"] . " " . $_SESSION["logged_user_lname"]?>">
					</form>			
			</div>

			<?=$form_tag?>
			<input type="hidden" name="user_id" id="user_id" value="<? if(isset($_SESSION["logged_user_id"])){ echo $_SESSION["logged_user_id"];} ?>">
			<input type="hidden" name="conference_id" id="conference_id" value="<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"];} ?>">	
			<div class="field">
				<label for="title">Paper Title: <span class="required" title="this field is required">*</span></label>
				<input type="text" class="text" name="title" id="title" maxlength="250" size="350" title="enter paper title" value="<? if(isset($_SESSION["title"])){ echo $_SESSION["title"]; }?>">
				<div class="notes">(maximum of 250 characters)</div>
			</div>
			<input type="hidden" name="authors" id="authors" value="<? if(isset($_SESSION["authors"])){ echo $_SESSION["authors"];} ?>">
			<!--
			<div class="field">
				<label for="subject">Subject: <span class="required" title="this field is required">*</span></label>
				<input type="text" class="text" name="subject" id="subject" maxlength="100" size="18" title="enter paper subject" value="<? //echo $_SESSION["subject"];?>">
				<div class="notes">(maximum of 100 characters)</div>
			</div>
			-->
			<input type="hidden" name="subject" id="subject" value="">
            <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "papers") . $csrf_password_generator?>" />
            
			<div class="field">
				<label for="abstract">Abstract: <span class="required" title="this field is required">*</span></label>
				<textarea class="bigger_text" cols="50" rows="30" name="abstract" id="abstract" wrap="hard" title="enter paper abstract"><? if(isset($_SESSION["abstract"])){ echo stripslashes($_SESSION["abstract"]);} ?></textarea>			
				<div class="notes">(maximum of 5000 characters)</div>
			</div>
			<input type="hidden" name="submition_date" id="submition_date" value="<?php echo date("Y-m-d") . " " . date("H:i:s") ?>">
			<?php
			if(isset($_SESSION["updatepaper"]))
			{
				if($_SESSION["updatepaper"] == "no" ){
					echo "<input type=\"hidden\" name=\"status_code\" id=\"status_code\" value=\"" . $papers_status_array["rejected"] . "\">";
					echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Create it\"></div></div>";
				}
				elseif ($_SESSION["updatepaper"] == "yes"){
					echo "<input type=\"hidden\" name=\"status_code\" id=\"status_code\" value=\"" . $_SESSION["status_code"] . "\">";
					echo "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Update it\"></div></div>";
				}//else
			}//if
			?>
			</form>
		</fieldset>
		
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
