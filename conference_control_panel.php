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
	require("./include/functionsinc.php");
	require("./include/layoutinc.php");
	require("./include/errorreportinc.php"); 
	
	global $csrf_password_generator;
	
	whereUgo(0);
	whereUgo(9);
	
	load_conference_options();
	
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
				
<div class="conference_control_panel">
	<div id="page_title">conferences control panel</div>
	<div id="spacer"></div>
		<? 
			//if the length of a conference name is more than 25 characters, then use 2 lines,
			//else use only one.
			if ( (isset($_SESSION["conf_name"])) && (strlen($_SESSION["conf_name"]) > 25)) { echo "<div id=\"cconferenceInfo2\">"; }
			else {echo "<div id=\"cconferenceInfo\">"; } 
		?>
			<div id="cconferenceTitle">
				Conference: <a href="./conference_info.php?confid=<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"]; }?>" target="_parent" title="Conference Info."><? if(isset($_SESSION["conf_name"])){ echo $_SESSION["conf_name"];} ?></a>
			</div>
			
		<? 
			//if the length of a conference name is more than 25 characters, then use 2 lines,
			//else use only one
			if ( (isset($_SESSION["conf_name"])) && (strlen($_SESSION["conf_name"]) > 25)) { echo "<div id=\"cconference_search_form2\" title=\"Change Conference.\">"; }
			else {echo "<div id=\"cconference_search_form\" title=\"Change Conference.\">"; } 
		?>
			<form id="sconfform" name="sconfform" method="post" action="./include/functionsinc.php?type=9&action=conference_control_panel">
				<? conf_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "conference_control_panel") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--cconference_search_form-->
		</div><!--cconferenceInfo-->

	<div id="conference_control_panel_content">
	<?
		if(isset($_SESSION["conf_id"]))
		{
	?>
	<fieldset>
			<legend>
				Control Panel for Conference: 
				<?php 
					if(strlen($_SESSION["conf_name"]) >= 25) { echo "<div id=\"LargeConferenceName\">" . strtoupper($_SESSION["conf_name"]) . "</div>"; }
					else {echo strtoupper($_SESSION["conf_name"]); }
				?>
			</legend>	
			<div class="dataTypeGroup">
				Options
				<form id="ccp" name="ccp" method="post" action="./include/functionsinc.php?type=16">
					<input type="hidden" id="conference_id" name="conference_id" value="<?=$_SESSION["conf_id"]?>">
           			<input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "update_conference_control_panel") . $csrf_password_generator?>" />
                   		<div class="messages"><?php VariousMessages($flg); ?></div>
						<div class="notes">Fields marked with <span class="required">*</span> are required.</div>
						<div class="field">
							<label for="CIA">1. Conference is active.</label>
							<?php 
								if($_SESSION["CIA"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"CIA\" name=\"CIA\" checked />";
								}//if
								elseif($_SESSION["CIA"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"CIA\" name=\"CIA\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"CIA\" name=\"CIA\" />";
								}//
							?>
						</div>
					
						<div class="field">
							<label for="ASA">2. Let authors submit abstracts.</label>
							<?php 
								if($_SESSION["ASA"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"ASA\" name=\"ASA\" checked />";
								}//if
								elseif($_SESSION["ASA"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"ASA\" name=\"ASA\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"ASA\" name=\"ASA\" />";
								}//
							?>
						</div>

						<div class="field">
							<label for="AUA">3. Let authors update abstracts.</label>
							<?php 
								if($_SESSION["AUA"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"AUA\" name=\"AUA\" checked />";
								}//if
								elseif($_SESSION["AUA"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"AUA\" name=\"AUA\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"AUA\" name=\"AUA\" />";
								}//
							?>
						</div>

						<div class="field">
							<label for="ASM">4. Let authors submit manuscripts.</label>
							<?php 
								if($_SESSION["ASM"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"ASM\" name=\"ASM\" checked />";
								}//if
								elseif($_SESSION["ASM"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"ASM\" name=\"ASM\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"ASM\" name=\"ASM\" />";
								}//
							?>
						</div>
						
						<div class="field">
							<label for="AUM">5. Let authors update manuscripts.</label>
							<?php  
								if($_SESSION["AUM"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"AUM\" name=\"AUM\" checked />";
								}//if
								elseif($_SESSION["AUM"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"AUM\" name=\"AUM\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"AUM\" name=\"AUM\" />";
								}//
							?>
						</div>

						<div class="field">
							<label for="ASCRP">6. Let authors submit camera-ready papers.</label>
							<?php  
								if($_SESSION["ASCRP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"ASCRP\" name=\"ASCRP\" checked />";
								}//if
								elseif($_SESSION["ASCRP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"ASCRP\" name=\"ASCRP\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"ASCRP\" name=\"ASCRP\" />";
								}//
							?>
						</div>

						<div class="field">
							<label for="AUCRP">7. Let authors update camera-ready papers.</label>
							<?php  
								if($_SESSION["AUCRP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"AUCRP\" name=\"AUCRP\" checked />";
								}//if
								elseif($_SESSION["AUCRP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"AUCRP\" name=\"AUCRP\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"AUCRP\" name=\"AUCRP\" />";
								}//
							?>
						</div>
						
 						<div class="field">
							<label for="AVP">8. Let authors view reviews of their papers.</label>
							<?php  
								if($_SESSION["AVP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"AVP\" name=\"AVP\" checked />";
								}//if
								elseif($_SESSION["AVP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"AVP\" name=\"AVP\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"AVP\" name=\"AVP\" />";
								}//
							?>
						</div>

 						<div class="field">
							<label for="ACR">9. Let authors enter conflicts with reviewers.</label>
							<?php  
								if($_SESSION["ACR"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"ACR\" name=\"ACR\" checked />";
								}//if
								elseif($_SESSION["ACR"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"ACR\" name=\"ACR\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"ACR\" name=\"ACR\" />";
								}//
							?>
						</div>

 						<div class="field">
							<label for="NORPC"><span class="required">*</span>10. How many reviewers for each paper in this conference?</label>
							<div class="text"><input type="text" class="text" id="NORPC" name="NORPC" maxlength="3" size="5" value="<?=$_SESSION["NORPC"] ?>" onblur="numberValidation(this);"></div>
						</div>

 						<div class="field">
							<label for="RELIC">11. Let reviewers view papers and enter levels of interest and conflicts.</label>
							<?php  
								if($_SESSION["RELIC"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"RELIC\" name=\"RELIC\" checked />";
								}//if
								elseif($_SESSION["RELIC"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"RELIC\" name=\"RELIC\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"RELIC\" name=\"RELIC\" />";
								}//
							?>
						</div>
												
						<div class="field">
							<label for="RDPR">12. Let reviewers download their assigned papers and review them.</label>
							<?php  
								if($_SESSION["RDPR"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"RDPR\" name=\"RDPR\" checked />";
								}//if
								elseif($_SESSION["RDPR"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"RDPR\" name=\"RDPR\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"RDPR\" name=\"RDPR\" />";
								}//
							?>
						</div>
						
						<div class="field">
							<label for="RVRP">13. Let reviewers view other reviews of their assigned papers.</label>
							<?php  
								if($_SESSION["RVRP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"RVRP\" name=\"RVRP\" checked />";
								}//if
								elseif($_SESSION["RVRP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"RVRP\" name=\"RVRP\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"RVRP\" name=\"RVRP\" />";
								}//
							?>
						</div>
						
						<div class="field">
							<label for="UVP">14. Let users view all conference papers.</label>
							<?php  
								if($_SESSION["UVP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"UVP\" name=\"UVP\" checked />";
								}//if
								elseif($_SESSION["UVP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"UVP\" name=\"UVP\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"UVP\" name=\"UVP\" />";
								}//
							?>
						</div>
						
						<div class="field">
							<label for="UDP">15. Let users download all conference papers (manuscripts and camera-ready versions).</label>
							<?php  
								if($_SESSION["UDP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"UDP\" name=\"UDP\" checked onClick=\"conference_options_restriction('UDP')\"/>";
								}//if
								elseif($_SESSION["UDP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"UDP\" name=\"UDP\" onClick=\"conference_options_restriction('UDP')\"/>";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"UDP\" name=\"UDP\" onClick=\"conference_options_restriction('UDP')\"/>";
								}//
							?>
						</div>
						
						<div class="field">
							<label for="UVAP">16. Let users view ONLY the accepted papers.</label>
							<?php  
								if($_SESSION["UVAP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"UVAP\" name=\"UVAP\" checked />";
								}//if
								elseif($_SESSION["UVAP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"UVAP\" name=\"UVAP\" />";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"UVAP\" name=\"UVAP\" />";
								}//
							?>
						</div>
						
						<div class="field">
							<label for="UDAP">17. Let users download ONLY the accepted papers (only camera-ready versions</label>
							<?php  
								if($_SESSION["UDAP"]=="1")
								{
									echo "<input type=\"checkbox\" id=\"UDAP\" name=\"UDAP\" checked onClick=\"conference_options_restriction('UDAP')\"/>";
								}//if
								elseif($_SESSION["UDAP"]=="0")
								{
									echo "<input type=\"checkbox\" id=\"UDAP\" name=\"UDAP\" onClick=\"conference_options_restriction('UDAP')\"/>";
								}//elseif
								else
								{
									echo "<input type=\"checkbox\" id=\"UDAP\" name=\"UDAP\" onClick=\"conference_options_restriction('UDAP')\"/>";
								}//
							?>
						</div>
						<div class="field">
							<div class="submit"><input type="submit" value="Submit"></div>
						</div>
				</form>
			</div>
		</fieldset>
	<? 
		}//if(isset($_SESSION["conf_id"]))
		else
		{
			echo "<div id=\"instructions\">";
			echo "To view the control panel of a confernece, first select the conference from the list above.";
			echo "</div>";
		}//else
	
	?>
	</div><!--conference_control_panel_content-->
<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>	
</div><!--conference_control_panel-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>
