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
	whereUgo(8);
	whereUgo(3);
	
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

<div class="update_conference">
	<div id="page_title">Update Conference</div>
	<div id="spacer"></div>
	<div id="update_conference_content">
    
    <div class="messages"><?php VariousMessages($flg); ?></div>
    
		<fieldset>
			<legend>Conference: <? if(isset($_SESSION["name"])){ echo strtoupper($_SESSION["name"]);} ?></legend>
				
				<div class="notes">Fields marked with <span class="required">*</span> are required.</div>
				<form id="cufrm" name="cufrm" method="post" action="./include/functionsinc.php?type=8&user_type=chairman">
				<div class="dataTypeGroup">
					<div class="conf_info_type">Basic Info</div>
							<div class="field">
								<label for="alias">Conference alias: <span class="required" title="this field is required">*</span></label>
								<input type="text" class="text" name="alias" id="alias" maxlength="50" size="18" title="enter conference alias" value="<? if(isset($_SESSION["alias"])){ echo $_SESSION["alias"];} ?>">
								<div class="notes">(maximum of 50 characters)</div>
							</div>
							<div class="field">
								<label for="place">Where is the conference held?: <span class="required" title="this field is required">*</span></label>
								<input type="text" class="text" name="place" id="place" maxlength="100" size="18" value="<? if(isset($_SESSION["place"])){ echo $_SESSION["place"];} ?>" title="enter conference place">
								<div class="notes">(maximum of 100 characters)</div>
							</div>
							<div class="field">
								<label for="date_conference_held">When is the conference held?: <span class="required" title="this field is required">*</span></label>
								<textarea class="text" cols="10" rows="10" name="date_conference_held" id="date_conference_held" wrap="hard" title="enter conference date"><? if(isset($_SESSION["date_conference_held"])){ echo $_SESSION["date_conference_held"];} ?></textarea>					
								<div class="notes">(maximum of 100 characters)</div>
							</div>
							<div class="field">
								<label for="contact_email">Contact E-mail: <span class="required" title="this field is required">*</span></label>
								<input type="text" class="text" name="contact_email" id="contact_email" maxlength="35" size="18" title="enter contact e-mail" value="<? if(isset($_SESSION["contact_email"])){ echo $_SESSION["contact_email"];} ?>" onblur="emailValidation(contact_email);">
								<div class="notes">(maximum of 35 characters)</div>
							</div>
							<div class="field">
								<label for="contact_phone">Contact Phone Number: <span class="required" title="this field is required">*</span></label>
								<input type="text" class="text" name="contact_phone" id="contact_phone" maxlength="10" size="18" title="enter contact phone number" value="<? if(isset($_SESSION["contact_phone"])){ echo $_SESSION["contact_phone"];} ?>" onblur="phoneValidation(contact_phone);">
								<div class="notes">(maximum of 10 characters)</div>
							</div>
							<div class="field">
								<label for="website">Website: </label>
								<input type="text" class="text" name="website" id="website" maxlength="80" size="18" title="enter website" value="<? if(isset($_SESSION["website"])){ echo $_SESSION["website"];} ?>" onblur="websiteValidation(website);">
								<div class="notes">(maximum of 80 characters)</div>
							</div>
							<br><br>
							<div class="conf_info_type">System Info</div>
							<!--deadline-->
							<div class="field">
								<label for="deadline_month">Deadline Date: <span class="required" title="this field is required">*</span></label>
									<select name="deadline_month" id="deadline_month" title="Select a month">
                                    <?
										if(isset($_SESSION["deadline_month_no"]))
										{
											if($_SESSION["deadline_month_no"] == "" ){ echo "<option value=\"\">[Select Month]</option>";}
											else
											{
												echo "<option value=\"" . $_SESSION["deadline_month_no"] . "\">" . $_SESSION["deadline_month"] . "</option>"; 
												echo "<option value=\"\"></option>";
											}
										}else { echo "<option value=\"\">[Select Month]</option>";}
                                    ?>
                                    <option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
									</select>
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="deadline_day" id="deadline_day" size="2" value="<? if(isset($_SESSION["deadline_day"])){ echo $_SESSION["deadline_day"];}else{ echo "dd"; } ?>" autocomplete="off" onblur="dayValidation(deadline_day);" title="Enter a day">,&nbsp;						
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="deadline_year" id="deadline_year" size="4" value="<? if(isset($_SESSION["deadline_year"])){ echo $_SESSION["deadline_year"];}else{ echo "yyyy"; } ?>" autocomplete="off" onblur="yearValidation(deadline_year);" title="Enter a year">
							</div><!--deadline-->							
							<!--papers submittion deadline-->
							<div class="field">
								<label for="abstracts_deadline_month">Abstracts Submittion Deadline: <span class="required" title="this field is required">*</span></label>
									<select name="abstracts_deadline_month" id="abstracts_deadline_month" title="Select a month">
                                    <?
										if(isset($_SESSION["abstracts_deadline_month_no"]))
										{
											if($_SESSION["abstracts_deadline_month_no"] == "" ){ echo "<option value=\"\">[Select Month]</option>";}
											else
											{
												echo "<option value=\"" . $_SESSION["abstracts_deadline_month_no"] . "\">" . $_SESSION["abstracts_deadline_month"] . "</option>"; 
												echo "<option value=\"\"></option>";
											}
										}else { echo "<option value=\"\">[Select Month]</option>";}
                                    ?>									
                                    <option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
									</select>
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="abstracts_deadline_day" id="abstracts_deadline_day" size="2" value="<? if(isset($_SESSION["abstracts_deadline_day"])){ echo $_SESSION["abstracts_deadline_day"];}else{ echo "dd"; } ?>" autocomplete="off" onblur="dayValidation(abstracts_deadline_day);" title="Enter a day">,&nbsp;						
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="abstracts_deadline_year" id="abstracts_deadline_year" size="4" value="<? if(isset($_SESSION["abstracts_deadline_year"])){ echo $_SESSION["abstracts_deadline_year"];}else{ echo "yyyy"; } ?>" autocomplete="off" onblur="yearValidation(abstracts_deadline_year);" title="Enter a year">
							</div><!--papers submittion deadline-->
							<!--manuscripts submittion deadline-->
							<div class="field">
								<label for="manuscripts_deadline_month">Manuscripts Submittion Deadline: <span class="required" title="this field is required">*</span></label>
									<select name="manuscripts_deadline_month" id="manuscripts_deadline_month" title="Select a month">
                                    <?
										if(isset($_SESSION["manuscripts_deadline_month_no"]))
										{
											if($_SESSION["manuscripts_deadline_month_no"] == "" ){ echo "<option value=\"\">[Select Month]</option>";}
											else
											{
												echo "<option value=\"" . $_SESSION["manuscripts_deadline_month_no"] . "\">" . $_SESSION["manuscripts_deadline_month"] . "</option>"; 
												echo "<option value=\"\"></option>";
											}
										}else { echo "<option value=\"\">[Select Month]</option>";}
                                    ?>									
                                    <option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
									</select>
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="manuscripts_deadline_day" id="papers_deadline_day" size="2" value="<? if(isset($_SESSION["manuscripts_deadline_day"])){ echo $_SESSION["manuscripts_deadline_day"];}else{ echo "dd"; } ?>" autocomplete="off" onblur="dayValidation(manuscripts_deadline_day);" title="Enter a day">,&nbsp;						
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="manuscripts_deadline_year" id="manuscripts_deadline_year" size="4" value="<? if(isset($_SESSION["manuscripts_deadline_year"])){ echo $_SESSION["manuscripts_deadline_year"];}else{ echo "yyyy"; } ?>" autocomplete="off" onblur="yearValidation(manuscripts_deadline_year);" title="Enter a year">
							</div><!--manuscripts submittion deadline-->
							<!--camera_ready paper submittion deadline-->
							<div class="field">
								<label for="camera_ready_deadline_month">Camera-Ready Submittion Deadline: <span class="required" title="this field is required">*</span></label>
									<select name="camera_ready_deadline_month" id="camera_ready_deadline_month" title="Select a month">
                                    <?
										if(isset($_SESSION["camera_ready_deadline_month_no"]))
										{
											if($_SESSION["camera_ready_deadline_month_no"] == "" ){ echo "<option value=\"\">[Select Month]</option>";}
											else
											{
												echo "<option value=\"" . $_SESSION["camera_ready_deadline_month_no"] . "\">" . $_SESSION["camera_ready_deadline_month"] . "</option>"; 
												echo "<option value=\"\"></option>";
											}
										}else { echo "<option value=\"\">[Select Month]</option>";}
                                    ?>								
                                	<option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
									</select>
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="camera_ready_deadline_day" id="camera_ready_deadline_day" size="2" value="<? if(isset($_SESSION["camera_ready_deadline_day"])){ echo $_SESSION["camera_ready_deadline_day"];}else{ echo "dd"; } ?>" autocomplete="off" onblur="dayValidation(camera_ready_deadline_day);" title="Enter a day">,&nbsp;						
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="camera_ready_deadline_year" id="camera_ready_deadline_year" size="4" value="<? if(isset($_SESSION["camera_ready_deadline_year"])){ echo $_SESSION["camera_ready_deadline_year"];}else{ echo "yyyy"; } ?>" autocomplete="off" onblur="yearValidation(camera_ready_deadline_year);" title="Enter a year">
							</div><!--camera_ready paper submittion deadline-->
							<!--preferencies submittion date-->
							<div class="field">
								<label for="preferencies_deadline_month">Preferencies Submittion Deadline: <span class="required" title="this field is required">*</span></label>
									<select name="preferencies_deadline_month" id="preferencies_deadline_month" title="Select a month">
                                    <?
										if(isset($_SESSION["preferencies_deadline_month_no"]))
										{
											if($_SESSION["preferencies_deadline_month_no"] == "" ){ echo "<option value=\"\">[Select Month]</option>";}
											else
											{
												echo "<option value=\"" . $_SESSION["preferencies_deadline_month_no"] . "\">" . $_SESSION["preferencies_deadline_month"] . "</option>"; 
												echo "<option value=\"\"></option>";
											}
										}else { echo "<option value=\"\">[Select Month]</option>";}
                                    ?>
                                    <option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
									</select>
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="preferencies_deadline_day" id="preferencies_deadline_day" size="2" value="<? if(isset($_SESSION["deadline_day"])){ echo $_SESSION["deadline_day"];}else{ echo "dd"; } ?>" autocomplete="off" onblur="dayValidation(preferencies_deadline_day);" title="Enter a day">,&nbsp;						
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="preferencies_deadline_year" id="preferencies_deadline_year" size="4" value="<? if(isset($_SESSION["deadline_year"])){ echo $_SESSION["deadline_year"];}else{ echo "yyyy"; } ?>" autocomplete="off" onblur="yearValidation(preferencies_deadline_year);" title="Enter a year">
							</div><!--camera_ready paper submittion deadline-->
							<!--reviews submittion deadline-->
							<div class="field">
								<label for="reviews_deadline_month">Reviews Submittion Deadline: <span class="required" title="this field is required">*</span></label>
									<select name="reviews_deadline_month" id="reviews_deadline_month" title="Select a month">
                                    <?
										if(isset($_SESSION["reviews_deadline_month_no"]))
										{
											if($_SESSION["reviews_deadline_month_no"] == "" ){ echo "<option value=\"\">[Select Month]</option>";}
											else
											{
												echo "<option value=\"" . $_SESSION["reviews_deadline_month_no"] . "\">" . $_SESSION["reviews_deadline_month"] . "</option>"; 
												echo "<option value=\"\"></option>";
											}
										}else { echo "<option value=\"\">[Select Month]</option>";}
                                    ?>		
                                    <option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
									</select>
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="reviews_deadline_day" id="reviews_deadline_day" size="2" value="<? if(isset($_SESSION["reviews_deadline_day"])){ echo $_SESSION["reviews_deadline_day"];}else{ echo "dd"; } ?>" autocomplete="off" onblur="dayValidation(reviews_deadline_day);" title="Enter a day">,&nbsp;						
									&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="reviews_deadline_year" id="reviews_deadline_year" size="4" value="<? if(isset($_SESSION["reviews_deadline_year"])){ echo $_SESSION["reviews_deadline_year"];}else{ echo "yyyy"; } ?>" autocomplete="off" onblur="yearValidation(reviews_deadline_year);" title="Enter a year">
							</div><!--reviews submittion deadline-->
							<div class="field">
								<label for="comments">Comments: </label>
								<textarea class="text" cols="35" rows="18" name="comments" id="comments" wrap="hard" title="enter comments"><? if(isset($_SESSION["comments"])){ echo $_SESSION["comments"];} ?></textarea>
								<div class="notes">(maximum of 2000 characters)</div>
							</div>
							<input type="hidden" name="date_of_creation" id="date_of_creation" value="<?php echo date("Y-m-d") . " " . date("H:i:s") ?>">
							<input type="hidden" name="csrf" id="csrf" value="<?=hash('sha256', "update_conference") . $csrf_password_generator?>" />
                            <div class="field"><div class="submit"><input type="submit" title="Submit form" value="Update it"></div></div>				
						</div><!--dataTypeGroup-->
						</form>
					</fieldset>									
	</div><!--update_conference_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--update_conference-->
				
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>