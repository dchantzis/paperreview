<?php
##################################################
################selectdbauthinc.php############
#################################################
/*
This file includes all the functions that include
select queries to the DataBase, that refer to authors.
This file doesn't include some functions that are common to
all the users of the system, (these are included in the
selectdbcommoninc.php file).
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
display_reviews_a(),
find_paper($forpage),
show_uploaded_paper_body($paper_id, $type),
display_reviewers_for_conflicts()
*/

function display_reviews_a()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];
	$author_id = $_SESSION["logged_user_id"];

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"paper_reviews\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper</th>
		<th scope=\"col\" class=\"view\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_reviews_a()","selectdbauthinc.php",39,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_reviews_a()","selectdbauthinc.php",40,"Unable to select database: " . $database);
	////@mysql_query("SET NAMES greek");

	//get the papers that he submitted.
	$query01 = "SELECT paper.id, paper.title "
				. "FROM paper "
				. "WHERE conference_id = '" . $conference_id . "' AND paper.user_id = '" . $author_id . "' ORDER BY (paper.id) ASC;";

	//Get all the reviewers and what paper they are assigned to review for this conference
	$query02 = "SELECT papertoreviewer.paper_id, user.id, user.fname, user.lname "
				. "FROM user, papertoreviewer "
				. "WHERE user.id=papertoreviewer.user_id AND papertoreviewer.conference_id = '" . $conference_id . "' ORDER BY (papertoreviewer.paper_id) ASC;";


	$result01 = @mysql_query($query01) or dbErrorHandler("display_reviews_a()","selectdbauthinc.php",54,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//no papers submitted for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			$paper_id = mysql_result($result01,$i,"id");
			$papers[$paper_id]["title"] = mysql_result($result01,$i,"title");
		}//for
	}//else

	$result02 = @mysql_query($query02) or dbErrorHandler("display_reviews_a()","selectdbauthinc.php",71,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//no reviewers assigned for any paper of this conference
	}//if
	else
	{
		//fill the array with values
		for($j=0; $j<$num02; $j++)
		{
			$reviewers[$j]["paper_id"] = mysql_result($result02,$j,"paper_id");
			$reviewers[$j]["reviewer_id"]  = mysql_result($result02,$j,"id");
			$reviewers[$j]["reviewer_name"]  = mysql_result($result02,$j,"fname") . " " . $reviewers[$j]["reviewer_lname"]  = mysql_result($result02,$j,"lname");
		}//for
	}//else

	if(count($papers) == 0 || count($reviewers) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"3\" align=\"center\">Currently you have no papers for this conference.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($papers) != 0 || count($reviewers) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the arrays
		reset($papers);

		$flag=0;//if this flag gets the value '1', this means that there is at least 1 reviewer assigned for the paper
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

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
			echo "\n\t\t\t<td class=\"name\"><a href=\"./paper_info.php?paperid=" . $key01 . "\">" . $papers[$key01]["title"] . "</a>" . "</td>";

			for($j=0; $j<count($reviewers); $j++)
			{
				if($key01 == $reviewers[$j]["paper_id"])
				{
					//don't echo the names of the reviewers
					//echo "<li>" . "<a href=\"./user_info.php?userid=" . $reviewers[$j]["reviewer_id"] . "\">" . $reviewers[$j]["reviewer_name"] . "</a>" . "</li>";
					$flag=1;
				}//if
			}//for

			//if there are no reviewers don't echo a link to a paper review
			if($flag == 0)
			{
				echo "\n\t\t\t<td class=\"view\">" . "" . "</td>";

			}//if
			elseif($flag == 1)
			{
				echo "\n\t\t\t<td class=\"view\">" . "<a href=\"./paper_reviews_info.php?paperid=" . $key01 . "\" class=\"simple\">" . "view" . "</a>" . "</td>";
			}//elseif
			echo "\n\t\t</tr>";

			$flag=0;
			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//else

	@mysql_close();//closes the connection to the DB
}//display_reviews_a()

##################################
##################################

function find_paper($forpage)
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	global $csrf_password_generator;

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST))
	{
		if($forpage == "papers"){ Redirects(38,"?flg=157","");}
		elseif($forpage == "paper_body"){ Redirects(44,"?flg=157","");}
		elseif($forpage == "conflicts"){ Redirects(45,"?flg=157","");}
	}
	//check for CSRF (Cross Site Request Forgery)
	if($forpage == "papers"){ $csrf_temp = hash('sha256', "papers") . $csrf_password_generator;}
	elseif($forpage == "paper_body"){ $csrf_temp = hash('sha256', "paper_body") . $csrf_password_generator;}
	elseif($forpage == "conflicts"){ $csrf_temp = hash('sha256', "conflicts") . $csrf_password_generator;}
	if($_POST["csrf"] != $csrf_temp)
	{
		if($forpage == "papers"){ Redirects(38,"?flg=157","");}
		elseif($forpage == "paper_body"){ Redirects(44,"?flg=157","");}
		elseif($forpage == "conflicts"){ Redirects(45,"?flg=157","");}
	}
	else { unset($_POST["csrf"]);}


	//the array $arVals stores the names of all the values of the form
	$arVals = array( "paper_id"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "paper_id"=>"");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.
	$arValsValidations = array( "paper_id"=>"/^[0-9]([0-9]*)/");

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


	if($forpage == "papers")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,38,"");//send 38 because the page we want is papers.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,38,"");//send 38 because the page we want is papers.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,38,"");
	}//if
	elseif($forpage == "paper_body")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,44,"");//send 44 because the page we want is paper_body.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,44,"");//send 44 because the page we want is paper_body.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,44,"");
	}//elseif
	elseif($forpage == "conflicts")
	{
		// check to see if these variables have been set...
		variablesSet($arValsRequired,45,"");//send 45 because the page we want is conflicts.php
		// check if the form variables have something in them...
		variablesFilled($arValsRequired,45,"");//send 45 because the page we want is conflicts.php
		// make sure the variables match the corresponding regular expressions
		variablesValidate($arValsValidations,45,"");
	}//elseif


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("find_paper()","selectdbauthinc.php",231,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("find_paper()","selectdbauthinc.php",232,"Unable to select database: " . $database);
	//////@mysql_query("SET NAMES greek");

	$query = "SELECT id, user_id, title, abstract, authors, subject, status_code "
			 . "FROM paper WHERE id='" . $_POST["paper_id"] . "' AND user_id='" . $_SESSION["logged_user_id"] ."';";
	$result = @mysql_query($query) or dbErrorHandler("find_paper()","selectdbauthinc.php",237,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num
	//create the combo box

	//store all the DB values in array $cvalues
	$pvalues["id"] = @mysql_result($result,$i,"id");
	$pvalues["title"] = @mysql_result($result,$i,"title");
	$pvalues["abstract"] = @mysql_result($result,$i,"abstract");
	$pvalues["authors"] = @mysql_result($result,$i,"authors");
	$pvalues["subject"] = @mysql_result($result,$i,"subject");
	$pvalues["status_code"] = @mysql_result($result,$i,"status_code");

	$_SESSION["paper_id"] = $pvalues["id"];
	$_SESSION["paper_title"] = $pvalues["title"];
	$_SESSION["title"] = $pvalues["title"];
	$_SESSION["abstract"] = $pvalues["abstract"];
	$_SESSION["authors"] = $pvalues["authors"];
	$_SESSION["subject"] = $pvalues["subject"];
	$_SESSION["status_code"] = $pvalues["status_code"];

	$_SESSION["update_authors"] = 1;

	@mysql_close();//closes the connection to the DB

	if($forpage == "papers")
	{
		if($num != 0){$_SESSION["updatepaper"] = "yes";}
		else {$_SESSION["updatepaper"] = "no";}
		Redirects(38,"#insertform","");
	}//if
	elseif($forpage == "paper_body")
	{
		if($num != 0){$_SESSION["updatepaperbody"] = "yes";}
		else {$_SESSION["updatepaperbody"] = "no";}
		Redirects(44,"#insertform","");
	}//elseif
	elseif($forpage == "conflicts")
	{
		$_SESSION["temp_token"] = "0";
		Redirects(45,"#instructions","");
	}//elseif

}//find_paper($forpage)

##################################
##################################

//returns array
//$type is just used for the way the link to download the paper will be shown
function show_uploaded_paper_body($paper_id, $type)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	if(!isset($paper_id)){return -1;}

	global $coptions2D;
	$paper_type = "";
	$paper_type["manuscript"] = "";
	$paper_type["camera_ready"] = "";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("show_uploaded_paper_body()","Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("show_uploaded_paper_body()","selectdbauthinc.php",298,"Unable to select database: " . $database);
	//////@mysql_query("SET NAMES greek");

	$query = "SELECT paperbody.id, fileformat.extension, fileformat.mime_type, paperbody.filename, paperbody.filesize, paperbody.filecontent, paperbody.fileurl, paperbody.paper_type, paperbody.upload_type "
		. "FROM paperbody, fileformat WHERE fileformat.id = paperbody.format_id AND paperbody.paper_id = '" . $paper_id . "' ";

	$result = @mysql_query($query) or dbErrorHandler("show_uploaded_paper_body()","selectdbauthinc.php",304,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	if($num == 0)
	{
		//check if the author is allowed to upload a new manuscript.
		//Since there are no other paper bodies in the db for that paper, then there are not allowed any camera-ready versions of the paper.
		if ($coptions2D["author"]["ASM"] == 0)
		{
			////user not allowed to upload any manuscripts
			$paper_type["manuscript"] = "disabled";
			$paper_type["camera_ready"] = "disabled";
		}//
		else
		{
			//user not allowed to upload any camera-ready versions of the paper
			$paper_type["camera_ready"] = "disabled";
		}//
	}//if
	else if($num == 1 || $num == 2)
	{

		//check if the author is allowed to upload or update a new manuscript or a camera-ready version of the paper.
		if($num == 1)//user has uploaded only a manuscript
		{
			if($coptions2D["author"]["AUM"] == 0) {$paper_type["manuscript"] = "disabled";}//user not allowed to update manuscript
			if($coptions2D["author"]["ASCRP"] == 0) {$paper_type["camera_ready"] = "disabled";}//user not allowed to insert camera-ready
		}//if
		else if($num == 2)//user has already uploaded a manuscript and a camera_ready version of the paper
		{
			if($coptions2D["author"]["AUM"] == 0) {$paper_type["manuscript"] = "disabled";}//user not allowed to update manuscript
			if($coptions2D["author"]["AUCRP"] == 0) {$paper_type["camera_ready"] = "disabled";}//user not allowed to update camera-ready
		}//if

		//get the DB data
		for($i=0; $i<$num; $i++)
		{
			$paper_body_ar[$i]["id"] = mysql_result($result,$i,"id");
			$paper_body_ar[$i]["fileextension"] = mysql_result($result,$i,"extension");
			$paper_body_ar[$i]["filemime_type"] = mysql_result($result,$i,"mime_type");
			$paper_body_ar[$i]["filename"] = mysql_result($result,$i,"filename");
			$paper_body_ar[$i]["filesize"] = mysql_result($result,$i,"filesize");
			$paper_body_ar[$i]["filecontent"] = mysql_result($result,$i,"filecontent");
			$paper_body_ar[$i]["fileurl"] = mysql_result($result,$i,"fileurl");
			$paper_body_ar[$i]["paper_type"] = mysql_result($result,$i,"paper_type");
			$paper_body_ar[$i]["upload_type"] = mysql_result($result,$i,"upload_type");
		}//for

		switch($type)
		{
			case 0:
				echo "\n<br><br>";
				echo "\t\n<ul class=\"uploaded_papers\">\n";
				for($j=0; $j<count($paper_body_ar); $j++)
				{
					echo "\t\t\n<li>\n";
					echo "\t\t\t<b>" . "uploaded " . $paper_body_ar[$j]["paper_type"] . "</b>" . ": " . "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paper_body_ar[$j]["id"] . "\" class=\"simple\" title=\"Download paper body.\">" . $paper_body_ar[$j]["filename"] . "</a>" . " (<i>size: " . $paper_body_ar[$j]["filesize"] . " bytes</i>)";
					echo "\t\t\n</li>\n";
				}//for
				echo "\t\n</ul>";
				break;
			case 1:
				for($j=0; $j<count($paper_body_ar); $j++)
				{
					echo "\t" . "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paper_body_ar[$j]["id"] . "\" class=\"simple\" title=\"Download paper body.\">" . "Download ". $paper_body_ar[$j]["paper_type"] . "</a>" . " (<i>size: " . $paper_body_ar[$j]["filesize"] . " bytes</i>)";
				}//for
				echo "<br>";
				break;
			default:
				//do nothing
				break;
		}//switch

	}//elseif

	@mysql_close();//closes the connection to the DB

	return ($paper_type);
}//show_uploaded_paper_body($paper_id, $type)

##################################
##################################

//used for the reviewers.php page, where authors enter conflicts
function display_reviewers_for_conflicts()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];
	$paper_id = $_SESSION["paper_id"];
	$checkbox_status = "";
	$disabled = "";

	global $coptions2D;
	if($coptions2D["author"]["ACR"] == 0)
	{
		$disabled = "disabled";
	}//if

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"candidate_reviewers\">";
	$str_02 = "<caption>Conference chairmen list</caption>";

	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"\" title=\"Click for Reviewers Info.\">reviewer</th>
		<th scope=\"col\" class=\"\" title=\"Click to send e-mail.\">e-mail</th>
		<th scope=\"col\" class=\"checkbox\" title=\"Check box for conflict.\"></th>
		</tr>\n</thead>";

	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_reviewers_for_conflicts()","selectdbauthinc.php",417,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_reviewers_for_conflicts()","selectdbauthinc.php",418,"Unable to select database: " . $database);
	//////@mysql_query("SET NAMES greek");

	$query01 = "SELECT user.id, user.lname, user.fname, user.email "
			. " FROM user, usertype"
			. " WHERE user.id = usertype.user_id AND usertype.type = 'reviewer' AND usertype.conference_id = '" . $conference_id . "' ORDER BY (user.lname) ;";

	$query02 = "SELECT user_id "
			. " FROM interest "
			. " WHERE conference_id = '" . $conference_id . "' AND conflict_by_author='1' AND paper_id='" . $paper_id . "'; ";


	echo $str_01;
	echo $str_03;
	echo $str_04;

	//execute query01
	//get all the reviewers of conference
	$result01 = @mysql_query($query01) or dbErrorHandler("display_reviewers_for_conflicts()","selectdbauthinc.php",436,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row01 = mysql_fetch_row($result01);
	$num01 = mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		echo "\n\t\t<tr>";
		echo "\n<td colspan=\"4\" align=\"center\">There are no users in the review committee.</td>";
		echo "\n\t\t</tr>";
		$temp_value=0;
	}//if

	for($i=0; $i<$num01; $i++)
	{
		//check if the author is also a reviewer in this conference. If he is, exclude his name from the list
		if ((mysql_result($result01,$i,"lname") == $_SESSION["logged_user_lname"]) && (mysql_result($result01,$i,"fname") == $_SESSION["logged_user_fname"]))
		{
			continue;
		}//if

		$reviewers[$i]["id"] = mysql_result($result01,$i,"id");
		$reviewers[$i]["lname"] = mysql_result($result01,$i,"lname");
		$reviewers[$i]["fname"] = mysql_result($result01,$i,"fname");
		$reviewers[$i]["email"] = mysql_result($result01,$i,"email");
	}//for

	//execute query02
	//get all the reviewers that the author has already stated that he has conflicts with, i.e.: the conflict_by_author field is 1
	$result02 = @mysql_query($query02) or dbErrorHandler("display_reviewers_for_conflicts()","selectdbauthinc.php",464,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$row02 = mysql_fetch_row($result02);
	$num02 = mysql_num_rows($result02);//num

	for($j=0; $j<$num02; $j++)
	{
		$conflicts_by_author[$j]["user_id"] = mysql_result($result02,$j,"user_id");
	}//for

	$count=0;
	for($i=0; $i<$num01; $i++)
	{
		for($j=0; $j<$num02; $j++)
		{
			if ($reviewers[$i]["id"] == $conflicts_by_author[$j]["user_id"])
			{
				//this means that the author has already stated that he has a conflict with reviewer with id: $reviewers[$i]["id"]
				//...so that checkbox should be checked!
				$checkbox_status = "checked";
				break;
			}//
			else
			{
				$checkbox_status = "";
				continue;
			}
		}//for

		if(!isset($reviewers[$i])){ continue; }

		if (($count%2)==0) {
			$bgColor = "#F5F0EA";
			$trClass = "even";
		} else {
			$bgColor = "#FFFFFF";
			$trClass = "odd";
		}

		echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

		//echo "\n\t\t<td class=\"reviewer\"><a href=\"user_info.php?userid=" . $reviewers[$i]["id"] . "\" title=\"Click for user info.\">" . $reviewers[$i]["lname"] . " " . $reviewers[$i]["fname"] . "</a></td>";
		echo "\n\t\t<td class=\"reviewer\">" . $reviewers[$i]["lname"] . " " . $reviewers[$i]["fname"] . "</td>";
		echo "\n\t\t<td>" . "<a href=\"mailto:" . $reviewers[$i]["email"] . "\">" . $reviewers[$i]["email"] . "</a>" . "</td>";
		echo "\n\t\t<td class=\"checkbox\">" . "<input type=\"checkbox\" id=\"" . $reviewers[$i]["id"] . "\" name=\"" . $reviewers[$i]["id"] . "\" " . $checkbox_status . " " . $disabled . "  >" . "</td>";

		echo "\n\t\t</tr>";
		$count++;

		$temp_value=1;
	}
	echo $str_05;
	echo $str_06;

	return $temp_value;

	@mysql_close();//closes the connection to the DB
}//display_reviewers_for_conflicts()

##################################
##################################

?>
