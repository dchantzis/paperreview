<?php
###############################################################
/*
	insertQuery($DBtableName, $arrayValues),
	user_registration(),
	create_conference(),
	assign_chairmen_to_conferences($user_type),
	assign_old_user_conference_chairman(),
	assign_new_user_conference_chairman(),
	create_review_committee($user_type),
	create_review_committee_old_user(),
	create_review_committee_new_user(),
	create_announcement(),
	insert_new_file_format(),
	create_paper(),
	insert_paper_interest_level(),
	assign_reviewers_to_paper(),
	insert_paper_body(),
	force_interest_levels(),
	insert_conference_file_format(),
	assign_reviewers_to_paper2(),
	insert_paper_interest_level2(),
	review_paper()
*/
###############################################################

//function that gets the name of the DB table and an array with the names of the fields of that DB table, and the values of those to-be-inserted fields
//then the function returns the query in string format.
//CAUTION the field names of the DB table HAVE TO BE THE SAME as the ones in the form, and the ones passed with the arrayValus table.
function insertQuery($DBtableName, $arrayValues)
{
	$query = "INSERT INTO " . $DBtableName . " (";
	$i=0;
	while (list($key, $val) = each($arrayValues))
	{
		if($i==(count($arrayValues)-1))//don't add a comma after the table field name
		{
			$query .= $key . " ";
		}//
		else {$query .= $key . ", ";}
		$i++;
	}//while
	$query .=") VALUES (";

	reset ($arrayValues);
	$i=0;
	while (list($key, $val) = each($arrayValues))
	{
		if($i==(count($arrayValues)-1))//don't add a coma after the table field vale
		{
			$query .= $val . " ";
		}//
		else {$query .= $val . ", ";}
		$i++;
	}//while

	$query .=");";
	//echo $query;
	return $query;
}//insertQuery

#####################################################
############## user_registration() function #########
#####################################################
function user_registration()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "user_registration") . $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(1,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(1,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "fname"=>"", "lname"=>"",
					"address_01"=>"", "address_02"=>"", "address_03"=>"", "city"=>"", "country"=>"",
					"phone_01"=>"", "phone_02"=>"", "fax"=>"",
					"website"=>"","email"=>"", "password"=>"",
					"security_question"=>"", "security_answer"=>"",
					"birthday"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "fname"=>"", "lname"=>"",
					"address_01"=>"",
					"phone_01"=>"",
					"email"=>"", "password"=>"",
					"security_question"=>"", "security_answer"=>"",
					"birthday_month"=>"", "birthday_day"=>"", "birthday_year"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array( "fname"=>35, "lname"=>35,
					"address_01"=>100, "address_02"=>100, "address_03"=>100, "city"=>35, "country"=>35,
					"phone_01"=>10, "phone_02"=>10, "fax"=>10,
					"website"=>80,"email"=>35, "password"=>15,
					"security_question"=>50, "security_answer"=>30,
					"birthday_month"=>2, "birthday_day"=>2, "birthday_year"=>4);
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array( "email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",
						"website"=>"/(http:\/\/)?([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",
						"phone_01"=>"/^[0-9]([0-9]+)/","phone_02"=>"/^[0-9]([0-9]+)/", "fax"=>"/^[0-9]([0-9]+)/",
						"birthday_month"=>"/^[0-9]([0-9]*)/",
						"birthday_day"=>"/^[0-9]([0-9]*)/", "birthday_year"=>"/^[0-9]([0-9]*)/");

	$birthday = trim($_POST["birthday_year"]) . "-" . trim($_POST["birthday_month"]) . "-" . trim($_POST["birthday_day"]);

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		if($key == "birthday_month")
		{
			$arVals["birthday"] =  (get_magic_quotes_gpc()) ? $birthday : addslashes($birthday);
			$arVals["birthday"] = htmlentities($arVals["birthday"]);
		}
		else if($key != "birthday_month" && $key != "birthday_day" && $key != "birthday_year" && $key != "repassword"){
			//we don't want the birthday_month, birthday_day, birthday_year, repassword fields and field values inserted into the table
			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");;
			$arVals[$key] = trim($arVals[$key]);
		}

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = strtolower($val);
		}

		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/

		if($key == "birthday_month")
		{
			$_SESSION["birthday"] = $arVals["birthday"];
			$arVals["birthday"] =  "'" . $arVals["birthday"] . "'";
		}
		else if($key != "birthday_month" && $key != "birthday_day" && $key != "birthday_year" && $key != "repassword" && $key != "password" ){
			$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
		}
	}//while
	//print_r ($arVals); //print the whole array

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check if the password entries are the same
	if ($_POST["password"] != $_POST["repassword"]){resendToForm("?flg=107",1,"");}
	// check to see if these variables have been set...
	variablesSet($arValsRequired,1,"");//send 1 because the page we want is user_registration.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,1,"");//send 1 because the page we want is user_registration.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,1,"");//send 1 because the page we want is user_registration.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);
	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,1,"");//send 1 because the page we want is user_registration.php


	/* WHEN YOU INSERT USE sha1 for Passwords!!!! */
	$password = $arVals["password"];
	$arVals["password"] = "'".hash('sha256', $arVals["password"])."'";


	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...

	$query01 = "SELECT COUNT(email) FROM user where email = ".$arVals["email"]." ";
	$query02 = "SELECT COUNT(*) FROM user where fname = " . $arVals["fname"] . " AND lname= " . $arVals["lname"] . " ";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
					   or dbErrorHandler("user_registration()","insertdbinc.php",187,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
    @mysql_select_db($database) or dbErrorHandler("user_registration()","insertdbinc.php",188,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	$result = @mysql_query($query01) or dbErrorHandler("user_registration()","insertdbinc.php",191,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if ($row[0] > 0) {  // an email aleady exists in the database, because the row count > 0...
		resendToForm("?flg=101",1,"");
	}

	$result = @mysql_query($query02) or dbErrorHandler("user_registration()","insertdbinc.php",198,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$row = mysql_fetch_row($result);

	if ($row[0] > 0) {  // the combination of first name and last name aleady exists in the database, because the row count > 0...
		resendToForm("?flg=102",1,"");
	}

	//insert into table user the values of the $arVals table
	$query = insertQuery("user", $arVals);

	$result = @mysql_query($query) or dbErrorHandler("user_registration()","insertdbinc.php",208,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	//prepare the message of the email
	$message = "Hello " . strtoupper($_SESSION["fname"]) . " " . strtoupper($_SESSION["lname"]) . ",\n\n";
	$message .= "Thank you for registering in the PaperReview system.\r\n\r\n";
	$message .= "User LogIn Info:\n";
	$message .= "(You are required to remember the following to login.)\n";
	$message .= "Your username: " . $_SESSION["email"] . "\n";
	$message .= "Your password: " . $_SESSION["password"] . "\n\n";
	$message .= "Change Password Info: \n";
	$message .= "(If you forget your password you would be asked the following)\n";
	$message .= "Security question: " . $_SESSION["security_question"] . "\n";
	$message .= "Your answer: " . $_SESSION["security_answer"] . "\n";
	$message .= "Date of birth: " . $_SESSION["birthday"] . "\n\n\n";
	$message .= "Do not reply to this email.";
	//send the email
	registration_email($_SESSION["email"],"PaperReview: Successful Registration", $message);


	$_SESSION["user_registered"] = TRUE;
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("user_registration()");
	Redirects(2,"","");

}//user_registration

//create_conference()
function create_conference()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(7,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "conferences") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(7,"?flg=157","");} else { unset($_POST["csrf"]);}

	//default values for papers acceptance status's for every conference that is creates, as defined in 'sessioninitinc.php'
	global $papers_status_array;

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "name"=>"", "alias"=>"", "place"=>"", "date_conference_held"=>"",
					"contact_email"=>"", "contact_phone"=>"", "website"=>"",
					"comments"=>"","date_of_creation"=>"",
					"deadline"=>"", "abstracts_deadline"=>"",
					"manuscripts_deadline"=>"", "camera_ready_deadline"=>"",
					"preferencies_deadline"=>"", "reviews_deadline"=>"");

	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "name"=>"", "alias"=>"", "place"=>"", "date_conference_held"=>"",
					"contact_email"=>"", "contact_phone"=>"",
					"deadline_month"=>"", "deadline_day"=>"", "deadline_year"=>"",
					"abstracts_deadline_month"=>"", "abstracts_deadline_day"=>"", "abstracts_deadline_year"=>"",
					"manuscripts_deadline_month"=>"", "manuscripts_deadline_day"=>"", "manuscripts_deadline_year"=>"",
					"camera_ready_deadline_month"=>"", "camera_ready_deadline_day"=>"", "camera_ready_deadline_year"=>"",
					"preferencies_deadline_month"=>"", "preferencies_deadline_day"=>"", "preferencies_deadline_year"=>"",
					"reviews_deadline_month"=>"", "reviews_deadline_day"=>"", "reviews_deadline_year"=>""
					);
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array( "name"=>250, "alias"=>50, "place"=>100, "date_conference_held"=>100,
					"contact_email"=>35, "contact_phone"=>10, "website"=>80,
					"comments"=>2000,
					"deadline_month"=>2, "deadline_day"=>2, "deadline_year"=>4,
					"abstracts_deadline_month"=>2, "abstracts_deadline_day"=>2, "abstracts_deadline_year"=>4,
					"manuscripts_deadline_month"=>2, "manuscripts_deadline_day"=>2, "manuscripts_deadline_year"=>4,
					"camera_ready_deadline_month"=>2, "camera_ready_deadline_day"=>2, "camera_ready_deadline_year"=>4,
					"preferencies_deadline_month"=>2, "preferencies_deadline_day"=>2, "preferencies_deadline_year"=>4,
					"reviews_deadline_month"=>2, "reviews_deadline_day"=>2, "reviews_deadline_year"=>4);
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array( "email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/",
						"website"=>"/(http:\/\/)?([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",
						"contact_phone"=>"/^[0-9]([0-9]+)/",
						"deadline_day"=>"/^[0-9]([0-9]*)/", "deadline_year"=>"/^[0-9]([0-9]*)/",
						"abstracts_deadline_day"=>"/^[0-9]([0-9]*)/", "abstracts_deadline_year"=>"/^[0-9]([0-9]*)/",
						"manuscripts_deadline_day"=>"/^[0-9]([0-9]*)/", "manuscripts_deadline_year"=>"/^[0-9]([0-9]*)/",
						"camera_ready_deadline_day"=>"/^[0-9]([0-9]*)/", "camera_ready_deadline_year"=>"/^[0-9]([0-9]*)/",
						"preferencies_deadline_day"=>"/^[0-9]([0-9]*)/", "preferencies_deadline_year"=>"/^[0-9]([0-9]*)/",
						"reviews_deadline_day"=>"/^[0-9]([0-9]*)/", "reviews_deadline_year"=>"/^[0-9]([0-9]*)/");

	$deadline = trim($_POST["deadline_year"]) . "-" . trim($_POST["deadline_month"]) . "-" . trim($_POST["deadline_day"]);
	$abstracts_deadline = trim($_POST["abstracts_deadline_year"]) . "-" . trim($_POST["abstracts_deadline_month"]) . "-" . trim($_POST["abstracts_deadline_day"]);
	$manuscripts_deadline = trim($_POST["manuscripts_deadline_year"]) . "-" . trim($_POST["manuscripts_deadline_month"]) . "-" . trim($_POST["manuscripts_deadline_day"]);
	$camera_ready_deadline = trim($_POST["camera_ready_deadline_year"]) . "-" . trim($_POST["camera_ready_deadline_month"]) . "-" . trim($_POST["camera_ready_deadline_day"]);
	$preferencies_deadline = trim($_POST["preferencies_deadline_year"]) . "-" . trim($_POST["preferencies_deadline_month"]) . "-" . trim($_POST["preferencies_deadline_day"]);
	$reviews_deadline = trim($_POST["reviews_deadline_year"]) . "-" . trim($_POST["reviews_deadline_month"]) . "-" . trim($_POST["reviews_deadline_day"]);

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		switch($key)
		{
			case "deadline_month":
				$arVals["deadline"] =  (get_magic_quotes_gpc()) ? $deadline : addslashes($deadline);
				$arVals["deadline"] = htmlentities($arVals["deadline"]);
				break;
			case "abstracts_deadline_month":
				$arVals["abstracts_deadline"] =  (get_magic_quotes_gpc()) ? $abstracts_deadline : addslashes($abstracts_deadline);
				$arVals["abstracts_deadline"] = htmlentities($arVals["abstracts_deadline"]);
				break;
			case "manuscripts_deadline_month":
				$arVals["manuscripts_deadline"] =  (get_magic_quotes_gpc()) ? $manuscripts_deadline : addslashes($manuscripts_deadline);
				$arVals["manuscripts_deadline"] = htmlentities($arVals["manuscripts_deadline"]);
				break;
			case "camera_ready_deadline_month":
				$arVals["camera_ready_deadline"] =  (get_magic_quotes_gpc()) ? $camera_ready_deadline : addslashes($camera_ready_deadline);
				$arVals["camera_ready_deadline"] = htmlentities($arVals["camera_ready_deadline"]);
				break;
			case "preferencies_deadline_month":
				$arVals["preferencies_deadline"] =  (get_magic_quotes_gpc()) ? $preferencies_deadline : addslashes($preferencies_deadline);
				$arVals["preferencies_deadline"] = htmlentities($arVals["preferencies_deadline"]);
				break;
			case "reviews_deadline_month":
				$arVals["reviews_deadline"] =  (get_magic_quotes_gpc()) ? $reviews_deadline : addslashes($reviews_deadline);
				$arVals["reviews_deadline"] = htmlentities($arVals["reviews_deadline"]);
				break;
			//for these cases i don't want anything to happen
			case "deadline_day": break;
			case "deadline_year": break;
			case "abstracts_deadline_day": break;
			case "abstracts_deadline_year": break;
			case "manuscripts_deadline_day": break;
			case "manuscripts_deadline_year": break;
			case "camera_ready_deadline_day": break;
			case "camera_ready_deadline_year": break;
			case "preferencies_deadline_day": break;
			case "preferencies_deadline_year": break;
			case "reviews_deadline_day": break;
			case "reviews_deadline_year": break;
			default:
				//we don't want the deadline_month, deadline_day, deadline_year fields and field values inserted into the table
				$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
				$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
				$arVals[$key] = trim($arVals[$key]);
				break;
		}//switch

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = strtolower($val);
		}

		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/

		switch($key)
		{
			case "deadline_month":
				$arVals["deadline"] =  "'" . $arVals["deadline"] . "'";
				break;
			case "abstracts_deadline_month":
				$arVals["abstracts_deadline"] =  "'" . $arVals["abstracts_deadline"] . "'";
				break;
			case "manuscripts_deadline_month":
				$arVals["manuscripts_deadline"] =  "'" . $arVals["manuscripts_deadline"] . "'";
				break;
			case "camera_ready_deadline_month":
				$arVals["camera_ready_deadline"] =  "'" . $arVals["camera_ready_deadline"] . "'";
				break;
			case "preferencies_deadline_month":
				$arVals["preferencies_deadline"] =  "'" . $arVals["preferencies_deadline"] . "'";
				break;
			case "reviews_deadline_month":
				$arVals["reviews_deadline"] =  "'" . $arVals["reviews_deadline"] . "'";
				break;
			//for these cases i don't want anything to happen
				case "deadline_day": break;
				case "deadline_year": break;
				case "abstracts_deadline_day": break;
				case "abstracts_deadline_year": break;
				case "manuscripts_deadline_day": break;
				case "manuscripts_deadline_year": break;
				case "camera_ready_deadline_day": break;
				case "camera_ready_deadline_year": break;
				case "preferencies_deadline_day": break;
				case "preferencies_deadline_year": break;
				case "reviews_deadline_day": break;
				case "reviews_deadline_year": break;
			default:
				$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
				break;
		}//switch

	}//while
	//print_r ($arVals); //print the whole array

	$_SESSION["deadline_month_no"] = intval(substr($deadline, 5, 2));
	$_SESSION["abstracts_deadline_month_no"] = intval(substr($abstracts_deadline, 5, 2));
	$_SESSION["manuscripts_deadline_month_no"] = intval(substr($manuscripts_deadline, 5, 2));
	$_SESSION["camera_ready_deadline_month_no"] = intval(substr($camera_ready_deadline, 5, 2));
	$_SESSION["preferencies_deadline_month_no"] = intval(substr($preferencies_deadline, 5, 2));
	$_SESSION["reviews_deadline_month_no"] = intval(substr($reviews_deadline, 5, 2));

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,7,"");//send 7 because the page we want is create_conference.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,7,"");//send 7 because the page we want is create_conference.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,7,"");//send 7 because the page we want is create_conference.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,7,"");//send 7 because the page we want is create_conference.php


	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...
	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
                    or dbErrorHandler("create_conference()","insertdbinc.php",425,"Unable to connect to SQL as administrator");
    @mysql_select_db($database) or dbErrorHandler("create_conference()","insertdbinc.php","426","Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT COUNT(name) FROM conference where name = '".$_SESSION['name']."'";

	$result = @mysql_query($query01) or dbErrorHandler("create_conference()","insertdbinc.php",431,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if ($row[0] != 0) {  // a conference with this name aleady exists in the database, because the row count > 0...
		resendToForm("?flg=112",7,"");
	}

	//insert into table user the values of the $arVals table
	$query02 = insertQuery("conference", $arVals);

	$result = @mysql_query($query02) or dbErrorHandler("create_conference()","insertdbinc.php",441,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$insertid = mysql_insert_id();

	//find the id of this conference
	$query03 = "SELECT id FROM conference WHERE name=" . $arVals["name"]. ";";
	$result = @mysql_query($query03) or dbErrorHandler("create_conference()","insertdbinc.php",446,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	for($i=0; $i<$num; $i++)
	{
		$conference_id = @mysql_result($result,$i,"id");
	}

	//$conf_options is loaded from the file sessioninitinc.php which is called in this function.
	$conf_options["conference_id"] = $conference_id;
	//insert into table options the default option values for this conference
	$query04 = insertQuery("options", $conf_options);

	$result = @mysql_query($query04) or dbErrorHandler("create_conference()","insertdbinc.php",460,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query04);
	$insertid = mysql_insert_id();

	//insert the default papers acceptance status's for this conference,
	//as defined in the 'sessioninitinc.php' file
	while (list($key, $val) = each ($papers_status_array))
	{
		$status_temp_arr["conference_id"] = $conference_id;
		$status_temp_arr["status_description"] = "'" . $key . "'";
		$status_temp_arr["status_code"] = $papers_status_array[$key];
		//insert new row into table 'paperacceptancestatus'
		$query05 = insertQuery("paperacceptancestatus", $status_temp_arr);

		$result = @mysql_query($query05) or dbErrorHandler("create_conference()","insertdbinc.php",473,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query05);
		$insertid = mysql_insert_id();
	}//while

	empty_conference_sessions();
	unset($_SESSION["CONFERENCES"]); //to refresh the conference combo box, so that includes the new conference
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("create_conference()");
	Redirects(8,"?flg=116","");
}//create_conference

//assign_chairmen_to_conferences($user_type)
function assign_chairmen_to_conferences($user_type)
{
	if($user_type=="old_user")
	{
		assign_old_user_conference_chairman();
	}//if
	elseif($user_type=="new_user")
	{
		assign_new_user_conference_chairman();
	}//elseif
	else
	{
		Redirects(0,"","");//logout();
	}//else
}//assign_chairmen_to_conferences

//assign_old_user_conference_chairman()
function assign_old_user_conference_chairman()
{
		if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

		global $csrf_password_generator;

		//check if %_POST is set. If it's not set, then the form was not submitted normaly.
		if(!isset($_POST)){ Redirects(23,"?flg=157","");}
		//check for CSRF (Cross Site Request Forgery)
		$csrf_temp = hash('sha256', "chairmen_assignments_old_user") . $csrf_password_generator;
		if($_POST["csrf"] != $csrf_temp){Redirects(23,"?flg=157","");} else { unset($_POST["csrf"]);}

		//the array $arVals stores the names of all the values of the form
		$arVals = array( "user_id"=>"", "conference_id"=>"", "type"=>"");
		//the array $arValsRequired stores the name of the values of the form that are required for the registration
		$arValsRequired = array( "user_id"=>"", "conference_id"=>"", "type"=>"");

		//All the values in the $_POST are stored in an array.
		reset ($_POST);
		//This resets the cursor of the array.
		while (list($key, $val) = each ($_POST))
		{
			if ($val =="") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
			//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
			$arVals[$key] = trim($arVals[$key]);
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


		/**********************************************************************************************
		   Make sure session variables have been set and then check for required fields
		   otherwise return to the registration form to fix the errors.
		**********************************************************************************************/
		// check to see if these variables have been set...
		variablesSet($arValsRequired,23,"");
		//variablesSet($arValsRequired,23,"&conference_id=15");//send 23 because the page we want is chairmen_assignements.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,23,"");
		//variablesFilled($arValsRequired,23,"&conference_id=15");//send 23 because the page we want is chairmen_assignements.php
		// make sure the variables are in the accepted range


		/**********************************************************************************************
		Check the DB for records...
		**********************************************************************************************/
		@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
                    or dbErrorHandler("assign_old_user_conference_chairman()","insertdbinc.php",559,"Unable to connect to SQL as administrator");

		@mysql_select_db($database) or dbErrorHandler("assign_old_user_conference_chairman()","insertdbinc.php",561,"Unable to select database: " . $database);
		//@mysql_query("SET NAMES greek");

		//check if this user is already a chairman for this conference
		$query_001 = "SELECT conference_id FROM usertype WHERE user_id = '" . $_SESSION["user_id"] . "'AND type = 'chairman' AND conference_id='" . $_SESSION["conference_id"] . "';";


		$result = @mysql_query($query_001) or dbErrorHandler("assign_old_user_conference_chairman()","insertdbinc.php",568,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_001);
		$row = mysql_fetch_row($result);
		$num = mysql_num_rows($result);//num

		if ($row[0] > 0)
		{
			@mysql_close();//closes the connection to the DB
			redirects(23,"","?flg=114");
		}//if
		else if($row[0] == 0)
		{
			$query = insertQuery("usertype", $arVals);
			$result = @mysql_query($query) or dbErrorHandler("assign_old_user_conference_chairman()","insertdbinc.php",580,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
			$insertid = mysql_insert_id();

			unset($_SESSION["UNASSIGNED_CHAIRMEN"]); //resets the unassigned chairmen combo box

			@mysql_close();//closes the connection to the DB
			save_to_usersactionlog("assign_old_user_conference_chairman()");
			redirects(23,"","?flg=117");
		}//else if
		else{
		}//else

		//insert into table user the values of the $arVals table
}//assign_old_user_conference_chairman

function assign_new_user_conference_chairman()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;
	global $default_password;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(23,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "chairmen_assignments_new_user") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(23,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals_01 = array( "fname"=>"", "lname"=>"",
			"address_01"=>"", "address_02"=>"", "address_03"=>"", "city"=>"", "country"=>"",
			"phone_01"=>"", "phone_02"=>"", "fax"=>"",
			"website"=>"","email"=>"", "password"=>"",
			"security_question"=>"", "security_answer"=>"",
			"birthday"=>"");
	$arVals_02 = array( "user_id"=>"", "conference_id"=>"", "type"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired_new_user = array( "fname"=>"", "lname"=>"","email"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize_new_user = array( "fname"=>35, "lname"=>35,"email"=>35);
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations_new_user = array( "email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val =="") { $val = "null";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		//we don't want the birthday_month, birthday_day, birthday_year, repassword fields and field values inserted into the table
		$arVals_01[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals_01[$key] = htmlentities($arVals_01[$key]);
		$arVals_01[$key] = trim($arVals_01[$key]);

		//Load the session variables
		if ($val == "null"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			if($key == "conference_id" || $key == "type")
			{
				//don't save these values in the $arVals_01 array
			}//if
			else if($key != "conference_id" && $key != "type")
			{
				$_SESSION[$key] = strtolower($val);
			}//else if
		}//else
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
		if($key == "conference_id" || $key == "type")
		{
			//don't save these values in the $arVals_01 array
		}//if
		else if($key != "conference_id" && $key != "type")
		{
			$arVals_01[$key] = "'" . strtolower($arVals_01[$key]) . "'";
		}//else if
	}//while
	//print_r ($arVals); //print the whole array

	// check to see if these variables have been set...
	// check to see if these variables have been set...
	variablesSet($arValsRequired_new_user,23,"");//send 23 because the page we want is chairmen_assignements.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired_new_user,23,"");//send 23 because the page we want is chairmen_assignements.php
	// make sure the variables are in the accepted range
	//variablesCheckRangeCutExtra($arValsMaxSize_new_user);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations_new_user,23,"");//send 23 because the page we want is chairmen_assignements.php

	// check for the email already in the database...
	$db_user = $_SESSION["logged_user_email"];
	$db_password = $_SESSION["logged_user_password"];

	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
                    or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",701,"Unable to connect to SQL as administrator");
	@mysql_select_db($database) or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",702,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT id FROM user WHERE email = '".$_SESSION["email"]."' OR (fname=" . $arVals_01["fname"] . "AND lname=" . $arVals_01["lname"] . ");";

	$result = @mysql_query($query01) or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",707,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row_01 = mysql_fetch_row($result);
	$num_01= mysql_num_rows($result);//num
	//create the combo box

	if ($row_01[0] > 0)
	{  //the user already exists in the database
		//echo "user already exists in the database, just make him chairman";

		//just add him to the usertype table

		//but first get his id from the $result
		for($i=0; $i<$num_01; $i++)
		{
			$db_id = @mysql_result($result,$i,"id");
		}//for

		$_POST["conference_id"] = "" . $_POST["conference_id"] . "";//hidden field from the form
		$_POST["type"] = "'" . $_POST["type"] . "'";//hidden field from the form

		$arVals_02["user_id"] = $db_id;
		$arVals_02["conference_id"] = $_POST["conference_id"];
		$arVals_02["type"] = $_POST["type"];

		//check if this user is already a chairman for this conference
		$query_002 = "SELECT COUNT(conference_id) FROM usertype WHERE user_id = " . $arVals_02["user_id"] . " AND type = 'chairman' AND conference_id=" . $arVals_02["conference_id"] . ";";
		//echo $query_002;
		$result = @mysql_query($query_002) or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",714,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_002);
		$row_02 = mysql_fetch_row($result);
		$num_02 = mysql_num_rows($result);//num

		if ($row_02[0] > 0)
		{
			unset($_SESSION["fname"]);
			unset($_SESSION["lname"]);
			unset($_SESSION["email"]);

			@mysql_close();//closes the connection to the DB
			//redirects(11,"?conference_id=" . $_POST["conference_id"],"&flg=114");
			redirects(23,"","?flg=114");
		}//if
		else if($row_02[0] == 0)
		{
			//insert into table user the values of the $arVals table
			$query = insertQuery("usertype", $arVals_02);
			//echo $query;
			$result = @mysql_query($query) or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",733,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
			$insertid = mysql_insert_id();

			unset($_SESSION["fname"]);
			unset($_SESSION["lname"]);
			unset($_SESSION["email"]);

			unset($_SESSION["UNASSIGNED_CHAIRMEN"]); //resets the unassigned chairmen combo box

			@mysql_close();//closes the connection to the DB
			save_to_usersactionlog("assign_new_user_conference_chairman()");
			redirects(23,"","?flg=117");
		}//else if
		else{
			//unreachable case
		}//else

	}//if
	elseif ($row_01[0] == 0)
	{
		//echo "insert new user to the database, and then make him chairman";

		//insert new user to the database
		$query_01 = "INSERT INTO user (fname, lname, address_01, address_02, address_03, " .
					"city, country, phone_01, phone_02, fax, email, website, password, " .
					"security_question, security_answer, birthday)" .
					" VALUES (".
					"'" . $_SESSION["fname"] . "', " .
					"'" . $_SESSION["lname"] . "', " .
					"'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null'," .
					" '" . $_SESSION["email"] . "', " .
					"'null', '" . $default_password . "', 'null', 'null', '0000-00-00'" .
					");" ;

		$result = @mysql_query($query_01) or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",767,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_01);
		$insertid = mysql_insert_id();

		//get the new users' id
		$query_02 = "SELECT id FROM user WHERE email = '".$_POST[email]."'";
		$result = @mysql_query($query_02) or die("Invalid query (login): " . mysql_error());
		$row_03 = mysql_fetch_row($result);
		$num_03 = mysql_num_rows($result);//num

		for($i=0; $i<$num_03; $i++)
		{
			$db_id = @mysql_result($result,$i,"id");
		}//for

		$_POST["conference_id"] = "'" . $_POST["conference_id"] . "'";//hidden field from the form
		$_POST["type"] = "'" . $_POST["type"] . "'";//hidden field from the form

		$arVals_02["user_id"] = $db_id;
		$arVals_02["conference_id"] = $_POST["conference_id"];
		$arVals_02["type"] = $_POST["type"];
		//insert into table user the values of the $arVals table
		$query_03 = insertQuery("usertype", $arVals_02);
		//echo $query_03;
		$result = @mysql_query($query_03) or dbErrorHandler("assign_new_user_conference_chairman()","insertdbinc.php",790,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_03);
		$insertid = mysql_insert_id();

		//prepare the message of the email
		$message = "Hello " . strtoupper($_SESSION["fname"]) . " " . strtoupper($_SESSION["lname"]) . ",\n\n";
		$message .= "You have just been registered in the PaperReview system by the system administrator.\r\n\r\n";
		$message .= "You have chairman status in the " . strtoupper($_SESSION["conf_name"]) . " conference.\n\n";
		$message .= "User LogIn Info:\n";
		$message .= "(You are required to remember the following to login.)\n";
		$message .= "Your username: " . $_SESSION["email"] . "\n";
		$message .= "Your password: " . $default_password . "\n\n";
		$message .= "You are urged to change your password as soon as possible using the \"Forget Password?\" option on the startup page.\n";
		$message .= "On your first visit in the PaperReview System, select the \"Profile\" option on the top of the page to set your account information.\n\n\n";
		$message .= "Do not reply to this email";
		//send the email
		registration_email($_SESSION["email"],"PaperReview: New Conference Chairman", $message);

		unset($_SESSION["fname"]);
		unset($_SESSION["lname"]);
		unset($_SESSION["email"]);

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("assign_new_user_conference_chairman()");
		redirects(23,"","?flg=117");

	}//elseif

}//assign_new_user_conference_chairman


function create_review_committee($user_type)
{
	if($user_type=="old_user")
	{
		create_review_committee_old_user();
	}//if
	elseif($user_type=="new_user")
	{
		create_review_committee_new_user();
	}//elseif
	else
	{
		Redirects(0,"","");//logout();
	}//else
}//create_review_committee


function create_review_committee_old_user()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;
	global $csrf_password_generator;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(24,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(24,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "reviewes_assignments_old_user") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(24,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "user_id"=>"", "conference_id"=>"", "type"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "user_id"=>"", "conference_id"=>"", "type"=>"");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val =="") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

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


	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,24,"");
	//variablesSet($arValsRequired,24,"");//send 18 because the page we want is reviewers_assignments.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,24,"");
	//variablesFilled($arValsRequired,24,"");//send 18 because the page we want is reviewers_assignments.php
	// make sure the variables are in the accepted range


	/**********************************************************************************************
	Check the DB for records...
	**********************************************************************************************/

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("create_review_committee_old_user()","insertdbinc.php",886,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("create_review_committee_old_user()","insertdbinc.php",887,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//check if this user is already in the review committee for this conference
	$query_001 = "SELECT conference_id FROM usertype WHERE user_id = '" . $_SESSION["user_id"] . "'AND type = 'reviewer' AND conference_id='" . $_SESSION["conference_id"] . "';";

	$result = @mysql_query($query_001) or dbErrorHandler("create_review_committee_old_user()","insertdbinc.php",893,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_001);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if ($row[0] > 0)
	{
		empty_reviewers_assignment_sessions();
		@mysql_close();//closes the connection to the DB
		redirects(24,"","?flg=115");
	}//if
	else if($row[0] == 0)//the user is not a reviewer for this conference
	{
		$query = insertQuery("usertype", $arVals);//insert him

		$result = @mysql_query($query) or dbErrorHandler("create_review_committee_old_user()","insertdbinc.php",907,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
		$insertid = mysql_insert_id();

		if($_SESSION["user_id"] == $_SESSION["logged_user_id"])
		{
			$_SESSION["reviewer"] = TRUE;
		}

		//redirects(24,"?conference_id=" . $_SESSION["conference_id"],"");
		empty_reviewers_assignment_sessions();

		unset($_SESSION["UNASSIGNED_REVIEWERS"]); //resets the unassigned reviewers combo box

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("create_review_committee_old_user()");
		redirects(24,"?flg=117","");
	}//else if
	else{
		empty_reviewers_assignment_sessions();
		@mysql_close();//closes the connection to the DB
	}//else

	//insert into table user the values of the $arVals table
}//create_review_committee_old_user

function create_review_committee_new_user()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;
	global $csrf_password_generator;
	global $default_password;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(24,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(24,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "reviewers_assignments_new_user") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(24,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals_01 = array( "fname"=>"", "lname"=>"",
			"address_01"=>"", "address_02"=>"", "address_03"=>"", "city"=>"", "country"=>"",
			"phone_01"=>"", "phone_02"=>"", "fax"=>"",
			"website"=>"","email"=>"", "password"=>"",
			"security_question"=>"", "security_answer"=>"",
			"birthday"=>"");
	$arVals_02 = array( "user_id"=>"", "conference_id"=>"", "type"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired_new_user = array( "fname"=>"", "lname"=>"","email"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize_new_user = array( "fname"=>35, "lname"=>35,"email"=>35);
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations_new_user = array( "email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val =="") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
			//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

			//we don't want the birthday_month, birthday_day, birthday_year, repassword fields and field values inserted into the table
			$arVals_01[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
			$arVals_01[$key] = htmlentities($arVals_01[$key]);
			$arVals_01[$key] = trim($arVals_01[$key]);

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			if($key == "conference_id" || $key == "type")
			{
				//don't save these values in the $arVals_01 array nor the sessions
			}//if
			else if($key != "conference_id" && $key != "type")
			{
				$_SESSION[$key] = strtolower($val);
			}//else if
		}//else
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
		if($key == "conference_id" || $key == "type")
		{
			//don't save these values in the $arVals_01 array nor the sessions
		}//if
		else if($key != "conference_id" && $key != "type")
		{
			$arVals_01[$key] = "'" . strtolower($arVals_01[$key]) . "'";
		}//else if
	}//while
	//print_r ($arVals); //print the whole array


	// check to see if these variables have been set...
	// check to see if these variables have been set...
	variablesSet($arValsRequired_new_user,24,"");//send 24 because the page we want is reviewers_assignments.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired_new_user,24,"");//send 24 because the page we want is reviewers_assignments.php
	// make sure the variables are in the accepted range
	//variablesCheckRangeCutExtra($arValsMaxSize_new_user);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations_new_user,24,"");//send 24 because the page we want is reviewers_assignments.php

	// check for the email already in the database...

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1021,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1022,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT id FROM user WHERE email = '".$_SESSION["email"]."' OR (fname=" . $arVals_01["fname"] . "AND lname=" . $arVals_01["lname"] . ");";

	$result = @mysql_query($query01) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1027,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row_01 = mysql_fetch_row($result);
	$num_01= mysql_num_rows($result);//num
	//create the combo box

	if ($row_01[0] > 0)
	{  //the user already exists in the database

		//echo "user already exists in the database, just make him chairman";

		//just add him to the usertype table

		//but first get his id from the $result
		for($i=0; $i<$num_01; $i++)
		{
			$db_id = @mysql_result($result,$i,"id");
		}//for

		$_POST["conference_id"] = "" . $_POST["conference_id"] . "";//hidden field from the form
		$_POST["type"] = "'" . $_POST["type"] . "'";//hidden field from the form

		$arVals_02["user_id"] = $db_id;
		$arVals_02["conference_id"] = $_POST["conference_id"];
		$arVals_02["type"] = $_POST["type"];

		//check if this user is already in the review committee for this conference
		$query_002 = "SELECT COUNT(conference_id) FROM usertype WHERE user_id = " . $arVals_02["user_id"] . " AND type = 'reviewer' AND conference_id=" . $arVals_02["conference_id"] . ";";
		//echo $query_002;
		$result = @mysql_query($query_002) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1055,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query002);
		$row_02 = mysql_fetch_row($result);
		$num_02 = mysql_num_rows($result);//num

		if ($row_02[0] > 0)
		{
			unset($_SESSION["fname"]);
			unset($_SESSION["lname"]);
			unset($_SESSION["email"]);

			@mysql_close();//closes the connection to the DB
			redirects(24,"","?flg=115");
		}//if
		else if($row_02[0] == 0)
		{

			//insert into table userType the values of the $arVals table
			$query = insertQuery("usertype", $arVals_02);
			//echo $query;
			$result = @mysql_query($query) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1074,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query0);
			$insertid = mysql_insert_id();

			unset($_SESSION["fname"]);
			unset($_SESSION["lname"]);
			unset($_SESSION["email"]);

			unset($_SESSION["UNASSIGNED_REVIEWERS"]); //resets the unassigned reviewers combo box

			@mysql_close();//closes the connection to the DB
			save_to_usersactionlog("create_review_committee_new_user()");
			redirects(24,"","?flg=117");

		}//else if
		else{
			//unreachable case
		}//else

	}//if
	elseif ($row_01[0] == 0)
	{
		//echo "insert new user to the database, and then make him chairman";

		//insert new user to the database
		$query_01 = "INSERT INTO user (fname, lname, address_01, address_02, address_03, " .
					 "city, country, phone_01, phone_02, fax, email, website, password, " .
					"security_question, security_answer, birthday)" .
					" VALUES (".
					"'" . $_SESSION["fname"] . "', " .
					"'" . $_SESSION["lname"] . "', " .
					"'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL', 'NULL'," .
					" '" . $_SESSION["email"] . "', " .
					"'NULL', '" . $default_password . "', 'NULL', 'NULL', '0000-00-00'" .
					");" ;

		$result = @mysql_query($query_01) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1109,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_01);
		$insertid = mysql_insert_id();

		//get the new users' id
		$query_02 = "SELECT id FROM user WHERE email = '".$_POST[email]."'";
		$result = @mysql_query($query_02) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1114,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_02);
		$row_03 = mysql_fetch_row($result);
		$num_03 = mysql_num_rows($result);//num

		for($i=0; $i<$num_03; $i++)
		{
			$db_id = @mysql_result($result,$i,"id");
		}//for

		$_POST["conference_id"] = "'" . $_POST["conference_id"] . "'";//hidden field from the form
		$_POST["type"] = "'" . $_POST["type"] . "'";//hidden field from the form

		$arVals_02["user_id"] = $db_id;
		$arVals_02["conference_id"] = $_POST["conference_id"];
		$arVals_02["type"] = $_POST["type"];
		//insert into table user the values of the $arVals table
		$query_03 = insertQuery("usertype", $arVals_02);
		//echo $query_03;
		$result = @mysql_query($query_03) or dbErrorHandler("create_review_committee_new_user()","insertdbinc.php",1132,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_03);
		$insertid = mysql_insert_id();

		//prepare the message of the email
		$message = "Hello " . strtoupper($_SESSION["fname"]) . " " . strtoupper($_SESSION["lname"]) . ",\n\n";
		$message .= "You have just been registered in the PaperReview system by the chairman of the " . strtoupper($_SESSION["conf_name"]) . " conference\r\n\r\n";
		$message .= "You have reviewer status in the " . strtoupper($_SESSION["conf_name"]) . " conference.\n\n";
		$message .= "User LogIn Info:\n";
		$message .= "(You are required to remember the following to login.)\n";
		$message .= "Your username: " . $_SESSION["email"] . "\n";
		$message .= "Your password: " . $default_password . "\n\n";
		$message .= "You are urged to change your password as soon as possible using the \"Forget Password?\" option on the startup page.\n";
		$message .= "On your first visit in the PaperReview System, select the \"Profile\" option on the top of the page to set your account information.\n\n\n";
		$message .= "Do not reply to this email";
		//send the email
		registration_email($_SESSION["email"],"PaperReview: New Conference Reviewer", $message);

		unset($_SESSION["fname"]);
		unset($_SESSION["lname"]);
		unset($_SESSION["email"]);
		unset($_SESSION["UNASSIGNED_REVIEWERS"]); //resets the unassigned reviewers combo box

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("create_review_committee_new_user()");
		redirects(24,"","?flg=117");

	}//elseif

}//create_review_committee_new_user

//create_announcement()
function create_announcement()
{

	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){
		if($_SESSION["administrator"] == TRUE){Redirects(35,"?flg=157","");}
		else{Redirects(32,"?flg=157","");}
	}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "create_announcement") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){
		if($_SESSION["administrator"] == TRUE){Redirects(35,"?flg=157","");}
		else{Redirects(32,"?flg=157","");}
	} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "post_date"=>"", "user_id"=>"", "message"=>"",
					"conference_id"=>"", "regardschairmen"=>"0", "regardsreviewers"=>"0", "regardsauthors"=>"0");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("message"=>"", "conference_id"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array("message"=>"2000");

	//All the values in the $_POST are stored in an array.

	reset ($_POST);
	//This resets the cursor of the array.

	while (list($key, $val) = each ($_POST))
	{
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

		if ($_POST[$key] == "on"){
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = 1;
		}
		else {
			$_SESSION[$key] = $val;
		}
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
			$arVals[$key] = $_SESSION[$key];
	}//while

	$arVals["post_date"] = "'" . $arVals["post_date"] . "'"; //add quotes to the date field
	$arVals["message"] = "'" . $arVals["message"] . "'"; //add quotes to the message field

	//if the user is the administrator, then he has used_id =1
	if($_SESSION["administrator"] == TRUE) { $arVals["user_id"] = 1; }
	//print_r ($arVals); //print the whole array
	//echo "<br>" . $arVals["conference_id"] . "<BR>";
	//echo "<br>" . $_SESSION["conference_id"] . "<BR>";

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/

	if($_SESSION["administrator"] == TRUE){$redirect_page_id=35;}
	else {$redirect_page_id=32;}

	// check to see if these variables have been set...
	variablesSet($arValsRequired,$redirect_page_id,"");//send 32 because the page we want is announcements.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,$redirect_page_id,"");//send 32 because the page we want is announcements.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,$redirect_page_id,"");//send 32 because the page we want is announcements.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	//we want AT LEAST ONE of the checkboxes....well....checked
	if(($arVals["regardschairmen"]  == 0) && ($arVals["regardsreviewers"] == 0) && ($arVals["regardsauthors"] == 0)) { Redirects(32,"?flg=103",""); }

	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...

	@mysql_connect($db_host,$db_common_user,$db_common_password)
   		or dbErrorHandler("create_announcement()","insertdbinc.php",1229,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
    @mysql_select_db($database) or dbErrorHandler("create_announcement()","insertdbinc.php",1230,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	//insert into table user the values of the $arVals table
	$query = insertQuery("announcement", $arVals);

	$result = @mysql_query($query) or dbErrorHandler("create_announcement()","insertdbinc.php",1236,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	//empty sessions
	empty_announcement_sessions();

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("create_announcement()");
	if($_SESSION["administrator"] == TRUE){ Redirects(35,"?flg=122",""); }
	else { Redirects(32,"?flg=122",""); }

}//create_announcement()

//insert_new_file_format()
function insert_new_file_format() {

	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(34,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "file_formats") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(34,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "extension"=>"", "description"=>"", "mime_type"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "extension"=>"", "mime_type"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array( "extension"=>"10", "description"=>"100", "mime_type"=>"80");
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	//$arValsValidations = array( "extension"=>"", "description"=>"", "mime_type"=>"");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		//we don't want the deadline_month, deadline_day, deadline_year fields and field values inserted into the table
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = strtolower($val);
		}
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
	}//while
	//print_r ($arVals); //print the whole array

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,34,"");//send 34 because the page we want is file_formats.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,34,"");//send 34 because the page we want is file_formats.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,34,"");//send 34 because the page we want is file_formats.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	// make sure the variables match the corresponding regular expressions
	//variablesValidate($arValsValidations,34,"");//send 34 because the page we want is file_formats.php


	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...
	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
                    or dbErrorHandler("insert_new_file_format()","insertdbinc.php",1322,"Unable to connect to SQL as administrator");
    @mysql_select_db($database) or dbErrorHandler("insert_new_file_format()","insertdbinc.php",1323,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT COUNT(extension) FROM fileformat WHERE extension = '".$_SESSION["extension"]."'";

	$result = @mysql_query($query01) or dbErrorHandler("insert_new_file_format()","insertdbinc.php",1328,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if ($row[0] != 0) {  // a file format with this extenstion aleady exists in the database, because the row count > 0...
		@mysql_close();//closes the connection to the DB
		resendToForm("?flg=123",34,"");
	}

	//insert into table user the values of the $arVals table
	$query02 = insertQuery("fileformat", $arVals);

	$result = @mysql_query($query02) or dbErrorHandler("insert_new_file_format()","insertdbinc.php",1339,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$insertid = mysql_insert_id();

	@mysql_close();//closes the connection to the DB

	empty_fileformat_sessions();
	save_to_usersactionlog("insert_new_file_format()");
	Redirects(34,"?flg=124","");
}//insert_new_file_format()

//create_paper()
function create_paper()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "papers") . $csrf_password_generator;

	if($coptions1D["CIA"] == 0){ Redirects(38,"?flg=156",""); }//check if conference is active
	else
	{
		if($coptions1D["ASA"] == 0){ Redirects(38,"?flg=158",""); }//check if authors are allowed to submit their manuscripts.
	}

	//check if $_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(38,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(38,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "user_id"=>"", "conference_id"=>"", "title"=>"",
					"abstract"=>"", "authors"=>"", "status_code"=>"",
					"subject"=>"","submition_date"=>"");

	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "user_id"=>"", "conference_id"=>"", "title"=>"",
					"abstract"=>"", "authors"=>"", "submition_date"=>"");

	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array("title"=>250,
					"abstract"=>5000, "authors"=>300, "status_code"=>1,
					"subject"=>100);

	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array();

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

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

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,38,"");//send 38 because the page we want is papers.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,38,"");//send 38 because the page we want is papers.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,38,"");//send 38 because the page we want is papers.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,38,"");//send 38 because the page we want is papers.php


	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...
	@mysql_connect($db_host,$db_common_user,$db_common_password)
   		or dbErrorHandler("create_paper()","insertdbinc.php",1437,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

	@mysql_select_db($database) or dbErrorHandler("create_paper()","insertdbinc.php",1439,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	//check if a paper with this title already exists in this conference submitted by the same user
	$query01 = "SELECT COUNT(title) FROM paper WHERE title = " . $arVals["title"] . " AND user_id = " . $_SESSION["user_id"] . " AND conference_id = " . $_SESSION["conference_id"] . ";";

	$result = @mysql_query($query01) or dbErrorHandler("create_paper()","insertdbinc.php",1445,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if ($row[0] != 0) {  // a paper with this title aleady exists in the database, because the row count > 0...
		resendToForm("?flg=127",38,"");
	}

	//insert into table paper the values of the $arVals table
	$query02 = insertQuery("paper", $arVals);
	$result02 = @mysql_query($query02) or dbErrorHandler("create_paper()","insertdbinc.php",1454,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$insertid02 = mysql_insert_id();

	//If this is the first paper that this user creates
	//them he has to be inserted into the DB table 'usertype' as an 'author'
	//But first check this is indeed his first paper
	$query03 = "SELECT * FROM usertype WHERE user_id='" . $_SESSION["user_id"] . "' AND conference_id='" . $_SESSION["conf_id"] . "' AND type='author'; ";
	$result03 = @mysql_query($query03) or dbErrorHandler("create_paper()","insertdbinc.php",1461,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$num03 = @mysql_num_rows($result03);//num
	if($num03 == 0)
	{
		//The user doesn't exist in the DB table 'usertype' as an author.
		//So let's insert him!
		$insert_query = "INSERT INTO usertype (user_id, conference_id, type) "
							. "VALUES ( "
							. "'" . $_SESSION["user_id"] . "',"
							. " '" . $_SESSION["conf_id"] . "',"
							. " 'author')";
		$insert_result = @mysql_query($insert_query) or dbErrorHandler("create_paper()","insertdbinc.php",1472,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $insert_query);
		$insert_insertid = mysql_insert_id();
	}//if
	else
	{
		//do nothing
	}//else

	empty_paper_sessions();
	@mysql_close();//closes the connection to the DB

	//to refresh the paper combo box, so that includes the new paper
	unset($_SESSION["PAPERS"]);
	save_to_usersactionlog("create_paper()");
	Redirects(38,"?flg=126","");
}//create_paper()


//insert_paper_interest_level()
function insert_paper_interest_level()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(39,"?flg=157","");}

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "paper_interest_level") . $csrf_password_generator;

	if($coptions1D["CIA"] == 0){ Redirects(40,"?flg=156",""); }//check if conference is active
	else
	{
		if($coptions1D["RELIC"] == 0){ Redirects(40, "?flg=160", "");}//check if reviewers are allowed to enter levels of interest and conflicts
	}

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(40,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(40,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "paper_id"=>"", "user_id"=>"", "conference_id"=>"", "level_of_interest"=>"",
					"conflict"=>"");

	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "paper_id"=>"", "user_id"=>"", "conference_id"=>"", "level_of_interest"=>"",
					"conflict"=>"");

	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array();

	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array();

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);

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

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,39,"");//send 39 because the page we want is paper_interest_level.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,39,"");//send 38 because the page we want is paper_interest_level.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,39,"");//send 38 because the page we want is paper_interest_level.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("insert_paper_interest_level()","insertdbinc.php",1570,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
   	@mysql_select_db($database) or dbErrorHandler("insert_paper_interest_level()","insertdbinc.php",1571,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	#############################
	//First check if this user, is the user who created the paper, or if he is one of the authors
	$query00 = "SELECT user.fname, user.lname, paper.user_id, paper.conference_id, paper.authors "
				. "FROM paper, user "
				. "WHERE user.id=paper.user_id AND paper.id='" . $_POST["paper_id"] . "' AND paper.conference_id='" . $_SESSION["conf_id"] . "'";
	$result00 = @mysql_query($query00) or dbErrorHandler("insert_paper_interest_level()","insertdbinc.php",1579,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query00);
	$row00 = mysql_fetch_row($result00);

	$reviewer_name_type_1 = strtolower($_SESSION["logged_user_fname"] . " " . $_SESSION["logged_user_lname"]);
	$reviewer_name_type_2 = strtolower($_SESSION["logged_user_lname"] . " " . $_SESSION["logged_user_fname"]);

	//Check if this user, is the user that submitted the paper.
	if( $_SESSION["user_id"] == mysql_result($result00,0,"user_id")){ Redirects(0,"",""); } //ERROR
	//if the reviewers name is included in the authors list of the paper.
	if(strchr(mysql_result($result00,0,"authors"), $reviewer_name_type_1)){ Redirects(0,"",""); } //ERROR
	//if the reviewers name is included in the authors list of the paper.
	if(strchr(mysql_result($result00,0,"authors"), $reviewer_name_type_2)){ Redirects(0,"",""); } //ERROR
	##############################


	//Check if a paper with this title already exists in this conference submitted by the same user
	$query01 = "SELECT COUNT(*) FROM interest WHERE user_id = '" . $_SESSION["user_id"] . "' AND paper_id = " . $_POST["paper_id"] . "; ";

	$result = @mysql_query($query01) or dbErrorHandler("insert_paper_interest_level()","insertdbinc.php",1597,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if ($row[0] != 0) {  //row count > 0...
		resendToForm("?flg=128",39,"");
	}

	//insert into table interest the values of the $arVals table
	$query02 = insertQuery("interest", $arVals);

	$result = @mysql_query($query02) or dbErrorHandler("insert_paper_interest_level()","insertdbinc.php",1607,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$insertid = mysql_insert_id();

	empty_paper_interest_level_sessions();
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("insert_paper_interest_level()");
	Redirects(40,"?flg=150","");
}//insert_paper_interest_level()


//assign_reviewers_to_paper()
function assign_reviewers_to_paper()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;
	global $csrf_password_generator;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(43,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(43,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "assign_reviewers") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(43,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "paper_id"=>"", "user_id"=>"", "conference_id"=>"");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
	   or dbErrorHandler("assign_reviewers_to_paper()","insertdbinc.php",1634,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

    @mysql_select_db($database) or dbErrorHandler("assign_reviewers_to_paper()","insertdbinc.php",1636,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);

	if(count($_POST) > $coptions2D["chairman"]["NORPC"])
	{
		$_SESSION["paper_id"] = $_SESSION["temp_paper_id"];
		Redirects(42,"?flg=131","");
	}
	else
	{
		//This resets the cursor of the array.
		while (list($key, $val) = each ($_POST))
		{
			$arVals["user_id"] = $_SESSION["user_id"] = $key;
			$arVals["paper_id"] = $_SESSION["temp_paper_id"];
			$arVals["conference_id"] = $_SESSION["conference_id"] = $_SESSION["conf_id"];

			$query02 = insertQuery("papertoreviewer", $arVals);

			$result = @mysql_query($query02) or dbErrorHandler("assign_reviewers_to_paper()","insertdbinc.php",1658,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
			$insertid = mysql_insert_id();
		}//while
	}//else
	empty_assign_reviewers_to_paper_sessions();
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("assign_reviewers_to_paper()");
	Redirects(43,"?flg=162","");
}//assign_reviewers_to_paper

//insert_paper_body()
function insert_paper_body()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D; //load the conference options
	global $coptions2D; //load the conference options
	global $papers_upload_dir; //default papers upload directory

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "paper_body") . $csrf_password_generator;

	$papers_upload_dir; //global variable
	$paper_upload_max_filesize; //global variable
	$paper_upload_type; //global variable
	$type_check_of_papers_to_upload; //upload type

	if($coptions1D["CIA"] == 0){ Redirects(44,"?flg=156",""); }//check if conference is active
	else
	{
		if($coptions1D["ASM"] == 0){ Redirects(44,"?flg=147",""); }//check if authors are allowed to submit their manuscripts.
		if($coptions1D["AUM"] == 0){ Redirects(44,"?flg=147",""); }//check if authors are allowed to update their manuscripts.
		if($coptions1D["ASCRP"] == 0){ Redirects(44,"?flg=148",""); }//check if authors are allowed to submit their camera-ready papers.
		if($coptions1D["AUCRP"] == 0){ Redirects(44,"?flg=148",""); }//check if authors are allowed to update their camera-ready papers.
	}
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(44,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(44,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "paper_id"=>"", "paper_type"=>"",
					"date_of_submition"=>"", "upload_type"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array("paper_id"=>"", "paper_type"=>"");

	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array();

	//All the values in the $_POST are stored in an array.
	reset ($_POST);

	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
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

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,44,"");//send 44 because the page we want is paper_body.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,44,"");//send 44 because the page we want is paper_body.php
	// make sure the variables are in the accepted range
	// variablesCheckRange($arValsMaxSize,44,"");//send 44 because the page we want is paper_body.php
	// make sure fields are within the proper range... else cut off any extra...
print_r($_FILES);
exit;

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("insert_paper_body()","insertdbinc.php",1745,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("insert_paper_body()","insertdbinc.php",1746,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//find what file formats are supported in this conference
	$query00 = "SELECT fileformat.id, fileformat.extension, fileformat.mime_type "
				. "FROM fileformat, fileformattoconference "
				. "WHERE fileformat.id = fileformattoconference.format_id "
				. "AND fileformattoconference.conference_id='" . $_SESSION["conf_id"] . "' ";
	$result00 = @mysql_query($query00) or dbErrorHandler("insert_paper_body()","insertdbinc.php",1754,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query00);
	$num00 = @mysql_num_rows($result00);//num

	for($i=0; $i<$num00; $i++)
	{
		$accepted_file_formats[$i]["id"] = mysql_result($result00,$i,"id");
		$accepted_file_formats[$i]["extension"] = mysql_result($result00,$i,"extension");
		$accepted_file_formats[$i]["mime_type"] = mysql_result($result00,$i,"mime_type");
	}//

	$temp = 0;
	$field_name = "paper_body";
	for($j=0; $j<count($accepted_file_formats); $j++)
	{
		if($type_check_of_papers_to_upload == "extension")
		{
			if($accepted_file_formats[$j]["extension"] == find_file_extension($_FILES[$field_name]['name']))
			{
				$temp = 1;//paper file type is accepted
				$arVals["format_id"] = $accepted_file_formats[$j]["id"];
			}//
		}//if
		elseif($type_check_of_papers_to_upload == "mime_type")
		{
			if($accepted_file_formats[$j]["mime_type"] == $_FILES[$field_name]['type'])
			{
				$temp = 1;//paper file type is accepted
				$arVals["format_id"] = $accepted_file_formats[$j]["id"];
			}//
		}//else
	}//for


	if($temp == 0)
	{
		//file type not accepted.
		Redirects(44,"?flg=133","");
	}

	//CHECK IF A PAPER_BODY EXISTS FOR THIS PAPER_ID
	$query01 = "SELECT pb.id, f.extension,f.mime_type, pb.filename, pb.filesize, pb.filecontent, pb.fileurl, pb.paper_type, pb.upload_type "
				. "FROM fileformat f, paperbody pb "
				. "WHERE f.id = pb.format_id AND pb.paper_id='" . $_POST["paper_id"] . "' ; ";

	$result01 = @mysql_query($query01) or dbErrorHandler("insert_paper_body()","insertdbinc.php",1793,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	$uploadnew = "no";
	$update = "no";
	$old_file_url = "";
	if($num01 == 0)//insert new manuscript, because there are no paper_bodies for this paper_id
	{

		######################
		//check if the author is allowed to upload a new manuscript.
		//Since there are no other paper bodies in the db for that paper, then there are not allowed any camera-ready versions of the paper.
		if ($coptions2D["author"]["ASM"] == 0)
		{
			Redirects(44,"?flg=147",""); //user not allowed to upload any manuscripts
		}//
		else
		{
			//All OK, do nothing
		}//
		######################

		$uploadnew = "yes";
		$update = "no";
	}
	else if($num01 == 1 || $num01 == 2) //upload/update camera-ready body OR update manuscript
	{

		###########################
		//check if the author is allowed to upload or update a new manuscript or a camera-ready version of the paper.
		if($num01 == 1)//user has uploaded only a manuscript
		{
			if($coptions2D["author"]["AUM"] == 0)
			{
				if($arVals["paper_type"] == "'manuscript'"){Redirects(44,"?flg=147","");}//user not allowed to update manuscript
			}
			if($coptions2D["author"]["ASCRP"] == 0)
			{
				if($arVals["paper_type"] == "'camera_ready'"){Redirects(44,"?flg=148","");}//user not allowed to insert camera-ready
			}
		}//if
		else if($num01 == 2)//user has already uploaded a manuscript and a camera_ready version of the paper
		{
			if($coptions2D["author"]["AUM"] == 0)
			{
				if($arVals["paper_type"] == "'manuscript'"){Redirects(44,"?flg=147","");}//user not allowed to update manuscript
			}
			if($coptions2D["author"]["AUCRP"] == 0)
			{
				if($arVals["paper_type"] == "'camera_ready'"){Redirects(44,"?flg=148","");}//user not allowed to update camera-ready
			}
		}//else if
		###########################

		//get the db data from $query01
		for($k=0; $k<$num01; $k++)
		{
			$paper_body_ar[$k]["id"] = mysql_result($result01,$k,"id");
			$paper_body_ar[$k]["fileextension"] = mysql_result($result01,$k,"extension");
			$paper_body_ar[$k]["filemime_type"] = mysql_result($result01,$k,"mime_type");
			$paper_body_ar[$k]["filename"] = mysql_result($result01,$k,"filename");
			$paper_body_ar[$k]["filesize"] = mysql_result($result01,$k,"filesize");
			$paper_body_ar[$k]["filecontent"] = mysql_result($result01,$k,"filecontent");
			$paper_body_ar[$k]["fileurl"] = mysql_result($result01,$k,"fileurl");
			$paper_body_ar[$k]["paper_type"] = mysql_result($result01,$k,"paper_type");
			$paper_body_ar[$k]["upload_type"] = mysql_result($result01,$k,"upload_type");

			if( $_POST["paper_type"] == $paper_body_ar[$k]["paper_type"] )
			{
				//then update the paper body of the selected paper_type
				$update = "yes";
				$uploadnew = "no";

				$old_file_url = $paper_body_ar[$k]["fileurl"];

				break;
			}//if
			else
			{
				$update = "no";
				$uploadnew = "yes";
			}
		}//for
	}//elseif


	if($paper_upload_type == "database")
	{
		$field_name = "paper_body"; //name of they type=file in the form
		$file = upload_to_database($field_name,44); //function returns array

		$arVals["filename"] = "'" . $file["filename"] . "'";
		$arVals["filesize"] = "'" . $file["filesize"] . "'";
		$arVals["filecontent"] = "'" . $file["filecontent"] . "'";
		$arVals["fileurl"] = "'NULL'";
		$arVals["format_id"] = "'" . $arVals["format_id"] . "'";
	}//if database
	if($paper_upload_type == "fileserver")
	{
		$result = ""; //reset value that is set in the uploadfile method

		//find the directory to upload the file
		$child_dir_name = "ConferenceID_" . $_SESSION["conf_id"];
		$full_path = create_directory($papers_upload_dir,$child_dir_name);

		//Inside the $full_path, if the file is a manuscript, save it in a directory called 'manuscripts'. Same if it's camera_ready
		if($arVals["paper_type"] == "'manuscript'") {$child_dir_name2 = "Manuscripts";}
		if($arVals["paper_type"] == "'camera_ready'") {$child_dir_name2 = "Camera_Ready";}
		$full_path2 = create_directory($full_path,$child_dir_name2);

		//paper file name
		$file_name = "PaperID" . $_SESSION["paper_id"] . "_";

		$field_name = "paper_body"; //name of they type=file in the form
		$file = upload_to_fileserver($field_name,44,$full_path2,$file_name); //Upload the file. Function returns array

		if ($result == "upload") {
			//echo "ok";//OK. File is uploaded with a new name, now insert paper body info to database!
		} elseif ($result == "noupload") {
			resendToForm(44,"?flg=135","");
		}//elseif

		$arVals["filename"] = "'" . $file["filename"] . "'";
		$arVals["filesize"] = "'" . $file["filesize"] . "'";
		$arVals["filecontent"] = "'NULL'";
		$arVals["fileurl"] = "'" . $file["fileurl"] . "'";
		$arVals["format_id"] = "'" . $arVals["format_id"] . "'";
	}// fileserver

	//upload new manuscript OR new camera-ready version of paper
	if($update == "no" && $uploadnew == "yes")
	{
		//insert into table paperbody the values of the $arVals table
		$query02 = insertQuery("paperbody", $arVals);

		$result02 = @mysql_query($query02) or dbErrorHandler("insert_paper_body()","insertdbinc.php",1925,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
		$insertid = mysql_insert_id();

		empty_paper_body_sessions();
		@mysql_close();//closes the connection to the DB

		Redirects(44,"?flg=132","");
	}//outer if

	//update manuscript or camera-ready version of paper
	if($update == "yes" && $uploadnew == "no")
	{
		if( $old_file_url != "NULL" )
		{
			//this means that the old file is in the fileserver.
			//so we have to delete the old file because we have already uploaded the new one
			if($arVals["paper_type"] == "'manuscript'") { $delete_path = $papers_upload_dir . "ConferenceID_" . $_SESSION["conf_id"] . "/" . "Manuscripts/";}
			if($arVals["paper_type"] == "'camera_ready'") { $delete_path = $papers_upload_dir . "ConferenceID_" . $_SESSION["conf_id"] . "/" . "Camera_Ready/";}

			$result = delete_from_fileserver($old_file_url,44,$delete_path);
		}//

		//update table paperbody the values of the $arVals table
		$query02 = "UPDATE paperbody SET filename = " . $arVals["filename"]
		. ", filesize = " . $arVals["filesize"]
		. ", filecontent = " . $arVals["filecontent"]
		. ", fileurl = " . $arVals["fileurl"]
		. ", format_id = " . $arVals["format_id"]
		. ", date_of_submition = " . $arVals["date_of_submition"]
		. ", upload_type = " . "'" . $paper_upload_type . "'"
		. " WHERE paper_id='" . $_POST["paper_id"] . "' AND paper_type='" . $_POST["paper_type"] . "' ; ";

		$result02 = @mysql_query($query02) or dbErrorHandler("insert_paper_body()","insertdbinc.php",1957,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
		$insertid = mysql_insert_id();

		empty_paper_body_sessions();
		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("insert_paper_body()");
		Redirects(44,"?flg=134","");
	}//outer if

}//insert_paper_body

//force_interest_levels
function force_interest_levels()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(46,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	//if(!isset($_POST)){ Redirects(46,"?flg=157","");}
	if((!isset($_SESSION["chairman"])) ||($_SESSION["chairman"] != TRUE) ){ Redirects(0,"",""); }

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("force_interest_levels()","insertdbinc.php",1982,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

    @mysql_select_db($database) or dbErrorHandler("force_interest_levels()","insertdbinc.php",1984,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	$reviewers = array(); //array that will store all the reviewers of the conference
	$papers = array(); //array that will store all the papers of the conference
	$interest = array(); //array that will store all the interest entries of the conference

	$wanted = array(); //array that will contain all that data that would be forced in the
			//interests table

	//$query01 = "SELECT user_id FROM usertype WHERE type='reviewer' AND conference_id=" . $_SESSION["conf_id"] . " ;";
	$query01 = "SELECT user.id, user.fname, user.lname FROM user, usertype WHERE user.id = usertype.user_id AND usertype.type='reviewer' AND usertype.conference_id=" . $_SESSION["conf_id"] . " ORDER BY user.id ;";
	$query02 = "SELECT id, user_id, authors FROM paper WHERE conference_id=" . $_SESSION["conf_id"] . " ;";
	$query03 = "SELECT user_id, paper_id, level_of_interest, conflict_by_author FROM interest WHERE conference_id=" . $_SESSION["conf_id"] . " ;";

	$result01 = @mysql_query($query01) or dbErrorHandler("force_interest_levels()","insertdbinc.php",1999,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num01

	$result02 = @mysql_query($query02) or dbErrorHandler("force_interest_levels()","insertdbinc.php",2002,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num02

	$result03 = @mysql_query($query03) or dbErrorHandler("force_interest_levels()","insertdb.inc.ph",2005,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$num03 = @mysql_num_rows($result03);//num03

	if($num01 == 0)
	{
		//there are no reviewers for this conference
		Redirects(46,"?flg=139","");
		//echo "hey";
	}//
	if($num02 == 0)
	{
		//there are no papers for this conference
		Redirects(46,"?flg=140","");
	}//
	if($num03 == 0)
	{
		//no reviewer has entered interest for any paper
		Redirects(46,"?flg=141","");
	}//


	//load the data to the arrays

	//fill the reviewers array
	for($i=0; $i<$num01; $i++)
	{
		$reviewers[$i]["id"] = mysql_result($result01,$i,"id");
		$reviewers[$i]["fname"] = mysql_result($result01,$i,"fname");
		$reviewers[$i]["lname"] = mysql_result($result01,$i,"lname");
	}//for i

	//fill the papers array
	for($j=0; $j<$num02; $j++)
	{
		$papers[$j]["paper_id"] = mysql_result($result02,$j,"id");
		$papers[$j]["user_id"] = mysql_result($result02,$j,"user_id");
		$papers[$j]["authors"] = mysql_result($result02,$j,"authors");
	}//for j

	//fill the interests array
	for($z=0; $z<$num03; $z++)
	{
		$interests[$z]["user_id"] = mysql_result($result03,$z,"user_id");
		$interests[$z]["paper_id"] = mysql_result($result03,$z,"paper_id");

		if(mysql_result($result03,$z,"level_of_interest") == NULL){ $interests[$z]["level_of_interest"] = "-";}
		else{ $interests[$z]["level_of_interest"] = mysql_result($result03,$z,"level_of_interest");}

		if(mysql_result($result03,$z,"conflict_by_author") == NULL){ $interests[$z]["conflict_by_author"] = "-";}
		else{ $interests[$z]["conflict_by_author"] = mysql_result($result03,$z,"conflict_by_author");}

	}//for
	//print_r ($interests);

	//find out which authors haven't entered levels of interest for a paper.
	//reviewers that happen to also be the authors of a paper are excluded.
	for($i=0; $i<$num01; $i++)//for the reviewers array
	{
		$reviewer_name_type_1 = strtolower($reviewers[$i]["fname"] . " " . $reviewers[$i]["lname"]);
		$reviewer_name_type_2 = strtolower($reviewers[$i]["lname"] . " " . $reviewers[$i]["fname"]);

		for($j=0; $j<$num02; $j++)//for the papers array
		{
			//if the reviewer is the user that submitted this paper, then exclude him.
			if( $reviewers[$i]["id"] == $papers[$j]["user_id"]){continue;}
			//if the reviewers name is included in the authors list of the paper.
			if(strchr($papers[$j]["authors"], $reviewer_name_type_1)){continue;}
			//if the reviewers name is included in the authors list of the paper.
			if(strchr($papers[$j]["authors"], $reviewer_name_type_2)){continue;}

			for($z=0; $z<$num03; $z++)//for the interests array
			{
				//If an entry with the reviewers id, and the papers id exists in the DB table interest, then
				//either the reviewer has already entered his interest for this paper (which excludes him from the
				//"force interests" action, or an author has entered conflict with him (which includes him in the action
				if( ($reviewers[$i]["id"] == $interests[$z]["user_id"]) && ($papers[$j]["paper_id"] == $interests[$z]["paper_id"]))
				{
					if($interests[$z]["level_of_interest"] != "-")
					{
						//Reviewer who has already enter his interest

						//echo $j . " " . $z . " <font color=\"blue\">";
						//echo "Reviewer who has entered his interest: " . "<b> " . $reviewers[$i]["id"] . " </b>" . $reviewers[$i]["fname"] . " " . $reviewers[$i]["lname"];
						//echo " for paper with id: " . $papers[$j]["paper_id"] . "<br>";
						//echo "</font>";

						$wanted[$i . " " . $j]["user_id"] = $reviewers[$i]["id"];
						$wanted[$i . " " . $j]["paper_id"] = $papers[$j]["paper_id"];
						$wanted[$i . " " . $j]["action"] = "nothing";

						break;
					}
					elseif($interests[$z]["level_of_interest"] == "-")
					{
						//Reviewers who authors have conflicts with for this paper

						//update
						//echo $j . " " . $z . " <font color=\"green\">";
						//echo "Author has conflict with him: " . "<b> " . $reviewers[$i]["id"] . " </b>" . $reviewers[$i]["fname"] . " " . $reviewers[$i]["lname"];
						//echo " for paper with id: " . $papers[$j]["paper_id"] . "<br>";
						//echo "</font>";

						$wanted[$i . " " . $j]["user_id"] = $reviewers[$i]["id"];
						$wanted[$i . " " . $j]["paper_id"] = $papers[$j]["paper_id"];
						$wanted[$i . " " . $j]["action"] = "update";
						break;
					}
					continue;
				}//if
				elseif( ($reviewers[$i]["id"] != $interests[$z]["user_id"]) || ($papers[$j]["paper_id"] != $interests[$z]["paper_id"]))
				{
					//Reviewer didn't enter his levels of interest for this paper,
					//and no author had any conflicts with him.
					//SO insert new entry in the DB table interest

					$wanted[$i . " " . $j]["user_id"] = $reviewers[$i]["id"];
					$wanted[$i . " " . $j]["paper_id"] = $papers[$j]["paper_id"];
					$wanted[$i . " " . $j]["action"] = "insert";
				}
			}//for z, interests

		}//for j, papers
		//echo "<br>";
	}//for i, reviewers

	reset($wanted);
	$temp = 0;
	while (list($key, $val) = each ($wanted))
	{
		$arVals["user_id"] = $wanted[$key]["user_id"];
		$arVals["paper_id"] = $wanted[$key]["paper_id"] ;
		$arVals["conference_id"] = $_SESSION["conf_id"];

		if($wanted[$key]["action"] == "insert")
		{
			//echo "INSERT: " . $wanted[$key]["user_id"] . " " . $wanted[$key]["paper_id"] . "<br>";
			$arVals["level_of_interest"] = "1";
			$arVals["conflict"] = "0";

			$query_insert = insertQuery("interest", $arVals);
			$result_insert = @mysql_query($query_insert) or dbErrorHandler("force_interest_levels()","insertdbinc.php",2144,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_insert);
			$insertid_insert = mysql_insert_id();
			$temp++;
		}//if
		elseif($wanted[$key]["action"] == "update")
		{
			//echo "UPDATE: " . $wanted[$key]["user_id"] . " " . $wanted[$key]["paper_id"] . "<br>";
			$query_update = "UPDATE interest "
					. " SET level_of_interest = '1', conflict = '0' "
					. " WHERE paper_id='" . $arVals["paper_id"] . "' AND user_id='" . $arVals["user_id"] . "'; ";
			$result_update = @mysql_query($query_update) or dbErrorHandler("force_interest_levels()","insertdbinc.php",2154,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_update);
			$insertid_update = mysql_insert_id();
			$temp++;
		}//elseif
	}//while

	@mysql_close();//closes the connection to the DB

	if($temp == 0)
	{
		//no DB entries have been effected
		Redirects(46,"?flg=142","");
	}
	else
	{
		//force action completed. Show message.
		save_to_usersactionlog("force_interest_levels()");
		Redirects(46,"?flg=143","");
	}
}//force_interest_levels()

//insert_conference_file_format
function insert_conference_file_format()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "chairman_file_formats") . $csrf_password_generator;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(47,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(47,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(47,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "format_id"=>"", "conference_id"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "format_id"=>"", "conference_id"=>"");
	$arValsMaxSize = array();
	$arValsValidations = array("format_id"=>"/^[0-9]([0-9]*)/");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val =="") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
		$arVals[$key] = trim($arVals[$key]);
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


	/**********************************************************************************************
	Make sure session variables have been set and then check for required fields
	otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,47,"");
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,47,"");
	// make sure the variables are in the accepted range

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,47,"");

	/**********************************************************************************************
	Check the DB for records...
	**********************************************************************************************/
	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("insert_conference_file_format()","insertdbinc.php",2240,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

	@mysql_select_db($database) or dbErrorHandler("insert_conference_file_format()","insertdbinc.php",2242,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//check if this file format is already in the conference file formats list
	$query_001 = "SELECT format_id FROM fileformattoconference WHERE format_id = '" . $_SESSION["format_id"] . "'AND conference_id='" . $_SESSION["conference_id"] . "' ;";

	$result = @mysql_query($query_001) or dbErrorHandler("insert_conference_file_format()","insertdbinc.php",2248,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_001);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if ($row[0] > 0)
	{
		@mysql_close();//closes the connection to the DB
		redirects(47,"","?flg=144");
	}//if
	else if($row[0] == 0)
	{
		$query = insertQuery("fileformattoconference", $arVals);
		$result = @mysql_query($query) or dbErrorHandler("insert_conference_file_format()","insertdbinc.php",2260,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
		$insertid = mysql_insert_id();

		unset($_SESSION["UNSELECTED_FILEFORMATS"]); //resets the unselected fileformats combo box

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("insert_conference_file_format()");
		redirects(47,"","?flg=145");
	}//else if
	else{
		//do nothing
	}//else

}//insert_conference_file_format()

//assign_reviewers_to_paper2()
function assign_reviewers_to_paper2()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;
	global $csrf_password_generator;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(48,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(48,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "assign_reviewersf") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(48,"?flg=157","");} else { unset($_POST["csrf"]);}

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("assign_reviewers_to_paper2()","insertdbinc.php",2289,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

	@mysql_select_db($database) or dbErrorHandler("assign_reviewers_to_paper2()","insertdbinc.php",2291,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$ptr_ar = array();
	$no_delete = array();

	$query = "SELECT papertoreviewer.paper_id, papertoreviewer.user_id, user.fname, user.lname "
			. "FROM papertoreviewer, user "
			. "WHERE user.id = papertoreviewer.user_id "
			. "AND papertoreviewer.conference_id = '" . $_SESSION["conf_id"] . "'; ";

	$result = @mysql_query($query) or dbErrorHandler("assign_reviewers_to_paper2()","insertdbinc.php",2302,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	for($i=0; $i<$num; $i++)
	{
		$paper_id = mysql_result($result,$i,"paper_id");
		$reviewer_id = mysql_result($result,$i,"user_id");

		$reviewer_fname = mysql_result($result,$i,"fname");
		$reviewer_lname = mysql_result($result,$i,"lname");

		//reviewers that are already assigned to the papers
		$ptr_ar[$paper_id . "-" .  $reviewer_id] = $reviewer_fname . " " . $reviewer_lname;
	}//for

	reset($_POST);
	while (list($key, $val) = each ($_POST))
	{
		if($val != "")
		{
			$ptr = explode("-",$key);
			//reviewers just selected from the form to be assigned to the papers.
			$ar_var[$ptr[0] . "-" . $val] = "1";
			$ar[$key] = $val;
		}//if
	}//while

	reset($ar_var);
	reset($ptr_ar);
	while (list($key1, $val1) = each ($ar_var))
	{
		reset($ptr_ar);
		while (list($key2, $val2) = each ($ptr_ar))
		{
			if($key1 == $key2)
			{
				//Select which from the selected (by the form) reviewers
				//are already assigned to that paper.

				//echo "ALREADY ASSIGNED" . " ";
				$temp = explode("-",$key2);
				//echo "Paper_id: " . $temp[0] . " R_id: " . $temp[1] . " - " . $ptr_ar[$key2] . "<br>";

				$ar_var[$key1]=0; //remove that user from the reviewers selected from the form
				$no_delete[$key2] = 1; //this assignment should not be deleted
				continue;
			}//
		}//inner while
		if($ar_var[$key1]==1)
		{
			//Select which from the selected (by the form) reviewers
			//are not assigned to that paper.
			//These assignments would be inserted in the DB table 'papertoreviewer'

			//echo " " . " ";
			$temp = explode("-",$key1);
			//echo "Paper_id: " . $temp[0] . " R_id: " . $temp[1] . " - " . $ar_var[$key1] . "<br>";

			$arVals["paper_id"] = $temp[0];
			$arVals["user_id"] = $temp[1];
			$arVals["conference_id"] = $_SESSION["conf_id"];

			$query_i = insertQuery("papertoreviewer", $arVals);
			$result_i = @mysql_query($query_i) or dbErrorHandler("assign_reviewers_to_paper2()","insertdbinc.php",2366,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_i);
			$insertid = mysql_insert_id();
		}
	}//outer while

	//select the assignments to be deleted from the DB table 'papertoreviewer'
	reset($ptr_ar);
	reset($no_delete);
	while (list($key1, $val) = each ($ptr_ar))
	{
		reset($no_delete);
		while (list($key2, $val) = each ($no_delete))
		{
			if($key1 == $key2)
			{
				$ptr_ar[$key1] = 0;
				continue;
			}
		}//while
		if($ptr_ar[$key1] != "")
		{
			//echo "DELETE: " . $ptr_ar[$key1] . "<br>";

			$temp_d = explode("-",$key1);

			$query_d = "DELETE FROM papertoreviewer WHERE paper_id='" . $temp_d[0] . "' AND user_id='" . $temp_d[1] . "' ; ";
			$result_d = @mysql_query($query_d) or dbErrorHandler("assign_reviewers_to_paper2()","insertdbinc.php",2392,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_d);
			$num_d = @mysql_num_rows($result_d);//num
		}
	}//while

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("assign_reviewers_to_paper2()");
	Redirects(48,"?flg=149","");
}//assign_reviewers_to_paper2()

//insert_paper_interest_level2()
function insert_paper_interest_level2()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "set_intesest_levelsf") . $csrf_password_generator;

	if($coptions1D["CIA"] == 0){ Redirects(49,"?flg=156",""); }//check if conference is active
	else
	{
		if($coptions1D["RELIC"] == 0){ Redirects(49, "?flg=160", "");}//check if reviewers are allowed to enter levels of interest and conflicts
	}
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(49,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(49,"?flg=157","");} else { unset($_POST["csrf"]);}

	$user_id = $_SESSION["logged_user_id"];//$user_id is the id of the logged user
	$conference_id = $_SESSION["conf_id"];//$conference_id is the id of the present conference

	$array = array();
	$interests_ar = array();
	$array = array();
	$update_ar = array();

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("insert_paper_interest_level2()","insertdbinc.php",2427,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

	@mysql_select_db($database) or dbErrorHandler("insert_paper_interest_level2()","insertdbinc.php",2429,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Get the interest levels and conflicts, for all the papers of this user, for this conference.
	$query01 = "SELECT user_id, paper_id, level_of_interest, conflict "
			 . " FROM interest"
			 . " WHERE conference_id = '" . $conference_id . "' AND user_id = '" . $user_id . "';";

	$result01 = @mysql_query($query01) or dbErrorHandler("insert_paper_interest_level2()","insertdbinc.php",2437,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//This user hasn't entered any interest or conflict for any paper of this conference.
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			$paper_id = mysql_result($result01,$i,"paper_id");

			//$interests_ar[$paper_id]["user_id"] = mysql_result($result01,$i,"user_id");
			$interests_ar[$paper_id]["level_of_interest"] = mysql_result($result01,$i,"level_of_interest");
			$interests_ar[$paper_id]["conflict"] = mysql_result($result01,$i,"conflict");
		}//for
	}//else

	//get all the values from the form
	reset($_POST);
	while (list($key, $val) = each($_POST))
	{
		$exploded_string = explode("_",$key);
		switch($exploded_string[0])
		{
			case "i":
				//Interest
				break;
			case "c":
				//Conflict
				break;
			default:
				//do nothing
				break;
		}//switch

		if($exploded_string[0] == "i")
		{
			//echo "interest for paper : " . $exploded_string[1] . " is " . $val . "<br>";
			$array[$exploded_string[1]]["level_of_interest"] = $val;
		}//if
		if($exploded_string[0] == "c")
		{
			//if the checkbox is 'off' then it doesn't show in the $_POST. So by default if the checkbox appears, it's 'on'
			$array[$exploded_string[1]]["conflict"] = "1";
		}//if
	}//while

	//set the default value for the checkboxes that are not checked
	reset($array);
	while(list($key, $val) = each($array))
	{
		if($array[$key]["conflict"] == "")
		{
			$array[$key]["conflict"] = 0;
		}
	}//while

	//Find if the interest of a paper is going to be UPDATED or INSERTED in the DB table 'interest'
	reset($array);
	reset($interests_ar);
	while(list($key01, $val01) = each($interests_ar))
	{
		reset($array);
		while(list($key02, $val02) = each($array))
		{
			if($key01 == $key02)
			{
				$update_ar[$key01] = $array[$key01]; //the $update_ar gets the values of the form, for the to-be-updated DB row;
				$array[$key01] = "-"; //unset this row from the $interests_ar;
				break;
			}//if
		}//inner while
	}//outer while

	reset($array);
	//the values in $array are going to be INSERTED
	while(list($key, $val) = each($array))
	{
		if($array[$key] == "-")
		{
			//do nothing
		}//if
		else
		{
			$arVals["paper_id"] = $key;
			$arVals["user_id"] = $_SESSION["logged_user_id"];
			$arVals["conference_id"] = $_SESSION["conf_id"];
			$arVals["level_of_interest"] = $array[$key]["level_of_interest"];
			$arVals["conflict"] = $array[$key]["conflict"];

			$query_i = insertQuery("interest", $arVals);
			//echo $query_i . "<br>";
			$result_i = @mysql_query($query_i) or dbErrorHandler("insert_paper_interest_level2()","insertdbinc.php",2532,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_i);
			$insertid = mysql_insert_id();
		}//else
	}//while

	reset($update_ar);
	//the values in $update_ar are going to be used for UPDATE
	while(list($key, $val) = each($update_ar))
	{
		$arVals["paper_id"] = $key;
		$arVals["user_id"] = $_SESSION["logged_user_id"];
		$arVals["conference_id"] = $_SESSION["conf_id"];
		$arVals["level_of_interest"] = $update_ar[$key]["level_of_interest"];
		$arVals["conflict"] = $update_ar[$key]["conflict"];

		$query_u = "UPDATE interest "
					. " SET "
					. " level_of_interest = '" .  $arVals["level_of_interest"] . "',  "
					. " conflict = '" . $arVals["conflict"] . "' "
					. " WHERE conference_id='" .  $arVals["conference_id"] . "' AND paper_id='" . $arVals["paper_id"] . "' AND user_id='" . $arVals["user_id"] . "' ";

		$result_u = @mysql_query($query_u) or dbErrorHandler("insert_paper_interest_level2()","insertdbinc.php",2553,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_u);

	}//while

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("insert_paper_interest_level2()");
	Redirects(49,"?flg=150","");
}//insert_paper_interest_level2()

//for page reviews.php
function review_paper()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator . hash('sha256', "reviews") . $csrf_password_generator;

	if($coptions1D["CIA"] == 0){ Redirects(52,"&flg=156","?paperid=" . $_POST["paper_id"]); }//check if conference is active
	else
	{
		if($coptions1D["RDPR"] == 0){ Redirects(52,"&flg=161","?paperid=" . $_POST["paper_id"]); }//check if reviewers are allowed to download and review their assigned papers.
	}
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(52,"&flg=157","?paperid=" . $_POST["paper_id"]); }
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(52,"&flg=157","?paperid=" . $_POST["paper_id"]);} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "user_id"=>"", "paper_id"=>"", "conference_id"=>"",
					"referee_name"=>"", "originality"=>"", "significance"=>"", "quality"=>"",
					"relevance"=>"", "presentation"=>"", "overall"=>"",
					"expertise"=>"", "confidential"=>"", "contributions"=>"",
					"positive"=>"", "negative"=>"",
					"further"=>"", "date_of_submition"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "user_id"=>"", "paper_id"=>"", "conference_id"=>"",
					"originality"=>"", "significance"=>"", "quality"=>"",
					"relevance"=>"", "presentation"=>"", "overall"=>"",
					"expertise"=>"", "confidential"=>"", "contributions"=>"",
					"positive"=>"", "negative"=>"",
					"further"=>"", "date_of_submition"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array( "referee_name"=>80, "originality"=>1,
					"significance"=>1, "quality"=>1, "relevance"=>1,
					"presentation"=>1, "overall"=>1,
					"confidential"=>3000, "contributions"=>3000,
					"positive"=>3000, "negative"=>3000,
					"further"=>3000);
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array("originality"=>"/^[1-7]$/", "significance"=>"/^[1-7]$/",
								"quality"=>"/^[1-7]$/", "relevance"=>"/^[1-7]$/",
								"presentation"=>"/^[1-7]$/", "overall"=>"/^[1-7]$/");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL"; $arVals[$key] = NULL; } //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries
		else
		{
			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes($val);
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
			$arVals[$key] = trim($arVals[$key]);
		}

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = strtolower($val);
		}
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/
		$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";

	}//while
	//print_r($arVals); //print the whole array

	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	$_SESSION["rev_varcheck"] = "1"; // variables check not ok
	// check to see if these variables have been set...
	variablesSet($arValsRequired,52,"?paperid=" . $_POST["paper_id"]);//send 52 because the page we want is reviews.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,52,"?paperid=" . $_POST["paper_id"]);//send 52 because the page we want is reviews.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,52,"?paperid=" . $_POST["paper_id"]);//send 52 because the page we want is reviews.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,52,"?paperid=" . $_POST["paper_id"]);//send 52 because the page we want is reviews.php

	unset($_SESSION["rev_varcheck"]); // variables check ok

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("review_paper()","insertdbinc.php",2659,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

	@mysql_select_db($database) or dbErrorHandler("review_paper()","insertdbinc.php",2661,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	if($_SESSION["updatereview"] == "no")
	{
		//insert this review to the DB table 'review'

		$insert_query = insertQuery("review", $arVals);

		$insert_result = @mysql_query($insert_query) or dbErrorHandler("review_paper()","insertdbinc.php",2670,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $insert_query);
		$insertid = mysql_insert_id();

		empty_review_sessions();

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("review_paper()");
		Redirects(51,"?flg=153","");
	}//if

	elseif($_SESSION["updatereview"] == "yes")
	{
		//update this review
		$update_query = "UPDATE review SET referee_name = " . $arVals["referee_name"]
		. ", originality = " . $arVals["originality"]
		. ", significance = " . $arVals["significance"]
		. ", quality = " . $arVals["quality"]
		. ", relevance = " . $arVals["relevance"]
		. ", presentation = " . $arVals["presentation"]
		. ", overall = " . $arVals["overall"]
		. ", expertise = " . $arVals["expertise"]
		. ", confidential = " . $arVals["confidential"]
		. ", contributions = " . $arVals["contributions"]
		. ", positive = " . $arVals["positive"]
		. ", negative = " . $arVals["negative"]
		. ", further = " . $arVals["further"]
		. ", date_of_submition = " . $arVals["date_of_submition"]
		. " WHERE paper_id=" . $arVals["paper_id"] . " AND user_id=" . $arVals["user_id"] . " ; ";

		$update_result = @mysql_query($update_query) or dbErrorHandler("review_paper()","insertdbinc.php",2699,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $update_query);
		$updateid = mysql_insert_id();

		empty_review_sessions();

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("review_paper()");
		Redirects(51,"?flg=154","");
	}//else

}//review_paper()

############################
############################

?>
