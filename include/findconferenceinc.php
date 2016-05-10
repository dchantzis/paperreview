<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	###################################################################################

	whereUgo(0);
	whereUgo(1);

	global 	$cvalues;

	//load DataBase info
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	######################
	//check if the $_GET table has only the value we want,
	//and the value is of the type we want
	//returns the value we want trimmed
	//$conference_id = checkGetVariable("confid",0,"([^0-9]+)");
	if(!isset($_GET["confid"])){ header("Location: ./ulounge.php"); exit;}
	$get_var_type["confid"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$conference_id = $validated_vars["confid"];
	######################


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("findconferenceinc.php","",32,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("findconferenceinc.php","",33,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, name, alias, place, date_conference_held, contact_email, contact_phone, website, comments, "
			. "deadline, abstracts_deadline, manuscripts_deadline, camera_ready_deadline, "
			. "preferencies_deadline, reviews_deadline "
			. "FROM conference WHERE id='" . $conference_id . "';";
	$result = @mysql_query($query) or dbErrorHandler("findconferenceinc.php","",40,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

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
			//store all the DB values in array $cvalues
			$cvalues["find_conf_id"] = mysql_result($result,$i,"id");
			$cvalues["find_conf_name"] = mysql_result($result,$i,"name");
			$cvalues["find_conf_alias"] = mysql_result($result,$i,"alias");
			$cvalues["find_conf_place"] = mysql_result($result,$i,"place");
			$cvalues["date_conference_held"] = mysql_result($result,$i,"date_conference_held");
			$cvalues["find_conf_contact_email"] = mysql_result($result,$i,"contact_email");
			$cvalues["find_conf_contact_phone"] = mysql_result($result,$i,"contact_phone");
			$cvalues["find_conf_website"] = mysql_result($result,$i,"website");
			$cvalues["find_conf_comments"] = mysql_result($result,$i,"comments");

			$cvalues["find_conf_deadline"] = mysql_result($result,$i,"deadline");
			$cvalues["find_conf_abstracts_deadline"] = @mysql_result($result,$i,"abstracts_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["find_conf_manuscripts_deadline"] = @mysql_result($result,$i,"manuscripts_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["find_conf_camera_ready_deadline"] = @mysql_result($result,$i,"camera_ready_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["find_conf_preferencies_deadline"] = @mysql_result($result,$i,"preferencies_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["find_conf_reviews_deadline"] = @mysql_result($result,$i,"reviews_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)

			//convert all NULL of the $uvalues to '-'
			$cvalues = convert_ar_vals($cvalues, "NULL", "*unspecified*");

			### for all the different conference deadlines, load the appropriate deadline values to sessions
			//for conference deadline
			$cvalues["g_" . "find_conf_deadline"] = load_conference_dates_to_array($cvalues["find_conf_deadline"], "find_conf_deadline");
			//for papers deadline
			$cvalues["g_" . "find_conf_abstracts_deadline"] = load_conference_dates_to_array($cvalues["find_conf_abstracts_deadline"], "find_conf_abstracts_deadline");
			//for manuscripts deadline
			$cvalues["g_" . "find_conf_manuscripts_deadline"] = load_conference_dates_to_array($cvalues["find_conf_manuscripts_deadline"], "find_conf_manuscripts_deadline");
			//for camera-ready deadline
			$cvalues["g_" . "find_conf_camera_ready_deadline"] = load_conference_dates_to_array($cvalues["find_conf_camera_ready_deadline"], "find_conf_camera_ready_deadline");
			//for preferencies deadline
			$cvalues["g_" . "find_conf_preferencies_deadline"] = load_conference_dates_to_array($cvalues["find_conf_preferencies_deadline"], "find_conf_preferencies_deadline");
			//find reviews deadline
			$cvalues["g_" . "find_conf_reviews_deadline"] = load_conference_dates_to_array($cvalues["find_conf_reviews_deadline"], "find_conf_reviews_deadline");

		}//for
	}//else

	@mysql_close();//closes the connection to the DB
?>
