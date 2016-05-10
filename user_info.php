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
	require("./include/finduserinc.php");
	require("./include/errorreportinc.php");

	global $csrf_password_generator;

	whereUgo(0);
	whereUgo(10);
	whereUgo(1);

	$flg = "";
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
	@import url(./scripts/allstyles.inc.css);*/
</style>
<link rel="stylesheet" rev="stylesheet" href="./scripts/print.inc.css" type="text/css" media="print" />
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="user_info">
	<div id="page_title">Users </div>
	<div id="spacer"></div>
	<div id="user_info_content">

				<div id="userSearchInfo">
					<div id="userSearchTitle">search for user: </div>			
					<div id="userSearchForm" title="Change Conference.">
						<form id="fusrform" name="fusrform" method="post" action="./include/functionsinc.php?type=23">
							<label for="search_lname">By Last Name: </label>
							<input type="text" class="text" name="search_lname" id="search_lname" value="<? if(isset($_SESSION["search_lname"])){ stripslashes(trim($_SESSION["search_lname"])); }?>" maxlength="35" size="24" title="Enter last name to search" />
							<input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
                        <input type="submit" value="GO">
					</form>
					</div><!--userSearchForm-->
					<div id="advancedSearch">
						<!--<a href="#" target="secondaryfr">advanced search</a>-->
					</div>
				</div><!--userSearchInfo-->
	<div id="mainColumn">
		<div class="dataTypeGroup">
			<fieldset>
				<legend>User: <?=strtoupper($uvalues["user_info_fname"]) . " " . strtoupper($uvalues["user_info_lname"])?></legend>
					<div class="field"></div>
                    <div class="field">
						<label for="">E-mail: </label>
						<div class="text"><div class="red"><a href="mailto:<?=$uvalues["user_info_email"]?>" class="simple"><?=$uvalues["user_info_email"]?></a></div></div>
					</div>
					<div class="field">
						<label for="">Address 01: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_address_01"]?></div></div>
					</div>
					<div class="field">
						<label for="">Address 02: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_address_02"]?></div></div>
					</div>
					<div class="field">
						<label for="">Address 03: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_address_03"]?></div></div>
					</div>
					<div class="field">
						<label for="">City: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_city"]?></div></div>
					</div>
					<div class="field">
						<label for="">Country: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_country"]?></div></div>
					</div>
					<div class="field">
						<label for="">Phone Number 01: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_phone_01"]?></div></div>
					</div>
					<div class="field">
						<label for="">Phone Number 02: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_phone_02"]?></div></div>
					</div>
					<div class="field">
						<label for="">Fax Number: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_fax"]?></div></div>
					</div>
					<div class="field">
						<label for="">Website Address: </label>
						<div class="text"><div class="red"><?=$uvalues["user_info_website"]?></div></div>
					</div>
			</fieldset>
			</div>

<?php
		if(isset($_SESSION["administrator"]))
		{
?>
			<br>
			<fieldset>
				<legend>Chairman to the following conferences</legend>
					<? find_user_conference_participation($uvalues["user_info_id"],"chairman"); ?>
			</fieldset>
			<br>
			<fieldset>
				<legend>Part of the review committee for the following conferences</legend>
					<? find_user_conference_participation($uvalues["user_info_id"],"reviewer"); ?>
			</fieldset>
            <br>
           	<fieldset>
				<legend>Author of the following papers</legend>
					<? find_user_conference_participation($uvalues["user_info_id"],"author"); ?>
			</fieldset>
<?php
	}
?>
			<!--<div class="print_link"><a href="user_info_pv.php?userid=<? //echo $uvalues["user_info_id"]; ?>" class="simple">view printable version</a></div>-->
            <center><div class="print_button" onClick="javascript:if (window.print) window.print();" title="print page"></div></center>
		</div><!--mainColumn-->
				
			<div id="separator"></div>
			<div id="extraColumn">extraColumn</div>

	</div><!--user_info_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--user_info-->
		
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>

</body>
</html>