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
	require("./include/findpaperinc.php");
	require("./include/findpaperinterestinc.php");
	require("./include/errorreportinc.php"); 

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "paper_interest_level") . $csrf_password_generator;

	whereUgo(0);
	whereUgo(8);
	whereUgo(4);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="./scripts/prvalidations.js"></script>
<script language="JavaScript" type="text/javascript" src="./scripts/navigation.js"></script>
<!--for the slider-->
<script language="JavaScript" type="text/javascript" src="./scripts/slider/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="./scripts/slider/scriptaculous.js?load=slider"></script>
<script language="JavaScript" type="text/javascript" src="./scripts/slider/interest_slider.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">
<?php layout_fragment_start(); ?>
<div class="paper_interest_level">
	<div id="page_title">papers interest levels and conflicts</div>
	<div id="spacer"></div>
	<div id="paper_interest_level_content">
		<div id="instructions">
			Use the slider to select your interest.
			<br>
			Note: Default value for interest is 1.
			<?php 
				if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";}  
				else
				{
					if($coptions1D["RELIC"] == 0){echo "<br><br><span class=\"red\">" . "Reviewers are not allowed to enter their levels of interest and conflicts." . "</span>";} 
				}
			?>		
		</div>

				<?php
					if($_SESSION["updatepaper_interestlevel"] == "no"){
						echo "<form id=\"ilifrm\" name=\"ilifrm\" method=\"post\" action=\"./include/functionsinc.php?type=33\">";
						$legend = "Insert Interest Level";
						$submit_button = "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\"></div></div>";
					}//if
					elseif ($_SESSION["updatepaper_interestlevel"] == "yes"){
						echo "<form id=\"ilufrm\" name=\"ilufrm\" method=\"post\" action=\"./include/functionsinc.php?type=35\">";
						$legend = "Update Interest Level";
						$submit_button = "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Update\"></div></div>";;					
					}//else
				?>
				<fieldset>
					<legend><?=$legend?></legend>
					<div class="messages"><?php VariousMessages($flg); ?></div>
					<div class="field"><div class="notes">Fields marked with <span class="required">*</span> are required.</div></div>
					<input type="hidden" name="user_id" id="user_id" value="<? if(isset($_SESSION["logged_user_id"])){ echo $_SESSION["logged_user_id"];} ?>">
					<input type="hidden" name="paper_id" id="paper_id" value="<?=$pvalues["find_paper_id"]?>">
					<input type="hidden" name="conference_id" id="conference_id" value="<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"];} ?>">
					<input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
                    <div class="field">
						<label for="Interest">Interest: <span class="required" title="this field is required">*</span></label>
						
						<div id="track" style="display:none;">
							<div id="handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="value">1</div>
							</div>
						</div><span id="callback"></span>

						<select name="level_of_interest" id="level_of_interest" title="Select interest level" style="width:135px">
						<?php 
							if(isset($interest_values["level_of_interest"]))
							{
								if($interest_values["level_of_interest"] == "" ){ echo "<option value=\"\">[Select interest level]</option>"; }
								else 
								{ 
									echo "<option value=\"" . $interest_values["level_of_interest"] . "\">" . $interest_values["level_of_interest"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else { echo "<option value=\"\">[Select interest level]</option>"; }
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						
						<div class="notes">(on a scale of 1 to 7, how much do you want to review this paper?)</div>

					</div>
					<div class="field">
						<label for="conflict">Conflict: <span class="required" title="this field is required">*</span></label>
						No <input type="radio" class="text" name="conflict" id="conflict" value="0" <?php if( isset($interest_values["conflict"]) && $interest_values["conflict"]==0 ){ echo "checked='checked'";}?> >
						Yes <input type="radio" class="text" name="conflict" id="conflict" value="1" <?php if( isset($interest_values["conflict"]) && $interest_values["conflict"]==1 ){ echo "checked='checked'";}?> >
						<div class="notes">(conflict with any of the authors?)</div>
					</div>
					<?=$submit_button?>
				</fieldset>
					</form>
	</div><!--paper_interest_level_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--paper_interest_level-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>
