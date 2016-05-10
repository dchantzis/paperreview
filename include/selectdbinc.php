<?php
###############################################################
/*
view_user_info($user_id,$redirect_to),
load_conference_options_to_table($conference_id),
display_conferences()
*/
###############################################################

#################################
#################################
#################################
/*
	THIS FILE CONTAINS SOME OLD AND UNUSED FUNCTIONS
*/
#################################
#################################
#################################


function view_user_info($user_id,$redirect_to)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("view_user_info()","selectdbinc.php",125,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("view_user_info()","selectdbinc.php",126,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, fname, lname, email, address_01, address_02, address_03, city, country, phone_01, phone_02, fax, website FROM user WHERE id='" . $user_id . "';";
	$result = @mysql_query($query) or dbErrorHandler("view_user_info()","selectdbinc.php",130,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		//unreachable statement
	}//if

	for($i=0; $i<$num; $i++)
	{
		$db_temp = mysql_result($result,$i,"id");

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

		//convert all NULL of the $uvalues to '-'
		$uvalues = convert_ar_vals($uvalues, "NULL", "*unspecified*");

		//save all values of $uvalues to sessions of the same name
		reset ($uvalues);
		while(list($key, $val) = each ($uvalues))
		{
			$_SESSION[$key] = strtolower($val);
		}//while
	}//for

	@mysql_close();//closes the connection to the DB

	if($redirect_to == "") {Redirects(20,"","");}
	elseif($redirect_to == "again") {Redirects(28,"","");}
}//view_user_info

//In order for this function to work, the function load_conference_options($conference_id)
//has to be called to fill the sessions
function load_conference_options_to_table()
{
	//$_SESSION["CIA"]//Conference is active ==> CODE: CIA
	//$_SESSION["ASA"]//Let authors submit abstracts  ==> CODE: ASA
	//$_SESSION["AUA"]//Let authors update abstracts  ==> CODE: AUA
	//$_SESSION["ASM"]//Let authors submit manuscripts  ==> CODE: ASM
	//$_SESSION["AUM"]//Let authors update manuscripts  ==> CODE: AUM
	//$_SESSION["ASCRP"]//Let authors submit camera_ready papers  ==> CODE: ASCRP
	//$_SESSION["AUCRP"]//Let authors update camera_ready papers  ==> CODE: AUCRP
	//$_SESSION["AVP"]//Let authors view reviews for their papers  ==> CODE: AVP
	//$_SESSION["ACR"]//Let authors enter conflicts with reviewers ==> CODE: ACR
	//$_SESSION["NORPC"]//How many reviewers for each paper in this conference?  ==> CODE: NORPC
	//$_SESSION["RELIC"]//Let reviewer view papers and enter level of interest and conflicts  ==> CODE: RELIC
	//$_SESSION["RDPR"]//Let reviewer download his assigned papers and review them  ==> CODE: RDPR
	//$_SESSION["RVRP"]//Let reviewer view reviews of his assigned papers by other reviewers  ==> CODE: RVRP
	//$_SESSION["UVP"]//Let users view all conference papers. ==> CODE: UVP
	//$_SESSION["UDP"]//Let users download all conference papers.(manuscripts and camera-ready versions). ==> CODE: UDP
	//$_SESSION["UVAP"]//Let users view ONLY the accepted papers. ==> CODE: UVAP
	//$_SESSION["UDAP"]//Let users download ONLY the accepted papers (only camera-ready versions ==> CODE: UDAP

	//these conference options refer to the authors
	$conference_options["author"]["ASA"]=$_SESSION["ASA"];
	$conference_options["author"]["AUA"]=$_SESSION["AUA"];
	$conference_options["author"]["ASM"]=$_SESSION["ASM"];
	$conference_options["author"]["AUM"]=$_SESSION["AUM"];
	$conference_options["author"]["ASCRP"]=$_SESSION["ASCRP"];
	$conference_options["author"]["AUCRP"]=$_SESSION["AUCRP"];
	$conference_options["author"]["AVP"]=$_SESSION["AVP"];
	$conference_options["author"]["ACR"]=$_SESSION["ACR"];
	//these conference options are used for both authors and reviewers
	$conference_options["author"]["UVP"]=$_SESSION["UVP"];
	$conference_options["author"]["UDP"]=$_SESSION["UDP"];
	$conference_options["author"]["UVAP"]=$_SESSION["UVAP"];
	$conference_options["author"]["UDAP"]=$_SESSION["UDAP"];

	//these conference options refer to the reviewers
	$conference_options["reviewer"]["RELIC"]=$_SESSION["RELIC"];
	$conference_options["reviewer"]["RDPR"]=$_SESSION["RDPR"];
	$conference_options["reviewer"]["RVRP"]=$_SESSION["RVRP"];
	//these conference options are used for both authors and reviewers
	$conference_options["reviewer"]["UVP"]=$_SESSION["UVP"];
	$conference_options["reviewer"]["UDP"]=$_SESSION["UDP"];
	$conference_options["reviewer"]["UVAP"]=$_SESSION["UVAP"];
	$conference_options["reviewer"]["UDAP"]=$_SESSION["UDAP"];

	//these conference options refer to the chairmen
	$conference_options["chairman"]["CIA"] =$_SESSION["CIA"];
	$conference_options["chairman"]["NORPC"]=$_SESSION["NORPC"];


	return $conference_options;
}//load_conference_options_to_table($conference_id)

//function that displays all the conferences titles along with their dates of creation
function display_conferences()
{

	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
		or dbErrorHandler("display_conferences()","selectdbinc.php",237,"Unable to connect to SQL as administrator");
	@mysql_select_db($database) or dbErrorHandler("display_conferences()","selectdbinc.php",238,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, name, date_of_creation"
			. " FROM conference ORDER BY date_of_creation ASC;";
	$result = @mysql_query($query) or dbErrorHandler("display_conferences()","selectdbinc.php",243,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		//echo "<div class=\"text\">empty</div>";
	}//if

	echo "\n\t\t\t<ul id=\"conferencelist\">";
	for($i=0; $i<$num; $i++)
	{
		$db_id = mysql_result($result,$i,"id");
		$db_name = mysql_result($result,$i,"name");
		$db_date_of_creation = mysql_result($result,$i,"date_of_creation");

		echo "\n\t\t\t\t<li>";
		echo "<div onclick=\"load_hidden_field('" . $db_id . "', '" . $db_name . "');\" title=\"Created: " . $db_date_of_creation . "\">" . $db_name . "</div>";
		echo "\n\t\t\t\t</li>";
	}//for
	echo "\n\t\t\t</ul>";
	@mysql_close();//closes the connection to the DB

}//display_conferences

?>
