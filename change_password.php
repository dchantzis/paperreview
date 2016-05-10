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

	require("./include/functionsinc.php"); 
	
	global $csrf_password_generator;
	
	whereUgo(6);

	$step = $_SESSION["STEP"];
	$flg = "";
	$error = "";
	
	if (isset($_GET["flg"]))
	{
		$flg = $_GET["flg"];
	}//if

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::Paper Reviews::.</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script language="JavaScript" type="text/JavaScript" src="scripts/prvalidations.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);*/
</style>
</head>

<body class="change_password">
<div id="wrapper">
<div id="masthead">PAPER <div class="red">REVIEW</div></div>
	<div id="userOptions">
		<ul>
			<li><a href="./include/functionsinc.php?type=2" title="Log in">Log In</a></li>
		</ul>
	</div>
	<!--image wrapper background bars -->
	<div id="topbar"></div>	
	<div id="leftbar"></div>
	<div id="rightbar"></div>
	
	<div id="logo">logo</div>
	
	<div id="content">
			<div id="mainColumn">
				<fieldset>
					<legend>Change your Password</legend>
                    <div class="messages"><?php VariousMessages($flg); ?></div>
					<div class="notes">Fields marked with <span class="required">*</span> are required.</div>
				<form id="chgpwform01" name="chgpwform01"  method="post" action="./include/functionsinc.php?type=5">
                <input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />					
					<div class="dataTypeGroup">
						Enter your E-mail address
						<div class="field">
							<label for="email">E-mail: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="email" id="email" value="<?=$_SESSION["email"]?>" maxlength="35" size="24" title="Type your e-mail address" />
							<div class="notes">(maximum of 35 characters)</div>
						</div>
						<div class="field">
							<div class="submit"><input type="submit" value="Search"></div>
						</div>
					</div>
				</form>
				<?php 
					if(($step == 2) || ($step == 3))
					{
				?>
				<form id="chgpwform02" name="chgpwform02" method="post" action="./include/functionsinc.php?type=5">
                <input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />
					<div class="dataTypeGroup">
						Answer the security question and enter you birthday
						<div class="field">
							<label for="security_question">Security question:</label>
							<div class="text"><?=$_SESSION["security_question"]?></div>
							<br>
							<label for="security_answer">Security anwser: <span class="required" title="this field is required">*</span></label>
							<input type="text" class="text" name="security_answer" id="security_answer" maxlength="30" size="24" title="Type your security answer"  autocomplete="off">
							<div class="notes">(maximum of 30 characters)</div>
						</div>
						<div class="field">
							<label for="birthday_month">Birthdate: <span class="required" title="this field is required">*</span></label>
								<select name="birthday_month" id="birthday_month" title="Select a month">
									<option value="">[Select a Month]</option>
									<option value="01">January</option><option value="02">February</option><option value="03">March</option>
									<option value="04">April</option><option value="05">May</option><option value="06">June</option><option value="07">July</option><option value="08">August</option>
									<option value="09">September</option><option value="10">October</option><option value="11">November</option><option value="12">December</option>
								</select>
								&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="2" name="birthday_day" id="birthday_day" size="2" value="dd" autocomplete="off" onblur="dayValidation(birthday_day);" title="Enter a day">&nbsp;,
								&nbsp;<input type="text" class="text" onfocus="this.value=''" maxlength="4" name="birthday_year" id="birthday_year" size="4" value="yyyy" autocomplete="off" onblur="yearValidation(birthday_year);" title="Enter a year">
						</div>
						<div class="field">
							<div class="submit"><input type="submit" value="Submit"></div>
						</div>
					</div><!--security question-->
				</form>
				<?php
					}//if(($step == 2) || ($step == 3))
					if ($step == 3)
					{										
				?>
				<form id="chgpwform03" name="chgpwform03" method="post" action="./include/functionsinc.php?type=5">
                <input type="hidden" name="csrf" id="csrf" value="<?=$csrf_password_generator?>" />			
					<div class="dataTypeGroup">						 	
						Enter your new password
						<div class="field">
							<label for="password">Password: <span class="required" title="this field is required">*</span></label>
							<input type="password" class="text" name="password" id="password" maxlength="15" size="24" title="Type your password" nblur="check_password(this);" autocomplete="off" />
							<div class="notes">(Less than 15 characters, more than 6; capitalization doesn't matter)</div>
						</div>
						<div class="field">
							<label for="repassword">Re-type password: <span class="required" title="this field is required">*</span></label>
							<input type="password" class="text" name="repassword" id="repassword" maxlength="15" size="24" title="Re-type your password" onblur="check_password(this);" />
						</div>
						<div class="field">
							<div class="submit"><input type="submit" value="Change"></div>
						</div>
					</div>
				</form>
				<?php
					}//if ($step == 3)
					if ($_SESSION["STEP"] == 4)
					{
				?>
				<form id="logoutfrm" name="logoutfrm" method="post" action="./include/functionsinc.php?type=2">			
					<div class="dataTypeGroup">						 	
						A message with your new password has been sent to your email.
						<div class="field">
							<div class="submit"><input type="submit" value="Log In" title="Log In"></div>
						</div>
					</div>
				</form>
				<?php
					}//if ($step == 4)
				?>
				</fieldset>

			</div><!--mainColumn-->
	</div><!--content-->
	<div id="footer"></div>
</div><!--wrapper-->
</body>

</html>
