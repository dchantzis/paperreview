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
	//require("./include/errorreportinc.php"); //DO NOT USE IN THIS PAGE. TOO MANY E_NOTICE 8 NON-FATAL RUNTIME NOTICES

	global $csrf_password_generator;

	whereUgo(0);
	whereUgo(8);
	whereUgo(3);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
	
	######################
	if(!isset($_GET["order"])){ $order_by=0; }
	elseif(isset($_GET["order"])){ $order_by=$_GET["order"]; }
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
	@import url(./scripts/allstyles.inc.css);
</style>
<link rel="stylesheet" rev="stylesheet" href="./scripts/print.inc.css" type="text/css" media="print" />
</head>

<body class="ulounge">

<div class="ap">

<?php layout_fragment_start(); ?>

<div class="accept_papers">
	<div id="page_title">Accept Papers for Conference</div>
	<div id="spacer"></div>
	<div id="accept_papers_content">
	
		<div id="instructions">
			Use this form to accept papers for this conference.
			<br />
            Click on a Paper ID to read reviews for the corresponding paper.
            <br /><br />
            <b>Or</b>=Originality, <b>S</b>=Significance, <b>Q</b>=Technical Quality, <b>R</b>=Relevance
			<b>P</b>=Presentation, <b>Ov</b>=Overall Rating, <b>E</b>=Referee Expertise 
            <br /><br />
            <span class="boxes">
                Accept (6-7): <span class="air_born" title="color for accepted papers">&nbsp;</span>
                Weak Accept (5): <span class="merky_blue" title="color for weakly accepted papers">&nbsp;</span>
                Neutral (4): <span class="neutral_blue" title="color for neutral papers">&nbsp;</span>
                Weak Reject (3): <span class="im_so_sorry" title="color for weakly rejected papers">&nbsp;</span>
                Reject (1-2): <span class="berry_pink" title="color for rejected papers">&nbsp;</span>
            </span>
			<?php if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";}  ?>
        </div>
        
		<div class="messages"><?php VariousMessages($flg)?></div>	
        
        <form id="apufm" name="apufm" method="post" action="./include/functionsinc.php?type=59">		
		<fieldset>
		<legend>Accept papers for conference: <span class="red"> <?=$_SESSION["conf_name"]?></span></legend>

        
        <ul class="order_by">
        	<li><a href="./accept_papers.php?order=1" class="simple">Order by Paper ID</a></li>
            <li><a href="./accept_papers.php?order=0" class="simple">Order by Average</a></li>
            <li><a href="./accept_papers.php?order=2" class="simple">Order by Discrepancy</a></li>
        </ul>
        
		<?php
			display_papers_and_reviews($order_by);  
		?>
        <input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "accept_papers") . $csrf_password_generator?>" />
		<div class="field"><div class="submit"><input type="submit" title="Submit form" value="Submit"></div></div>
		</fieldset>
		</form>
        
	</div><!--accept_papers_content-->
    <div class="print_button" onClick="javascript:if (window.print) window.print();" title="print page"></div>
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--accept_papers-->
	
<?php layout_fragment_end(); ?>

</div><!-- <div class="ap"> -->

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>