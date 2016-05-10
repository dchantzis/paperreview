<?php
##################################################
################selectdbadmininc.php############
##################################################
/*
This file includes all the functions that include
select queries to the DataBase, that refer to administrators.
This file doesn't include some functions that are common to
all the users of the system, (these are included in the
selectdbcommoninc.php file).
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
display_ff_extensions(),
find_file_format(),
display_db_users_actions_log()
*/

function display_ff_extensions()
{
		if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

		@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
			or dbErrorHandler("display_ff_extensions()","selectdbadmininc.php",25,"Unable to connect to SQL as administrator");
		@mysql_select_db($database) or dbErrorHandler("display_ff_extensions()","selectdbadmininc.php",26,"Unable to select database: " . $database);
		//@mysql_query("SET NAMES greek");

		$query = "SELECT id, extension, description FROM fileformat ORDER BY extension ASC;";
		$result = @mysql_query($query) or dbErrorHandler("display_ff_extensions()","selectdbadmininc.php",29,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

		$row = mysql_fetch_row($result);
		$num = mysql_num_rows($result);//num

		if($num == 0)
		{
			//echo "<div class=\"text\">empty</div>";
		}//if

		echo "\n\t\t\t<select name=\"fileformatslist\" id=\"fileformatslist\" size=\"10\" style=\"width:200px\" multiple >";
		for($i=0; $i<$num; $i++)
		{

			$db_id = mysql_result($result,$i,"id");
			$db_extension = mysql_result($result,$i,"extension");
			$db_description = mysql_result($result,$i,"description");

				echo "\n\t\t\t<option value=\"" . $db_id . "\" " . 	" onclick=\"load_hidden_field('" . $db_id . "', '" . $db_extension . "');\" " . ">";
						echo $db_extension;
				echo "</option>";
		}//for
		echo "\n\t\t\t</select>";
	@mysql_close();//closes the connection to the DB
}//display_ff_extensions()

##################################
##################################

//function that searches for a file format info for the search form of page file_formats.php
function find_file_format()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(34,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "find_file_format") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(34,"?flg=157","");} else { unset($_POST["csrf"]);}

	$arVals = array( "fileformatslist"=>"");
	$arValsRequired = array( "fileformatslist"=>"");
	$arValsMaxSize = array( "fileformatslist"=>2 );
	$arValsValidations = array( "fileformatslist"=>"/^[0-9]([0-9]*)/" );

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
		$arVals["fileformatslist"] = htmlentities($arVals["fileformatslist"]);
	}//

	// check to see if these variables have been set...
	variablesSet($arValsRequired,34,"");
	variablesFilled($arValsRequired,34,"");
	variablesCheckRange($arValsMaxSize,34,"");

	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
		or dbErrorHandler("find_file_format()","selectdbadmininc.php",75,"Unable to connect to SQL as administrator");

	@mysql_select_db($database) or dbErrorHandler("find_file_format()","selectdbadmininc.php",77,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//if the $conference_id is empty, that means that we are useing the form from the conferences.php
	//page to search for a conference data. If the $conference_id is not empty, that means
	//that we are searching for a coference data using a "view" button from a users list

	$query = "SELECT id, extension, description, mime_type"
		. " FROM fileformat WHERE id='" . $arVals["fileformatslist"] . "';";

	$result = @mysql_query($query) or dbErrorHandler("find_file_format()","selectdbadmininc.php",87,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		empty_fileformat_sessions();//empty conference sessions
		$_SESSION["updatefileformat"] = "no";
	}//if
	else{
		for($i=0; $i<$num; $i++)
		{
			$ffvalues["ff_id"] = mysql_result($result,$i,"id");
			$ffvalues["ff_extension"] = mysql_result($result,$i,"extension");
			$ffvalues["ff_description"] = mysql_result($result,$i,"description");
			$ffvalues["ff_mime_type"] = mysql_result($result,$i,"mime_type");

			$ffvalues = convert_ar_vals($ffvalues, "NULL", "");

			$_SESSION["file_format_id"] = $ffvalues["ff_id"];
			$_SESSION["extension"] = $ffvalues["ff_extension"];
			$_SESSION["description"] = $ffvalues["ff_description"];
			$_SESSION["mime_type"] = $ffvalues["ff_mime_type"];

			$_SESSION["updatefileformat"] = "yes";
		}//for
	}//else

	@mysql_close();//closes the connection to the DB

	Redirects(34,"","");
}//find_file_format()

##################################
##################################

//variable $mode can either be '0' or '1'
function display_db_users_actions_log($mode)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	if($mode==1){ $conference_id = $_SESSION["conf_id"];}
	else { $conference_id = 0;}

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"users_actions\">";
	$str_02 = "<caption>Users Action Log</caption>";

	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"date_time\">Date-time</th>
		<th scope=\"col\" class=\"user_ip\">User IP</th>
		<th scope=\"col\" class=\"user_privileges\">User Privileges</th>
		<th scope=\"col\" class=\"function\">function</th>
		<th scope=\"col\" class=\"user\">User</th>
		<th scope=\"col\" class=\"conference\">Conference</th>
		<th scope=\"col\" class=\"error\">error</th>
		</tr>\n</thead>";

	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
		or dbErrorHandler("display_db_users_actions_log()","selectdbadmininc.php",150,"Unable to connect to SQL server using: username: " . $_SESSION["logged_user_email"] . ", password: " . $_SESSION["logged_user_password"]);
	@mysql_select_db($database) or dbErrorHandler("display_db_users_actions_log()","selectdbadmininc.php",151,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query_01 = "SELECT usersactionlog.user_id, usersactionlog.conference_id, "
		. "usersactionlog.user_conf_privileges, usersactionlog.function, usersactionlog.error, "
		. "usersactionlog.action_datetime, usersactionlog.user_ip "
		. "FROM usersactionlog "
		. "WHERE usersactionlog.conference_id = '" . $conference_id . "'"
		. " ORDER BY (action_datetime) DESC";

	$result_01 = @mysql_query($query_01) or dbErrorHandler("display_db_users_actions_log()","selectdbadmininc.php",161,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_01);
	$num_01 = @mysql_num_rows($result_01);//num

	$query_02 = "SELECT id, fname, lname FROM user";
	$result_02 = @mysql_query($query_02) or dbErrorHandler("display_db_users_actions_log()","selectdbadmininc.php",165,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_02);
	$num_02 = @mysql_num_rows($result_02);//num

	$query_03 = "SELECT id, name FROM conference";
	$result_03 = @mysql_query($query_03) or dbErrorHandler("display_db_users_actions_log()","selectdbadmininc.php",169,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_03);
	$num_03 = @mysql_num_rows($result_03);//num

	$actionslog = array();

	//EXECUTE $query_01
	if($num_01 == 0)
	{
		//no papers have been assigned for him to review
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num_01; $i++)
		{
			$actionslog[$i]["user_id"] = mysql_result($result_01,$i,"user_id");
			$actionslog[$i]["conference_id"] = mysql_result($result_01,$i,"conference_id");
			$actionslog[$i]["user_conf_privileges"] = mysql_result($result_01,$i,"user_conf_privileges");
			$actionslog[$i]["function"] = mysql_result($result_01,$i,"function");
			$actionslog[$i]["error"] = mysql_result($result_01,$i,"error");
			$actionslog[$i]["action_datetime"] = mysql_result($result_01,$i,"action_datetime");
			$actionslog[$i]["user_ip"] = mysql_result($result_01,$i,"user_ip");
		}//for
	}//else


	###########WE WILL EXECUTE $query_02 and $query_03 AND COMBINE THE DATA TO $actionslog ARRAY###########
	//EXECUTE $query_02
	if($num_02 == 0)
	{
		//nothing
	}//if
	else
	{
		for($j=0; $j<$num_02; $j++)
		{
			$user_id = mysql_result($result_02, $j, "id");
			$user_name = mysql_result($result_02,$j,"fname") . " " . mysql_result($result_02,$j,"lname");

			for($i=0; $i<$num_01; $i++)
			{
				if($user_id == $actionslog[$i]["user_id"])
				{
					$actionslog[$i]["user_name"] = $user_name;
				}//if
				else { if(!isset($actionslog[$i]["user_name"])){ $actionslog[$i]["user_name"] = ""; } }
			}//for
		}//for
	}//else

	//EXECUTE $query_02
	if($num_03 == 0)
	{
		//nothing
	}//if
	else
	{
		for($z=0; $z<$num_03; $z++)
		{
			$conference_id = mysql_result($result_03, $z, "id");
			$conference_name = mysql_result($result_03,$z,"name");

			for($i=0; $i<$num_01; $i++)
			{
				if($conference_id == $actionslog[$i]["conference_id"])
				{
					$actionslog[$i]["conference_name"] = $conference_name;
				}//if
				else { if(!isset($actionslog[$i]["conference_name"])){ $actionslog[$i]["conference_name"] = ""; } }
			}//for
		}//for
	}//else


	//reset the array
	reset($actionslog);
	if(count($actionslog) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"7\" align=\"center\">There are no entries in the database log.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($actionslog) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		$count=0;
		while (list($key, $val) = each($actionslog))
		{
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
			echo "\n\t\t\t<td class=\"date_time\">" . $actionslog[$key]["action_datetime"] . "</td>";
			echo "\n\t\t\t<td class=\"user_ip\">" . $actionslog[$key]["user_ip"] . "</td>";
			echo "\n\t\t\t<td class=\"user_privileges\">" . $actionslog[$key]["user_conf_privileges"] . "</td>";
			echo "\n\t\t\t<td class=\"function\">" . $actionslog[$key]["function"] . "</td>";
			echo "\n\t\t\t<td class=\"user\" title=\"" . $actionslog[$key]["user_name"] . "\">" . $actionslog[$key]["user_id"] . "</td>";
			echo "\n\t\t\t<td class=\"conference\" title=\"" . $actionslog[$key]["conference_name"] . "\">" . $actionslog[$key]["conference_id"] . "</td>";

			if( $actionslog[$key]["error"] != "-"){ echo "\n\t\t\t<td id=\"button\"><a onClick=\"toggle_hidden_content('" . "e" . $key . "', this, 'errors');\" class=\"simple\">view</a></td>"; }
			else {echo "\n\t\t\t<td id=\"button\">-</td>";}

			echo "\n\t\t</tr>";

			if( $actionslog[$key]["error"] != "-")
			{
				//print the error
				echo "\n\t\t<tr>";
					echo "\n\t\t\t<td colspan=\"7\"  title=\"Paper Abstract.\">";
						echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "e" . $key . "\">";
							echo  "<div class=\"err\">" . $actionslog[$key]["error"] . "</div>";
						echo "\n\t\t\t\t</div>";
					echo "</td>";
				echo "\n\t\t</tr>";
			}
			else {}//print nothing
			$count++;

		}//while
		echo $str_05;
		echo $str_06;
	}//elseif

	@mysql_close();//closes the connection to the DB
}//display_db_users_actions_log()
?>
