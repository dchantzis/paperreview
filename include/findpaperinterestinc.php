<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	###################################################################################

	whereUgo(0);
	whereUgo(1);

	global 	$interest_values;

	//load DataBase info
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("findpaperinterestinc.php","",18,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("findpaperinterestinc.php","",19,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT level_of_interest, conflict "
			. " FROM interest "
			. " WHERE conference_id='" . $_SESSION["conf_id"] . "' AND paper_id = '" . $paper_id . "' AND user_id = '" . $_SESSION["logged_user_id"] . "' ";
	$result = @mysql_query($query) or dbErrorHandler("findpaperinc.php","",36,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		//No interest level for this paper yet.
		Redirects(0,"","");
	}//if
	else{
		for($i=0; $i<$num; $i++)
		{
			//store all the DB values in array $cvalues
			$interest_values["level_of_interest"] = mysql_result($result,$i,"level_of_interest");
			$interest_values["conflict"] = mysql_result($result,$i,"conflict");

			//convert all NULL of the $uvalues to '-'
			$interest_values = convert_ar_vals($interest_values, "NULL", "*unspecified*");

		}//for
	}//else
	@mysql_close();//closes the connection to the DB
?>
