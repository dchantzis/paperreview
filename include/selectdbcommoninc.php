<?php
##################################################
################selectdbcommoninc.php###########
##################################################
/*
This file includes all the functions that include
select queries to the DataBase, that refer to all
the users of the system.
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
display_announcements($options),
search_conference($action),
show_conference_participants($user_type,$conference_id,$option),
display_users($limitPerPage,$search_lname),
display_login_conferences(),
display_reviews($user_type),
show_conference_file_formats($user_type),
find_user_data(),
display_deadlines(),
load_all_conferences($conf_status),
display_sorted_conferences($active_c_ar,$expired_c_ar),
display_conference_options_for_each_user($conference_options,$user_type),
find_user_conference_participation($user_id,$user_type),
load_conference_options(),
display_all_papers($user_type),
display_all_papers_allusers(),
find_user()
*/


function display_announcements($options)
{

	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$startLimit = "";
	$numberOfRows = "";
	$limitQuery = "";

	//display just the first five announcements
	if($options != "1")//1 is for "show_all"
	{
		$startLimit = 0;
		$numberOfRows = 5;

		$limitQuery = " LIMIT ".$startLimit.",".$numberOfRows;
	}

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_announcements()","selectdbcommoninc.php",54,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_announcements()","selectdbcommoninc.php",55,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$i = "";
	$j = "";

	if(!isset($_SESSION["administrator"]))
	{
		$j = " AND announcement.conference_id=" . $_SESSION["conf_id"] . " ";
	}
	elseif(isset($_SESSION["administrator"])){ $i = " 1=1 "; }

	//create the query
	if((isset($_SESSION["author"]) && ( isset($_SESSION["reviewer"]) || isset($_SESSION["chairman"]) )) || !isset($_SESSION["administrator"]) )
	{
		$i = $i . " regardsauthors = 1 OR";
	}
	elseif(isset($_SESSION["author"])) { $i = $i . " regardsauthors = 1 "; }

	if((isset($_SESSION["reviewer"]) && ( isset($_SESSION["author"]) || isset($_SESSION["chairman"]) )) || !isset($_SESSION["administrator"]) )
	{
		$i = $i . " regardsreviewers = 1 OR";
	}
	elseif(isset($_SESSION["reviewer"]))
	{
		$i = $i . " regardsreviewers = 1 ";
	}

	if(( isset($_SESSION["chairman"])) || !isset($_SESSION["administrator"]))
	{
		$i = $i . " regardschairmen = 1";
	}

	//append the query specifications to the rest of the query
	$query = "SELECT conference.name, announcement.conference_id, announcement.user_id, announcement.id, user.fname, user.lname, user.email, "
		. "announcement.message, announcement.post_date, announcement.regardschairmen, "
		. "announcement.regardsreviewers, announcement.regardsauthors "
		. "FROM announcement, user, conference "
		. "WHERE announcement.conference_id=conference.id AND announcement.user_id = user.id  " . $j . " AND (" . $i . ") ORDER BY announcement.post_date DESC " . $limitQuery;
	$result = @mysql_query($query) or dbErrorHandler("display_announcements()","selectdbcommoninc.php",94,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);

	if($num == 0)
	{
		echo"<div class=\"required\" align=\"center\">" .  strtoupper("Currently there are no announcements for this conference.") . "</span>";
	}

	for($i=0; $i<$num; $i++)
	{
		$db_conference_name = mysql_result($result,$i,"conference.name");
		$db_conference_id = mysql_result($result,$i,"announcement.conference_id");
		$db_user_id = mysql_result($result,$i,"announcement.user_id");
		$db_announcement_id = mysql_result($result,$i,"announcement.id");
		$db_fname = mysql_result($result,$i,"user.fname");
		$db_lname = mysql_result($result,$i,"user.lname");
		$db_email = mysql_result($result,$i,"user.email");
		$db_message= mysql_result($result,$i,"announcement.message");
		$db_post_date = mysql_result($result,$i,"announcement.post_date");
		$db_regardschairmen = mysql_result($result,$i,"announcement.regardschairmen");
		$db_regardsreviewers = mysql_result($result,$i,"announcement.regardsreviewers");
		$db_regardsauthors = mysql_result($result,$i,"announcement.regardsauthors");

		echo "<div class=\"announcement\">";

		echo "<div class=\"info\" title=\"posted on\">On " . substr($db_post_date, 0, 10) . " at " . substr($db_post_date, 11,5) . "</div>";

		if ($db_user_id == 1)
		{
		//poster is the administrator
			echo "<div class=\"info\" title=\"poster\">, <a href=\"#\">" . $db_fname . "</a> posted:</div>";
		}
		else { echo "<div class=\"info\" title=\"poster\">, <a href=\"./user_info.php?userid=" . $db_user_id . "\">" . $db_fname . " " . $db_lname . "</a> posted:</div>"; }

		echo "<div class=\"message\" title=\"message\"><pre>" . $db_message . "</pre></div>";

		echo "<div class=\"refers\" title=\"announcement for\">";
		//if the user is the administrator we want the conference name of the announcement to be displayed
		if(isset($_SESSION["administrator"]))
		{
			echo "<span class=\"info\">For conference: <a href=\"./conference_info.php?confid=" . $db_conference_id . "\">" . $db_conference_name . "</a></span>";
		}
		echo "This post referes to: ";
		if($db_regardschairmen == 1){ echo " <b>chairmen</b> "; }
		if($db_regardsreviewers == 1){ echo " <b>reviewers</b> "; }
		if($db_regardsauthors == 1){ echo " <b>authors</b> "; }
		echo "</div>";//refers

		echo "</div>";//announcement

	}//for

	@mysql_close();//closes the connection to the DB
}//display_announcements($options)

##################################
##################################

function search_conference($action)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	if(!isset($_POST)){ Redirects(0,"","");}


	//check for CSRF (Cross Site Request Forgery)
	if($action == "chairmen_assignments"){ $csrf_temp = hash('sha256', "chairmen_assignments") . $csrf_password_generator; }
	elseif($action == "conference_control_panel"){ $csrf_temp = hash('sha256', "conference_control_panel") . $csrf_password_generator; }
	elseif($action == "conference_papers"){ $csrf_temp = hash('sha256', "conference_papers") . $csrf_password_generator; }
	elseif($action == "conference_announcements"){ $csrf_temp = hash('sha256', "administrator_announcements") . $csrf_password_generator; }
	elseif($action == "update_conference"){ $csrf_temp = hash('sha256', "update_conference") . $csrf_password_generator; }
	elseif($action == "update_assignments"){ $csrf_temp = hash('sha256', "update_assignments") . $csrf_password_generator; }
	elseif($action == "user_login"){$_POST["csrf"] = $csrf_temp;}
	elseif($action == "users_actions_log_db"){ $csrf_temp = hash('sha256', "users_actions_log_db") . $csrf_password_generator; }
	elseif($action == "users_actions_log_file"){ $csrf_temp = hash('sha256', "users_actions_log_file") . $csrf_password_generator; }
	elseif($action == "administrator_help"){ $csrf_temp = hash('sha256', "administrator_help") . $csrf_password_generator; }
	else{Redirects(0,"","");}

	if($_POST["csrf"] != $csrf_temp){Redirects(0,"","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "conference_name"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "conference_name"=>"");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.
	$arValsValidations = array( "conference_name"=>"/^[0-9]([0-9]*)/");

	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val =="") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = $val;
		}
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
		$arVals[$key] = "'" . $arVals[$key] . "'";
	}//while
	//print_r ($arVals); //print the whole array

	if($action == "chairmen_assignments")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,23,"");//send 23 because the page we want is chairmen_assignments.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,23,"");//send 23 because the page we want is chairmen_assignments.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,23,"");
	}//if
	elseif($action == "conference_control_panel")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,16,"");//send 16 because the page we want is conference_control_panel.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,16,"");//send 16 because the page we want is conference_control_panel.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,16,"");
	}//else if
	elseif($action == "conference_papers")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,54,"");//send 54 because the page we want is display_papers_ad.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,54,"");//send 54 because the page we want is display_papers_ad.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,54,"");
	}//else if
	elseif($action == "conference_announcements")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,35,"&action=3");//send 35 because the page we want is administrator_announcements.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,35,"&action=3");//send 35 because the page we want is administrator_announcements.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,35,"");
	}//else if
	elseif($action == "update_conference")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,8,"");//send 8 because the page we want is conferences.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,8,"");//send 8 because the page we want is conferences.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,8,"");
 	}//else if
	elseif($action == "update_assignments")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,9,"&action=update_assignments");//send 9 because the page we want is choose_conference.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,9,"&action=update_assignments");//send 9 because the page we want is choose_conference.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,9,"");
	}//else if
	elseif($action == "user_login")
	{
		// check to see if these variables have been set...
		//variablesSet($arValsRequired,13,"&action=user_login");//send 13 because the page we want is login_choose_conference.php
		variablesSet($arValsRequired,0,"");
		// check if the form variables have something in them...
		//variablesFilled($arValsRequired,13,"&action=user_login");//send 13 because the page we want is login_choose_conference.php
		variablesFilled($arValsRequired,0,"");
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,0,"");
	}//else if
	elseif($action == "users_actions_log_db")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,56,"");//send 56 because the page we want is users_actions_log_db.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,56,"");//send 56 because the page we want is users_actions_log_db.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,56,"");
	}//else if
	elseif($action == "users_actions_log_file")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,57,"");//send 57 because the page we want is users_actions_log_file.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,57,"");//send 57 because the page we want is users_actions_log_file.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,57,"");
	}//else if
	elseif($action == "administrator_help")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,58,"");//send 58 because the page we want is administrator_help.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,58,"");//send 58 because the page we want is administrator_help.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,58,"");
	}//else if
	else { logout(); }

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("search_conference()","selectdbcommoninc.php",264,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("search_conference()","selectdbcommoninc.php",265,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, name, alias, place, date_conference_held, contact_email, contact_phone, website, comments, date_of_creation, "
			 . "deadline, abstracts_deadline, manuscripts_deadline, camera_ready_deadline, preferencies_deadline, reviews_deadline "
			 . "FROM conference WHERE id='" . $_POST["conference_name"] . "';";
	$result = @mysql_query($query) or dbErrorHandler("search_conference()","selectdbcommoninc.php",271,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	//create the combo box

	$i=0;
	//store all the DB values in array $cvalues
	$cvalues["id"] = @mysql_result($result,$i,"id");
	$cvalues["name"] = @mysql_result($result,$i,"name");
	$cvalues["alias"] = @mysql_result($result,$i,"alias");
	$cvalues["place"] = @mysql_result($result,$i,"place");
	$cvalues["date_conference_held"] = @mysql_result($result,$i,"date_conference_held");
	$cvalues["contact_email"] = @mysql_result($result,$i,"contact_email");
	$cvalues["contact_phone"] = @mysql_result($result,$i,"contact_phone");
	$cvalues["website"] = @mysql_result($result,$i,"website");
	$cvalues["comments"] = @mysql_result($result,$i,"comments");
	$cvalues["date_of_creation"] = @mysql_result($result,$i,"date_of_creation");

	$cvalues["deadline"] = @mysql_result($result,$i,"deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
	$cvalues["abstracts_deadline"] = @mysql_result($result,$i,"abstracts_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
	$cvalues["manuscripts_deadline"] = @mysql_result($result,$i,"manuscripts_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
	$cvalues["camera_ready_deadline"] = @mysql_result($result,$i,"camera_ready_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
	$cvalues["preferencies_deadline"] = @mysql_result($result,$i,"preferencies_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
	$cvalues["reviews_deadline"] = @mysql_result($result,$i,"reviews_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)

	unset($_SESSION["PAPERS"]);

	//depending on the value of $action, difference conference values are stored in Sessions
	if($action == "user_login" || $action == "chairmen_assignments" || $action == "conference_control_panel" || $action == "conference_announcements" || $action == "conference_papers" || $action == "users_actions_log_db" || $action == "users_actions_log_file" || $action == "administrator_help")
	{
		$_SESSION["conf_id"] = $cvalues["id"];
		$_SESSION["conf_name"] = $cvalues["name"];
		$_SESSION["conf_deadline"] = intval(substr($cvalues["deadline"], 5, 2)) . "-" . intval(substr($cvalues["deadline"], 8, 2)) . "-" . intval(substr($cvalues["deadline"], 0, 4)) ; //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
		$_SESSION["conf_date_of_creation"] = $cvalues["date_of_creation"];
	}
	elseif ($action == "update_conference")
	{
		$_SESSION["conf_id"] = $cvalues["id"];
		$_SESSION["conf_name"] = $cvalues["name"];

		//convert all NULL of the $uvalues to ' '
		$cvalues = convert_ar_vals($cvalues, "NULL", "");

		//save all values of $uvalues to sessions of the same name
		reset ($cvalues);
		while(list($key, $val) = each ($cvalues))
		{
			$_SESSION[$key] = strtolower($val);
		}//while

		### for all the different conference deadlines, load the appropriate deadline values to sessions
		//for conference deadline
		load_conference_dates_to_sessions($cvalues["deadline"], "deadline");
		//for papers deadline
		load_conference_dates_to_sessions($cvalues["abstracts_deadline"], "abstracts_deadline");
		//for manuscripts deadline
		load_conference_dates_to_sessions($cvalues["manuscripts_deadline"], "manuscripts_deadline");
		//for camera-ready deadline
		load_conference_dates_to_sessions($cvalues["camera_ready_deadline"], "camera_ready_deadline");
		//for preferencies deadline
		load_conference_dates_to_sessions($cvalues["preferencies_deadline"], "preferencies_deadline");
		//find reviews deadline
		load_conference_dates_to_sessions($cvalues["reviews_deadline"], "reviews_deadline");

		$_SESSION["updateconference"] = "yes";
	}

	@mysql_close();//closes the connection to the DB
	//if($action == "assign_conferences") { Redirects(11,"?conference_id=" . $_POST["id"] . "","");
	if($action == "chairmen_assignments"){ unset($_SESSION["UNASSIGNED_CHAIRMEN"]); Redirects(23,"","");}
	elseif($action == "update_conference") { Redirects(8,"",""); }
	elseif($action == "update_assignments") { Redirects(12,"","");}
	elseif($action == "user_login")
	{
		//unset all the combo boxes, so that they would be reloaded
		unset($_SESSION["UNASSIGNED_CHAIRMEN"]);
		unset($_SESSION["UNASSIGNED_REVIEWERS"]);
		unset($_SESSION["CONFERENCES"]);
		unset($_SESSION["PAPERS"]);
		unset($_SESSION["UNSELECTED_FILEFORMATS"]);

		find_user_type("user_login");
	}
	elseif($action == "conference_control_panel") { Redirects(16,"","");}
	elseif($action == "conference_announcements") { Redirects(35,"?action=3","");}
	elseif($action == "conference_papers") { Redirects(54,"","");}
	elseif($action == "users_actions_log_db") { Redirects(56,"?mode=1","");}
	elseif($action == "users_actions_log_file") { Redirects(57,"?mode=1","");}
	elseif($action == "administrator_help"){ Redirects(58,"",""); }

}//search_conference($action)

##################################
##################################

function show_conference_participants($user_type,$conference_id,$option)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\">";
	$str_02 = "<caption>Conference chairmen list</caption>";

	if($option != "no_remove_option")
	{
		$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"fname\">First Name</th>
		<th scope=\"col\" class=\"lname\">Last Name</th>
		<th scope=\"col\" class=\"\">E-mail</th>
		<th scope=\"col\" class=\"remove\"></th>
		</tr>\n</thead>";
	}else {
		if(isset($_SESSION["administrator"]) || isset($_SESSION["chairman"]))
		{
			//we don't want tha authors and reviewers to be able to view the users date
			//so, no 'view' option for them.
			$str_03 = "\n<thead>\n\t<tr>
				<th scope=\"col\" class=\"fname\">First Name</th>
				<th scope=\"col\" class=\"lname\">Last Name</th>
				<th scope=\"col\" class=\"\">E-mail</th>
				<th scope=\"col\" class=\"view\"></th>
				</tr>\n</thead>";
		}//if
		else
		{
			$str_03 = "\n<thead>\n\t<tr>
				<th scope=\"col\" class=\"fname\">First Name</th>
				<th scope=\"col\" class=\"lname\">Last Name</th>
				<th scope=\"col\" class=\"\">E-mail</th>
				</tr>\n</thead>";
		}//else
	}

	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("show_conference_participants()","selectdbcommoninc.php",404,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("show_conference_participants()","selectdbcommoninc.php",405,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, lname, fname, email FROM user, usertype "
			. "WHERE id = user_id AND type = '" . $user_type . "' AND conference_id = '" . $conference_id . "' ORDER BY (lname)";


	$result = @mysql_query($query) or dbErrorHandler("show_conference_participants()","selectdbcommoninc.php",412,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	echo $str_01;
	echo $str_03;
	echo $str_04;

	if($num == 0)
	{
		if($user_type == "chairman")
		{
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"4\" align=\"center\">There are no chairmen for this conference.</td>";
			echo "\n\t\t</tr>";
		}
		elseif ($user_type == "reviewer") {
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"4\" align=\"center\">There are no users in the review committee.</td>";
			echo "\n\t\t</tr>";
		}
	}//if

	for($i=0; $i<$num; $i++)
	{
		$db_id = mysql_result($result,$i,"id");
		$db_lname = mysql_result($result,$i,"lname");
		$db_fname = mysql_result($result,$i,"fname");
		$db_email = mysql_result($result,$i,"email");

		if (($i%2)==0) {
			$bgColor = "#F5F0EA";
			$trClass = "even";
		} else {
			$bgColor = "#FFFFFF";
			$trClass = "odd";
		}

		echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
		echo "<td>" . $db_fname . "</td>";
		echo "<td>" . $db_lname . "</td>";
		echo "<td><a href=\"mailto:" . $db_email . "\">" . $db_email . "</a></td>";

		if($option == "")
		{
			if($user_type == "chairman")
			{
				echo "<td class=\"tdview\"><a href=\"./include/functionsinc.php?type=11&id=" . $db_id . "\" class=\"simple\">remove</a></td>";
			}//if
			elseif($user_type = "reviewer")
			{
				echo "<td class=\"tdview\"><a href=\"./include/functionsinc.php?type=18&id=" . $db_id . "\" class=\"simple\">remove</a></td>";
			}//elseif
		}
		elseif($option == "no_remove_option")
		{
			if(isset($_SESSION["administrator"]) || isset($_SESSION["chairman"]))
			{
				echo "<td class=\"tdview\"><a href=\"./user_info.php?userid=" . $db_id . "\" class=\"simple\">view</a></td>";
			}//
			else
			{
				//authors and reviewers don't have a 'view' option, to view user_info
			}//else
		}

		echo "\n\t\t</tr>";
	}
	echo $str_05;
	echo $str_06;

	@mysql_close();//closes the connection to the DB
}//show_conference_participants($user_type,$conference_id,$option)

##################################
##################################

function display_users($limitPerPage,$search_lname)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$startLimit=$numberOfRows=$sortBy=$sortOrder = null;
	$orderByQuery = "";

	$initStartLimit = 0;
	//$limitPerPage=5;

	if(isset($_REQUEST["startLimit"])){ $startLimit = $_REQUEST["startLimit"]; }
	if(isset($_REQUEST["rows"])){ $numberOfRows = $_REQUEST["rows"]; }
	if(isset($_REQUEST["sortBy"])){ $sortBy = $_REQUEST["sortBy"]; }
	if(isset($_REQUEST["sortOrder"])){ $sortOrder = $_REQUEST["sortOrder"]; }

	if ($startLimit=="") { $startLimit = $initStartLimit; }

	if ($numberOfRows==""){ $numberOfRows = $limitPerPage; }

	if ($sortOrder==""){ $sortOrder  = "DESC"; }

	if ($sortOrder == "DESC") { $newSortOrder = "ASC"; } else  { $newSortOrder = "DESC"; }

	$limitQuery = " LIMIT ".$startLimit.",".$numberOfRows;
	$nextStartLimit = $startLimit + $limitPerPage;
	$previousStartLimit = $startLimit - $limitPerPage;

	if ($sortBy!="")
	{
		$orderByQuery = " ORDER BY ".$sortBy." ".$sortOrder;
	}

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" summary=\"system users\">";
	$str_02 = "<caption>System user list</caption>";

	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"lname\">
			<a href=" . "users_list.php" . "?sortBy=lname&sortOrder=" . $newSortOrder . "&startLimit=" . $startLimit . "&rows=" . $limitPerPage . ">Last Name</a>
		</th>
		<th scope=\"col\" class=\"fname\">
			<a href=" . "users_list.php" . "?sortBy=fname&sortOrder=" . $newSortOrder . "&startLimit=" . $startLimit . "&rows=" . $limitPerPage . ">First Name</a>
		</th>
		<th scope=\"col\" class=\"\">
			<a href=" . "users_list.php" . "?sortBy=email&sortOrder=" . $newSortOrder . "&startLimit=" . $startLimit . "&rows=" . $limitPerPage . ">E-mail</a>
		</th>
		<th scope=\"col\" class=\"view\"></th>
		</tr>\n</thead>";

	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	//if this value is empty, that means that
	//this function should display all the users of the database
	//if this value is not empty, it was filled by the find_user() function
	//during a search
	if($search_lname == "")
	{
		$str_limit_form = "<form id=\"limitfrm\" name=\"limitfrm\" method=\"post\" action = \"users_list.php\" />
				<label for=\"limitPerPage\">users per page: </label>
				<select name=\"limitPerPage\" id=\"limitPerPage\" title=\"users per page\">
					<option value=\"$limitPerPage\">$limitPerPage</option>
					<option value=\"$limitPerPage\"></option>
					<option value=\"10\">10</option><option value=\"20\">20</option>
					<option value=\"30\">30</option><option value=\"40\">40</option>
					<option value=\"50\">50</option>
				</select>
				<input type=\"submit\" value=\"GO\">
				</form>";
	}//if
	elseif($search_lname != "")
	{
		$str_limit_form = "<form id=\"limitfrm\" name=\"limitfrm\" method=\"post\" action = \"users_list.php" . "?search_lname=" . $search_lname . "\" />
			<label for=\"limitPerPage\">users per page: </label>
			<select name=\"limitPerPage\" id=\"limitPerPage\" title=\"users per page\">
				<option value=\"$limitPerPage\">$limitPerPage</option>
				<option value=\"5\">5</option><option value=\"10\">10</option>
				<option value=\"25\">25</option><option value=\"50\">50</option>
			</select>
			<input type=\"submit\" value=\"GO\">
			</form>";
	}//

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("show_conference_participants()","selectdbcommoninc.php",574,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("show_conference_participants()","selectdbcommoninc.php",575,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//if this value is empty, that means that
	//this function should display all the users of the database
	//if this value is not empty, it was filled by the find_user() function
	//during a search
	if($search_lname == "")
	{
		if(isset($_SESSION["administrator"]))
		{
			$query = "SELECT id, lname, fname, email, phone_01 FROM user".$orderByQuery.$limitQuery;
		}//
		elseif(isset($_SESSION["chairman"]))
		{
			$query = "SELECT distinct(user.id), user.lname, user.fname, user.email "
						. "FROM user, usertype "
						. "WHERE user.id = usertype.user_id  AND conference_id='" . $_SESSION["conf_id"] . "' ".$orderByQuery.$limitQuery;
		}//elseif
	}//if
	elseif($search_lname != "")
	{
		if(isset($_SESSION["administrator"]))
		{
			$query = "SELECT id, lname, fname, email, phone_01 FROM user WHERE lname LIKE '%" . $search_lname . "%'" . $orderByQuery.$limitQuery;
		}//if
		elseif(isset($_SESSION["chairman"]))
		{
			$query = "SELECT distinct(user.id), user.lname, user.fname, user.email "
					. "FROM user, usertype "
					. "WHERE user.id = usertype.user_id AND conference_id='" . $_SESSION["conf_id"] . "' AND lname LIKE '%" . $search_lname . "%'" . $orderByQuery.$limitQuery;
		}//elseif

	}//
	$result = @mysql_query($query) or dbErrorHandler("show_conference_participants()","selectdbcommoninc.php",611,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$numberOfRows = mysql_num_rows($result);//num

	echo $str_limit_form;

	echo "<div id=\"view_options\">";
	if ($numberOfRows>0) {
			$i=0;
			echo "<div id=\"previous\">";
			if ((isset($_REQUEST["startLimit"])) && ($_REQUEST["startLimit"] != "" && $_REQUEST["startLimit"] > 0))
			{
				//Previous
				echo "<a href=" . $_SERVER["PHP_SELF"] . "?startLimit=" . $previousStartLimit . "&limitPerPage=" . $limitPerPage . "&sortBy=" . $sortBy . "&sortOrder=" . $sortOrder . "><< Previous " . $limitPerPage . "</a>";
			}//
			echo "</div>";

			echo "<div id=\"next\">";
			if ($numberOfRows == $limitPerPage)
			{
				//Next
				echo "<a href=" . $_SERVER["PHP_SELF"] . "?startLimit=" . $nextStartLimit . "&limitPerPage=" . $limitPerPage . "&sortBy=" . $sortBy . "&sortOrder=" .  $sortOrder . ">Next " . $limitPerPage . " >></a>";
			}//if
			echo "</div>";
	}//if
	echo "</div>"; //view_options

	echo $str_01;
	echo $str_03;
	echo $str_04;

	//there is always one user in the database, the administrator (which is a dummy entry)
	if($numberOfRows == 1)
	{
		if($search_lname == "")
		{
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"4\" align=\"center\">There are no users in the system</td>";
			echo "\n\t\t</tr>";
		}
		elseif($search_lname != "")
		{
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"4\" align=\"center\">User not found</td>";
			echo "\n\t\t</tr>";
		}
	}//if

	for($i=0; $i<$numberOfRows; $i++)
	{
		$db_id = mysql_result($result,$i,"id");
		$db_lname = mysql_result($result,$i,"lname");
		$db_fname = mysql_result($result,$i,"fname");
		$db_email = mysql_result($result,$i,"email");

		if($db_id == 1) { continue; } //the administrator has id=1, so i don't want him to show up in the users list

		/*
		//for skyblue
		if($db_id == 0) { continue; }
		*/

		if (($i%2)==0) {
			$bgColor = "#F5F0EA";
			$trClass = "even";
		} else {
			$bgColor = "#FFFFFF";
			$trClass = "odd";
		}

		echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
		echo "<td>" . $db_lname . "</td>";
		echo "<td>" . $db_fname . "</td>";
		echo "<td><a href=\"mailto:" . $db_email . "\">" . $db_email . "</a></td>";
		echo "<td><a href=\"./user_info.php?userid=" . $db_id . "\" target=\"_parent\" class=\"simple\">view</a></td>";
		echo "\n\t\t</tr>";
	}//for

	echo $str_05;
	echo $str_06;

	echo "<div id=\"view_options\">";
	if ($numberOfRows>0) {
			$i=0;
			echo "<div id=\"previous\">";
			if ((isset($_REQUEST["startLimit"])) && ($_REQUEST["startLimit"] != "" && $_REQUEST["startLimit"] > 0))
			{
				//Previous
				echo "<a href=" . $_SERVER["PHP_SELF"] . "?startLimit=" . $previousStartLimit . "&limitPerPage=" . $limitPerPage . "&sortBy=" . $sortBy . "&sortOrder=" . $sortOrder . "><< Previous " . $limitPerPage . "</a>";
			}//
			echo "</div>";

			echo "<div id=\"next\">";
			if ($numberOfRows == $limitPerPage)
			{
				//Next
				echo "<a href=" . $_SERVER["PHP_SELF"] . "?startLimit=" . $nextStartLimit . "&limitPerPage=" . $limitPerPage . "&sortBy=" . $sortBy . "&sortOrder=" .  $sortOrder . ">Next " . $limitPerPage . " >></a>";
			}//if
			echo "</div>";
	}//if
	echo "</div>"; //view_options

	@mysql_close();//closes the connection to the DB
}//display_users($limitPerPage,$search_lname)

##################################
##################################

function display_login_conferences()
{
	if (!isset($_SESSION["CONFERENCES"]))
	{
		//load all the conferences
		load_conferences();
	}

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"conferences\">";
	$str_02 = "<caption>Conference list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"name\">conference</th>
		<th scope=\"col\" class=\"deadlines\">deadlines</th>
		<th scope=\"col\" class=\"enter\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	if (count($_SESSION["CONFERENCES"]) == 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "<tr><td colspan=\"2\" align=\"center\">" . "<span class=\"red\">" . "Currently there are no conferences. Please return later." . "</span>" . "</td></tr>";
		echo $str_05;
		echo $str_06;
	}//
	else
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		reset($_SESSION["CONFERENCES"]);

		$count=0;
		while (list($key, $val) = each ($_SESSION["CONFERENCES"]))
		{
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else

			//for papers deadline
			$deadlines["abstracts_deadline"] = load_conference_dates_to_array($_SESSION["CONFERENCES"][$key]["abstracts_deadline"], "abstracts_deadline");

			//for manuscripts deadline
			$deadlines["manuscripts_deadline"] = load_conference_dates_to_array($_SESSION["CONFERENCES"][$key]["manuscripts_deadline"], "manuscripts_deadline");

			//for camera-ready deadline
			$deadlines["camera_ready_deadline"] = load_conference_dates_to_array($_SESSION["CONFERENCES"][$key]["camera_ready_deadline"], "camera_ready_deadline");

			//for preferencies deadline
			$deadlines["preferencies_deadline"] = load_conference_dates_to_array($_SESSION["CONFERENCES"][$key]["preferencies_deadline"], "preferencies_deadline");

			//find reviews deadline
			$deadlines["reviews_deadline"] = load_conference_dates_to_array($_SESSION["CONFERENCES"][$key]["reviews_deadline"], "reviews_deadline");


			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

			echo "\n\t\t\t<td class=\"name\" title=\"Conference Alias: " . $_SESSION["CONFERENCES"][$key]["alias"] . "\">" . $_SESSION["CONFERENCES"][$key]["name"] . "</td>";
			echo "\n\t\t\t<td class=\"deadlines\">" . "Manuscripts: " . "<span class=\"red\">" . $deadlines["manuscripts_deadline"] . "</span>" . " <div id=\"button\"><a onClick=\"toggle_hidden_content('" . "v" . $_SESSION["CONFERENCES"][$key]["id"] . "', this, 'loggin');\">.::View All::.</a></div></td>"; //View Deadlines</a></td>";

			echo "\n\t\t\t<td class=\"enter\">";
				echo "<a href=\"" . "./include/functionsinc.php?type=48&c_id=" . $_SESSION["CONFERENCES"][$key]["id"] . "\" class=\"simple\">enter</a>";
			echo "\n\t\t\t</td>";

			echo "\n\t\t</tr>";


			//print the deadlines
			echo "\n\t\t<tr>";
			echo "\n\t\t\t<td colspan=\"5\"  title=\"Conference Deadlines.\">";
				echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "v" . $_SESSION["CONFERENCES"][$key]["id"] . "\">";
					echo "\n\t\t\t\t<ul>";
						echo "\n\t\t\t\t<li>Abstracts: " . "<span class=\"red\">" . $deadlines["abstracts_deadline"] . "</span>" . "</li>";
						echo "\n\t\t\t\t<li>Manuscripts: " . "<span class=\"red\">" . $deadlines["manuscripts_deadline"] . "</span>" . "</li>";
						echo "\n\t\t\t\t<li>Camera-Ready Papers: " . "<span class=\"red\">" . $deadlines["camera_ready_deadline"] . "</span>" . "</li>";
						echo "\n\t\t\t\t<li>Reviewers Preferencies: " . "<span class=\"red\">" . $deadlines["preferencies_deadline"] . "</span>" . "</li>";
						echo "\n\t\t\t\t<li>Reviews Submition: " . "<span class=\"red\">" . $deadlines["reviews_deadline"] . "</span>" . "</li>";
					echo "\n\t\t\t\t</ul>";
				echo "\n\t\t\t\t</div>";
			echo "</td>";
			echo "\n\t\t</tr>";

			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//else
}//display_login_conferences()

##################################
##################################

function display_reviews($user_type)
{
	switch($user_type)
	{
		case "administrator":
			display_reviews_ch();
			break;
		case "chairman":
			display_reviews_ch();
			break;
		case "reviewer":
			display_reviews_r();
			break;
		case "author":
			display_reviews_a();
			break;
		default:
			//do nothing
			break;
	}//switch
}//display_reviews($user_type)

##################################
##################################

function show_conference_file_formats($user_type)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\">";
	$str_02 = "<caption>Conference chairmen list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"lname\">Extension</th>
		<th scope=\"col\" class=\"\">Mime-Type</th>
		<th scope=\"col\" class=\"fname\">Description</th>
		<th scope=\"col\" class=\"remove\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("show_conference_file_formats()","selectdbcommoninc.php",913,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("show_conference_file_formats()","selectdbcommoninc.php",914,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT fileformat.id, fileformat.extension, fileformat.description, fileformat.mime_type "
				. "FROM fileformat, fileformattoconference "
				. "WHERE fileformat.id = fileformattoconference.format_id "
					. "AND fileformattoconference.conference_id='" . $_SESSION["conf_id"] . "' ORDER BY (fileformat.extension) ASC";
	$result = @mysql_query($query) or dbErrorHandler("show_conference_file_formats()","selectdbcommoninc.php",921,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num


	if($num==0)
	{
		if($user_type=="chairman")
		{
			echo $str_01;
			echo $str_03;
			echo $str_04;
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"4\" align=\"center\">There are no file formats selected for this conference.</td>";
			echo "\n\t\t</tr>";
			echo $str_05;
			echo $str_06;
		}//
		elseif($user_type=="author")
		{
			echo "\n\t\t\t<div class=\"notes\">(Supported file formats for this confernece are not defined yet.)</div>\n";
		}//
	}//
	else
	{
		if($user_type=="chairman")
		{
			echo $str_01;
			echo $str_03;
			echo $str_04;
			for($i=0; $i<$num; $i++)
			{
				$fformat_ar[$i]["id"] = mysql_result($result,$i,"id");
				$fformat_ar[$i]["extension"] = mysql_result($result,$i,"extension");
				$fformat_ar[$i]["description"] = mysql_result($result,$i,"description");
				$fformat_ar[$i]["mime_type"] = mysql_result($result,$i,"mime_type");

				if (($i%2)==0) {
					$bgColor = "#F5F0EA";
					$trClass = "even";
				} else {
					$bgColor = "#FFFFFF";
					$trClass = "odd";
				}

				echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
				echo "<td>" . $fformat_ar[$i]["extension"] . "</td>";
				echo "<td>" . $fformat_ar[$i]["mime_type"] . "</td>";
				echo "<td>" . $fformat_ar[$i]["description"] . "</td>";
				echo "<td><a href=\"./include/functionsinc.php?type=45&id=" . $fformat_ar[$i]["id"] . "\" class=\"simple\">remove</a></td>";
				echo "\n\t\t</tr>";
			}//for
			echo $str_05;
			echo $str_06;
		}//if
		elseif($user_type=="author")
		{
			echo "\n\t\t<div class=\"notes\">(Supported file formats: ";
			for($i=0; $i<$num; $i++)
			{
				$fformat_ar[$i]["extension"] = mysql_result($result,$i,"extension");
				echo "<b>" . strtoupper($fformat_ar[$i]["extension"]) . "</b>, ";
			}//for
			echo ")</div>";
		}//elseif

	}//else

	@mysql_close();//closes the connection to the DB
}//show_conference_file_formats($user_type)

##################################
##################################

//find user data for the update_user_profile.php page
function find_user_data()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	$logged_user_id = $_SESSION["logged_user_id"];

	$arVals = array("address_01"=>"", "address_02"=>"", "address_03"=>"", "city"=>"", "country"=>"",
					"phone_01"=>"", "phone_02"=>"", "fax"=>"",
					"website"=>"",
					"security_question"=>"", "security_answer"=>"",
					"birthday"=>"");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("find_user_data()","selectdbcommoninc.php",1008,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("find_user_data()","selectdbcommoninc.php",1009,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT address_01, address_02, address_03, city, country, phone_01, phone_02, fax, email, website, security_question, security_answer, birthday "
			 . "FROM user WHERE id = '" . $logged_user_id . "';";

	//$query = "SELECT address_01, address_02, address_03, city, country, phone_01, phone_02, fax, email, website, security_question, security_answer, birthday "
		// . "FROM user WHERE id = '" . $_SESSION["logged_user_id"] . "';";


	$result = @mysql_query($query) or dbErrorHandler("find_user_data()","selectdbcommoninc.php",1019,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	$row = mysql_fetch_row($result);

	for($i=0; $i<$num; $i++)
	{
		$arVals["address_01"] = @mysql_result($result,$i,"address_01");
		$arVals["address_02"] = @mysql_result($result,$i,"address_02");
		$arVals["address_03"] = @mysql_result($result,$i,"address_03");
		$arVals["city"] = @mysql_result($result,$i,"city");
		$arVals["country"] = @mysql_result($result,$i,"country");
		$arVals["phone_01"] = @mysql_result($result,$i,"phone_01");
		$arVals["phone_02"] = @mysql_result($result,$i,"phone_02");
		$arVals["fax"] = @mysql_result($result,$i,"fax");
		$arVals["email"] = @mysql_result($result,$i,"email");
		$arVals["website"] = @mysql_result($result,$i,"website");
		$arVals["security_question"] = @mysql_result($result,$i,"security_question");
		$arVals["security_answer"] = @mysql_result($result,$i,"security_answer");
		$birthday = @mysql_result($result,$i,"birthday");

		$_SESSION["birthday_year"] = intval(substr($birthday, 0, 4));
		$_SESSION["birthday_month_no"] = intval(substr($birthday, 5, 2));
		//the session $_SESSION["birthday_month"] is loaded from the following function
		$_SESSION["birthday_month"] = find_month_from_month_no (intval(substr($birthday, 5, 2)));
		$_SESSION["birthday_day"] = intval(substr($birthday, 8, 2));

		reset ($arVals);
		while (list ($key, $val) = each ($arVals)) {
			if ($val == "NULL" || $val == "null"){
				$_SESSION[$key] = "";
			}//
			else{
				//set a session variable with name the name of the array field and value the value of the array value
				$_SESSION[$key] = $val;
			}//else
		}//while

	}//for

	@mysql_close();//closes the connection to the DB
	Redirects(14,"","");
}//find_user_data

##################################
##################################

function display_deadlines()
{
	global $cvalues;

	if(isset($_SESSION["administrator"])){$user_type = "administrator";}
	elseif(isset($_SESSION["chairman"])){$user_type = "chairman";}
	elseif(isset($_SESSION["reviewer"])){$user_type = "reviewer";}
	elseif(isset($_SESSION["author"]) ||
	!isset($_SESSION["chairman"]) ||
	!isset($_SESSION["reviewer"])) {$user_type = "author";}

	echo "<ul class=\"deadlines\">";
	switch($user_type)
	{
		case "administrator":
			//do nothing;
			break;
		case "author":
			echo "<li>Abstracts Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_abstracts_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			echo "<li>Manuscripts Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_manuscripts_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			echo "<li>Camera-Ready Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_camera_ready_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			break;
		case "reviewer":
			echo "<li>Reviews Submittion Deadline: " ."<div class=\"red\">" . $cvalues["g_find_conf_reviews_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			break;
		case "chairman":
			echo "<li>Conference Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			echo "<li>Abstracts Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_abstracts_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			echo "<li>Manuscripts Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_manuscripts_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			echo "<li>Camera-Ready Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_camera_ready_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			echo "<li>Reviews Submittion Deadline: " . "<div class=\"red\">" . $cvalues["g_find_conf_reviews_deadline"] . "</div>" . "</li>";//" ( days left)</li>";
			break;
		default:
			//do nothing
			break;
	}//switch
	echo "</ul>";
}//display_deadlines()

##################################
##################################

//this function is used for the display of conferences in the login.php page
//and the ulounge.php page when the system administrator is logged in
//load_all_conferences()
function load_all_conferences($conf_status)//conf_status can only have 2 values "active" and "expired"
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conf_ar;//array that stores the selected conferences (2 possible options: "active" and "expired"

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_all_conferences()","selectdbcommoninc.php",1117,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_all_conferences()","Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT conference.id, conference.name, conference.deadline, options.CIA "
				. "FROM conference,options "
				. "WHERE conference.id = options.conference_id "
				. "ORDER BY conference.deadline ASC;";

	$result = @mysql_query($query) or dbErrorHandler("load_all_conferences()","selectdbcommoninc.php",1126,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);

	if($num == 0) { return (0); }//There are no conferences in the database

	for($i=0; $i<$num; $i++)
	{
		$db_id = mysql_result($result,$i,"id");
		$db_name = mysql_result($result,$i,"name");
		$db_deadline = 	@mysql_result($result,$i,"deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
		$db_conference_status = mysql_result($result,$i,"CIA");

		$deadline_year = intval(substr($db_deadline, 0, 4));
		$deadline_month_no = intval(substr($db_deadline, 5, 2));
		$deadline_month = find_month_from_month_no (intval(substr($db_deadline, 5, 2)));
		$deadline_day = intval(substr($db_deadline, 8, 2));

		$deadline = intval(substr($db_deadline, 5, 2)) . "-" . intval(substr($db_deadline, 8, 2)) . "-" . intval(substr($db_deadline, 0, 4)) ; //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
		$days_left = dateDifference("-", date("m-d-Y", time()), $deadline);

		$active_c_ar;
		$expired_c_ar;

		if($db_conference_status == 1)//conference is ACTIVE
		{
			$active_c_ar[$i]["id"] = $db_id;
			$active_c_ar[$i]["name"] = $db_name;
			$active_c_ar[$i]["deadline"] = $deadline;
			$active_c_ar[$i]["days_left"] = $days_left;
		}//if
		elseif($db_conference_status == 0)//conference is NOT ACTIVE
		{
			$expired_c_ar[$i]["id"] = $db_id;
			$expired_c_ar[$i]["name"] = $db_name;
			$expired_c_ar[$i]["deadline"] = $deadline;
		}//else if

		/*
		//IF WE WANT TO CHECK IF A CONFERENCE IS ACTIVE BY CHECKING HOW MANY DAYS ARE LEFT, THEN USE THIS:

		//NOTE: $days_left < 0 and not $days_left =< 0 because we care about hours and minutes of same day.
		if($days_left < 0 )
		{
			//conference has expired.
			$expired_c_ar[$i]["id"] = $db_id;
			$expired_c_ar[$i]["name"] = $db_name;
			$expired_c_ar[$i]["deadline"] = $deadline;
		}//if
		else//conference is active
		{
			//conference is active.
			$active_c_ar[$i]["id"] = $db_id;
			$active_c_ar[$i]["name"] = $db_name;
			$active_c_ar[$i]["deadline"] = $deadline;
			$active_c_ar[$i]["days_left"] = $days_left;
		}//else
		*/
	}//for

	@mysql_close();//closes the connection to the DB

	if($conf_status == "active") {return ($active_c_ar);}
	elseif($conf_status == "inactive") {return($expired_c_ar);}

}//load_all_conferences($conf_status)

##################################
##################################

//this is used in the login.php and ulounge.php pages.
//It is used with the load_all_conferences() function
//for administrator ulounge
function display_sorted_conferences($active_c_ar,$expired_c_ar,$type)
{

	//if $active_c_ar and $expired_c_ar are 0, then there are no conferences in the Database yet.
	if($active_c_ar == 0 && $expired_c_ar == 0){}

	//$type can only have 2 values '0' and '1'.
	//'0' is for when the function is used on the login.php page, and
	//'1' is for when the function is used on the ulounge.php page for the administrator.

	if($type == 1)
	{
		echo "\n<div id=\"aec\">";//active and expired conferences.

		//first display all the active conferences
		echo "\n<div class=\"conf_list\">";
			echo "\n\t<div class=\"conf_list_title\">active conferences " . "(" . count($active_c_ar) . ")" . "</div>";
			if($active_c_ar == 0)
			{
				echo "<i>Currently there are no active conferences.</i>";
			}//if
			else
			{
				echo "\n\t\t<ul id=\"active\">";
				while (list($key, $val) = each ($active_c_ar))
				{
					echo "\n\t\t\t<li>";
					echo "\n\t\t\t\t<a href=\"./conference_info.php?confid=" . $active_c_ar[$key]["id"] . "\" title=\"Conference Info.\">" . $active_c_ar[$key]["name"] . "</a>" .  " will be active until: " . "<span class=\"red\">" . $active_c_ar[$key]["deadline"] . "</span>" . "."; //" and there are <span class=\"days_left\">" . $active_c_ar[$key]["days_left"] . "</span> days left.";
					echo "\n\t\t\t</li>";
				}//while
				echo "\n\t\t</ul>";
			}//else
		echo "\n</div>";

		echo "\n\n<center><div id=\"bar\"></div></center>\n\n";

		//then display all the expired conferences
		echo "\n<div class=\"conf_list\">";
			echo "\n\t<div class=\"conf_list_title\">Inactive conferences " . "(" . count($expired_c_ar) . ")" . "</div>";
			if($expired_c_ar == 0)
			{
			 	echo "<i>Currently there are no inactive conferences.</i>";
			}//if
			else
			{
				echo "\n\t\t<ul id=\"expired\">";
				while (list($key, $val) = each ($expired_c_ar))
				{
					echo "\n\t\t\t<li>";
					echo "\n\t\t\t\t<a href=\"./conference_info.php?confid=" . $expired_c_ar[$key]["id"] . "\" title=\"Conference Info.\">" . $expired_c_ar[$key]["name"] . "</a>" . " had deadline: " . $expired_c_ar[$key]["deadline"] . ".";
					echo "\n\t\t\t</li>";
				}//while
				echo "\n\t\t</ul>";
			}//else
		echo "\n</div>";

		echo "\n</div>";//active and expired conferences
	}//if
	elseif($type == 0)
	{
		echo "\n<div id=\"aec\">";//active and expired conferences.

		//first display all the active conferences
		echo "\n<div class=\"conf_list\">";
			echo "\n\t<div class=\"conf_list_title\">active conferences " . "(" . count($active_c_ar) . ")" . "</div>";
			echo "\n\t\t<ul id=\"active\">";
			if($active_c_ar == 0)
			{
				echo "<i>Currently there are no active conferences.</i>";
			}//if
			else
			{

				while (list($key, $val) = each ($active_c_ar))
				{
					if(($key%2) == 0 ){ $classname = "A";}else{ $classname = "B"; }

					echo "\n\t\t\t<li class=\"" . $classname . "\" title=\"Deadline: " . $active_c_ar[$key]["deadline"] . "\">";
					echo "\n\t\t\t\t" . $active_c_ar[$key]["name"] . ",";
					echo "\n\t\t\t</li>";
				}//while
			}//else
			echo "\n\t\t</ul>";
		echo "\n</div>";

		//then display all the expired conferences
		echo "\n<div class=\"conf_list\">";
			echo "\n\t<div class=\"conf_list_title\">Inactive conferences " . "(" . count($expired_c_ar) . ")" . "</div>";
			echo "\n\t\t<ul id=\"expired\">";
			if($expired_c_ar == 0)
			{
			 	echo "<i>Currently there are no inactive conferences.</i>";
			}//if
			else
			{
				while (list($key, $val) = each ($expired_c_ar))
				{
					if(($key%2) == 0 ){ $classname = "A";}else{ $classname = "B"; }

					echo "\n\t\t\t<li class=\"" . $classname . "\" title=\"Deadline: " . $expired_c_ar[$key]["deadline"] . "\">";
					echo "\n\t\t\t\t" . $expired_c_ar[$key]["name"] . ",";
					echo "\n\t\t\t</li>";
				}//while
			}//else
			echo "\n\t\t</ul>";
		echo "\n\t</div>";

		echo "\n</div>";//active and expired conferences

	}//else if

}//display_sorted_conferences($active_c_ar,$expired_c_ar)

##################################
##################################

//This functions takes the confernece_options table, and the user_type,
//and prints messages for that user
function display_conference_options_for_each_user($conference_options,$user_type)
{
	echo "\n\t\t\t<ul>";

	while (list($key, $val) = each ($conference_options))
	{
		if($key == $user_type)//so that it echos only the array cells that regard that type of user
		{
			while (list($key2, $val2) = each ($conference_options[$user_type]))
			{
				//echo $key2 . " ==> " . $conference_options[$user_type][$key2] . "<br>";
				//$_SESSION["NORPC"]//How many reviewers for each paper in this conference?  ==> CODE: NORPC
				if ($key2 == "NORPC") { echo "\n\t\t\t\t<li>Each paper can ONLY have <span class=\"red\">" . $conference_options[$user_type][$key2] . "</span> reviewers for this conference.</li>"; continue;}
				if( $conference_options[$user_type][$key2] == 1 )
				{
					switch($key2)
					{
						case "CIA":
							echo "\n\t\t\t\t<li>Conference is active.</li>";
							break;
						case "ASA":
							echo "\n\t\t\t\t<li>Are allowed to submit their abstracts.</li>";
							break;
						case "AUA":
							echo "\n\t\t\t\t<li>Are allowed to update their abstracts.</li>";
							break;
						case "ASM":
							echo "\n\t\t\t\t<li>Are allowed to submit their manuscripts.</li>";
							break;
						case "AUM":
							echo "\n\t\t\t\t<li>Are allowed to update their manuscripts.</li>";
							break;
						case "ASCRP":
							echo "\n\t\t\t\t<li>Are allowed to submit their camera-ready papers.</li>";
							break;
						case "AUCRP":
							echo "\n\t\t\t\t<li>Are allowed to update their camera-ready papers.</li>";
							break;
						case "AVP":
							echo "\n\t\t\t\t<li>Are allowed to view reviews for their papers.</li>";
							break;
						case "ACR":
							echo "\n\t\t\t\t<li>Are allowed to enter conflicts with reviewers for their papers.</li>";
							break;
						case "RELIC":
							echo "\n\t\t\t\t<li>Are allowed to view papers and enter level of interest and conflicts.</li>";
							break;
						case "RDPR":
							echo "\n\t\t\t\t<li>Are allowed to download their assigned papers and review them.</li>";
							break;
						case "RVRP":
							echo "\n\t\t\t\t<li>Are allowed to view reviews of his assigned papers by other reviewers.</li>";
							break;
						case "UVP":
							echo "\n\t\t\t\t<li>Are allowed to view all conference papers.</li>";
							break;
						case "UDP":
							echo "\n\t\t\t\t<li>Are allowed to download all conference papers.</li>";
							break;
						case "UVAP":
							echo "\n\t\t\t\t<li>Are allowed to view the accepted conference papers.</li>";
							break;
						case "UDAP":
							echo "\n\t\t\t\t<li>Are allowed to download the accepted conference papers.</li>";
							break;
						default:
							//nothing
							break;
					}//switch
				}//if
				elseif ( $conference_options[$user_type][$key2] == 0 )
				{
					switch($key2)
					{
						case "CIA":
							echo "\n\t\t\t\t<li>Conference is <span class=\"red\">inactive</span>.</li>";
							break;
						case "ASA":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to submit their abstracts.</li>";
							break;
						case "AUA":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to update their abstracts.</li>";
							break;
						case "ASM":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to submit their manuscripts.</li>";
							break;
						case "AUM":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to update their manuscripts.</li>";
							break;
						case "ASCRP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to submit their camera-ready papers.</li>";
							break;
						case "AUCRP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to update their camera-ready papers.</li>";
							break;
						case "AVP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to view reviews for their papers.</li>";
							break;
						case "ACR":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to enter conflicts with reviewers for their papers.</li>";
							break;
						case "RELIC":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to view papers and enter level of interest and conflicts.</li>";
							break;
						case "RDPR":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to download their assigned papers and review them.</li>";
							break;
						case "RVRP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to view reviews of his assigned papers by other reviewers.</li>";
							break;
						case "UVP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to view all conference papers.</li>";
							break;
						case "UDP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to download all conference papers.</li>";
							break;
						case "UVAP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to view the accepted conference papers.</li>";
							break;
						case "UDAP":
							echo "\n\t\t\t\t<li>Are <span class=\"red\">NOT</span> allowed to download the accepted conference papers.</li>";
							break;
						default:
							//nothing
							break;
					}//switch
				}//elseif
			}//inner whiler
		}//if

	}//while

	echo "\n\t\t\t</ul>\n\t\t";
}//display_conference_options_for_each_user($conference_options,$user_type)

##################################
##################################

function find_user_conference_participation($user_id,$user_type)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" summary=\"chairman_to_conferences\">";
	$str_02 = "<caption>Chairman to Conferences list</caption>";

	$str_03 = "\n\t\t<thead>
		<tr>
			<th scope=\"col\" class=\"conference_name\">Conference Name</th>
			<th scope=\"col\" class=\"view\"></th>
		</tr>
		</thead>\n";

	$str_04 = "\t\t<tbody>";
	$str_05 = "\n\t</tbody>";
	$str_06 = "\n\t</table>\n";

	if($user_type == "author") {
		$str_03 = "\n\t\t<thead>
			<tr>
				<th scope=\"col\" class=\"paper_name\">Paper Name</th>
				<th scope=\"col\" class=\"conference_name\">Conference Name</th>
			</tr>
			</thead>\n";
	}

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("find_user_conference_participation()","selectdbcommoninc.php",1473,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("find_user_conference_participation()","selectdbcommoninc.php",1474,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT conference.id, conference.name "
			. "FROM user, usertype, conference "
			. "WHERE user.id=usertype.user_id AND conference.id=usertype.conference_id "
			. "AND user.id=" . $user_id . " AND usertype.type='" . $user_type . "' ORDER BY(conference.name)";

	if($user_type == "author") {
		$query = "SELECT paper.id, paper.title, conference.name, conference.id "
				. "FROM paper, conference "
				. "WHERE conference.id = paper.conference_id AND paper.user_id = '" . $user_id . "' ORDER BY(paper.title)";
	}

	$result = @mysql_query($query) or dbErrorHandler("find_user_conference_participation()","selectdbcommoninc.php",1482,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	echo $str_01;
	echo $str_03;
	echo $str_04;

	if($num == 0)
	{
		if($user_type == "chairman")
		{
			echo "\n\t\t\t<tr>";
			echo "\n<td colspan=\"2\" align=\"center\">The user is not a chairman for any conference.</td>";
			echo "\n\t\t\t</tr>";
		}//
		elseif($user_type == "reviewer") {
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"2\" align=\"center\">The user is not part of the review committee for any conference.</td>";
			echo "\n\t\t</tr>";
		}
		elseif($user_type == "author") {
			echo "\n\t\t<tr>";
			echo "\n<td colspan=\"2\" align=\"center\">The user is not an author of any of the papers in the system.</td>";
			echo "\n\t\t</tr>";
		}
	}//if

	for($i=0; $i<$num; $i++)
	{
		if (($i%2)==0) {
			$bgColor = "#F5F0EA";
			$trClass = "even";
		} else {
			$bgColor = "#FFFFFF";
			$trClass = "odd";
		}

		if (($user_type == "chairman") || ($user_type == "reviewer"))
		{
			$db_id = mysql_result($result,$i,"conference.id");
			$db_name = mysql_result($result,$i,"conference.name");

			echo "\n\t\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
			echo "<td>" . $db_name . "</td>";
			echo "<td><a href=\"./conference_info.php?confid=" . $db_id . "\" target=\"_parent\">.::view::.</a></td>";
			echo "\n\t\t\t</tr>";
		}//if
		else
		{
			$db_paper_id = mysql_result($result,$i,"paper.id");
			$db_paper_title = mysql_result($result,$i,"paper.title");
			$db_conf_id = mysql_result($result,$i,"conference.id");
			$db_conf_name = mysql_result($result,$i,"conference.name");

			echo "\n\t\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
			echo "<td><a href=\"./paper_info.php?paperid=" . $db_paper_id . "\" target=\"_parent\">" . $db_paper_title . "</a></td>";
			echo "<td><a href=\"./conference_info.php?confid=" . $db_conf_id . "\" target=\"_parent\">" . $db_conf_name . "</a></td>";
			echo "\n\t\t\t</tr>";
		}//else
	}
	echo $str_05;
	echo $str_06;

	@mysql_close();//closes the connection to the DB
}//find_user_conference_participation($user_id,$user_type)

##################################
##################################

//loads conferenc options to sessions
function load_conference_options()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	if(isset($_SESSION["conf_id"])){ $conference_id = $_SESSION["conf_id"];}
	else{ $conference_id = "";}

	$_SESSION["CIA"] = 0; //Conference is active ==> CODE: CIA
	$_SESSION["ASA"] = 0; //Let authors submit abstracts  ==> CODE: ASA
	$_SESSION["AUA"] = 0; //Let authors update abstracts  ==> CODE: AUA
	$_SESSION["ASM"] = 0; //Let authors submit manuscripts  ==> CODE: ASM
	$_SESSION["AUM"] = 0; //Let authors update manuscripts  ==> CODE: AUM
	$_SESSION["ASCRP"] = 0; //Let authors submit camera_ready papers  ==> CODE: ASCRP
	$_SESSION["AUCRP"] = 0; //Let authors update camera_ready papers  ==> CODE: AUCRP
	$_SESSION["AVP"] = 0; //Let authors view reviews for their papers  ==> CODE: AVP
	$_SESSION["ACR"] = 0; //Let authors enter conflicts with reviewers ==> CODE: ACR
	$_SESSION["NORPC"] = 0; //How many reviewers for each paper in this conference?  ==> CODE: NORPC
	$_SESSION["RELIC"] = 0; //Let reviewer view papers and enter level of interest and conflicts  ==> CODE: RELIC
	$_SESSION["RDPR"] = 0; //Let reviewer download his assigned papers and review them  ==> CODE: RDPR
	$_SESSION["RVRP"] = 0; //Let reviewer view reviews of his assigned papers by other reviewers  ==> CODE: RVRP
	$_SESSION["UVP"] = 0; //Let users view all conference papers. ==> CODE: UVP
	$_SESSION["UDP"] = 0; //Let users download all conference papers.(manuscripts and camera-ready versions). ==> CODE: UDP
	$_SESSION["UVAP"] = 0; //Let users view ONLY the accepted papers. ==> CODE: UVAP
	$_SESSION["UDAP"] = 0; //Let users download ONLY the accepted papers (only camera-ready versions ==> CODE: UDAP


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_conference_options()","selectdbcommoninc.php",1615,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_conference_options()","selectdbcommoninc.php",1616,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT * FROM options WHERE conference_id='" . $conference_id . "'";

	$result = @mysql_query($query) or dbErrorHandler("load_conference_options()","selectdbcommoninc.php",1621,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	for($i=0; $i<$num; $i++)
	{
		$_SESSION["CIA"] = @mysql_result($result,$i,"CIA");
		$_SESSION["ASA"] = @mysql_result($result,$i,"ASA");
		$_SESSION["AUA"] = @mysql_result($result,$i,"AUA");
		$_SESSION["ASM"] = @mysql_result($result,$i,"ASM");
		$_SESSION["AUM"] = @mysql_result($result,$i,"AUM");
		$_SESSION["ASCRP"] = @mysql_result($result,$i,"ASCRP");
		$_SESSION["AUCRP"] = @mysql_result($result,$i,"AUCRP");
		$_SESSION["AVP"] = @mysql_result($result,$i,"AVP");
		$_SESSION["ACR"] = @mysql_result($result,$i,"ACR");
		$_SESSION["NORPC"] = @mysql_result($result,$i,"NORPC");
		$_SESSION["RELIC"] = @mysql_result($result,$i,"RELIC");
		$_SESSION["RDPR"] = @mysql_result($result,$i,"RDPR");
		$_SESSION["RVRP"] = @mysql_result($result,$i,"RVRP");
		$_SESSION["UVP"] = @mysql_result($result,$i,"UVP");
		$_SESSION["UDP"] = @mysql_result($result,$i,"UDP");
		$_SESSION["UVAP"] = @mysql_result($result,$i,"UVAP");
		$_SESSION["UDAP"] = @mysql_result($result,$i,"UDAP");

 	}//for

	@mysql_close();//closes the connection to the DB

}//load_conference_options()

##################################
##################################

function display_all_papers($user_type)
{
	switch($user_type)
	{
		case "administrator":
			display_all_papers_ch();
			break;
		case "chairman":
			display_all_papers_ch();
			break;
		default:
			display_all_papers_allusers();
			break;
	}//switch
}//display_reviews($user_type)

##################################
##################################

function display_all_papers_allusers()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	global $coptions1D; //so that we can use all the conference options

	if($coptions1D["UVP"] == 0)
	{
		if($coptions1D["UVAP"] == 0){return -1;} //users are not allowed to view any papers
		if($coptions1D["UVAP"] != 0){} //show only the accepted papers.
	}
	else{} //show all conference papers

	$conference_id = $_SESSION["conf_id"];

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"conference_papers\">";
	$str_02 = "<caption>papers list</caption>";

	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"abstract\">Abstract</th>
		</tr>\n</thead>";

	if( ($coptions1D["UDP"] != 0) || ($coptions1D["UDAP"] != 0) )
	{
		$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"download\">Download</th>
		<th scope=\"col\" class=\"abstract\">Abstract</th>
		</tr>\n</thead>";
	}

	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_all_papers_allusers()","selectdbcommoninc.php",1714,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_all_papers_allusers()","selectdbcommoninc.php",1715,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Get all the papers of this conference
	$query01 = "SELECT paper.id, paper.user_id, paper.title, paper.abstract, paper.authors, paper.status_code, user.fname, user.lname "
				. "FROM paper, user "
				. "WHERE conference_id = '" . $conference_id . "' AND user.id=paper.user_id ORDER BY (paper.status_code) DESC;";

	$result01 = @mysql_query($query01) or dbErrorHandler("display_all_papers_allusers()","selectdbcommoninc.php",1725,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//no papers have been assigned for him to review
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			$paper_id = mysql_result($result01,$i,"id");
			$papers[$paper_id]["title"] = mysql_result($result01,$i,"title");
			$papers[$paper_id]["submitted_by_id"] = mysql_result($result01,$i,"user_id");
			$papers[$paper_id]["submitted_by_name"] = mysql_result($result01,$i,"fname") . " " . mysql_result($result01,$i,"lname");
			$papers[$paper_id]["authors"] = mysql_result($result01,$i,"authors");
			$papers[$paper_id]["abstract"] = mysql_result($result01,$i,"abstract");
			$papers[$paper_id]["status_code"] = mysql_result($result01,$i,"status_code");
		}//for
	}//else


	//Get all the papers of this conference
	$query02 = "SELECT paperbody.id AS paperbody_id, paperbody.paper_id, paperbody.filename, paperbody.filesize, paperbody.paper_type "
				. "FROM paper, paperbody "
				. "WHERE paper.id = paperbody.paper_id AND paper.conference_id = '" . $conference_id . "' ORDER BY (paper_id) ASC;";
	$result02 = @mysql_query($query02) or dbErrorHandler("display_all_papers_allusers()","selectdbcommoninc.php",1752,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//no paper bodies uploaded yet
	}//if
	else
	{
		//fill the array with values
		for($j=0; $j<$num02; $j++)
		{
			$paperbodies[$j]["paperbody_id"] = mysql_result($result02,$j,"paperbody_id");
			$paperbodies[$j]["paper_id"] = mysql_result($result02,$j,"paper_id");
			$paperbodies[$j]["filename"] = mysql_result($result02,$j,"filename");
			$paperbodies[$j]["filesize"] = mysql_result($result02,$j,"filesize");
			$paperbodies[$j]["paper_type"] = mysql_result($result02,$j,"paper_type");
		}//for
	}//else

	if(count($papers) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"6\" align=\"center\">No papers submitted for this conference.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($papers) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the array
		reset($papers);

		$count=0;
		while (list($key01, $val01) = each($papers))
		{
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else

			if($coptions1D["UVP"] == 0)
			{
				if($coptions1D["UVAP"] == 0){} //users are not allowed to view any papers
				if($coptions1D["UVAP"] != 0)
				{
					//SHOW ONLY ACCEPTED CONFERENCE PAPERS
					if($papers[$key01]["status_code"] == 1)
					{

						echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

						//accepted papers have red titles
						echo "\n\t\t\t<td class=\"name\">" . "<span class=\"red\">" . $papers[$key01]["title"] . "</span>" . "</td>";

						/**/
						$papers[$key01]["authors"] = $papers[$key01]["authors"] . " ";
						$authors = explode(", ", $papers[$key01]["authors"]);
						unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it
						echo "\n\t\t\t<td class=\"authors\">";
							echo "\n\t\t\t\t<ul>";
							for($k=0; $k<count($authors); $k++)
							{
								echo "<li>" . $authors[$k] . "</li>";
							}//for
							echo "\n\t\t\t\t</ul>";
						echo "\n\t\t\t</td>";
						/**/

						/**/
						//find if there are is a manuscript and a paper-body for this paper.
						//if there is, echo a 'download' option for it.
						$manuscript = "-";
						$camera_ready = "-";

						//if users are not allowed to download the conference papers.
						if( ($coptions1D["UDP"] != 0) || ($coptions1D["UDAP"] != 0) )
						{
								for($j=0; $j<count($paperbodies); $j++)
								{
									if($key01 == $paperbodies[$j]["paper_id"])
									{
										if($paperbodies[$j]["paper_type"] == "manuscript")
										{
											$manuscript = "";
											//users are allowed to download only the camera ready versions of the papers
											//$manuscript = "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paperbodies[$j]["paperbody_id"] . "\" title=\"" . $paperbodies[$j]["filename"] . " (size: " . $paperbodies[$j]["filesize"] . " bytes)\">" . "manuscript" . "</a>";
										}//if
										elseif($paperbodies[$j]["paper_type"] == "camera_ready")
										{
											$camera_ready = "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paperbodies[$j]["paperbody_id"] . "\" title=\"" . $paperbodies[$j]["filename"] . " (size: " . $paperbodies[$j]["filesize"] . " bytes)\">" . "camera-ready" . "</a>";
										}//elseif
									}//if
								}//for
								echo "\n\t\t\t<td class=\"download\">";
								echo $manuscript . "<br>" . $camera_ready;
								echo "\n\t\t\t</td>";
								/**/
						}//if( ($coptions1D["UDP"] != 0) || ($coptions1D["UDAP"] != 0) )


						echo "\n\t\t\t<td class=\"button\"><a onClick=\"toggle_hidden_content('" . "a" . $key01 . "', this, 'papers');\" class=\"simple\">view</a></td>";
						echo "\n\t\t</tr>";

						//print the abstract
						echo "\n\t\t<tr>";
							echo "\n\t\t\t<td colspan=\"6\"  title=\"Paper Abstract.\">";
								echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "a" . $key01 . "\">";
									echo "\n\t\t\t<div class=\"a_of_p\">Abstract for: <span class=\"red\">" . $papers[$key01]["title"] . "</span></div>";
									echo  "<div class=\"abst\"><pre>" . $papers[$key01]["abstract"] . "</pre></div>";
								echo "\n\t\t\t\t</div>";
							echo "</td>";
						echo "\n\t\t</tr>";

						$count++;
					}//if($papers[$key01]["status_code"] == 1)
				}//if($coptions1D["UVAP"] != 0)
			}//if($coptions1D["UVP"] == 0)
			else
			{
				//SHOW ALL CONFERENCE PAPERS

				echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
				//accepted papers have red titles
				if($papers[$key01]["status_code"] == 1){echo "\n\t\t\t<td class=\"name\">" . "<span class=\"red\">" . $papers[$key01]["title"] . "</span>" . "</td>";}
				else{echo "\n\t\t\t<td class=\"name\">" . $papers[$key01]["title"] . "</td>";}

				/**/
				$papers[$key01]["authors"] = $papers[$key01]["authors"] . " ";
				$authors = explode(", ", $papers[$key01]["authors"]);
				unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it
				echo "\n\t\t\t<td class=\"authors\">";
					echo "\n\t\t\t\t<ul>";
					for($k=0; $k<count($authors); $k++)
					{
						echo "<li>" . $authors[$k] . "</li>";
					}//for
					echo "\n\t\t\t\t</ul>";
				echo "\n\t\t\t</td>";
				/**/

				/**/
				//find if there are is a manuscript and a paper-body for this paper.
				//if there is, echo a 'download' option for it.
				$manuscript = "-";
				$camera_ready = "-";

				//if users are not allowed to download the conference papers.
				if( ($coptions1D["UDP"] != 0) || ($coptions1D["UDAP"] != 0) )
				{
						for($j=0; $j<count($paperbodies); $j++)
						{
							//if the paper is not accepted and reviewers are allowed to download ONLY the accepted papers,
							// then don't print a link to download a rejected paper
							if(($papers[$key01]["status_code"] == 0) && ($coptions1D["UDAP"] != 0)){continue;}

							if($key01 == $paperbodies[$j]["paper_id"])
							{
								if($paperbodies[$j]["paper_type"] == "manuscript")
								{
									if($coptions1D["UDAP"] != 0){ $manuscript=""; }//users are allowed to download only the camera-ready versions
									else { $manuscript = "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paperbodies[$j]["paperbody_id"] . "\" title=\"" . $paperbodies[$j]["filename"] . " (size: " . $paperbodies[$j]["filesize"] . " bytes)\">" . "manuscript" . "</a>";}
								}//if
								elseif($paperbodies[$j]["paper_type"] == "camera_ready")
								{
									$camera_ready = "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paperbodies[$j]["paperbody_id"] . "\" title=\"" . $paperbodies[$j]["filename"] . " (size: " . $paperbodies[$j]["filesize"] . " bytes)\">" . "camera-ready" . "</a>";
								}//elseif
							}//if
						}//for
						echo "\n\t\t\t<td class=\"download\">";
						echo $manuscript . "<br>" . $camera_ready;
						echo "\n\t\t\t</td>";

				}//if( ($coptions1D["UDP"] != 0) || ($coptions1D["UDAP"] != 0) )


				echo "\n\t\t\t<td class=\"button\"><a onClick=\"toggle_hidden_content('" . "a" . $key01 . "', this, 'papers');\" class=\"simple\">view</a></td>";
				echo "\n\t\t</tr>";

				//print the abstract
				echo "\n\t\t<tr>";
					echo "\n\t\t\t<td colspan=\"6\"  title=\"Paper Abstract.\">";
						echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "a" . $key01 . "\">";
							echo "\n\t\t\t<div class=\"a_of_p\">Abstract for: <span class=\"red\">" . $papers[$key01]["title"] . "</span></div>";
							echo  "<div class=\"abst\"><pre>" . $papers[$key01]["abstract"] . "</pre></div>";
						echo "\n\t\t\t\t</div>";
					echo "</td>";
				echo "\n\t\t</tr>";

				$count++;
			} //else

		}//while
		echo $str_05;
		echo $str_06;
	}//elseif

	@mysql_close();//closes the connection to the DB
}//display_all_papers_allusers()

##################################
##################################

function find_user()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	if(!isset($_POST)){ Redirects(30,"","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(30,"","");} else { unset($_POST["csrf"]);}

	if(	((!isset($_SESSION["chairman"])) || ($_SESSION["chairman"] != TRUE) ) &&
		((!isset($_SESSION["administrator"])) ||($_SESSION["administrator"] != TRUE) )
		){ Redirects(0,"",""); }

	$arVals = array( "search_lname"=>"");
	$arValsRequired = array( "search_lname"=>"");
	$arValsMaxSize = array( "search_lname"=>35 );

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = "";
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = strtolower($val);
		}
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
		$arVals[$key] = trim(strtolower($arVals[$key]));
	}//while
	//print_r ($arVals); //print the whole array

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
		otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,30,"");//send 30 because the page we want is display_users.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,30,"");//send 30 because the page we want is display_users.php
	// make sure the variables are in the accepted range

	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("find_user()","selectdbinc.php",68,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("find_user()","Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, fname, lname, email, address_01, address_02, address_03, city, country, phone_01, phone_02, fax, website FROM user WHERE lname LIKE '%" . $arVals["search_lname"] . "%' ; ";
	$result = @mysql_query($query) or dbErrorHandler("find_user()","selectdbinc.php",73,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);

	if($num == 0)
	{
		//user was not found,
		//redirect to page display_user.php with flg=1 so that the iframe there
		//opens the database_errors.php page
		resendToForm("?flg=2",30,"");
	}//if
	elseif ($num == 1)//one user with this last name
	{
		for($i=0; $i<$num; $i++)
		{
			$db_id = mysql_result($result,$i,"id");
			$db_fname = mysql_result($result,$i,"fname");
			$db_lname = mysql_result($result,$i,"lname");
			$db_email = mysql_result($result,$i,"email");
			$db_address_01 = mysql_result($result,$i,"address_01");
			$db_address_02 = mysql_result($result,$i,"address_02");
			$db_address_03 = mysql_result($result,$i,"address_03");
			$db_city = mysql_result($result,$i,"city");
			$db_country = mysql_result($result,$i,"country");
			$db_phone_01 = mysql_result($result,$i,"phone_01");
			$db_phone_02 = mysql_result($result,$i,"phone_02");
			$db_fax = mysql_result($result,$i,"fax");
			$db_website = mysql_result($result,$i,"website");
		}//for

		@mysql_close();//closes the connection to the DB

		//user was found, the sessions are loaded,
		//redirect to page display_user.php with flg=0 so that the iframe there
		//opens the user_info.php page
		//Redirects(30,"?flg=1","");
		Redirects(36,"?userid=" . $db_id, "");
	}//elseif
	else {
		@mysql_close();//closes the connection to the DB
		//there are more than 1 users with this last name
		//Redirects(30,"?flg=3","&search_lname=" . $_POST["search_lname"]);
		Redirects(30,"","?search_lname=" . $_POST["search_lname"]);
	}//else

}//find_user()

?>
