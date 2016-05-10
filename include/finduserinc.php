<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	###################################################################################

	whereUgo(0);
	whereUgo(10);
	whereUgo(1);

	global 	$uvalues;

	//load DataBase info
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	######################
	//check if the $_GET table has only the value we want,
	//and the value is of the type we want
	//returns the value we want trimmed
	//$user_id = checkGetVariable("userid",0,"([^0-9]+)");
	if(!isset($_GET["userid"])){ header("Location: ./ulounge.php"); exit;}
	$get_var_type["userid"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$user_id = $validated_vars["userid"];

	######################


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("finduserinc.php","",34,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("finduserinc.php","",35,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, fname, lname, email, address_01, address_02, address_03, "
				. "city, country, phone_01, phone_02, fax, website "
				. "FROM user "
				. "WHERE id='" . $user_id . "';";
	$result = @mysql_query($query) or dbErrorHandler("finduserinc.php","",42,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		//echo "ERROR! A conference with this id doesn't exist.";
		Redirects(0,"","");
	}//if
	else{
		for($i=0; $i<$num; $i++)
		{
			//store all the DB values in array $uvalues
			$uvalues["user_info_id"] = mysql_result($result,$i,"id");
			$uvalues["user_info_fname"] = mysql_result($result,$i,"fname");
			$uvalues["user_info_lname"] = mysql_result($result,$i,"lname");
			$uvalues["user_info_email"] = mysql_result($result,$i,"email");
			$uvalues["user_info_address_01"] = mysql_result($result,$i,"address_01");
			$uvalues["user_info_address_02"] = mysql_result($result,$i,"address_02");
			$uvalues["user_info_address_03"] = mysql_result($result,$i,"address_03");
			$uvalues["user_info_city"] = mysql_result($result,$i,"city");
			$uvalues["user_info_country"] = mysql_result($result,$i,"country");
			$uvalues["user_info_phone_01"] = mysql_result($result,$i,"phone_01");
			$uvalues["user_info_phone_02"] = mysql_result($result,$i,"phone_02");
			$uvalues["user_info_fax"] = mysql_result($result,$i,"fax");
			$uvalues["user_info_website"] = mysql_result($result,$i,"website");

			$uvalues = convert_ar_vals($uvalues, "NULL", "*unspecified*");

		}//for
	}//else

	@mysql_close();//closes the connection to the DB
?>
