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
	
	whereUgo(0);
	whereUgo(1);

	######################
	//check if the $_GET table has only the value we want, 
	//and the value is of the type we want
	//returns the value we want trimmed
	if(!isset($_GET["flg"])){ header("Location: ./ulounge.php"); exit;}
	$get_var_type["flg"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$flg = $validated_vars["flg"];
	######################
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

<div class="display_announcements">
	<div id="page_title">Announcements</div>
	<div id="spacer"></div>
	<div id="display_announcements_content">
		<div id="user_types">
			<?php
				if (!isset($_SESSION["administrator"])){
					echo "<div id=\"blah\">for this conference you logged in as:</div>";
					echo "<ul>"; 
						if (isset($_SESSION["chairman"])) { echo "<li> chairman </li>"; }
						if (isset($_SESSION["reviewer"])) { echo "<li> reviewer </li>"; }
						if(isset($_SESSION["author"])) { echo "<li> author </li>"; }			
					echo "</ul>";
				}//
				else 
				{
					echo "<div id=\"blah\">Welcome</div><ul><li>administrator</li></ul>";
				}
			?>
		</div><!--user_types>-->
		<div id="announcements">
			<div id="instructions">
				<?php 
					if($flg == "0") { 
						echo "Displaying the <span class=\"required\">5</span> most recent announcements. ";
						echo " <a class=\"simple\" href=\"./display_announcements.php?flg=1\" title=\"View all announcements.\">VIEW ALL</a>"; 
					}//if
					else if($flg == "1")
					{
						echo "Displaying <span class=\"required\">ALL</span> announcements. Scroll down and read.";
					}//else 
				?>
			</div><!--instructions-->
			<?php display_announcements($flg); ?>
		</div><!--announcements-->	
		</div><!--display_announcements_content-->

</div><!--display_announcements-->	
<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>	
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>