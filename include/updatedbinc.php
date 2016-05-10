<?php
###############################################################
/*
	change_password(),
	update_conference($user_type)
	remove_chairman_from_conference($chairman_id,$conference_id,$action),
	update_user_profile(),
	remove_reviewer_from_committee($chairman_id,$conference_id,$action),
	update_conference_options(),
	update_file_format(),
	update_paper(),
	update_paper_interest_level(),
	update_assign_reviewers_to_paper(),
	enter_conflicts_with_reviewers(),
	remove_file_format_from_conference($format_id),
	accept_papers_for_conference()
*/
###############################################################

//change_password()
function change_password()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(4,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(4,"?flg=157","");} else { unset($_POST["csrf"]);}

	$_SESSION["STEP"] = "";
	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	if(isset($_POST["email"]) || $_POST["email"]!="")
	{
		//if the email field is filled, that means that the user is in the 1st step to change his password.
		$arVals = array( "email"=>"");
		$arValsRequired = array( "email"=>"");
		$arValsMaxSize = array( "email"=>35);
		$arValsValidations = array( "email"=>"/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/");
	}//if
	else if(isset($_POST["security_answer"]) || $_POST["security_answer"]!="")
	{
		//if the security_answer field is filled, that means that the user is in the 2nd step to change his password.
		$arVals = array( "security_answer"=>"","birthday"=>"");
		$arValsRequired = array("security_answer"=>"",
					"birthday_month"=>"", "birthday_day"=>"", "birthday_year"=>"");
		$arValsMaxSize = array("security_answer"=>30,
					"birthday_month"=>2, "birthday_day"=>2, "birthday_year"=>4);
		$arValsValidations = array("birthday_day"=>"/^[0-9]([0-9]*)/", "birthday_year"=>"/^[0-9]([0-9]*)/");
		$birthday = $_POST["birthday_year"] . "-" . $_POST["birthday_month"] . "-" . $_POST["birthday_day"];
	}//if
	else if(isset($_POST["password"]) || $_POST["password"]!="")
	{
		//if the password field is filled, that means that the user is in the 3nd and final step to change his password.
		$arVals = array( "password"=>"");
		$arValsRequired = array("password"=>"");
		$arValsMaxSize = array("password"=>15);
		$arValsValidations = array();
	}//else

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		if($key == "birthday_month")
		{
			$arVals["birthday"] =  (get_magic_quotes_gpc()) ? $birthday : addslashes(trim($birthday));
			$arVals["birthday"] = htmlentities($arVals["birthday"]);
		}
		else if($key != "birthday_month" && $key != "birthday_day" && $key != "birthday_year" && $key != "repassword"){
			//we don't want the birthday_month, birthday_day, birthday_year, repassword fields and field values inserted into the table
			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
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
			$arVals["birthday"] =  "'" . $arVals["birthday"] . "'";
		}
		else if($key != "birthday_month" && $key != "birthday_day" && $key != "birthday_year" && $key != "repassword" && $key != "password" ){
			$arVals[$key] = "'" . strtolower($arVals[$key]) . "'";
		}
	}//while

	if ($_POST["password"] != $_POST["repassword"]){resendToForm("?flg=107",4,"");}
	variablesSet($arValsRequired,4,"");//send 4 because the page we want is change_password.php
	variablesFilled($arValsRequired,4,"");//send 4 because the page we want is change_password.php
	variablesCheckRange($arValsMaxSize,4,"");//send 4 because the page we want is change_password.php
	variablesValidate($arValsValidations,4,"");//send 4 because the page we want is change_password.php

	// check for the email already in the database...

	$query01 = "SELECT security_question FROM user where email = '" . $_SESSION["email"] . "' ";
	$query02 = "SELECT security_answer, birthday FROM user where email = '" . $_SESSION["email"] . "' ";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("change_password()","updatedbinc.php",112,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("change_password()","updatedbinc.php",113,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");


	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	if(isset($_POST["email"]) || $_POST["email"]!="")
	{
		//if the email field is filled, that means that the user is in the 1st step to change his password.

		$result = @mysql_query($query01) or dbErrorHandler("change_password()","updatedbinc.php",124,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
		$row = mysql_fetch_row($result);
		$num = mysql_num_rows($result);//num

		for($i=0; $i<$num; $i++)
		{
			$security_question = mysql_result($result,$i,"security_question");
			$_SESSION["security_question"] = $security_question;
		}//

		if ($num < 1) {
			//if his email is not found in the  DB then he isn't a user of the system
			//redirect him to the same page and let him try again
			$_SESSION["STEP"] = 0;
			//Redirects(4,"?step=0&flg=110","");
			Redirects(4,"?flg=110","");
		} else {
			if($_SESSION["security_question"] == NULL || $_SESSION["security_question"] == "")
			{
				$_SESSION["STEP"] = "temp";
				//then the user doesn't have to answer anything in order to change his password
				Redirects(4,"","");
			}
			else if($_SESSION["security_question"] != NULL || $_SESSION["security_question"] != "")
			{
				$_SESSION["STEP"] = 2;
				//redirect to the page and let the user answer his security question
				//code 2 is for the second step to change password
				//Redirects(4,"?step=2","");
				Redirects(4,"","");
			}//else if
		}

	}//if
	else if(isset($_POST["security_answer"]) || $_POST["security_answer"]!="")
	{
		//if the security_answer field is filled, that means that the user is in the 2nd step to change his password.
		$result = @mysql_query($query02) or dbErrorHandler("change_password()","updatedbinc.php",162,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
		$row = mysql_fetch_row($result);
		$num = mysql_num_rows($result);//num

		for($i=0; $i<$num; $i++)
		{
			$db_security_answer = mysql_result($result,$i,"security_answer");
			$db_birthday = mysql_result($result,$i,"birthday");

			$_SESSION["security_answer"] = $db_security_answer;
			$_SESSION["birthday"] = $db_birthday;
		}//

		if ($num < 1) {
			$_SESSION["STEP"] = 0;
			//if his email is not found in the  DB then he isn't a user of the system
			//redirect him to the same page and let him try again
			//Redirects(4,"?step=0&flg=110","");
			Redirects(4,"?flg=110","");
		} else {
			if((strtolower($_POST["security_answer"]) == $_SESSION["security_answer"]) && ($birthday == $_SESSION["birthday"]))
			{
				$_SESSION["STEP"] = 3;
				//then the user can change his password
				//Redirects(4,"?step=3","");
				Redirects(4,"","");
			}
			else if(($_POST["security_answer"] != $_SESSION["security_answer"]) || ($birthday != $_SESSION["birthday"]))
			{
				$_SESSION["STEP"] = 2;
				//the user can't change his password
				//redirect him to the same page and let him try again
				//Redirects(4,"?step=2&flg=111","");

				Redirects(4,"?flg=111","");
			}//else if
		}

	}//if
	else if(isset($_POST["password"]) || $_POST["password"]!="")
	{
		//if the password field is filled, that means that the user is in the 3nd and final step to change his password.

		/* WHEN YOU INSERT USE sha1 for Passwords!!!! */
		$password = $arVals["password"];
		$arVals["password"] = "'".hash('sha256', $arVals["password"])."'";

		//update table users with the new password
		$query = "UPDATE user SET password = " . $arVals["password"] . " WHERE email='" . $_SESSION["email"] . "' ;";

		$result = @mysql_query($query) or dbErrorHandler("change_password()","updatedbinc.php",212,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
		$insertid = mysql_insert_id();

		//prepare the message of the email
		//$message = "Hello " . $fname . " " . $lname . ",\n\n";
		$message = "Your new account LogIn Info:\r\n\r\n";
		$message .= "Your username: " . $_SESSION["email"] . "\n";
		$message .= "Your password: " . $password . "\n\n\n";
		$message .= "Do not reply to this email.";
		//send the email
		registration_email($_SESSION["email"],"PaperReview: Account Password Changed", $message);

		$_SESSION["STEP"] = 4;
		save_to_usersactionlog("change_password()");
		Redirects(4,"","");//change password complete
	}//else

	// Now we close the connection...
	@mysql_close();//closes the connection to the DB
}//change_password()

#######################################
#######################################

function update_conference($user_type)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST))
	{
		if($user_type == "administrator"){Redirects(7,"?flg=157","");}
		elseif($user_type == "chairman"){Redirects(10,"?flg=157","");}
	}

	//check for CSRF (Cross Site Request Forgery)
	if($user_type == "administrator"){ $csrf_temp = hash('sha256', "conferences") . $csrf_password_generator;}
	elseif($user_type == "chairman"){ $csrf_temp = hash('sha256', "update_conference") . $csrf_password_generator;}
	else{Redirects(0,"","");}

	if($_POST["csrf"] != $csrf_temp)
	{
		if($user_type == "administrator"){Redirects(7,"?flg=157","");}
		if($user_type == "chairman"){Redirects(10,"?flg=157","");}
		else{Redirects(8,"","");}
	}
	else { unset($_POST["csrf"]);}

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
	$arValsMaxSize = array( "name"=>"250", "alias"=>"50", "place"=>"100", "date_conference_held"=>100,
					"contact_email"=>"35", "contact_phone"=>"10", "website"=>"80",
					"comments"=>"2000",
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

	$deadline = $_POST["deadline_year"] . "-" . $_POST["deadline_month"] . "-" . $_POST["deadline_day"];
	$abstracts_deadline = $_POST["abstracts_deadline_year"] . "-" . $_POST["abstracts_deadline_month"] . "-" . $_POST["abstracts_deadline_day"];
	$manuscripts_deadline = $_POST["manuscripts_deadline_year"] . "-" . $_POST["manuscripts_deadline_month"] . "-" . $_POST["manuscripts_deadline_day"];
	$camera_ready_deadline = $_POST["camera_ready_deadline_year"] . "-" . $_POST["camera_ready_deadline_month"] . "-" . $_POST["camera_ready_deadline_day"];
	$preferencies_deadline = $_POST["preferencies_deadline_year"] . "-" . $_POST["preferencies_deadline_month"] . "-" . $_POST["preferencies_deadline_day"];
	$reviews_deadline = $_POST["reviews_deadline_year"] . "-" . $_POST["reviews_deadline_month"] . "-" . $_POST["reviews_deadline_day"];

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
				$arVals["deadline"] =  (get_magic_quotes_gpc()) ? $deadline : addslashes(trim($deadline));
				$arVals["deadline"] =  "'" . trim($arVals["deadline"]) . "'";
				$arVals["deadline"] = htmlentities($arVals["deadline"]);
				break;
			case "abstracts_deadline_month":
				$arVals["abstracts_deadline"] =  (get_magic_quotes_gpc()) ? $abstracts_deadline : addslashes(trim($abstracts_deadline));
				$arVals["abstracts_deadline"] =  "'" . trim($arVals["abstracts_deadline"]) . "'";
				$arVals["abstracts_deadline"] = htmlentities($arVals["abstracts_deadline"]);
				break;
			case "manuscripts_deadline_month":
				$arVals["manuscripts_deadline"] =  (get_magic_quotes_gpc()) ? $manuscripts_deadline : addslashes(trim($manuscripts_deadline));
				$arVals["manuscripts_deadline"] =  "'" . trim($arVals["manuscripts_deadline"]) . "'";
				$arVals["manuscripts_deadline"] = htmlentities($arVals["manuscripts_deadline"]);
				break;
			case "camera_ready_deadline_month":
				$arVals["camera_ready_deadline"] =  (get_magic_quotes_gpc()) ? $camera_ready_deadline : addslashes(trim($camera_ready_deadline));
				$arVals["camera_ready_deadline"] =  "'" . trim($arVals["camera_ready_deadline"]) . "'";
				$arVals["camera_ready_deadline"] = htmlentities($arVals["camera_ready_deadline"]);
				break;
			case "preferencies_deadline_month":
				$arVals["preferencies_deadline"] =  (get_magic_quotes_gpc()) ? $preferencies_deadline : addslashes(trim($preferencies_deadline));
				$arVals["preferencies_deadline"] =  "'" . trim($arVals["preferencies_deadline"]) . "'";
				$arVals["preferencies_deadline"] = htmlentities($arVals["preferencies_deadline"]);
				break;
			case "reviews_deadline_month":
				$arVals["reviews_deadline"] =  (get_magic_quotes_gpc()) ? $reviews_deadline : addslashes(trim($reviews_deadline));
				$arVals["reviews_deadline"] =  "'" . trim($arVals["reviews_deadline"]) . "'";
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
				$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
				$arVals[$key] = "'" . trim(strtolower($arVals[$key])) . "'";
				break;
		}//switch

		//Load the session variables
		if ($val == "NULL"){
			$_SESSION[$key] = NULL;
		}//
		else{
			//set a session variable with name the name of the array field and value the value of the array value
			//$_SESSION[$key] = trim(strtolower($val));
			switch($key)
			{
			/*
				case "deadline_month":
					$_SESSION["deadline_month"] = find_month_from_month_no($_POST["deadline_month"]);
					break;
				case "abstracts_deadline_month":
					$_SESSION["abstracts_deadline_month"] = find_month_from_month_no($_POST["abstracts_deadline_month"]);
					break;
				case "manuscripts_deadline_month":
					$_SESSION["manuscripts_deadline_month"] = find_month_from_month_no($_POST["manuscripts_deadline_month"]);
					break;
				case "camera_ready_deadline_month":
					$_SESSION["camera_ready_deadline_month"] = find_month_from_month_no($_POST["camera_ready_deadline_month"]);
					break;
				case "preferencies_deadline_month":
					$_SESSION["preferencies_deadline_month"] = find_month_from_month_no($_POST["preferencies_deadline_month"]);
					break;
				case "reviews_deadline_month":
					$_SESSION["reviews_deadline_month"] = find_month_from_month_no($_POST["reviews_deadline_month"]);
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
				*/
				default:
					//set a session variable with name the name of the array field and value the value of the array value
					$_SESSION[$key] = trim(strtolower($val));
					break;
			}//load session swith
		}

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
	if($user_type == "administrator")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,7,"");//send 7 because the page we want is conferences.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,7,"");//send 7 because the page we want is conferences.php
		// make sure the variables are in the accepted range
		//variablesCheckRange($arValsMaxSize,7,"");//send 7 because the page we want is conferences.php
		// make sure fields are within the proper range... else cut off any extra...
		// we will use the function variablesCheckRange() instaid of this
		//variablesCheckRangeCutExtra($arValsMaxSize);

		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,31,"");//send 31 because the page we want is conferences.php
	}
	else if($user_type == "chairman")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,10,"");//send 10 because the page we want is update_conference.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,10,"");//send 10 because the page we want is update_conference.php
		// make sure the variables are in the accepted range
		variablesCheckRange($arValsMaxSize,10,"");//send 10 because the page we want is update_conference.php
		// make sure fields are within the proper range... else cut off any extra...
		// we will use the function variablesCheckRange() instaid of this
		//variablesCheckRangeCutExtra($arValsMaxSize);
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,10,"");//send 10 because the page we want is create_conference.php
	}
	else {
		logout();
	}


	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("update_conference()","updatedbinc.php",456,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("update_conference()","updatedbinc.php",457,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT COUNT(name) FROM conference where name = '".$_SESSION["name"]."'";

	$result = @mysql_query($query01) or dbErrorHandler("update_conference()","updatedbinc.php",462,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if (($row[0] != 0) && ($row[0] != 1)) {  // a conference with this name aleady exists in the database, because the row count > 0...
		//resendToForm("?flg=112",8,"");
	}

	//insert into table user the values of the $arVals table
	$query = "UPDATE conference SET place = " . $arVals["place"]
			. ", alias = " .$arVals["alias"]
			. ", date_conference_held = " . $arVals["date_conference_held"]
			. ", contact_email = " . $arVals["contact_email"]
			. ", contact_phone = ". $arVals["contact_phone"]
			. ", website = ". $arVals["website"]
			. ", comments = ". $arVals["comments"]
			. ", deadline = ". $arVals["deadline"]
			. ", abstracts_deadline = ". $arVals["abstracts_deadline"]
			. ", manuscripts_deadline = ". $arVals["manuscripts_deadline"]
			. ", camera_ready_deadline = ". $arVals["camera_ready_deadline"]
			. ", preferencies_deadline = ". $arVals["preferencies_deadline"]
			. ", reviews_deadline = ". $arVals["reviews_deadline"]
			. " WHERE id='" . $_SESSION["conf_id"] . "' ;";
			//$_SESSION["conf_id"] was filled from the find_conference() function

	$result = @mysql_query($query) or dbErrorHandler("update_conference()","updatedbinc.php",486,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	@mysql_close();//closes the connection to the DB

	save_to_usersactionlog("update_conference()");

	if($user_type == "administrator") {
		empty_conference_sessions();
		$_SESSION["updateconference"] = "no";
		Redirects(7,"?flg=121","");
	}
	elseif($user_type == "chairman") {
		//empty_conference_sessions();
		unset($_SESSION["updateconference"]);
		find_conference(); //Redirects(37,"?flg=121","");
	}
}//update_conference()

#######################################
#######################################

function remove_chairman_from_conference()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(22,"?flg=157","");}
	if((!isset($_SESSION["administrator"])) ||($_SESSION["administrator"] != TRUE) ){ Redirects(0,"",""); }

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "chairman_id"=>"", "conference_id"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "chairman_id"=>"", "conference_id"=>"");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.
	$arValsValidations = array( "chairman_id"=>"/^[0-9]([0-9]*)/", "conference_id"=>"/^[0-9]([0-9]*)/");

	reset($_POST);
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
	//print_r($arVals);

	// check to see if these variables have been set...
	variablesSet($arValsRequired,23,"");//send 23 because the page we want is chairmen_assignments.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,23,"");//send 23 because the page we want is chairmen_assignments.php
	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,23,"");

	@mysql_connect($db_host,$_SESSION["logged_user_email"],$_SESSION["logged_user_password"])
	or dbErrorHandler("remove_chairman_from_conference","updatedbinc.php",513,"Unable to connect to SQL as administrator");
	@mysql_select_db($database) or dbErrorHandler("remove_chairman_from_conference","updatedbinc.php",514,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "DELETE FROM usertype WHERE user_id = " . $arVals["chairman_id"] . " AND conference_id= " . $arVals["conference_id"] . " AND type='chairman'";
	$result = @mysql_query($query) or dbErrorHandler("remove_chairman_from_conference","updatedbinc.php",518,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	unset($_SESSION["UNASSIGNED_CHAIRMEN"]); //reload the unassigned chairmen combo box

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("remove_chairman_from_conference()");
	redirects(23,"","?flg=118");

}//remove_chairman_from_conference

#######################################
#######################################

function update_user_profile()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "update_user_profile") . $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(14,"&flg=157",""); }
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(14,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "fname"=>"", "lname"=>"",
					"address_01"=>"", "address_02"=>"", "address_03"=>"",
					"city"=>"", "country"=>"",
					"phone_01"=>"", "phone_02"=>"", "fax"=>"",
					"website"=>"", "security_question"=>"", "security_answer"=>"",
					"birthday"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "fname"=>"", "lname"=>"", "address_01"=>"",
							"phone_01"=>"", "security_question"=>"", "security_answer"=>"",
							"birthday_month"=>"", "birthday_day"=>"", "birthday_year"=>"");
	/*the array $arValsMaxSize stores the names of all the values of the form
	and the maximum size that each value is allowed to have
	*/
	$arValsMaxSize = array(	"fname"=>35, "lname"=>35,
					"address_01"=>100, "address_02"=>100, "address_03"=>100, "city"=>35, "country"=>35,
					"phone_01"=>10, "phone_02"=>10, "fax"=>10,
					"website"=>80,"security_answer"=>30,
					"birthday_month"=>2, "birthday_day"=>2, "birthday_year"=>4);
	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array("website"=>"/(http:\/\/)?([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/",
						"phone_01"=>"/^[0-9]([0-9]+)/","phone_02"=>"/^[0-9]([0-9]+)/", "fax"=>"/^[0-9]([0-9]+)/",
						"birthday_day"=>"/^[0-9]([0-9]*)/", "birthday_year"=>"/^[0-9]([0-9]*)/");

	$birthday = $_POST["birthday_year"] . "-" . $_POST["birthday_month"] . "-" . $_POST["birthday_day"];
	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		if($key == "birthday_month")
		{
			$arVals["birthday"] =  (get_magic_quotes_gpc()) ? $birthday : addslashes(trim($birthday));
			$arVals["birthday"] = htmlentities($arVals["birthday"]);
		}
		else if($key != "birthday_month" && $key != "birthday_day" && $key != "birthday_year" && $key != "repassword"){
			//we don't want the birthday_month, birthday_day, birthday_year, repassword fields and field values inserted into the table
			$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
			$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
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
			$arVals["birthday"] =  "'" . trim($arVals["birthday"]) . "'";
		}
		else if($key != "birthday_month" && $key != "birthday_day" && $key != "birthday_year" && $key != "repassword" && $key != "password" ){
			$arVals[$key] = "'" . trim(strtolower($arVals[$key])) . "'";
		}
	}//while
	//print_r ($arVals); //print the whole array


	/**********************************************************************************************
	   Make sure session variables have been set and then check for required fields
	   otherwise return to the registration form to fix the errors.
	**********************************************************************************************/
	// check to see if these variables have been set...
	variablesSet($arValsRequired,14,"");//send 14 because the page we want is update_user_profile.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,14,"");//send 14 because the page we want is update_user_profile.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,14,"");//send 14 because the page we want is update_user_profile.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,14,"");//send 14 because the page we want is update_user_profile.php


	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check if the combination of first name and last name aleady exists in the database.

	$query01 = "SELECT COUNT(*) FROM user where fname = '" . $_SESSION["fname"] . "' AND lname= '" . $_SESSION["lname"] . "' ";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("update_user_profile()","updatedbinc.php",634,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("update_user_profile()","updatedbinc.php",635,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$result = @mysql_query($query01) or dbErrorHandler("update_user_profile()","updatedbinc.php",638,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	 //there is a chance that the user doesn't want to change
	 //his first name and last name. The query will return 1
	if (($row[0] != 0 ) && ($row[0] != 1)) {  // the combination of first name and last name aleady exists in the database, because the row count > 0...
		resendToForm("?flg=102",14,"");
	}

	//insert into table user the values of the $arVals table
	$query = "UPDATE user SET fname =" . $arVals["fname"] . " , lname = " . $arVals["lname"] .
			" , address_01 =" . $arVals["address_01"] . " , address_02 = " . $arVals["address_02"] .
			" , address_03 =" . $arVals["address_03"] . " , city =" . $arVals["city"] .
			" , country = " . $arVals["country"] . ", phone_01 = " . $arVals["phone_01"] .
			", phone_02 = " . $arVals["phone_02"] . ", fax = " . $arVals["fax"] .
			", website = " . $arVals["website"] .
			", security_question = " . $arVals["security_question"] .
			", security_answer = " . $arVals["security_answer"] .
			", birthday = " . $arVals["birthday"] .
			" WHERE id = " . $_SESSION["logged_user_id"] . ";";

	$result = @mysql_query($query) or dbErrorHandler("update_user_profile()","updatedbinc.php",659,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	$_SESSION["birthday_year"] = intval(substr($birthday, 0, 4));
	$_SESSION["birthday_month_no"] = intval(substr($birthday, 5, 2));
	//the session $_SESSION["birthday_month"] is loaded from the following function
	$_SESSION["birthday_month"] = find_month_from_month_no (intval(substr($birthday, 5, 2)));
	$_SESSION["birthday_day"] = intval(substr($birthday, 8, 2));

	$_SESSION["birthday"] = $arVals["birthday"];

	$_SESSION["user_updated"] = TRUE;

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("update_user_profile()");
	Redirects(15,"","");

}//update_user_profile

#######################################
#######################################

function update_conference_options()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST))
	{
		if(($_SESSION["chairman"] == TRUE)){ Redirects(26,"?flg=157","");}
		else{ Redirects(16,"?flg=157","");}
	}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "update_conference_control_panel") . $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp)
	{
		if(($_SESSION["chairman"] == TRUE)){ Redirects(26,"?flg=157","");}
		else{ Redirects(16,"?flg=157","");}
	} else { unset($_POST["csrf"]);}

	/*
	1. Conference is active. ==> CODE: CIA
	2. Let authors submit abstracts. ==> CODE: ASA
	3. Let authors update abstracts. ==> CODE: AUA
	4. Let authors submit manuscripts. ==> CODE: ASM
	5. Let authors update manuscripts. ==> CODE: AUM
	6. Let authors submit camera_ready papers. ==> CODE: ASCRP
	7. Let authors update camera_ready papers. ==> CODE: AUCRP
	8. Let authors view reviews for their papers. ==> CODE: AVP
	9. Let authors enter conflicts with reviewers. ==> CODE: ACR
	10. How many reviewers for each paper in this conference?. ==> CODE: NORPC
	11. Let reviewer view papers and enter level of interest and conflicts. ==> CODE: RELIC
	12. Let reviewer download his assigned papers and review them. ==> CODE: RDPR
	13. Let reviewer view reviews of his assigned papers by other reviewers. ==> CODE: RVRP
	14. Let users view all conference papers. ==> CODE: UVP
	15. Let users download all conference papers.(manuscripts and camera-ready versions). ==> CODE: UDP
	16. Let users view ONLY the accepted papers. ==> CODE: UVAP
	17. Let users download ONLY the accepted papers (only camera-ready versions ==> CODE: UDAP
	*/

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "conference_id"=>"","CIA"=>"0", "ASA"=>"0", "AUA"=>"0", "ASM"=>"0", "AUM"=>"0", "ASCRP"=>"0", "AUCRP"=>"0",
					"AVP"=>"0", "ACR"=>"0", "NORPC"=>"", "RELIC"=>"0",
					"RDPR"=>"0", "RVRP"=>"0", "UVP"=>"0" , "UDP"=>"0", "UVAP"=>"0", "UDAP"=>"0");
	$arValsRequired = array("NORPC"=>"");
	$arValsValidations = array("NORPC"=>"/^[0-9]([0-9]*)/");

	//All the values in the $_POST are stored in an array.
	reset ($_POST);
	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		if ($val == "") { $val = "NULL";} //if the $_POST[$key] == "" then make it NULL
		//use addslashes to avoid sql injections from the values of the form fields that are going to be passed in the queries

		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
		if ($_POST[$key] == "on"){
			//set a session variable with name the name of the array field and value the value of the array value
			$_SESSION[$key] = 1;
		}
		elseif($key == "NORPC" && $val == "NULL") {unset($_SESSION["NORPC"]);}
		else {$_SESSION[$key] = $val;}
		/*fill the array $arVals with the values that where send to the form
			each array element has as a name the name of the form field that stores
			the value
		*/

		$arVals[$key] = $_SESSION[$key];
	}//while
	//print_r ($arVals) . "<br>"; //print the whole array

	if(($_SESSION["chairman"] == TRUE)){//if the user is the chairman
		variablesSet($arValsRequired,26,"?flg=103");//send 16 because the page we want is conference_control_panel.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,26,"");//send 16 because the page we want is conference_control_panel.php
	}//if
	else{//else the user is the system administrator
		variablesSet($arValsRequired,16,"?flg=103");//send 16 because the page we want is conference_control_panel.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,16,"");//send 16 because the page we want is conference_control_panel.php
	}//else



	/**********************************************************************************************
  	Check the DB for records...
	**********************************************************************************************/
	// check for the email already in the database...

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("update_conference_options()","updatedbinc.php",762,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("update_conference_options()","updatedbinc.php",763,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//update the conference options in the DB table options
	$query = "UPDATE options SET ".
			"CIA=" . $arVals["CIA"] . "," .
			"ASA=" . $arVals["ASA"] . "," .
			"AUA=" . $arVals["AUA"] . "," .
			"ASM=" . $arVals["ASM"] . "," .
			"AUM=" . $arVals["AUM"] . "," .
			"ASCRP=" . $arVals["ASCRP"] . "," .
			"AUCRP=" . $arVals["AUCRP"] . "," .
			"AVP=" . $arVals["AVP"] . "," .
			"ACR=" . $arVals["ACR"] . "," .
			"NORPC=" . $arVals["NORPC"] . "," .
			"RELIC=" . $arVals["RELIC"] . "," .
			"RDPR=" . $arVals["RDPR"] . "," .
			"RVRP=" . $arVals["RVRP"] . "," .
			"UVP=" . $arVals["UVP"] . "," .
			"UDP=" . $arVals["UDP"] . "," .
			"UVAP=" . $arVals["UVAP"] . "," .
			"UDAP=" . $arVals["UDAP"] . " " .
			" WHERE conference_id=" . $arVals["conference_id"];

	$result = @mysql_query($query) or dbErrorHandler("update_conference_options()","updatedbinc.php",787,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	@mysql_close();//closes the connection to the DB

	empty_conference_options();
	save_to_usersactionlog("update_conference_options()");
	if(($_SESSION["chairman"] == TRUE)){//if the user is the chairman
		Redirects(26,"","?flg=120");
	}//if
	else{//else the user is the system administrator
		Redirects(16,"","?flg=120");
	}//else

}//update_conference_options

#######################################
#######################################

function remove_reviewer_from_committee($reviewer_id,$conference_id)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(24,"?flg=156",""); }

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(22,"?flg=157","");}
	if((!isset($_SESSION["chairman"])) ||($_SESSION["chairman"] != TRUE) ){ Redirects(0,"",""); }

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "reviewer_id"=>"", "conference_id"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "reviewer_id"=>"", "conference_id"=>"");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.
	$arValsValidations = array( "reviewer_id"=>"/^[0-9]([0-9]*)/", "conference_id"=>"/^[0-9]([0-9]*)/");

	reset($_POST);
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
	//print_r($arVals);

	// check to see if these variables have been set...
	variablesSet($arValsRequired,24,"");//send 24 because the page we want is reviewers_assignments.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,24,"");//send 24 because the page we want is reviewers_assignments.php
	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,24,"");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("remove_reviewer_from_committee()","updatedbinc.php",817,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("remove_reviewer_from_committee()","updatedbinc.php",818,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "DELETE FROM usertype WHERE user_id = " . $arVals["reviewer_id"] . " AND conference_id= " . $arVals["conference_id"] . " AND type='reviewer'";
	$result = @mysql_query($query) or dbErrorHandler("remove_reviewer_from_committee()","updatedbinc.php",822,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	//create the combo box

	if( $reviewer_id == $_SESSION["logged_user_id"])
	{
		unset($_SESSION["reviewer"]);
	}

	unset($_SESSION["UNASSIGNED_REVIEWERS"]); //reload the unassigned reviewers combo box

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("remove_reviewer_from_committee()");
	redirects(24,"","?flg=119");

}//remove_reviewer_from_committee

#######################################
#######################################

//update_file_format()
function update_file_format()
{

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
	$arValsRequired = array( "extension"=>"");
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
		$arVals[$key] = (get_magic_quotes_gpc()) ? $val : addslashes(trim($val));
		$arVals[$key] = htmlentities($arVals[$key], ENT_QUOTES, "UTF-8");
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
		or dbErrorHandler("update_file_format()","updatedbinc.php",913,"Unable to connect to SQL server using: username: " . $_SESSION["logged_user_email"] . ", password: " . $_SESSION["logged_user_password"]);
	@mysql_select_db($database) or dbErrorHandler("update_file_format()","updatedbinc.php",914,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT COUNT(extension) FROM fileformat where extension = '".$_SESSION["extension"]."'";

	$result = @mysql_query($query01) or dbErrorHandler("update_file_format()","updatedbinc.php",919,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if (($row[0] != 0) && ($row[0] != 1)) {  // a file format with this extenstion aleady exists in the database, because the row count > 0...
		//@mysql_close();//closes the connection to the DB
		//resendToForm("?flg=123",34,"");
	}

	//insert into table user the values of the $arVals table
	$query = "UPDATE fileformat SET extension = " . $arVals["extension"]
			. ", description = " . $arVals["description"]
			. ", mime_type = ". $arVals["mime_type"]
			. " WHERE id='" . $_SESSION["file_format_id"] . "' ;";
			//$_SESSION["conference_id"] was filled from the find_conference() function

	$result = @mysql_query($query) or dbErrorHandler("update_file_format()","updatedbinc.php",934,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	@mysql_close();//closes the connection to the DB

	empty_fileformat_sessions();
	save_to_usersactionlog("update_file_format()");
	Redirects(34,"?flg=125","");
}//update_file_format()

#######################################
#######################################

//update_paper
function update_paper()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "papers") . $csrf_password_generator;

	if($coptions1D["CIA"] == 0){ Redirects(38,"?flg=156",""); }//check if conference is active
	else
	{
		if($coptions1D["ASA"] == 0){ Redirects(38,"?flg=159",""); }//check if authors are allowed to update their manuscripts.
	}
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
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
	//print_r($arVals);

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
		or dbErrorHandler("update_paper()","updatedbinc.php",1038,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("update_paper()","updatedbinc.php",1039,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//check if a paper with this title already exists in this conference submitted by the same user
	$query01 = "SELECT COUNT(title) FROM paper WHERE title = " . $arVals["title"] . " AND user_id = " . $arVals["user_id"] . " AND conference_id = " . $arVals["conference_id"] . ";";

	$result = @mysql_query($query01) or dbErrorHandler("update_paper()","updatedbinc.php",1045,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row = mysql_fetch_row($result);

	if (($row[0] != 0) && ($row[0] != 1)) {  // a paper with this title aleady exists in the database, because the row count > 0...
		//resendToForm("?flg=127",38,"");
	}

	//insert into table user the values of the $arVals table
	$query = "UPDATE paper SET authors = " . $arVals["authors"]
			. ", title = " . $arVals["title"]
			. ", subject = " .$arVals["subject"]
			. ", submition_date = " . $arVals["submition_date"]
			. ", status_code = " . $arVals["status_code"]
			. ", abstract = " . $arVals["abstract"]
			. " WHERE id='" . $_SESSION["paper_id"] . "' ;";
			//$_SESSION["paper_id"] was filled from the find_paper() function

	$result = @mysql_query($query) or dbErrorHandler("update_paper()","updatedbinc.php",1062,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	@mysql_close();//closes the connection to the DB

	empty_paper_sessions();
	$_SESSION["updatepaper"] = "no";
	unset($_SESSION["PAPERS"]);//to reset the papers combo box
	save_to_usersactionlog("update_paper()");
	Redirects(38,"?flg=152","");
}//update_paper

#######################################
#######################################

function update_paper_interest_level()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

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
	$arValsMaxSize = array("level_of_interest"=>1);

	/*the array $arValsValidations stores the names of the fields and the regular expression
	their values have to much with.
	*/
	$arValsValidations = array("level_of_interest"=>"/^[1-7]$/");

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
	variablesSet($arValsRequired,40,"");//send 39 because the page we want is set_interest_levels.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,40,"");//send 38 because the page we want is set_interest_levels.php
	// make sure the variables are in the accepted range
	variablesCheckRange($arValsMaxSize,40,"");//send 38 because the page we want is set_interest_levels.php
	// make sure fields are within the proper range... else cut off any extra...
	// we will use the function variablesCheckRange() instaid of this
	//variablesCheckRangeCutExtra($arValsMaxSize);

	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,40,"");//send 52 because the page we want is set_interest_levels.php


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("update_paper_interest_level()","updatedbinc.php",1157,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("update_paper_interest_level()","updatedbinc.php",1158,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");


	#############################
	//First check if this user, is the user who created the paper, or if he is one of the authors
	$query00 = "SELECT user.fname, user.lname, paper.user_id, paper.conference_id, paper.authors "
				. "FROM paper, user "
				. "WHERE user.id=paper.user_id AND paper.id='" . $_POST["paper_id"] . "' AND paper.conference_id='" . $_SESSION["conf_id"] . "'";
	$result00 = @mysql_query($query00) or dbErrorHandler("update_paper_interest_level()","updatedbinc.php",1167,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query00);
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

	//insert into table interest the values of the $arVals table
	$query = "UPDATE interest "
			. "SET level_of_interest = " . $arVals["level_of_interest"]
			. ", conflict = " .$arVals["conflict"]
			. " WHERE paper_id='" . $_SESSION["paper_id"] . "' AND user_id='" . $_SESSION["user_id"] . "' ;";

	$result = @mysql_query($query) or dbErrorHandler("update_paper_interest_level()","updatedbinc.php",1187,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$insertid = mysql_insert_id();

	empty_paper_interest_level_sessions();
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("update_paper_interest_level()");
	Redirects(40,"?flg=150","");

}//update_paper_interest_level()

#######################################
#######################################

function update_assign_reviewers_to_paper()
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
		or dbErrorHandler("update_assign_reviewers_to_paper()","updatedbinc.php",1216,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("update_assign_reviewers_to_paper()","updatedbinc.php",1217,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "DELETE "
				. "FROM papertoreviewer "
				. "WHERE conference_id='" . $_SESSION["conf_id"] . "' AND paper_id='" . $_SESSION["temp_paper_id"] . "'; ";
	$result01 = @mysql_query($query01) or dbErrorHandler("update_assign_reviewers_to_paper()","updatedbinc.php",1223,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	//All the values in the $_POST are stored in an array.
	reset ($_POST);

	if(count($_POST) > $coptions2D["chairman"]["NORPC"])
	{
		$_SESSION["paper_id"] = $_SESSION["temp_paper_id"];
		Redirects(42,"?flg=131","");
	}//if
	else
	{
		//This resets the cursor of the array.
		while (list($key, $val) = each ($_POST))
		{
			$arVals["user_id"] = $_SESSION["user_id"] = $key;
			$arVals["paper_id"] = $_SESSION["temp_paper_id"];
			$arVals["conference_id"] = $_SESSION["conference_id"] = $_SESSION["conf_id"];

			$query02 = insertQuery("papertoreviewer", $arVals);

			$result02 = @mysql_query($query02) or dbErrorHandler("update_assign_reviewers_to_paper()","updatedbinc.php",1245,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
			$insertid = mysql_insert_id();
		}//while
	}//else

	empty_assign_reviewers_to_paper_sessions();
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("update_assign_reviewers_to_paper()");
	Redirects(43,"?flg=163","");

}//update_assign_reviewers_to_paper

#######################################
#######################################

//enter_conflicts_with_reviewers()
function enter_conflicts_with_reviewers()
{

	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;
	$csrf_password_generator = hash('sha256', "conflicts") . $csrf_password_generator;

	$interests = array();
	$reviewers = array();
	$wanted = array();

	if($coptions1D["CIA"] == 0){ Redirects(45,"?flg=156",""); }	//check if conference is active
	if($coptions2D["author"]["ACR"] == 0) { Redirects(45,"?flg=138","");}//Check if authors are allowed to enter conflicts with reviewers.
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(45,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = $csrf_password_generator;
	if($_POST["csrf"] != $csrf_temp){Redirects(45,"?flg=157","");} else { unset($_POST["csrf"]);}

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "paper_id"=>"", "user_id"=>"", "conference_id"=>"", "conflict_by_author"=>"");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1278,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1279,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");


	$query01 = "SELECT user.id, user.lname, user.fname"
			. " FROM user, usertype"
			. " WHERE user.id = usertype.user_id AND usertype.type = 'reviewer' AND usertype.conference_id = '" . $_SESSION["conf_id"] . "' ORDER BY (user.id);";

	$query02 = "SELECT user_id, level_of_interest, conflict_by_author "
			. " FROM interest "
			. " WHERE conference_id = '" . $_SESSION["conf_id"] . "' AND paper_id='" . $_SESSION["paper_id"] . "' ORDER BY (user_id); ";

	//execute query01
	//get all the reviewers of conference
	$result01 = @mysql_query($query01) or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1293,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row01 = mysql_fetch_row($result01);
	$num01 = mysql_num_rows($result01);//num
	//execute query02
	//get all the reviewers that the author has already stated that he has conflicts with, i.e.: the conflict_by_author field is 1
	$result02 = @mysql_query($query02) or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1298,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$row02 = mysql_fetch_row($result02);
	$num02 = mysql_num_rows($result02);//num

	for($i=0; $i<$num01; $i++)
	{
		$reviewers[$i]["id"] = mysql_result($result01,$i,"id");
		$reviewers[$i]["lname"] = mysql_result($result01,$i,"lname");
		$reviewers[$i]["fname"] = mysql_result($result01,$i,"fname");
	}//for

	for($j=0; $j<$num02; $j++)
	{
		$interests[$j]["user_id"] = mysql_result($result02,$j,"user_id");

		if(mysql_result($result02,$j,"level_of_interest") == NULL){ $interests[$j]["level_of_interest"] = "-";}
		else{ $interests[$j]["level_of_interest"] = mysql_result($result02,$j,"level_of_interest");}

		if(mysql_result($result02,$j,"conflict_by_author") == NULL){ $interests[$j]["conflict_by_author"] = "-";}
		else{ $interests[$j]["conflict_by_author"] = mysql_result($result02,$j,"conflict_by_author");}
	}//for

	reset($interests);
	reset($reviewers);
	//Let's see if we need to UPDATE an existing DB entry, or INSERT a new one
	//If the user_id of a reviewer doesn't exist in the interests array, then there is no entry for him in the DB table 'interest'.
	//If the user_id of a reviewer does exist in the interest array, then there is an entry for him in the DB table 'interest'.
	for($i=0; $i<$num01; $i++)
	{
		//check if the author is also a reviewer in this conference. If he is, exclude his name from the list
		if ( ($reviewers[$i]["lname"] == $_SESSION["logged_user_lname"]) && ($reviewers[$i]["fname"] == $_SESSION["logged_user_fname"]))
		{
			continue;
		}//if

		for($j=0; $j<$num02; $j++)
		{
			if($reviewers[$i]["id"] == $interests[$j]["user_id"])
			{
				$wanted[$i]["user_id"] = $reviewers[$i]["id"];
				$wanted[$i]["action"] = "update";
				break;
			}//if
			elseif($reviewers[$i]["id"] != $interests[$j]["user_id"])
			{
				$wanted[$i]["user_id"] = $reviewers[$i]["id"];
				$wanted[$i]["action"] = "insert";
			}
		}//for
	}//for

	reset($wanted);

	$insert_index=0;
	$update_index=0;
	while (list($key, $val) = each ($wanted))
	{
		if($wanted[$key]["action"] == "insert")
		{
			//echo "<font color='red'>Insert </font>" . "reviewer id: " . $wanted[$key]["user_id"] . "<br>";
			$wanted_insert[$insert_index]["user_id"] = $wanted[$key]["user_id"];
			$insert_index++;
		}
		else if($wanted[$key]["action"] == "update")
		{
			//echo "<font color='blue'>Update </font>" . "reviewer id: " . $wanted[$key]["user_id"] . "<br>";
			$wanted_update[$update_index]["user_id"] = $wanted[$key]["user_id"];
			$update_index++;
		}
	}//while

	//All the values in the $_POST are stored in an array.
	reset ($_POST);

	$query03 = "UPDATE interest "
			. " SET conflict_by_author='0' "
			. " WHERE conference_id='" . $_SESSION["conf_id"] . "' AND paper_id='" . $_SESSION["paper_id"] . "'; ";
	$result03 = @mysql_query($query03) or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1375,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$insertid03 = mysql_insert_id();

	//This resets the cursor of the array.
	while (list($key, $val) = each ($_POST))
	{
		//if the reviewers id is in the $_POST array, then the checkbox with his name
		//is checked. That means that there is a conflict with him.
		//For every reviewer, execute an Update query.

		$arVals["user_id"] = $_SESSION["user_id"] = $key;
		$arVals["paper_id"] = $_SESSION["paper_id"];
		$arVals["conference_id"] = $_SESSION["conf_id"];
		$arVals["conflict_by_author"] = "1";

		for($i=0; $i<count($wanted_update); $i++)
		{
			if($arVals["user_id"] == $wanted_update[$i]["user_id"])
			{
				//UPDATE ENTRY
				//echo "execute <font color='red'>update</font> for user" . $arVals["user_id"] . "<br>";
				$update_query = "UPDATE interest "
						. " SET conflict_by_author='1' "
						. " WHERE conference_id='" . $arVals["conference_id"] . "' AND paper_id='" . $arVals["paper_id"] . "' AND user_id='" . $arVals["user_id"] . "' ;";
				$update_result = @mysql_query($update_query) or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1399,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $update_query);
				$update_insertid = mysql_insert_id();
			}//if
		}//for

		for($j=0; $j<count($wanted_insert); $j++)
		{
			if($arVals["user_id"] == $wanted_insert[$j]["user_id"])
			{
				//INSERT ENTRY
				//echo "execute <font color='blue'>insert</font> for user" . $arVals["user_id"] . "<br>";
				$insert_query = insertQuery("interest", $arVals);
				$insert_result = @mysql_query($insert_query) or dbErrorHandler("enter_conflicts_with_reviewers()","updatedbinc.php",1411,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $insert_query);
				$insert_insertid = mysql_insert_id();
			}//if
		}//for

	}//while

	//empty_assign_reviewers_to_paper_sessions();
	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("enter_conflicts_with_reviewers()");
	Redirects(45,"?flg=151","");

}//enter_conflicts_with_reviewers()

#######################################
#######################################

//remove_file_format_from_conference()
function remove_file_format_from_conference()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(47,"?flg=156",""); }

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(47,"?flg=157","");}
	if((!isset($_SESSION["chairman"])) ||($_SESSION["chairman"] != TRUE) ){ Redirects(0,"",""); }

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "file_format_id"=>"", "conference_id"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "file_format_id"=>"", "conference_id"=>"");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.
	$arValsValidations = array( "file_format_id"=>"/^[0-9]([0-9]*)/", "conference_id"=>"/^[0-9]([0-9]*)/");

	reset($_POST);
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
	//print_r($arVals);

	// check to see if these variables have been set...
	variablesSet($arValsRequired,47,"");//send 47 because the page we want is chairman_file_formats.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,47,"");//send 47 because the page we want is chairman_file_formats.php
	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,47,"");


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("remove_file_format_from_conference()","updatedbinc.php",1440,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("remove_file_format_from_conference()","updatedbinc.php",1441,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "DELETE FROM fileformattoconference WHERE format_id = " . $arVals["file_format_id"] . " AND conference_id= " . $arVals["conference_id"] . "";
	$result = @mysql_query($query) or dbErrorHandler("remove_file_format_from_conference()","updatedbinc.php",1445,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	//create the combo box

	unset($_SESSION["UNSELECTED_FILEFORMATS"]); //reload the unselected file formats combo box

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("remove_file_format_from_conference()");
	Redirects(47,"","?flg=146");

}//remove_file_format_from_conference($format_id)

#######################################
#######################################

//submit from page 'accept_papers'
function accept_papers_for_conference()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $coptions1D;
	global $coptions2D;

	global $csrf_password_generator;

	//check if conference is active
	if($coptions1D["CIA"] == 0){ Redirects(53,"?flg=156",""); }
	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(53,"?flg=157","");}
	//check for CSRF (Cross Site Request Forgery)
	$csrf_temp = hash('sha256', "accept_papers") . $csrf_password_generator;

	if($_POST["csrf"] != $csrf_temp){Redirects(53,"?flg=157","");} else { unset($_POST["csrf"]);}

	if((!isset($_SESSION["chairman"])) ||($_SESSION["chairman"] != TRUE) ){ Redirects(0,"",""); }

	$conference_id = $_SESSION["conf_id"];
	$user_id = $_SESSION["logged_user_id"];

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("accept_papers_for_conference()","updatedbinc.php",1477,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("accept_papers_for_conference()","updatedbinc.php",1478,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");


	$query01 = "SELECT id AS paper_id, title AS paper_title, status_code "
					. " FROM paper "
					. " WHERE conference_id = '" . $conference_id . "' ORDER BY paper_id ASC;";

	$result01 = @mysql_query($query01) or dbErrorHandler("accept_papers_for_conference()","updatedbinc.php",1486,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	//Load all the old status codes from each paper
	if($num01 == 0)
	{
		//no papers submitted for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			$paper_id = mysql_result($result01,$i,"paper_id");
			$paper_status_code = mysql_result($result01,$i,"status_code");

			$old_papers_status[$paper_id] = $paper_status_code;
			$new_papers_status[$paper_id] = 0; //default value
		}//for
	}//else

	//reset the arrays
	reset($_POST);
	reset($new_papers_status);
	//Load all the selected papers, to the '$new_papers_status' array
	while (list($key, $val) = each ($_POST))
	{
		//for select box use this //$new_papers_status[$key] = 1;
		//for drop down options use this
		$new_papers_status[$key] = $val;
	}//while


	//Now we have to create a new array, for all the papers that would be updated (just the 'status_code' field').
	//We would compare the status of each paper from the DataBase with the status that we want each paper to have
	//	(when we checked/unchecked them from the form).

	//reset the arrays
	reset($old_papers_status);
	reset($new_papers_status);

	$papers_tobe_updated = array(); //initialize

	$counter = 0;
	while (list($key, $val) = each($old_papers_status))
	{
		if($old_papers_status[$key] == $new_papers_status[$key])
		{
			//this means that the status of that paper hasn't changed, so we don't have to update it
			//do nothing here
		}//if
		elseif($old_papers_status[$key] != $new_papers_status[$key])
		{
			//this paper's status value would be updated with the new status value
			$papers_tobe_updated[$key] = $new_papers_status[$key];
			$counter++; //count the papers that would be updated
		}//elseif
	}//while

	reset($papers_tobe_updated);

	//create and execute all the necessary update queries
	while (list($key, $val) = each($papers_tobe_updated))
	{
		$query = "UPDATE paper "
			. "SET status_code = " . $papers_tobe_updated[$key]
			. " WHERE id='" . $key . "' ;";

		$result = @mysql_query($query) or dbErrorHandler("accept_papers_for_conference()","updatedbinc.php",1552,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
		$insertid = mysql_insert_id();
	}//while

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("accept_papers_for_conference()");
	Redirects(53,"","?flg=155");

}//accept_papers_for_conference()

?>
