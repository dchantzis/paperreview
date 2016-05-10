<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	###################################################################################

	whereUgo(0);
	whereUgo(1);

	global 	$pvalues;

	//load DataBase info
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	######################
	//check if the $_GET table has only the value we want,
	//and the value is of the type we want
	//returns the value we want trimmed
	//$paper_id = checkGetVariable("paperid",0,"([^0-9]+)");
	if(!isset($_GET["paperid"])){ header("Location: ./ulounge.php"); exit;}
	$get_var_type["paperid"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$paper_id = $validated_vars["paperid"];
	######################

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("findpaperinc.php","",31,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("findpaperinc.php","",32,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT paper.id, user.id, user.fname, user.lname, paper.title, paper.authors, paper.subject, paper.abstract "
		. " FROM paper, user WHERE user.id = paper.user_id AND paper.id='" . $paper_id . "' AND conference_id='" . $_SESSION["conf_id"] . "';";
	$result = @mysql_query($query) or dbErrorHandler("findpaperinc.php","",36,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		//echo "ERROR! A paper with this id doesn't exist.";
		Redirects(0,"","");
	}//if
	else{
		//store all the DB values in array $cvalues
		$pvalues["find_paper_id"] = mysql_result($result,0,"id");
		$pvalues["find_user_id"] = mysql_result($result,0,"id");
		$pvalues["find_user_fname"] = mysql_result($result,0,"fname");
		$pvalues["find_user_lname"] = mysql_result($result,0,"lname");
		$pvalues["find_paper_title"] = stripslashes(mysql_result($result,0,"title"));
		$pvalues["find_paper_authors"] = mysql_result($result,0,"authors");
		$pvalues["find_paper_subject"] = mysql_result($result,0,"subject");
		$pvalues["find_paper_abstract"] = stripslashes(mysql_result($result,0,"abstract"));

		//convert all NULL of the $uvalues to '-'
		$pvalues = convert_ar_vals($pvalues, "NULL", "*unspecified*");

	}//else

	@mysql_close();//closes the connection to the DB

?>
