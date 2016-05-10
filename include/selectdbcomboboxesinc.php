<?php
###############################################################
/*
	conference_combo_box(),
	user_combo_box(),
	load_unassigned_conf_chairmen(),
	unassigned_conf_chairmen_combo_box(),
	load_unassigned_conf_reviewers(),
	unassigned_conf_reviewers_combo_box()
	load_conferences(),
	conf_combo_box(),
	load_papers(),
	paper_combo_box(),
	load_file_formats(),
	fileformats_combo_box(),
	candidate_paper_reviewers_combo_box($paper_id)
*/
###############################################################

//create conference_combo_box
function conference_combo_box()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("conference_combo_box()","selectdbcomboboxesinc.php",26,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("conference_combo_box()","selectdbcomboboxesinc.php",27,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//$query = "SELECT id, DATE_FORMAT(date_of_creation, GET_FORMAT(DATE,'EUR')), name FROM conference ORDER BY date_of_creation DESC";
	$query = "SELECT id, date_of_creation, name FROM conference ORDER BY date_of_creation DESC";

	$result = @mysql_query($query) or dbErrorHandler("conference_combo_box()","selectdbcomboboxesinc.php",33,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	//create the combo box

	echo "\t<select name=\"conference_name\" id=\"conference_name\" style=\"width:250px\">";
	echo "\n\t\t<option value=\"\">[CONFERENCE NAME]</option>";
	for($i=0; $i<$num; $i++)
	{
		$db_id = @mysql_result($result,$i,"id");
		//$db_date_of_creation = @mysql_result($result,$i,"DATE_FORMAT(date_of_creation, GET_FORMAT(DATE,'EUR'))");
		$db_date_of_creation = @mysql_result($result,$i,"date_of_creation");
		$db_name = @mysql_result($result,$i,"name");

		echo "\n\t\t<option value='" . $db_id . "'>";
		//echo $db_date_of_creation . " - " . $db_name;
		//echo substr($db_date_of_creation, 0, 10) . " - " . strtoupper($db_name);
		echo strtoupper($db_name);
		echo "</option>";
 	}//for
	echo "\n\t</select>\n";

	@mysql_close();//closes the connection to the DB
}//conference_combo_box

function user_combo_box()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("user_combo_box()","selectdbcomboboxesinc.php",61,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("user_combo_box()","selectdbcomboboxesinc.php",62,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, lname, fname, email FROM user ORDER BY lname ASC;";
	$result = @mysql_query($query) or dbErrorHandler("user_combo_box()","selectdbcomboboxesinc.php",66,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	//create the combo box

	echo "\n\t<select name=\"user_id\" id=\"user_id\" style=\"width:250px\">";
	echo "\n\t\t<option value=\"\">[Last Name, First Name - E-mail]</option>";
	for($i=0; $i<$num; $i++)
	{
		$db_id = @mysql_result($result,$i,"id");
		$db_lname = @mysql_result($result,$i,"lname");
		$db_fname = @mysql_result($result,$i,"fname");
		$db_email = @mysql_result($result,$i,"email");

		if($db_id == 1) { continue; }

		/*
		//for skyblue
		if($db_id == 0) { continue; }
		*/

		echo "\n\t\t<option value='" . $db_id . "'>";
		echo strtoupper($db_lname) . ", " . strtoupper($db_fname) . " - " . $db_email;
		echo "</option>";
 	}//for
	echo "\n\t</select>\n";

	@mysql_close();//closes the connection to the DB
}//user_combo_box

//load_unassigned_conf_chairmen
function load_unassigned_conf_chairmen()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
		or dbErrorHandler("load_unassigned_conf_chairmen()","selectdbcomboboxesinc.php",101,"Unable to connect to SQL as administrator");
	@mysql_select_db($database) or dbErrorHandler("load_unassigned_conf_chairmen()","selectdbcomboboxesinc.php",102,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Select all the database users, and save them in the 2D-array $uses_array
	$query01 = "SELECT id, lname, fname, email FROM user ORDER BY lname ASC";
	$result01 = @mysql_query($query01) or dbErrorHandler("load_unassigned_conf_chairmen()","selectdbcomboboxesinc.php",107,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	for($i=0; $i<$num01; $i++)
	{
		$users_array[$i]["id"] = @mysql_result($result01,$i,"id");
		$users_array[$i]["lname"] = @mysql_result($result01,$i,"lname");
		$users_array[$i]["fname"] = @mysql_result($result01,$i,"fname");
		$users_array[$i]["email"] = @mysql_result($result01,$i,"email");
 	}//for

	//Find which users are chairmen from the selected conference and store them in a 2D-array.
	$query02 = "SELECT user.id, user.fname, user.lname, user.email "
				. "FROM user, usertype "
				. "WHERE user.id = usertype.user_id "
						. "AND usertype.type = 'chairman' "
						. "AND usertype.conference_id='" . $_SESSION["conf_id"] . "' "
						. "ORDER BY (user.lname) ASC";
	$result02 = @mysql_query($query02) or dbErrorHandler("load_unassigned_conf_chairmen()","selectdbcomboboxesinc.php",125,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	for($j=0; $j<$num02; $j++)
	{
		$chairmen_array[$j]["id"] = @mysql_result($result02,$j,"id");
		$chairmen_array[$j]["lname"] = @mysql_result($result02,$j,"lname");
		$chairmen_array[$j]["fname"] = @mysql_result($result02,$j,"fname");
		$chairmen_array[$j]["email"] = @mysql_result($result02,$j,"email");
 	}//for

	//remove the chairmen from the users_array
	for($i=0; $i<count($users_array); $i++)
	{
		if($users_array[$i]["id"] == 1)
		{
			$users_array[$i] = NULL;
			continue;
		}//exclude the administrator

		for($j=0; $j<count($chairmen_array); $j++)
		{
			if($chairmen_array[$j]["id"] == $users_array[$i]["id"])
			{
				$users_array[$i] = NULL;
				break;
			}//exclude the users that are already chairmen for this conference
		}//for $j
	}//for $i

	while (list($key, $val) = each ($users_array))
	{
		if($users_array[$key] == NULL) {continue;}
		$unassigned_chairmen[$key] = $users_array[$key];
	}//

	//save the unassigned chairmen to a session
	$_SESSION["UNASSIGNED_CHAIRMEN"] = $unassigned_chairmen;

	@mysql_close();//closes the connection to the DB

}//load_unassigned_conf_chairmen()

//This function creates the unassigned chairmen combo box.
//It is to be used with function load_unassigned_conf_chairmen.
function unassigned_conf_chairmen_combo_box()
{
	if (!isset($_SESSION["UNASSIGNED_CHAIRMEN"]))
	{
		load_unassigned_conf_chairmen();
	}

	if (count($_SESSION["UNASSIGNED_CHAIRMEN"]) == 0)
	{
		echo "\n\t<select name=\"user_id\" id=\"user_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[Last Name, First Name - E-mail]</option>";
		echo "\n\t</select>\n";
	}//
	else
	{
		reset($_SESSION["UNASSIGNED_CHAIRMEN"]);
		echo "\n\t<select name=\"user_id\" id=\"user_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[Last Name, First Name - E-mail]</option>";
		while (list($key, $val) = each ($_SESSION["UNASSIGNED_CHAIRMEN"]))
		{
			echo "\n\t\t<option value='" . $_SESSION["UNASSIGNED_CHAIRMEN"][$key]["id"] . "'>";
			echo strtoupper($_SESSION["UNASSIGNED_CHAIRMEN"][$key]["lname"]) . ", " . strtoupper($_SESSION["UNASSIGNED_CHAIRMEN"][$key]["fname"]) . " - " . $_SESSION["UNASSIGNED_CHAIRMEN"][$key]["email"];
			echo "</option>";
		}//
		echo "\n\t</select>\n";
	}
}//unassigned_conf_chairmen_combo_box()

//load_unassigned_conf_reviewers
function load_unassigned_conf_reviewers()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$reviewers_array = array();

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_unassigned_conf_reviewers()","selectdbcomboboxesinc.php",204,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_unassigned_conf_reviewers()","selectdbcomboboxesinc.php",205,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Select all the database users, and save them in the 2D-array $uses_array
	$query01 = "SELECT id, lname, fname, email FROM user ORDER BY lname ASC";
	$result01 = @mysql_query($query01) or dbErrorHandler("load_unassigned_conf_reviewers()","selectdbcomboboxesinc.php",210,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	for($i=0; $i<$num01; $i++)
	{
		$users_array[$i]["id"] = @mysql_result($result01,$i,"id");
		$users_array[$i]["lname"] = @mysql_result($result01,$i,"lname");
		$users_array[$i]["fname"] = @mysql_result($result01,$i,"fname");
		$users_array[$i]["email"] = @mysql_result($result01,$i,"email");
 	}//for

	//Find which users are reviewers from the selected conference and store them in a 2D-array.
	$query02 = "SELECT user.id, user.fname, user.lname, user.email "
				. "FROM user, usertype "
				. "WHERE user.id = usertype.user_id "
						. "AND usertype.type = 'reviewer' "
						. "AND usertype.conference_id='" . $_SESSION["conf_id"] . "' "
						. "ORDER BY (user.lname) ASC";
	$result02 = @mysql_query($query02) or dbErrorHandler("load_unassigned_conf_reviewers()","selectdbcomboboxesinc.php",228,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num02 = @mysql_num_rows($result02);//num

	for($j=0; $j<$num02; $j++)
	{
		$reviewers_array[$j]["id"] = @mysql_result($result02,$j,"id");
		$reviewers_array[$j]["lname"] = @mysql_result($result02,$j,"lname");
		$reviewers_array[$j]["fname"] = @mysql_result($result02,$j,"fname");
		$reviewers_array[$j]["email"] = @mysql_result($result02,$j,"email");
 	}//for

	//remove the reviewers from the users_array
	for($i=0; $i<count($users_array); $i++)
	{
		if($users_array[$i]["id"] == 1)
		{
			$users_array[$i] = NULL;
			continue;
		}//exclude the administrator

		for($j=0; $j<count($reviewers_array); $j++)
		{
			if($reviewers_array[$j]["id"] == $users_array[$i]["id"])
			{
				$users_array[$i] = NULL;
				break;
			}//exclude the users that are already reviewers for this conference
		}//for $j
	}//for $i


	while (list($key, $val) = each ($users_array))
	{
		if($users_array[$key] == NULL) {continue;}
		$unassigned_reviewers[$key] = $users_array[$key];
	}//

	//save the unassigned reviewers to a session
	$_SESSION["UNASSIGNED_REVIEWERS"] = $unassigned_reviewers;

	@mysql_close();//closes the connection to the DB

}//load_unassigned_conf_reviewers()


//This function creates the unassigned reviewers combo box.
//It is to be used with function load_unassigned_reviewers_chairmen.
function unassigned_conf_reviewers_combo_box()
{
	if (!isset($_SESSION["UNASSIGNED_REVIEWERS"]))
	{
		load_unassigned_conf_reviewers();
	}

	if (count($_SESSION["UNASSIGNED_REVIEWERS"]) == 0)
	{
		echo "\n\t<select name=\"user_id\" id=\"user_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[Last Name, First Name - E-mail]</option>";
		echo "\n\t</select>\n";
	}//
	else
	{
		reset($_SESSION["UNASSIGNED_REVIEWERS"]);
		echo "\n\t<select name=\"user_id\" id=\"user_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[Last Name, First Name - E-mail]</option>";
		while (list($key, $val) = each ($_SESSION["UNASSIGNED_REVIEWERS"]))
		{
			echo "\n\t\t<option value='" . $_SESSION["UNASSIGNED_REVIEWERS"][$key]["id"] . "'>";
			echo strtoupper($_SESSION["UNASSIGNED_REVIEWERS"][$key]["lname"]) . ", " . strtoupper($_SESSION["UNASSIGNED_REVIEWERS"][$key]["fname"]) . " - " . $_SESSION["UNASSIGNED_REVIEWERS"][$key]["email"];
			echo "</option>";
		}//
		echo "\n\t</select>\n";
	}//
}//unassigned_conf_reviewers_combo_box()


//this function loads all the conferences
//to an array. This array is later saved in ONE session variable.
//We use this function to load the conferences for the conference combo boxes
function load_conferences()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conf_array = array();

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_conferences()","selectdbcomboboxesinc.php",312,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_conferences()","selectdbcomboboxesinc.php",313,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, name, date_of_creation, alias, "
					. "deadline, abstracts_deadline, manuscripts_deadline, "
					. "camera_ready_deadline, preferencies_deadline, reviews_deadline "
				. "FROM conference "
				. "ORDER BY date_of_creation DESC";

	$result = @mysql_query($query) or dbErrorHandler("load_conferences()","selectdbcomboboxesinc.php",322,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	for($i=0; $i<$num; $i++)
	{
		$conf_array[$i]["id"] = @mysql_result($result,$i,"id");
		$conf_array[$i]["name"] = @mysql_result($result,$i,"name");
		$conf_array[$i]["alias"] = @mysql_result($result,$i,"alias");
		$conf_array[$i]["date_of_creation"] = @mysql_result($result,$i,"date_of_creation");
		$conf_array[$i]["deadline"] = @mysql_result($result,$i,"deadline");
		$conf_array[$i]["abstracts_deadline"] = @mysql_result($result,$i,"abstracts_deadline");
		$conf_array[$i]["manuscripts_deadline"] = @mysql_result($result,$i,"manuscripts_deadline");
		$conf_array[$i]["camera_ready_deadline"] = @mysql_result($result,$i,"camera_ready_deadline");
		$conf_array[$i]["preferencies_deadline"] = @mysql_result($result,$i,"preferencies_deadline");
		$conf_array[$i]["reviews_deadline"] = @mysql_result($result,$i,"reviews_deadline");
	}//for

	//save conferences array to session
	$_SESSION["CONFERENCES"] = $conf_array;

	@mysql_close();//closes the connection to the DB
}//load_conferences

//This function creates the conference combo box.
//It is to be used with function load_conferences.
//It's different than function "conference_combo_box()"
function conf_combo_box()
{
	if (!isset($_SESSION["CONFERENCES"]))
	{
		load_conferences();
	}

	if (count($_SESSION["CONFERENCES"]) == 0)
	{
		echo "\t<select name=\"conference_name\" id=\"conference_name\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[CONFERENCE NAME]</option>";
		echo "\n\t</select>\n";
	}//
	else
	{
		reset($_SESSION["CONFERENCES"]);
		echo "\t<select name=\"conference_name\" id=\"conference_name\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[CONFERENCE NAME]</option>";
		while (list($key, $val) = each ($_SESSION["CONFERENCES"]))
		{
			echo "\n\t\t<option value='" . $_SESSION["CONFERENCES"][$key]["id"] . "'>";
			//echo substr($_SESSION["CONFERENCES"][$key]["date_of_creation"], 0, 10) . " - " . $_SESSION["CONFERENCES"][$key]["name"];
			echo $_SESSION["CONFERENCES"][$key]["name"];
			echo "</option>";
		}//
		echo "\n\t</select>\n";
	}//
}//conf_combo_box()

//this function loads all the papers (title, id, submition date)
//to an array. This array is later saved in ONE session variable.
//We use this function to load the papers for the paper combo boxes.
//this function loads the papers of the logged-in user BUT for one conference(the one he is logged in for)
function load_papers()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$papers_array = array();

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_papers()","selectdbcomboboxesinc.php",385,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_papers()","selectdbcomboboxesinc.php",386,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, submition_date, title FROM paper "
			. " WHERE user_id = " . $_SESSION["logged_user_id"]
			. " AND conference_id = " . $_SESSION["conf_id"]
			. " ORDER BY submition_date DESC ;";

	$result = @mysql_query($query) or dbErrorHandler("load_papers()","selectdbcomboboxesinc.php",394,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	if($num == 0)
	{
		/*
			$papers_array[0]["id"] = "";
			$papers_array[0]["submition_date"] = "";
			$papers_array[0]["title"] = "";
		*/
	}//if
	else if ($num != 0)
	{
		for($i=0; $i<$num; $i++)
		{
			$papers_array[$i]["id"] = @mysql_result($result,$i,"id");
			$papers_array[$i]["submition_date"] = @mysql_result($result,$i,"submition_date");
			$papers_array[$i]["title"] = @mysql_result($result,$i,"title");
		}//for
	}//else

	//save papers array to session
	$_SESSION["PAPERS"] = $papers_array;

	@mysql_close();//closes the connection to the DB
}//load_papers()

//This function creates the papers combo box.
//It is to be used with function load_papers.
function paper_combo_box()
{
	if (!isset($_SESSION["PAPERS"]))
	{
		load_papers();
	}

	if (count($_SESSION["PAPERS"]) == 0)
	{
		echo "\t<select name=\"paper_id\" id=\"paper_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[PAPER NAME]</option>";
		echo "\n\t</select>\n";
	}//
	else
	{
		reset($_SESSION["PAPERS"]);
		echo "\t<select name=\"paper_id\" id=\"paper_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[PAPER NAME]</option>";
		while (list($key, $val) = each ($_SESSION["PAPERS"]))
		{
			echo "\n\t\t<option value='" . $_SESSION["PAPERS"][$key]["id"] . "'>";
			//echo substr($_SESSION["PAPERS"][$key]["submition_date"], 0, 10) . " - " . $_SESSION["PAPERS"][$key]["title"];
			echo $_SESSION["PAPERS"][$key]["title"];
			echo "</option>";
		}//
		echo "\n\t</select>\n";
	}//
}//paper_combo_box()

//this function loads all the file formats (that where inserted by the system administrator)
// and where already selected for the confernece to an array.
//This array is later saved in ONE session variable.
//We use this function to load the file formats for the fileformat combo box featured
//in the page 'chairman_file_formats'.
function load_unselected_file_formats()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$unselected_reviewers = array();

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_unselected_file_formats()","selectdbcomboboxesinc.php",464,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_unselected_file_formats()","selectdbcomboboxesinc.php",465,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Select all the file formats and save them in the 2D array '$fileformats_array'
	$query01 = "SELECT id, extension, description, mime_type FROM fileformat ORDER BY extension ASC";

	$result01 = @mysql_query($query01) or dbErrorHandler("load_unselected_file_formats()","selectdbcomboboxesinc.php",471,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	for($i=0; $i<$num01; $i++)
	{
		$fileformats_array[$i]["id"] = @mysql_result($result01,$i,"id");
		$fileformats_array[$i]["extension"] = @mysql_result($result01,$i,"extension");
		$fileformats_array[$i]["description"] = @mysql_result($result01,$i,"description");
		$fileformats_array[$i]["mime_type"] = @mysql_result($result01,$i,"mime_type");
 	}//for

	//Find which file formats are already selected for this confernece and store them in a 2D-array.
	$query02 = "SELECT fileformat.id, fileformat.extension, fileformat.description, fileformat.mime_type "
				. "FROM fileformat, fileformattoconference "
				. "WHERE fileformat.id = fileformattoconference.format_id "
					. "AND fileformattoconference.conference_id='" . $_SESSION["conf_id"] . "' ORDER BY (fileformat.extension) ASC";
	$result02 = @mysql_query($query02) or dbErrorHandler("load_unselected_file_formats()","selectdbcomboboxesinc.php",487,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	for($j=0; $j<$num02; $j++)
	{
		$selected_fileformats_array[$j]["id"] = @mysql_result($result02,$j,"id");
		$selected_fileformats_array[$j]["extension"] = @mysql_result($result02,$j,"extension");
		$selected_fileformats_array[$j]["description"] = @mysql_result($result02,$j,"description");
		$selected_fileformats_array[$j]["mime_type"] = @mysql_result($result02,$j,"mime_type");
 	}//for

	//remove the reviewers from the users_array
	for($i=0; $i<count($fileformats_array); $i++)
	{
		for($j=0; $j<count($selected_fileformats_array); $j++)
		{
			if($selected_fileformats_array[$j]["id"] == $fileformats_array[$i]["id"])
			{
				$fileformats_array[$i] = NULL;
				break;
			}//
		}//for $j
	}//for $i

	while (list($key, $val) = each ($fileformats_array))
	{
		if($fileformats_array[$key] == NULL) {continue;}
		$unselected_reviewers[$key] = $fileformats_array[$key];
	}//

	//save conferences array to session
	$_SESSION["UNSELECTED_FILEFORMATS"] = $unselected_reviewers;

	@mysql_close();//closes the connection to the DB
}//load_unselected_file_formats()


//This function creates the fileformats combo box.
//It is to be used with function load_unselected_file_formats().
function unselected_fileformats_combo_box()
{
	if (!isset($_SESSION["UNSELECTED_FILEFORMATS"]))
	{
		load_unselected_file_formats();
	}

	if (count($_SESSION["UNSELECTED_FILEFORMATS"]) == 0)
	{
		echo "\t<select name=\"format_id\" id=\"format_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[File Format - Mime Type]</option>";
		echo "\n\t</select>\n";
	}//
	else
	{
		reset($_SESSION["UNSELECTED_FILEFORMATS"]);
		echo "\t<select name=\"format_id\" id=\"format_id\" style=\"width:250px\">";
		echo "\n\t\t<option value=\"\">[File Format - Mime Type]</option>";
		while (list($key, $val) = each ($_SESSION["UNSELECTED_FILEFORMATS"]))
		{
			echo "\n\t\t<option value='" . $_SESSION["UNSELECTED_FILEFORMATS"][$key]["id"] . "'>";
			echo strtoupper($_SESSION["UNSELECTED_FILEFORMATS"][$key]["extension"]) . " - " . $_SESSION["UNSELECTED_FILEFORMATS"][$key]["mime_type"];
			echo "</option>";
		}//
		echo "\n\t</select>\n";
	}//
}//unselected_fileformats_combo_box()

//this function is called inside the function 'load_assigned_papers()'
//for the page view_assignments.php
//$user_id --> if this is not NULL, then load the combo box with this user name selected
function candidate_paper_reviewers_combo_box($paper_id,$user_id,$combo_box_no)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("candidate_paper_reviewers_combo_box()","selectdbcomboboxesinc.php",562,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("candidate_paper_reviewers_combo_box()","selectdbcomboboxesinc.php",563,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query02 = "SELECT user.id, user.fname, user.lname, interest.level_of_interest, interest.conflict, interest.conflict_by_author "
				. "FROM user, usertype, interest "
				. "WHERE user.id = usertype.user_id AND usertype.user_id = interest.user_id "
					. "AND usertype.type = 'reviewer' "
					. "AND usertype.conference_id='" . $_SESSION["conf_id"] . "' "
					. "AND interest.paper_id='" . $paper_id . "' ORDER BY interest.level_of_interest DESC;";
	$result02 = @mysql_query($query02) or dbErrorHandler("candidate_paper_reviewers_combo_box()","selectdbcomboboxesinc.php",572,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num02

	//fill the array $paper_to_reviewers
	for($i=0; $i<$num02; $i++)
	{
		//ALL OK
		//$paper_id = mysql_result($result02,$i,"paper_id");
		//$user_id = mysql_result($result02,$i,"id");

		//we want to exclude the reviewers that have conflicts with the author(s) of the paper.
		if(mysql_result($result02,$i,"conflict") == 1){ continue; }

		$paper_to_reviewers[$i]["reviewer_id"] = mysql_result($result02,$i,"id");
		$paper_to_reviewers[$i]["lname"] = mysql_result($result02,$i,"lname");
		$paper_to_reviewers[$i]["fname"] = mysql_result($result02,$i,"fname");
		$paper_to_reviewers[$i]["level_of_interest"] = mysql_result($result02,$i,"level_of_interest");

		if(mysql_result($result02,$i,"conflict_by_author") == 0) {$paper_to_reviewers[$i]["conflict_by_author"] = "NO";}
		elseif(mysql_result($result02,$i,"conflict_by_author") == 1){$paper_to_reviewers[$i]["conflict_by_author"] = "YES";}

		if($paper_to_reviewers[$i]["reviewer_id"] == $user_id) {
			$assigned_rev_id = $paper_to_reviewers[$i]["reviewer_id"];
			$assigned_rev_lname = $paper_to_reviewers[$i]["lname"];
			$assigned_rev_fname = $paper_to_reviewers[$i]["fname"];
			$assigned_rev_level_of_interest = $paper_to_reviewers[$i]["level_of_interest"];
			$assigned_rev_conflict_by_author = $paper_to_reviewers[$i]["conflict_by_author"];
		}
	}//for

	echo "\t<select name=\"" . $paper_id . "-" . $combo_box_no . "\" id=\"\" style=\"width:300px\">";
	//echo the selected reviewer
	if($user_id != "")
	{
		echo "\n\t\t<option value='" . $assigned_rev_id . "' >";
		echo "<b>" . $assigned_rev_level_of_interest . "</b> - ";
		echo strtoupper($assigned_rev_lname) . " " . strtoupper($assigned_rev_fname) . " - ";
		echo "<b>" . $assigned_rev_conflict_by_author . "</b> - ";
		echo "</option>";
		echo "\n\t\t<option value=\"\"></option>";
	}
	else
	{
		echo "\n\t\t<option value=\"\">[Interest - Name - (No of Papers) - Author Conflict]</option>";
	}

	//for($j=0; $j<count($paper_to_reviewers); $j++)
	while (list($j, $val) = each ($paper_to_reviewers))
	{
		echo "\n\t\t<option value='" . $paper_to_reviewers[$j]["reviewer_id"] . "' >";
		echo "<b>" . $paper_to_reviewers[$j]["level_of_interest"] . "</b> - ";
		echo strtoupper($paper_to_reviewers[$j]["lname"]) . " " . strtoupper($paper_to_reviewers[$j]["fname"]) . " - ";
		echo "<b>" . $paper_to_reviewers[$j]["conflict_by_author"] . "</b> - ";
		echo "</option>";
	}
	echo "\n\t</select>\n";

//echo count($paper_to_reviewers);

	@mysql_close();//closes the connection to the DB

}//candidate_paper_reviewers_combo_box($paper_id)
?>
