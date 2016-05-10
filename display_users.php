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
	//regenerate session id if PHP version is lower than 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}

	require("./include/layoutfragmentsinc.php");
	require("./include/layoutinc.php"); 
	require("./include/functionsinc.php");
	require("./include/errorreportinc.php");

	global $csrf_password_generator;

	whereUgo(0);
	whereUgo(10);
	whereUgo(1);
	
	$flg = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
		
	$search_lname = "";
	if (isset($_GET["search_lname"]))
	{
		$search_lname = $_GET["search_lname"];
	}//	
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

<div class="display_users">
	<div id="page_title">Users </div>
	<div id="spacer"></div>
		<div id="display_users_content">
			<div id="userSearchInfo">
				<div id="userSearchTitle">search for user: </div>			
				<div id="userSearchForm" title="Change Conference.">
					<form id="fusrform" name="fusrform" method="post" action="./include/functionsinc.php?type=23">
						<label for="search_lname">By Last Name: </label>
						<input type="text" class="text" name="search_lname" id="search_lname" value="<? if(isset($_SESSION["search_lname"])){  echo stripslashes(trim($_SESSION["search_lname"]));} ?>" maxlength="35" size="24" title="Enter last name to search" />
						<input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
                        <input type="submit" value="GO">
					</form>
				</div><!--userSearchForm-->
			</div><!--userSearchInfo-->

			<div id="mainColumn">
				<?php	echo "<iframe src=\"./users_list.php?search_lname=" . $search_lname . "\" id=\"secondaryfr\" name=\"secondaryfr\" frameborder=\"0\"></iframe>"; ?>
			</div><!--mainColumn-->
		<div id="separator"></div>
		<div id="extraColumn">extraColumn</div>
	</div><!--display_users_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>	
</div><!--display_users-->
		
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>

</body>
</html>
