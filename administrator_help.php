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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);*/
</style>
</head>

<body class="ulounge">
<?php layout_fragment_start(); ?>
    <div class="help">
    <div id="page_title">Conference Actions Timetable</div>
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
			<form id="sconfform" name="sconfform" method="post" action="./include/functionsinc.php?type=9&action=administrator_help">
				<? conf_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "administrator_help") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--cconference_search_form-->
		</div><!--cconferenceInfo-->
        
    	<div id="help_content">
       	<?
			if(isset($_SESSION["conf_id"]))
			{
		?>
        	<div id="instructions">
				This page presents the Actions Timetable of the conference.
                <br />
                It contains all the actions that each user should execute during the lifetime of the conference. 
                <br />
                The conference actions that are written in <span class="strikethrough">strikethrough</span>, are currently inactive.
			</div>
        
			<?php 
                $conf_options_timetable = organize_conf_actions_timetable($coptions2D); 
                display_conf_timetable($conf_options_timetable); 
            ?>
       	<? 
			}//if(isset($_SESSION["conf_id"]))
			else
			{
				echo "\n<div id=\"instructions\">";
				echo "To view the help page of a conference, first select the conference from the list above.";
				echo "</div>";
			}//else
		?>
        </div><!-- help_content -->
        <div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>   
    </div><!-- help -->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>
