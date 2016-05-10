<?php
##################################################
################useractionsloginc.php############
##################################################

//INCLUDES THE FOLLOWING FUNCTIONS
/*####################################################
	dbErrorHandler($function, $file, $line, $error),
	save_to_usersactionlog($function),
	errors_hack(),
	display_file_users_actions_log($mode)
####################################################*/

//DEFINE THE USERS ACTION LOG FILE
$usersLog = "/home/public/PR/errors/users_actions_log.log"; //for localhost
//$usersLog = dirname($_SERVER['SCRIPT_FILENAME']) . "/errors/users_actions_log.log";

//this function is used to write ONLY the ERRORS that happen in a database
function dbErrorHandler($function, $file, $line, $error)
{
	//default database user
	$db_common_user = "prdbuser";
	//default database users' password
	$db_common_password = "prcmnusr";
	//database name
	$database = "prdb";

	global $usersLog;

	$report_db_errors_in_db = $_SESSION["adb"];
	$report_db_errors_in_file = $_SESSION["af"];

	$arVals = array( "user_id"=>"", "conference_id"=>"",
					"user_conf_privileges"=>"", "function"=>"",
					"error"=>"", "action_datetime"=>"", "user_ip"=>"");

	//Prepare the data to be passed to the database
	if($_SESSION["administrator"] == TRUE)
	{
		$arVals["user_id"] = 1;
		$arVals["user_conf_privileges"] = "administrator";
		$arVals["conference_id"] = 0;
	}//if
	else
	{
		if(isset($_SESSION["logged_user_id"])){$arVals["user_id"] = $_SESSION["logged_user_id"];}
		elseif(!isset($_SESSION["logged_user_id"])){$arVals["user_id"] = "0";}

		if(isset($_SESSION["conf_id"])){$arVals["conference_id"] = $_SESSION["conf_id"];}
		elseif(!isset($_SESSION["conf_id"])){$arVals["conference_id"] = "0";}

		if($_SESSION["chairman"] == TRUE){ $arVals["user_conf_privileges"] = "chairman, "; }
		if($_SESSION["reviewer"] == TRUE){ $arVals["user_conf_privileges"] = $arVals["user_conf_privileges"] . "reviewer, "; }
		if($_SESSION["author"] == TRUE){ $arVals["user_conf_privileges"] = $arVals["user_conf_privileges"] . "author, "; }
		else { $arVals["user_conf_privileges"] = "-"; }
	}//else

	$arVals["function"] = $function;
	$arVals["error"] = "Error in file: " . $file . " (line: " . $line .")" . "<hr>";
	$arVals["error"] .= $error;
	$arVals["action_datetime"] = date("Y-m-d") . " " . date("H:i:s");
	if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){ $arVals["user_ip"]=$_SERVER["HTTP_X_FORWARDED_FOR"]; }
    else { $arVals["user_ip"]=$_SERVER["REMOTE_ADDR"]; }
	//print_r($arVals);


	##### WRITE TO LOG FILE #####
	$errorString = $arVals["action_datetime"] . "\r\n"; //DATE
	$errorString .= $arVals["user_ip"] . "\r\n"; //USER IP
	$errorString .= $arVals["user_conf_privileges"] . "\r\n"; //USER PRIVILEGES
	$errorString .= $arVals["function"] . "\r\n"; //FUNCTION
	$errorString .= $arVals["user_id"] . "\r\n"; //USER ID
	if($arVals["user_id"] != "-"){$errorString .= $_SESSION["logged_user_fname"] . " " . $_SESSION["logged_user_lname"] . "\r\n";} //USER NAME
	else{$errorString.=" " . "\r\n";}
	$errorString .= $arVals["conference_id"] . "\r\n"; //CONFERENCE ID
	if($arVals["conference_id"] != "-"){$errorString .= $_SESSION["conf_name"] . "\r\n";} //CONFERENCE NAME
	else{$errorString.=" " . "\r\n";}
	$errorString .= $arVals["error"] . "\r\n"; //ERROR
	$errorString .= "\r\n";

	if($report_db_errors == "on")
	{
		if($report_db_errors_in_file == "on")
		{
			// write the error string to the specified log file
			$fp = fopen($usersLog, "a+");
			fwrite($fp, $errorString);
			fclose($fp);
		}
		elseif($report_db_errors_in_file == "off"){}//do nothing

		####format the data to be send to the database#####
		while (list($key, $val) = each ($arVals))
		{
			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
			$arVals[$key] = "\"" . $arVals[$key] . "\"";

		}

		if($report_db_errors_in_db == "on")
		{
			##### WRITE TO DATABASE #####
			@mysql_connect($db_host,$db_common_user,$db_common_password);
			@mysql_select_db($database);
			//@mysql_query("SET NAMES greek");
			$insert_query = insertQuery("usersactionlog", $arVals);
			$insert_result = @mysql_query($insert_query);
			$insertid = @mysql_insert_id();
			@mysql_close();//closes the connection to the DB
		}//if
		elseif($report_db_errors_in_db == "off")
		{
			//do nothing
		}//elseif
	}//if
	elseif($report_db_errors == "off")
	{
		//do nothing
	}//elseif
	###THE ERROR HAS BEEN SAVED IN THE DATABASE TABLE AND THE LOG FILE
	###NOW REDIRECT TO THE ERRORS PAGE.
	Redirects(55,"","");
}//dbErrorHandler($function, $file, $line, $error)

############################
############################

function save_to_usersactionlog($function)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $usersLog;

	if($save_db_actions == "on")
	{
		$arVals = array( "user_id"=>"", "conference_id"=>"",
						"user_conf_privileges"=>"", "function"=>"",
						"error"=>"", "action_datetime"=>"", "user_ip"=>"");

		//Prepare the data to be passed to the database
		if(isset($_SESSION["administrator"]) && $_SESSION["administrator"] == TRUE)
		{
			$arVals["user_id"] = 1;
			$arVals["user_conf_privileges"] = "administrator";
			$arVals["conference_id"] = 0;
		}//if
		else
		{
			$arVals["user_id"] = $_SESSION["logged_user_id"];

			if(isset($_SESSION["conf_id"])){$arVals["conference_id"] = $_SESSION["conf_id"];}
			elseif(!isset($_SESSION["conf_id"])){$arVals["conference_id"] = "-";}

			if($_SESSION["chairman"] == TRUE){ $arVals["user_conf_privileges"] = "chairman, "; }
			if($_SESSION["reviewer"] == TRUE){ $arVals["user_conf_privileges"] = $arVals["user_conf_privileges"] . "reviewer, "; }
			if($_SESSION["author"] == TRUE){ $arVals["user_conf_privileges"] = $arVals["user_conf_privileges"] . "author, "; }
			else { $arVals["user_conf_privileges"] = "-"; }
		}//else

		$arVals["function"] = $function;
		$arVals["error"] = "-";
		$arVals["action_datetime"] = date("Y-m-d") . " " . date("H:i:s");
		if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){ $arVals["user_ip"]=$_SERVER["HTTP_X_FORWARDED_FOR"]; }
		else { $arVals["user_ip"]=$_SERVER["REMOTE_ADDR"]; }

		//print_r($arVals);

		##### WRITE TO LOG FILE #####
		$errorString = $arVals["action_datetime"] . "\r\n"; //DATE
		$errorString .= $arVals["user_ip"] . "\r\n"; //USER IP
		$errorString .= $arVals["user_conf_privileges"] . "\r\n"; //USER PRIVILEGES
		$errorString .= $arVals["function"] . "\r\n"; //FUNCTION
		$errorString .= $arVals["user_id"] . "\r\n"; //USER ID
		if($arVals["user_id"] != "-"){$errorString .= $_SESSION["logged_user_fname"] . " " . $_SESSION["logged_user_lname"] . "\r\n";} //USER NAME
		else{$errorString.=" " . "\r\n";}
		$errorString .= $arVals["conference_id"] . "\r\n"; //CONFERENCE ID
		if($arVals["conference_id"] != "-"){$errorString .= $_SESSION["conf_name"] . "\r\n";} //CONFERENCE NAME
		else{$errorString.=" " . "\r\n";}
		$errorString .= "-" . "\r\n"; //ERROR
		$errorString .= "\r\n";

		if($save_db_actions_in_file == "on")
		{
			// write the error string to the specified log file
			$fp = fopen($usersLog, "a+");
			fwrite($fp, $errorString);
			fclose($fp);
		}
		elseif($save_db_actions_in_file == "off")
		{
			//do nothing
		}//else

		####format the data to be send to the database#####
		while (list($key, $val) = each ($arVals))
		{
			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
			$arVals[$key] = "'" . $val . "'";
		}

		if($save_db_actions_in_db == "on")
		{
			@mysql_connect($db_host,$db_common_user,$db_common_password)
				or dbErrorHandler("save_to_usersactionlog()","usersactionsloginc.php",126,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
			@mysql_select_db($database) or dbErrorHandler("save_to_usersactionlog()","usersactionsloginc.php",127,"Unable to select database: " . $database);
			//@mysql_query("SET NAMES greek");

			$insert_query = insertQuery("usersactionlog", $arVals);

			$insert_result = @mysql_query($insert_query) or dbErrorHandler("save_to_usersactionlog()","usersactionsloginc.php",132,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $insert_query);
			$insertid = mysql_insert_id();

			@mysql_close();//closes the connection to the DB
			}//if
		elseif($save_db_actions_in_db == "off")
		{
			//do nothing
		}//else

	}//if($save_db_actions == "on")
	elseif($save_db_actions == "off")
	{
		//do nothing
	}//elseif($save_db_actions == "off")

}//save_to_usersactionlog($action)

############################
############################

function errors_hack()
{
	echo "<div class=\"index\">
				<h1><span class=\"red\">404 ERROR</span></h1>
				<div id=\"instructions\">
					Sorry for the inconvenience.<br>
					Somekind of error occured. The system administrators would be notified.<br>
				</div>
			</div>
		</div>";

	///IMPORTANT HTML CODE TO ECHO
	echo "</div>"
		. "<div id=\"bottomspacer\"><a href=\"#wrapper\" title=\"Return to top\">return to top</a></div>"
		. "</div>";

	echo "<div id=\"separator\"></div>
			<div id=\"extraColumn\">extraColumn</div>
			</div><!--content-->
			<div id=\"footer\">footer</div>
			</div><!--wrapper-->";

	echo "<script type=\"text/javascript\">"
		. "ddtreemenu.createTree(\"navi\", true);"
		. "</script>";
	echo "</body>";
	echo "</html>";
}//errors_hack()

########################
########################

//variable $mode can either be '0' or '1'
function display_file_users_actions_log($mode)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	global $usersLog;

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

	$error_hack_str = "<center id=\"red\">" . "Cannot open log file." . "<center>"
		. "\n\t</div><!--chairmen_assignments_content-->"
		. "\n\t<div id=\"bottomspacer\"><a href=\"#wrapper\" title=\"Return to top\">return to top</a></div>"
		. "\n\t\t</div><div class=\"display_papers\">"
		. "\n\t\t<div id=\"separator\"></div>"
		. "\n\t\t<div id=\"extraColumn\">extraColumn</div>"
		. "\n\t\t</div><!--content-->"
		. "\n\t\t<div id=\"footer\">footer</div>"
		. "\n\t\t</div><!--wrapper-->"
		. "\n\t\t</div>"
		. "\n\t\t<script type=\"text/javascript\">"
		. "\n\t\t//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))"
		. "\n\t\tddtreemenu.createTree(\"navi\", true);"
		. "\n\t\t</script>"
		. "\n\t\t</body>"
		. "\n\t\t</html>";

	//$usersLog = "/wamp/www/PR/errors/users_actions_log.log"; //for localhost
	//$usersLog = dirname($_SERVER['SCRIPT_FILENAME']) . "/errors/users_actions_log.log";

	//open file
	$fh = fopen($usersLog, "r") or die($error_hack_str);

	//read file
	$counter = 0;
	while(!feof($fh))
	{
		for($i=0;$i<10;$i++)
		{
			$actionslog[$counter][$i] = fgets($fh, 3000);
		}
		$counter++;
	}//while

	unset($actionslog[sizeof($actionslog)-1]); //unset the last line in the errors log (because it's blank)

	//reset the array
	reset($actionslog);
	while (list($key, $val) = each($actionslog))
	{
		if((int)$actionslog[$key][6] != $conference_id){unset($actionslog[$key]);}
	}

	//reset the array
	arsort($actionslog);//reverse sort
	reset($actionslog);
	if(sizeof($actionslog) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"7\" align=\"center\">There are no entries in the text file log.</td>";
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
			echo "\n\t\t\t<td class=\"date_time\">" . $actionslog[$key][0] . "</td>";
			echo "\n\t\t\t<td class=\"user_ip\">" . $actionslog[$key][1] . "</td>";
			echo "\n\t\t\t<td class=\"user_privileges\">" . $actionslog[$key][2] . "</td>";
			echo "\n\t\t\t<td class=\"function\">" . $actionslog[$key][3] . "</td>";
			echo "\n\t\t\t<td class=\"user\" title=\"" . $actionslog[$key][5] . "\">" . $actionslog[$key][4] . "</td>";
			echo "\n\t\t\t<td class=\"conference\" title=\"" . $actionslog[$key][7] . "\">" . $actionslog[$key][6] . "</td>";

			if( trim($actionslog[$key][8]) != "-" ){ echo "\n\t\t\t<td id=\"button\"><a onClick=\"toggle_hidden_content('" . "e" . $key . "', this, 'errors');\" class=\"simple\">view</a></td>"; }
			else{echo "\n\t\t\t<td id=\"button\">-</td>";}

			echo "\n\t\t</tr>";

			if( trim($actionslog[$key][8]) != "-")
			{
				//print the error
				echo "\n\t\t<tr>";
					echo "\n\t\t\t<td colspan=\"7\"  title=\"Paper Abstract.\">";
						echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "e" . $key . "\">";
							echo  "<div class=\"err\">" . $actionslog[$key][8] . "</div>";
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

	//close file
	fclose($fh);

}//display_file_users_actions_log()

?>
