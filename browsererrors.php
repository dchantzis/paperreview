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
	
	if (isset($_GET["e"])) {$flg = $_GET["e"];}
	//if( $flg != hash('sha256', "javascript") && $flg != hash('sha256', "cookies")){Redirects(55,"","");}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if($_SESSION["administrator"] == FALSE){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);*/
</style>
</head>


<body class="index">

<div id="wrapper">
	<div id="content">
		<div id="masthead">PAPER <div class="red">REVIEW</div></div>
		<div id="logo">logo</div>

		<?php
		if( $flg == hash('sha256', "javascript")){ echo "<h1><span class=\"red\">ERROR: </span>Javascript disabled</h1>"; }
		if( $flg == hash('sha256', "cookies")){ echo "<h1><span class=\"red\">ERROR: </span>Cookies are disabled</h1>"; }
		?>

		<div id="instructions">
            Sorry for the inconvenience.<br>
        	Please enable javascript in your browser. <a href="#js_instructions" class="simple">Instructions</a><br>
            Please enable cookies in your browser. <a href="#c_instructions" class="simple">Instructions</a><br>
            <br>
            <span class="red">NOTE: </span>After you follow the instructions, choose one of the following options:<br>
            <ul class="order_by">
                <li><a href="./login.php" class="simple">Login</a></li>
                <li><a href="./include/functionsinc.php?type=2" class="simple">Logout</a></li>
                <li><a href="./ulounge.php" class="simple">Go to users lounge</a></li>
            </ul>
        </div> 
		
        <ul id="js_instructions">
        	<h3>Enable javascript: </h3>
            <li>
            	<span class="red">For Mozilla Firefox 1.5 & 2 users: </span>
            	<br><br>
                1. Click on the Tools menu.<br>
                2. Select Options.<br>
                3. Click the Content tab with the Earth graphic.<br>
                4. Check "Enable JavaScript".<br>
                5. Click OK to close the dialogue.<br>
                6. Click the Reload button or hit F5 to refresh the page.<br>
          	</li>
            <li>
            	<span class="red">For Internet Explorer 7 users: </span>
				<br><br>
               1. Click on the Tools button or "Tools" from the program menu.<br>
               2. Click on Internet Options.<br>
               3. Click the Security tab.<br>
               4. In the "Security level for this zone" box, click on Custom level.<br>
               5. Scroll toward the bottom of the Settings box to Scripting.<br>
               6. Enable active scripting.<br>
               7. Click OK to close the dialogue.<br>
               8. Click the Refresh button or hit F5 to refresh the page.<br>
          	</li>
            <li>
            	<span class="red">For Internet Explorer 5.X & 6.X  users: </span>
            	<br><br>
               1. Select Internet Options from the Tools menu.<br>
               2. In Internet Options dialog box select the Security tab.<br>
               3. Click Custom level button at bottom. <br>
               4. Under Scripting category enable Active Scripting, Allow paste options via script and Scripting of Java applets.<br>
               5. Click OK to close the dialogue.<br>
               6. Click the Refresh button or hit F5 to refresh the page.<br>
          	</li>
            <li>
            	<span class="red">For Opera 9 users: </span>
            	<br><br>
               1. Select the Tools menu.<br>
               2. Select Preferences.<br>
               3. Click the Advanced tad.<br>
               4. Select the Content option.<br>
               5. Check "Enable JavaScript".<br>
               6. Click OK to close the dialogue.<br>
               7. Click the Refresh button or hit F5 to refresh the page.<br>
          	</li>
            <li>
            	<span class="red">For Netscape 7.X users: </span>
                <br><br>
               1. Select Preferences from the Edit menu.<br>
               2. Click the arrow next to Advanced.<br>
               3. Click Scripts & Plugins.<br>
               4. Check Navigator beneath "Enable Javascript for".<br>
               5. Click OK.<br>
               6. Click the Refresh button or hit F5 to refresh the page.<br>
            </li>
            <li>
            	<span class="red">For Safari 2.X & 3.X users: </span>
                <br><br>
               1. Click on the Tools menu.<br>
               2. Select Preferencies.<br>
               3. From the Security Tab, check "Enable Javascript"<br>
               4. Close the dialogue.<br>
               5. Click the Refresh button or hit F5 to refresh the page.<br>
            </li>
        </ul>
        <br><br>
       	<ul id="c_instructions">
        	<h3>Enable cookies: </h3>
            <li>
            	<span class="red">For Mozilla Firefox 1.5 & 2 users: </span>
                <br><br>
                1. Click on the Tools menu.<br>
                2. Select Options.<br>
                3. Click the Privacy tab with the Lock graphic.<br>
                4. Check "Accept cookies from sites".<br>
                5. Click OK to close the dialogue.<br>
                6. Click the Reload button or hit F5 to refresh the page.<br>
            </li>
            <li>
            	<span class="red">For Internet Explorer 5.X & 6.X & 7 users: </span>
                <br><br>
               1. Click on the Tools button or "Tools" from the program menu.<br>
               2. Click on Internet Options.<br>
               3. Click the Privacy tab.<br>
               4. Under Settings, click Advanced button.<br>
               5. Check the box Override automatic cookie handling under Cookies section in Advanced Privacy Settings window.<br>
               6. Under First-party Cookies, select Accept.<br>
               7. Under Third-party Cookies, select Accept.<br>
               8. Check the box Always allow session cookies.<br>
               7. Click OK to close the dialogue.<br>
               8. Click the Refresh button or hit F5 to refresh the page.<br>
            </li>
            <li>
            	<span class="red">For Opera 9 users: </span>
            	<br><br>
               1. Select the Tools menu.<br>
               2. Select Preferences.<br>
               3. Click the Advanced tad.<br>
               4. Select the Cookies option.<br>
               5. Select "Accept cookies".<br>
               6. Click OK to close the dialogue.<br>
               7. Click the Refresh button or hit F5 to refresh the page.<br>
            </li>
           	<li>
            	<span class="red">For Netscape 7.X users: </span>
                <br><br>
               1. Select Preferences from the Edit menu.<br>
               2. From the Preferences dialog box, under Category, double-click Privacy & Security.<br>
               3. Under Privacy & Security, click to select Cookies.<br>
               4. Under Cookies, click to select "Allow all cookies".<br>
               5. Click OK.<br>
               6. Click the Refresh button or hit F5 to refresh the page.<br>
            </li>
            <li>
            	<span class="red">For Safari 2.X & 3.X users: </span>
                <br><br>
               1. Click on the Tools menu.<br>
               2. Select Preferencies.<br>
               3. From the Security Tab, select Accept Cookies: "Only from sites you navigate to"<br>
               4. Close the dialogue.<br>
               5. Click the Refresh button or hit F5 to refresh the page.<br>
            </li>
        </ul>
        <div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>	
   	</div><!--content-->
</div>

</body>
</html>