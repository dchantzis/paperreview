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
	
	whereUgo(0);
	whereUgo(1);

	##########################
	//In this page we need all the different conference deadlines, so 
	//we are gonna use the code of 'findconferenceinc.php' to load all the confernece variables.
	//From those we will get the dates we need. We do this to avoid duplication of the same code.
	//In order to use 'findconferenceinc.php' we have to set the '$_GET["confid"]' variable with the current conference id
	//We use this to avoid the laoding of these variables to sessions. We are loading them in global variables
	//Now how cool is that!!!?
	//function 'display_deadlines()' is going to use these variables
	########################
	if(!isset($_SESSION["administrator"]))
	{
		$_GET["confid"] = $_SESSION["conf_id"]; 
		require("./include/findconferenceinc.php");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<noscript> <meta http-equiv="refresh" content="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>" /></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);*/
</style>
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="ulounge_welcome">
	<div id="page_title">home</div>
	<div id="spacer"></div>
	<div id="ulounge_welcome_content">
		<div id="user_types">
			<?php				
				if (!isset($_SESSION["administrator"])){
					echo "<div id=\"blah\">for this conference you logged in as:</div>";
					echo "<ul>"; 
						if (isset($_SESSION["chairman"])) { echo "<li> chairman </li>"; }
						if (isset($_SESSION["reviewer"])) { echo "<li> reviewer </li>"; }
						if (isset($_SESSION["author"])) { echo "<li> author </li>"; }			
					echo "</ul>";
				}//
				else 
				{
					echo "<div id=\"blah\">Welcome</div><ul><li>administrator</li></ul>";
				}
			?>
		</div><!--user_types-->
        
		<div id="announcements">
			<?php 
				if(!isset($_SESSION["administrator"]))
				{	
					if($coptions1D["CIA"] == 1) {echo "<div id=\"cs\">" . "Conference is active" . "</div>";} 
					elseif($coptions1D["CIA"] == 0) {echo "<div id=\"cs\">" . "Conference is inactive" . "</div>";} 
				}
			?>        
			
			<?php display_deadlines(); ?>
            <br />
            <?php if(!isset($_SESSION["administrator"]))
				{
					echo "For more information about this conference, click on the title at the top-right of each page.";
				}//
			?>
			<?php
				if(isset($_SESSION["administrator"]))
				{
					//user is the administrator
					//load all the conferences to separate tables. 
					//One for active conferences, another for expired conferences
					$active_c_ar = load_all_conferences("active");
					$expired_c_ar = load_all_conferences("inactive");
				}//else
			?>
			
			<?php
			//call function load_conference_options_to_table
			//and store that table
						
			//$conference_options = load_conference_options_to_table();		
			if(isset($_SESSION["administrator"]))
			{
				display_sorted_conferences($active_c_ar,$expired_c_ar,1);
			}//user is the administrator
			else
			{
				if(isset($_SESSION["chairman"])){ 
					echo "\n\t\t<div class=\"options\">"; 
					echo "<div class=\"user_type\">Chairmen: </div>";
					display_conference_options_for_each_user($coptions2D,"chairman"); 
					echo "\n\t\t</div>"; 
				}//user is chairman
				if(isset($_SESSION["reviewer"])){ 
					echo "\n\t\t<div class=\"options\">"; 
					echo "<div class=\"user_type\">Reviewers: </div>";
					display_conference_options_for_each_user($coptions2D,"reviewer"); 
					echo "\n\t\t</div>"; 
				}//user is reviewer
				if(isset($_SESSION["author"])){
					echo "\n\t\t<div class=\"options\">"; 
					echo "\n\t\t\t<div class=\"user_type\">Authors: </div>";
					display_conference_options_for_each_user($coptions2D,"author"); 
					echo "\n\t\t</div>"; 
				}//user is author
			}//user is not the administrator
		?>
		</div><!--announcements-->
        
	</div><!--ulounge_welcome_content-->
</div><!--ulounge_welcome-->

	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>

<?php
/*
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	echo "<br><br><br><br><br>";
	reset($_SESSION);
	while (list($key, $val) = each ($_SESSION))
	{
		echo $key . " = " . $_SESSION[$key] . "<br>";
	}//
*/
?>
</body>
</html>
