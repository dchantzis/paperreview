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

<div class="chairmen_assignments">
	<div id="page_title">chairmen assignments</div>
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
			<form id="sconfform" name="sconfform" method="post" action="./include/functionsinc.php?type=9&action=chairmen_assignments">
				<? conf_combo_box(); ?>
                <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "chairmen_assignments") . $csrf_password_generator?>" />
				<input type="submit" value="GO">
			</form>
			</div><!--cconference_search_form-->
		</div><!--cconferenceInfo-->

	<div id="chairmen_assignments_content">

		<div id="instructions">
			Use the users combo box to insert a user to the chairmen list.
			<br>
			Use the text fields to create and add a new user to the chairmen list.
			<br>
			Note: Click <a href="#chairmen_list" class="simple">here</a> to view the chairmen list.
		</div>

	<?
		if(isset($_SESSION["conf_id"]))
		{
	?>
		<fieldset>
			<legend>
				Assign Chairmen to Conference: 
				<?php 
					if( (isset($_SESSION["conf_name"])) && (strlen($_SESSION["conf_name"]) >= 25)) { echo "<div id=\"LargeConferenceName\">" . strtoupper($_SESSION["conf_name"]) . "</div>"; }
					else {echo strtoupper($_SESSION["conf_name"]); }
				?>
			</legend>
			<div class="messages"><?php VariousMessages($flg); ?></div>
			<div class="notes">Fields marked with <span class="required">*</span> are required.</div>
			
			<div class="dataTypeGroup">
				Users
				<form id="acfrm01" name="acfrm01" method="post" action="./include/functionsinc.php?type=10&user_type=old_user">
				<div class="notes">Choose a user of the system to add to the chairmen list below.</div>
					<div class="field">
						<input type="hidden" id="conference_id" name="conference_id" value="<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"]; } ?>">
						<input type="hidden" id="type" name="type" value="chairman">                        
            			<input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "chairmen_assignments_old_user") . $csrf_password_generator?>" /> 
						<label for="user_id">User: <span class="required" title="this field is required">*</span></label>
						<div class="text"><? unassigned_conf_chairmen_combo_box(); ?><input type="submit" value="ADD"></div>
					</div>
				</form>
			</div>
			<div class="or">OR</div>
			<div class="dataTypeGroup">
				Create new user
				<form id="acfrm02" name="acfrm02" method="post" action="./include/functionsinc.php?type=10&user_type=new_user">
				<div class="notes">Create new users and add them to the chairmen list below.</div>
					<div class="field">
						<label for="fname">First Name: <span class="required" title="this field is required">*</span></label>
						<input type="text" class="text" name="fname" id="fname" value="<? if(isset($_SESSION["fname"])){ echo $_SESSION["fname"]; } ?>" maxlength="35" size="24" title="Enter users' first name" />
						<div class="notes">(maximum of 35 characters)</div>
					</div>
					<div class="field">
						<label for="lname">Last Name: <span class="required" title="this field is required">*</span></label>
						<input type="text" class="text" name="lname" id="lname" value="<? if(isset($_SESSION["lname"])){ echo $_SESSION["lname"]; } ?>" maxlength="35" size="24" title="Enter users' last name" />
						<div class="notes">(maximum of 35 characters)</div>
					</div>
					<div class="field">
						<label for="email">E-mail Address: <span class="required" title="this field is required">*</span></label>
						<input type="text" class="text" name="email" id="email" value="<? if(isset($_SESSION["email"])){ echo $_SESSION["email"]; } ?>" maxlength="35" size="24" title="Enter users' e-mail address" onblur="emailValidation(email);" />
						<div class="notes">(maximum of 35 characters)</div>
					</div>
					<input type="hidden" id="conference_id" name="conference_id" value="<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"]; } ?>" >                                              
					<input type="hidden" id="type" name="type" value="chairman">
            		<input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "chairmen_assignments_new_user") . $csrf_password_generator?>" /> 
					<div class="submit"><input type="submit" value="ADD"></div>
				</form>
			</div>
		</fieldset>
		<br>
		<fieldset>
			<legend legend id="chairmen_list">Conference Chairmen List</legend>
				<? //this session has the conference id 
					show_conference_participants("chairman",$_SESSION["conf_id"],"");
				?>
		</fieldset>
	<? 
		}//if(isset($_SESSION["conf_id"]))
		else
		{
			echo "<div id=\"instructions\">";
			echo "To assign chairmen to a conference, first select the conference from the list above.";
			echo "</div>";
		}//else
	
	?>
	</div><!--chairmen_assignments_content-->
<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>	
</div><!--chairmen_assignments-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>