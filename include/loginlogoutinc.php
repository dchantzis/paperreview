<?php
##################################################
################loginlogoutinc.php##############
#################################################


//INCLUDES THE FOLLOWING FUNCTIONS
/*
login(),
logout(),
find_user_type()
*/

function login()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $default_password; //default password for new users that are created by the administrator or the chairman

	// reset session variables...
	$_SESSION["user_logged_in"] = FALSE;
	$_SESSION["logged_user_email"] = "";
	$_SESSION["logged_user_password"] = "";
	$_SESSION["logged_user_fname"] = "";
	$_SESSION["logged_user_lname"] = "";

	$_SESSION["administrator"] = FALSE;
	$_SESSION["author"] = FALSE;
	$_SESSION["chairman"] = FALSE;
	$_SESSION["reviewer"] = FALSE;

	// initialize variables...
	$email = "";
	$password = "";

	// make sure post parameters were sent...
	if (isset($_POST["email"])) {$email = addslashes(trim($_POST["email"]));}
	if (isset($_POST["password"])) {$password = addslashes(trim($_POST["password"]));}

	//$_SESSION["logged_user_email"] = $email;
	//$_SESSION["logged_user_password"] = $password;

	$db_user = $email;
	$db_password = $password;

	// form variables must have something in them...
	if ($email == "" || $password == "")
	{
		session_unset(); unset($_COOKIE[session_name()]); session_destroy();
		Redirects(3,"?flg=103","");
	}//if
	else if ( (@mysql_connect($db_host,$db_user,$db_password)) || (@mysql_select_db($database)))
	{

		@mysql_select_db($database) or dbErrorHandler("login()","loginlogoutinc.php",55,"Unable to select database: " . $database);
		////@mysql_query("SET NAMES greek");
		$query_01 = "SELECT COUNT(*) FROM usersactionlog";
		//if this query can get executed then the user is the administrator
		//else its an imposter
		$result_01 = @mysql_query($query_01) or dbErrorHandler("login()","loginlogoutinc.php",58,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query_01 . "<br /><br />Unothorized Intruder.");
		$num_01 = @mysql_num_rows($result_01);//num

		//user is the administrator
		$_SESSION["logged_user_email"] = $email;
		$_SESSION["logged_user_password"] = $password; //hash('sha256', "dummy_password");
		$_SESSION["user_logged_in"] = TRUE;
		$_SESSION["logged_user_fname"] = "";
		$_SESSION["logged_user_lname"] = "Administrator";
		$_SESSION["logged_user_id"] = "0";
		$_SESSION["administrator"] = TRUE;
		unset($_SESSION["author"]);
		unset($_SESSION["chairman"]);
		unset($_SESSION["reviewer"]);

		@mysql_close();//closes the connection to the DB
		save_to_usersactionlog("login()");
		Redirects(5,"","");
	}//elseif
	else//common user i.e. chairman, reviewer, author
	{
		// check in database...

		@mysql_connect($db_host,$db_common_user,$db_common_password)
					   or dbErrorHandler("login()","loginlogoutinc.php",78,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
		@mysql_select_db($database) or dbErrorHandler("login()","loginlogoutinc.php",79,"Unable to select database: " . $database);
		//@mysql_query("SET NAMES greek");

		$query = sprintf("SELECT * FROM user WHERE email = '%s'", mysql_real_escape_string($email));
		$result = @mysql_query($query) or dbErrorHandler("login()","loginlogoutinc.php",86,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

		// if email is not present in DB go back to login page...
		if (mysql_affected_rows() != 1)
		{
			session_unset(); unset($_COOKIE[session_name()]); session_destroy();
			Redirects(3,"?flg=110","");
		}

		// check for password, and then send to appropriate section...
		if ($row = mysql_fetch_assoc($result))
		{
			if($row["password"] == $password)
			{
				//log him in and DON'T execute next line
			}
			//if the user exists but the password is incorrect, show him an error message
			else if (strcmp($row["password"], hash('sha256', $password)) != 0)
			{
				session_unset(); unset($_COOKIE[session_name()]); session_destroy();
			 	Redirects(3,"?flg=109","");
			}

			// set standard session variables...
			$_SESSION["logged_user_email"] = $email;
			//$_SESSION["logged_user_password"] = hash('sha256', $password);
			//we don;t want the encrypted user password to be saved in a session
			//so we will use a dummy password while the user is logged
			$_SESSION["logged_user_password"] = hash('sha256', "dummy_password");
			$_SESSION["user_logged_in"] = TRUE;
			$_SESSION["logged_user_fname"] = $row["fname"];
			$_SESSION["logged_user_lname"] = $row["lname"];
			$_SESSION["logged_user_id"] = $row["id"];
			unset($_SESSION["administrator"]);
			//$_SESSION["userloggin"] = TRUE;

			@mysql_close();//closes the connection to the DB
			//save_to_usersactionlog("login()");
			Redirects(13,"","");
		}//if
		else
		{//user not found
			session_unset(); unset($_COOKIE[session_name()]); session_destroy();
			Redirects(3,"?flg=110","");
		}//else
	}//else
}//login

##################################
##################################

function logout()
{
	save_to_usersactionlog("logout()");

	session_unset();
	// Clear the session cookie
	unset($_COOKIE[session_name()]);
	// Destroy session data
	session_destroy();

	Redirects(3,"","");
}//logout

##################################
##################################

function find_user_type($action)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
				or dbErrorHandler("find_user_type()","loginlogoutinc.php",158,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("find_user_type()","loginlogoutinc.php",160,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT type FROM usertype WHERE user_id = '" . $_SESSION["logged_user_id"] . "' AND conference_id='" . $_SESSION["conf_id"] . "';";

	$result = @mysql_query($query) or dbErrorHandler("find_user_type()","loginlogoutinc.php",164,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	$row = mysql_fetch_row($result);

	if ($num == 0)
	{
		//then the user is an author in this conference
		unset($_SESSION["administrator"]);
		unset($_SESSION["chairman"]);
		unset($_SESSION["reviewer"]);
		$_SESSION["author"] = TRUE;
	}//if
	else
	{
		unset($_SESSION["chairman"]);
		unset($_SESSION["reviewer"]);
		unset($_SESSION["author"]);

		for($i=0; $i<$num; $i++)
		{
			$db_type = @mysql_result($result,$i,"type");

			switch($db_type)
			{
				case "chairman":
					$_SESSION["chairman"] = TRUE;
					$_SESSION["author"] = TRUE;
					break;
				case "reviewer":
					$_SESSION["reviewer"] = TRUE;
					$_SESSION["author"] = TRUE;
					break;
				case "author":
					$_SESSION["author"] = TRUE;
					break;
				default:
					//
					break;
			}//switch
		}//for
	}//else

	@mysql_close();//closes the connection to the DB
	save_to_usersactionlog("login()");
	Redirects(5,"","");
}//find_user_type()

?>
