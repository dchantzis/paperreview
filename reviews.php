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
	require("./include/errorreportinc.php"); 

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "reviews") . $csrf_password_generator;

	whereUgo(0);
	whereUgo(8);
	whereUgo(4);

	$flg = "";
	$error = "";
	if (isset($_SESSION["flg"])) {$flg = $_SESSION["flg"];}

	if(isset($_SESSION["updatereview"])) 
	{ 
		if($_SESSION["updatereview"] == "no") 
		{
			if(!isset($_SESSION["rev_varcheck"])) {	empty_review_sessions(); $_SESSION["updatereview"] = "no"; }
		}
	}

	if(isset($_GET["paperid"]))
	{
		$get_var_type["paperid"] = "([^0-9]+)";
		$validated_vars = checkGetVariable(1,0,$get_var_type);
		$paper_id = $validated_vars["paperid"];
	}//if
	else if (isset($_SESSION["paper_id"]))//this is for when this page is reloaded with the error flag $_GET["flg"]
	{
		$paper_id = $_SESSION["paper_id"];
	}//else if
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="./scripts/prvalidations.js"></script>
<script type="text/javascript" src="./scripts/navigation.js"></script>
<script type="text/javascript" src="./scripts/content_toggle.js"></script>
<!--for the sliders-->
<script language="JavaScript" type="text/javascript" src="./scripts/slider/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="./scripts/slider/scriptaculous.js?load=slider"></script>
<script language="JavaScript" type="text/javascript" src="./scripts/slider/review_slider.js"></script>

<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="reviews">
	<div id="page_title">REVIEW PAPERS</div>
	<div id="spacer"></div>

	<div id="reviews_content">
		<div id="instructions">
			Use the form below to review a paper.
			<br>
			Default value of every field with a slider is 1.
            <br><br>
            <a href="#rguide" class="simple">View Review Guide</a>
			<?php 
				if($coptions1D["CIA"] == 0){echo "<br><br><span class=\"red\">" . "Conference is inactive. This action is not allowed" . "</span>";}  
				elseif($coptions1D["RDPR"] == 0){echo "<br><br><span class=\"red\">" . "Reviewers are not allowed to download their assigned papers and review them." . "</span>";} 
				else
				{
					echo "<br><br>";
					$paper_type = show_uploaded_paper_body($pvalues["find_paper_id"],1);
					echo "<br>";
					echo "<a href=\"./paper_reviews_info.php?paperid=" . $pvalues["find_paper_id"] . "\" class=\"simple\">Read others reviews for this paper</a>";
				}
			?> 
            </div>
				
				<?php
					if($_SESSION["updatereview"] == "no"){
						echo "<form id=\"rifrm\" name=\"rifrm\" method=\"post\" action=\"./include/functionsinc.php?type=50\">";
						$legend = "Review for Paper: " . "<a href=\"./paper_info.php?paperid=" . $pvalues["find_paper_id"] . "\" class=\"simple\">" . $pvalues["find_paper_title"] . "</a>";
						$submit_button = "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Submit\"></div></div>";
					}//if
					elseif ($_SESSION["updatereview"] == "yes"){
						echo "<form id=\"rufrm\" name=\"rufrm\" method=\"post\" action=\"./include/functionsinc.php?type=50\">";
						$legend = "Update Review for Paper: " . "<a href=\"./paper_info.php?paperid=" . $paper_id . "\" class=\"simple\">" . $pvalues["find_paper_title"] . "</a>";
						$submit_button = "<div class=\"field\"><div class=\"submit\"><input type=\"submit\" title=\"Submit form\" value=\"Update\"></div></div>";;					
					}//else
				?>
				<fieldset>
					<legend><?=$legend?></legend>
					<div class="messages"><?php VariousMessages($flg); ?></div>
					<div class="field"><div class="notes">Fields marked with <span class="required">*</span> are required.</div></div>
					<input type="hidden" name="user_id" id="user_id" value="<? if(isset($_SESSION["logged_user_id"])){ echo $_SESSION["logged_user_id"]; }?>">
					<input type="hidden" name="paper_id" id="paper_id" value="<?=$pvalues["find_paper_id"]?>">
					<input type="hidden" name="conference_id" id="conference_id" value="<? if(isset($_SESSION["conf_id"])){ echo $_SESSION["conf_id"]; }?>">
					<input type="hidden" name="date_of_submition" id="date_of_submition" value="<?php echo date("Y-m-d") . " " . date("H:i:s") ?>">
					<input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
                    <div class="field">
						<label for="referee_name">Referee: </label>
						<input type="text" class="text" id="referee_name" name="referee_name" maxlength="80" size="18" title="enter referee name" value="<? if(isset($_SESSION["referee_name"])){ echo $_SESSION["referee_name"];} ?>">
						<div class="notes">(maximum of 80 characters)</div>
					</div>

					<div class="field">
						<label for="originality">Originality: <span class="required" title="this field is required">*</span></label>
						
						<div id="originality_track" style="display:none;">
							<div id="originality_handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="originality_value">1</div>
							</div>
						</div>
						<select name="originality" id="originality" title="Select paper originality" style="width:135px">
						<?php
							if(isset($_SESSION["originality"]))
							{
								if ($_SESSION["originality"] == "" ){ echo "<option value=\"\">[Select Originality]</option>"; }
								else
								{ 
									echo "<option value=\"" . $_SESSION["originality"] . "\">" . $_SESSION["originality"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else{ echo "<option value=\"\">[Select Originality]</option>"; }
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						<div class="notes">(on a scale of 1 to 7, how was the paper overall?)</div>
					</div>

					<div class="field">
						<label for="significance">Significance: <span class="required" title="this field is required">*</span></label>
						
						<div id="significance_track" style="display:none;">
							<div id="significance_handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="significance_value">1</div>
							</div>
						</div>
						<select name="significance" id="significance" title="Select paper significance" style="width:135px">
						<?php 
							if(isset($_SESSION["significance"]))
							{
								if($_SESSION["significance"] == "" ){ echo "<option value=\"\">[Select Significance]</option>"; }
								else 
								{ 
									echo "<option value=\"" . $_SESSION["significance"] . "\">" . $_SESSION["significance"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else {echo "<option value=\"\">[Select Significance]</option>";} 
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						<div class="notes">(on a scale of 1 to 7, how was the paper overall?)</div>
					</div>

					<div class="field">
						<label for="quality">Quality: <span class="required" title="this field is required">*</span></label>
						
						<div id="quality_track" style="display:none;">
							<div id="quality_handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="quality_value">1</div>
							</div>
						</div>
						<select name="quality" id="quality" title="Select paper quality" style="width:135px">
						<?php 
							if(isset($_SESSION["quality"]))
							{
								if($_SESSION["quality"] == "" ){ echo "<option value=\"\">[Select Quality]</option>"; }
								else 
								{ 
									echo "<option value=\"" . $_SESSION["quality"] . "\">" . $_SESSION["quality"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else { echo "<option value=\"\">[Select Quality]</option>"; }
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						<div class="notes">(on a scale of 1 to 7, how was the paper overall?)</div>
					</div>

					<div class="field">
						<label for="relevance">Relevance: <span class="required" title="this field is required">*</span></label>
						
						<div id="relevance_track" style="display:none;">
							<div id="relevance_handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="relevance_value">1</div>
							</div>
						</div>
						<select name="relevance" id="relevance" title="Select paper relevance" style="width:135px">
						<?php
							if(isset($_SESSION["relevance"]))
							{ 
								if($_SESSION["relevance"] == "" ){ echo "<option value=\"\">[Select Relevance]</option>"; }
								else 
								{ 
									echo "<option value=\"" . $_SESSION["relevance"] . "\">" . $_SESSION["relevance"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else { echo "<option value=\"\">[Select Relevance]</option>"; }
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						<div class="notes">(on a scale of 1 to 7, how was the paper overall?)</div>
					</div>

					<div class="field">
						<label for="presentation">Presentation: <span class="required" title="this field is required">*</span></label>
						
						<div id="presentation_track" style="display:none;">
							<div id="presentation_handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="presentation_value">1</div>
							</div>
						</div>
						<select name="presentation" id="presentation" title="Select paper presentation" style="width:135px">
						<?php 
							if(isset($_SESSION["presentation"]))
							{
								if($_SESSION["presentation"] == "" ){ echo "<option value=\"\">[Select Presentation]</option>"; }
								else 
								{ 
									echo "<option value=\"" . $_SESSION["presentation"] . "\">" . $_SESSION["presentation"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else { echo "<option value=\"\">[Select Presentation]</option>"; }
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						<div class="notes">(on a scale of 1 to 7, how was the paper overall?)</div>
					</div>

					<div class="field">
						<label for="overall">Overall: <span class="required" title="this field is required">*</span></label>
												
						<div id="overall_track" style="display:none;">
							<div id="overall_handle">
								<img src="./images/slider/slider.gif" alt="Slider" /><div id="overall_value">1</div>
							</div>
						</div>
						<select name="overall" id="overall" title="Select overall" style="width:135px">
						<?php 
							if(isset($_SESSION["overall"]))
							{
								if($_SESSION["overall"] == "" ){ echo "<option value=\"\">[Select Overall]</option>"; }
								else
								{ 
									echo "<option value=\"" . $_SESSION["overall"] . "\"  >" . $_SESSION["overall"] . "</option>"; 
									echo "<option value=\"\"></option>";
								}
							}else { echo "<option value=\"\">[Select Overall]</option>"; }
						?>
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option>
						</select>
						<div class="notes">(on a scale of 1 to 7, how was the paper overall?)</div>
					</div>

					<div class="field">
						<label for="expertise">Expertise: <span class="required" title="this field is required">*</span></label>
							<?php
								if ((isset($_SESSION["expertise"])) && ($_SESSION["expertise"] == "low")){ $low = "checked"; }
								if ((isset($_SESSION["expertise"])) && ($_SESSION["expertise"] == "medium")){ $medium = "checked"; }
								if ((isset($_SESSION["expertise"])) && ($_SESSION["expertise"] == "high")){ $high = "checked"; }
							?>
							Low <input type="radio" class="text" id="expertise" name="expertise" value="low" <? if(isset($low)){ echo $low;} ?> >
							Medium <input type="radio" class="text" id="expertise" name="expertise" value="medium" <? if(isset($medium)){ echo $medium;}?> >
							High <input type="radio" class="text" id="expertise" name="expertise" value="high" <? if(isset($high)){ echo $high;} ?> >
						<div class="notes">(expertise on subject?)</div>
					</div>

					<div class="field">
						<label for="confidential">Confidential: <span class="required" title="this field is required">*</span></label>
						<textarea class="medium_text" cols="50" rows="30" name="confidential" id="confidential" wrap="hard" title="enter confidential"><? if(isset($_SESSION["confidential"])) { echo stripslashes($_SESSION["confidential"]);} ?></textarea>			
						<div class="notes">(maximum of 3000 characters)</div>
					</div>

					<div class="field">
						<label for="contributions">Contributions: <span class="required" title="this field is required">*</span></label>
						<textarea class="medium_text" cols="50" rows="30" name="contributions" id="contributions" wrap="hard" title="enter contributions"><? if(isset($_SESSION["contributions"])) { echo stripslashes($_SESSION["contributions"]);} ?></textarea>			
						<div class="notes">(maximum of 3000 characters)</div>
					</div>

					<div class="field">
						<label for="positive">Positive: <span class="required" title="this field is required">*</span></label>
						<textarea class="medium_text" cols="50" rows="30" name="positive" id="positive" wrap="hard" title="enter positive"><? if(isset($_SESSION["positive"])) { echo stripslashes($_SESSION["positive"]);} ?></textarea>			
						<div class="notes">(maximum of 3000 characters)</div>
					</div>

					<div class="field">
						<label for="negative">Negative: <span class="required" title="this field is required">*</span></label>
						<textarea class="medium_text" cols="50" rows="30" name="negative" id="negative" wrap="hard" title="enter negative"><? if(isset($_SESSION["negative"])) { echo stripslashes($_SESSION["negative"]);} ?></textarea>			
						<div class="notes">(maximum of 3000 characters)</div>
					</div>

					<div class="field">
						<label for="further">Further: <span class="required" title="this field is required">*</span></label>
						<textarea class="medium_text" cols="50" rows="30" name="further" id="further" wrap="hard" title="enter further"><? if(isset($_SESSION["further"])) { echo stripslashes($_SESSION["further"]);} ?></textarea>			
						<div class="notes">(maximum of 3000 characters)</div>
					</div>
					<?=$submit_button?>	
				</fieldset>
				</form>
                
                <div id="rguide"> 
                   Review guide for fields: 
                   <b>originality</b>, <b>significance</b>, <b>quality</b>, 
                   <b>relevance</b>, <b>presentation</b>, <b>overall</b>
                   <br /><br />
                   <ul>
                       <li>7: Strong Accept (award quality)</li>
                       <li>6: Accept (I will argue for this paper)</li>
                       <li>5: Weak Accept (vote accept, but won't object)</li>
                       <li>4: Neutral (not impressed, won't object)</li>
                       <li>3: Weak Reject (vote reject, but won't object)</li>
                       <li>2: Reject (I will argue against this paper)</li>
                       <li>1: Strong Reject</li>
					</ul>
               </div>
	</div><!--reviews_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--reviews-->	

<?php layout_fragment_end(); ?>

</body>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</html>