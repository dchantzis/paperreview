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
	$csrf_password_generator = hash('sha256', "update_user_profile") . $csrf_password_generator;
	
	whereUgo(0);
	whereUgo(1);
	
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

<div class="update_user_profile">
	<div id="page_title">User Profile</div>
	<div id="spacer"></div>
	<div id="update_user_profile_content">
	<fieldset>
		<legend>Registration Data</legend>
            <div id="instructions">
            	Use the following form to update your profile.<br />
            	<span class="red">NOTE: </span>
                In case you want to change your username, contact the <a href="mailto:#" title="admin@paperreviews.com" class="simple">system administrator</a>.
                To change your password, logout and select the "Forgot Password?" option.
            </div>
        	<div class="messages"><?php VariousMessages($flg); ?></div>
			<div class="notes">Fields marked with <span class="required">*</span> are required.</div>
			<form id="rfrm" name="rfrm" method="post" action="./include/functionsinc.php?type=15">
				<div class="dataTypeGroup">
					User LogIn Info
						<div class="notes">You can't change your login information.</div>
						<div class="field">
							<label for="username">Username:</label>
							<div class="text"><div class="red"><?=$_SESSION["logged_user_email"]; ?></div></div>
						</div>
						<div class="field">
							<label for="password">Password:</label>
							<div class="text"><div class="red">*encrypted*</div></div>
						</div>
					</div>

					<div class="dataTypeGroup">
						Basic Account Info
						<div class="field">
							<label for="fname">First Name: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="fname" id="fname" value="<?=$_SESSION["logged_user_fname"]?>" maxlength="35" size="24" title="Type your first name" />
							<div class="notes">(maximum of 35 characters)</div>
						</div>
						<div class="field">
							<label for="lname">Last Name: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="lname" id="lname" value="<?=$_SESSION["logged_user_lname"]?>" maxlength="35" size="24" title="Type your last name" />
							<div class="notes">(maximum of 35 characters)</div>
						</div>
					</div><!-- basic account info -->
				
				<div class="dataTypeGroup">
					Addresses Info
						<div class="field">
							<label for="address_01">Address 01: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="address_01" id="address_01" value="<?=$_SESSION["address_01"]?>" maxlength="100" size="24" title="Type your first address" />
							<div class="notes">(maximum of 100 characters)</div>
						</div>
						<div class="field">
							<label for="address_02">Address 02: </label>
							<input type="text" class="text" name="address_02" id="address_02" value="<?=$_SESSION["address_02"]?>" maxlength="100" size="24" title="Type your second address" />
							<div class="notes">(maximum of 100 characters)</div>
						</div>
						<div class="field">
							<label for="address_03">Address 03: </label>
							<input type="text" class="text" name="address_03" id="address_03" value="<?=$_SESSION["address_03"]?>" maxlength="100" size="24" title="Type your third address" />
							<div class="notes">(maximum of 100 characters)</div>
						</div>
						<div class="field">
							<label for="city">City: </label>
							<input type="text" class="text" name="city" id="city" value="<?=$_SESSION["city"]?>" maxlength="35" size="24" title="Type your city" />
							<div class="notes">(maximum of 35 characters)</div>
						</div>
						<div class="field">
							<label for="country">Country: </label>
							<input type="text" class="text" name="country" id="country" value="<?=$_SESSION["country"]?>" maxlength="35" size="24" title="Type your country" />
							<div class="notes">(maximum of 35 characters)</div>
						</div>
						<div class="field">
							<label for="phone_01">Phone Number 1: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="phone_01" id="phone_01" value="<?=$_SESSION["phone_01"]?>" maxlength="10" size="24" title="Type your phone number" onblur="phoneValidation(phone_01);" />
							<div class="notes">(maximum of 10 characters)</div>
						</div>
						<div class="field">
							<label for="phone_02">Phone Number 2: </label>
							<input type="text" class="text" name="phone_02" id="phone_02" value="<?=$_SESSION["phone_02"]?>" maxlength="10" size="24" title="Type your phone number" onblur="phoneValidation(phone_02);" />
							<div class="notes">(maximum of 10 characters)</div>
						</div>
						<div class="field">
							<label for="fax">Fax Number: </label>
							<input type="text" class="text" name="fax" id="fax" value="<?=$_SESSION["fax"]?>" maxlength="10" size="24" title="Type your fax number" onblur="phoneValidation(fax);" />
							<div class="notes">(maximum of 10 characters)</div>
						</div>
						<div class="field">
							<label for="website">Website Address: </label>
							<input type="text" class="text" name="website" id="website" value="<?=$_SESSION["website"]?>" maxlength="80" size="24" title="Type your website address" onblur="websiteValidation(website);" />
							<div class="notes">(maximum of 80 characters)</div>
						</div>
					</div><!-- addresses info -->
					
					<div class="dataTypeGroup">
						If you forget your password...
					
						<div class="field">
							<label for="security_question">Security question: <span class="required" title="this field is required">*</span></label>
							<select name="security_question" id="security_question" title="Select a security question">
								<option value="">[Select a Question]</option>
								<option value="What is your mothers surname?" >What is your mothers surname?</option>
								<option value="What was the name of your first school?" >What was the name of your first school?</option>
								<option value="Who was your childhood hero?" >Who was your childhood hero?</option>
								<option value="What is your all-time favorite sports team?" >What is your all-time favorite sports team?</option>
								<option value="What was your high school mascot?" >What was your high school mascot?</option>
								<option value="What make was your first car or bike?" >What make was your first car or bike?</option>
								<option value="What is your pets name?" >What is your pet's name?</option>
							</select>
						</div>
						<div class="field">
							<label for="security_answer">Your answer: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="security_answer" id="security_answer" value="<?=$_SESSION["security_answer"]?>" maxlength="30" size="24" title="Type your security answer" autocomplete="off" onblur="SAnswerValidation();" />
							<div class="notes">(Four characters or more. Make sure your answer is memorable for you, but hard for others to guess!)</div>
						</div>
					<div class="field">
						<label for="birthday_month">Birthday: <span class="required" title="this field is required">*</span></label>
							<select name="birthday_month" id="birthday_month" title="Select a month">
							<?php 
							if($_SESSION["birthday_month"] == "" || $_SESSION["birthday_month_no"] == 0){ echo "<option value=\"\">[Select a Month]</option>"; }
							else 
							{ 
								echo "<option value=\"" . $_SESSION["birthday_month_no"] . "\">" . $_SESSION["birthday_month"] . "</option>"; 
								echo "<option value=\"\"></option>";
							}
							?>
							
							<option value="01">January</option><option value="02">February</option><option value="03">March</option>
							<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
							<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
							</select>
							&nbsp;
							<?php
								if($_SESSION["birthday_day"] == "" || $_SESSION["birthday_day"] == 0){ echo "<input type=\"text\" class=\"text\" onfocus=\"this.value=''\" maxlength=\"2\" name=\"birthday_day\" id=\"birthday_day\" size=\"2\" value=\"dd\" autocomplete=\"off\" onblur=\"dayValidation(birthday_day);\" title=\"Enter a day\">,"; }
								else 
								{ 
									echo "<input type=\"text\" class=\"text\" onfocus=\"this.value=''\" maxlength=\"2\" name=\"birthday_day\" id=\"birthday_day\" size=\"2\" value=\"" . $_SESSION["birthday_day"] . "\" autocomplete=\"off\" onblur=\"dayValidation(birthday_day);\" title=\"Enter a day\">,"; 
								}
							?>
							&nbsp;						
							&nbsp;
							<?php
								if($_SESSION["birthday_year"] == "" || $_SESSION["birthday_year"] == 0 ){ echo "<input type=\"text\" class=\"text\" onfocus=\"this.value=''\" maxlength=\"4\" name=\"birthday_year\" id=\"birthday_year\" size=\"4\" value=\"yyyy\" autocomplete=\"off\" onblur=\"yearValidation(birthday_year);\" title=\"Enter a year\">"; }
								else 
								{ 
									echo "<input type=\"text\" class=\"text\" onfocus=\"this.value=''\" maxlength=\"4\" name=\"birthday_year\" id=\"birthday_year\" size=\"4\" value=\"" . $_SESSION["birthday_year"] . "\" autocomplete=\"off\" onblur=\"yearValidation(birthday_year);\" title=\"Enter a year\">"; 
								}
							?>					
					</div>
					</div><!--If you forget your password...-->
					<input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
						<div class="submit"><input type="submit" value="Update"  onclick="SubmitForm2(rfrm); return false;" ></div>
				</fieldset>
                </form>			
	</div><!--update_user_profile_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--update_user_profile-->

<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);

</script>
</body>
</html>
