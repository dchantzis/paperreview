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
	
	######################
	//check if the $_GET table has only the value we want, 
	//and the value is of the type we want
	//returns the value we want trimmed
	if(!isset($_GET["mode"])){ header("Location: ./ulounge.php"); exit;}
	$get_var_type["mode"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$mode = $validated_vars["mode"];
	######################
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if($_SESSION["administrator"] == FALSE){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="scripts/prvalidations.js"></script>
<script type="text/javascript" src="./scripts/navigation.js"></script>
<script type="text/javascript" src="./scripts/content_toggle.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">
<div class="ual">
<?php layout_fragment_start(); ?>

<div class="users_actions_log">
	<div id="page_title">Users Actions Log (Text File)</div>
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
			<form id="sconfform" name="sconfform" method="post" action="./include/functionsinc.php?type=9&action=users_actions_log_file">
				<? conf_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "users_actions_log_file") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--cconference_search_form-->
		</div><!--cconferenceInfo-->

	<div id="users_actions_log_content">
		<div id="instructions">
        	All the actions and database errors that are made in the system are saved in a log.
            <br>
        	Select actions (select queries) are omitted from the log.
            <br><br>
            To view the log regarding a conference, select one from the list above.
            <br>
            To view the log regarding actions/errors outside of a conference, click <a href="./users_actions_log_file.php?mode=0" class="simple">here</a>
		</div>

	<?php
		display_file_users_actions_log($mode);
	?>
	</div><!--chairmen_assignments_content-->
<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>	
</div><div class="display_papers">

<?php layout_fragment_end(); ?>
</div>
<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>