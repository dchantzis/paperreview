<?php
##################################################
################commonfunctionsinc.php###########
##################################################
/*
This file includes all the functions that are common 
to all the system users
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
whereUgo($cteva),
VariousMessages($flg),
new_user(),
find_month_from_month_no($month_no),
dateDifference($dformat, $endDate, $beginDate),
convert_ar_vals($ar_values, $from_str, $to_str),
load_conference_dates_to_sessions($deadline_date, $deadline_type),
create_directory($parent_dir_path,$child_dir_name),
find_file_extension($filename),
load_conference_dates_to_array($deadline_date, $deadline_type),
create_directory($parent_dir_path,$child_dir_name)
avgCompare($x, $y),
discrCompare($x, $y),
wavgCompare($x, $y),
ap_getColor($rating),
organize_conf_actions_timetable($coptions2D)
display_conf_timetable($conf_options_timetable),
*/

function whereUgo($cteva)
{

	if(!preg_match("/^[0-9]([0-9]*)/",$cteva)){ $cteva = NULL; }
	else {}//do nothing. ALL OK
	
	switch ($cteva){
	case 0:
		//if the user is not logged in, redirect him to 'login.php'
		if( 
			(!isset($_SESSION["user_logged_in"]) || $_SESSION["user_logged_in"] != TRUE) ||  //($_SESSION["user_logged_in"] != TRUE) || // 
			(!isset($_SESSION["logged_user_password"])) ||
			( $_SESSION["logged_user_password"] == "" )
		){
			header("Location: login.php");
			exit;
		}//if
	break;
	case 1:
		//if the user is not the administrator, and the user hasn't selected a conference
		//redirect him to 'login_choose_conference.php'
		if(
			( (!isset($_SESSION["administrator"])) || ($_SESSION["administrator"] != TRUE) ) &&
			( (!isset($_SESSION["conf_id"])) || ($_SESSION["conf_id"] == "") )
		){
			header("Location: login_choose_conference.php");
			exit;
		}//if
	break;
	case 2:
		//if the user is not the administrator
		//redirect him to 'ulounge.php'
		if(
			(!isset($_SESSION["administrator"])) ||
			($_SESSION["administrator"] != TRUE)
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	case 3:
		//if the user is not the chairman
		//redirect him to 'ulounge.php'
		if(
			(!isset($_SESSION["chairman"])) ||
			($_SESSION["chairman"] != TRUE)
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	case 4:
		//if the user is not the reviewer
		//redirect him to 'ulounge.php'
		if(
			(!isset($_SESSION["reviewer"])) ||
			($_SESSION["reviewer"] != TRUE)
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	case 5:
		//if the user is not the author
		//redirect him to 'ulounge.php'
		if(
			(!isset($_SESSION["author"])) ||
			($_SESSION["author"] != TRUE)
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	case 6:
		//if the user is  logged in, redirect him to 'ulounge.php'
		if( 
			(isset($_SESSION["user_logged_in"]) && ($_SESSION["user_logged_in"] == TRUE)) ||  
			((isset($_SESSION["logged_user_password"])) && ( $_SESSION["logged_user_password"] != "" ))
			//($_SESSION["user_logged_in"] == TRUE) ||
			//(isset($_SESSION["logged_user_password"])) |( $_SESSION["logged_user_password"] != "" )
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	case 7:
		//if the user in not registered, redirect him to 'login.php'	
		if( 
			(isset($_SESSION["user_registered"]) && ($_SESSION["user_registered"] != TRUE)) || 
			(!isset($_SESSION["user_registered"]))
		){
			header("Location: ./login.php");
			exit;
		}//if
	break;
	case 8:
		//if the user in hasn't selected a conference, redirect him to 'login_choose_conference.php'	
		if( 
			(!isset($_SESSION["conf_id"])) ||
			($_SESSION["conf_id"] == "")
		){
			header("Location: ./login_choose_conference.php");
			exit;
		}//if
	break;
	case 9:
		//if the user is not the administrator
		//redirect him to 'ulounge.php'
		if(
			(!isset($_SESSION["administrator"])) ||
			($_SESSION["administrator"] != TRUE)
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	case 10:
		//if the user is not the administrator and not the chairman
		//redirect him to 'ulounge.php'
		if(
			( (!isset($_SESSION["administrator"]))||($_SESSION["administrator"] != TRUE) ) &&
			( (!isset($_SESSION["chairman"])) || ($_SESSION["chairman"] !=TRUE) )
		){
			header("Location: ulounge.php");
			exit;
		}//if
	break;
	default:
		//do nothing
	break;
}//switch
}//whereUgo()

###########################
###########################

//VariousMessages	
function VariousMessages($flg)
{
	if(!preg_match("/^[0-9]([0-9]*)/",$flg)){ $flg = NULL; 	$error = "";}
	else { 	$error = "ERROR: "; }//do nothing. ALL OK

	switch($flg)
	{
		case 101:
			$message = $error . "That Email Address already exists in our Database. Please Select Another.";
			break;
		case 102:
			$message = $error . "This combination of First name AND Last name already exists in our Database. Please Select Another.";
			break;		
		case 103:
			$message = $error . "Please fill out all the required fields.";
			break;
		case 104:
			$message = $error . "Your Session has Expired. Please Login Again.";
			break;
		case 105:
			$message = $error . "The Special Code you entered is not valid. Please Try Again or Leave that field blank.";
			break;
		case 106:
			$message = $error . "The fields are too long for our Database. Please correct your data via this form.";
			break;
		case 107:
			$message = $error . "Your password entries didn't match.";
			break;
		case 108:
			$message = $error . "Enter valid values to the fields.";
			break;
		case 109:
			$message = $error . "The password you entered is invalid. Please Try Again.";
			break;
		case 110:
			$message = $error . "User doesn't exist. Please Register <a href=\"./user_registration.php\" class=\"simple\">here</a>.";
			break;
		case 111:
			$message = $error . "Your answers to the security questions were incorrect. Please Try Again.";
			break;
		case 112:
			$message = $error . "A conference with this name already exists.";
			break;
		case 113:
			$message = $error . "Anauthorized user.";
			break;
		case 114:
			$message = $error . "This user is already a chairman for this conference.";
			break;
		case 115:
			$message = $error . "This user is already a reviewer for this conference.";
			break;
		case 116:
			$message = "Conference created successfully.";
			break;
		case 117:
			$message = "User inserted successfully.";
			break;
		case 118: 
			$message = "Chairman removed successfully.";
			break;
		case 119:
			$message = "User removed successfully from review committee.";
			break;
		case 120:
			$message = "Conference Options updated successfully.";
			break;
		case 121:
			$message = "Conference updated successfully.";
			break;
		case 122:
			$message = "Announcement posted successfully.";
			break;
		case 123:
			$message =  $error . "File format with this extension already exists.";
			break;
		case 124:
			$message = "File format inserted successfully.";
			break;
		case 125:
			$message = "File format updated successfully.";
			break;
		case 126:
			$message = "Paper created successfully.";
			break;
		case 127:
			$message = $error . "A paper with this title already exists.";
			break;
		case 128:
			$message = $error . "You have already entered your interest level for this paper.";
			break;
		case 129:
			$message = "Interest levels entered successfully.";
			break;
		case 130:
			$message = "Interest levels updated successfully.";
			break;
		case 131:
			$message = $error . "Too many reviewers selected.";
			break;
		case 132:
			$message = "Paper Body uploaded successfully.";
			break;
		case 133:
			$message = $error . "File type is not supported.";
			break;
		case 134:
			$message = "Paper Body updated successfully.";
			break;
		case 135:
			$message = $error . "Your file did not upload, please try again.";
			break;
		case 136:
			$message = $error . "File too large to upload.";
			break;
		case 137:
			$message = $error . "Unable to update paper body.";
			break;
		case 138:
			$message = $error . "Authors are not allowed to enter conflicts with reviewers.";
			break;
		case 139:
			$message = $error . "There are no reviewers for this conference. Please create the review committe of this confernece first.";
			break;
		case 140:
			$message = $error . "There are no papers for this conference. Please wait until all the papers are submitted.";
			break;
		case 141:
			$message = $error . "No reviewer has enter interest for ANY paper. Please notify them.";
			break;
		case 142:
			$message = "All reviewers have their interest levels set.";
			break;
		case 143:
			$message = "Force action completed successfully.";
			break;
		case 144:
			$message = $error . "This file format is already selected for this conference.";
			break;
		case 145:
			$message = "This file format inserted successfully.";
			break;
		case 146:
			$message = "File format removed successfully.";
			break;
		case 147:
			$message = $error. "You are not allowed to submit any manuscripts.";
			break;
		case 148:
			$message = $error. "You are not allowed to submit any camera-ready versions of the paper.";
			break;
		case 149:
			$message = "Assignments saved successfully.";
			break;
		case 150:
			$message = "Interest levels and conflicts saved successfully.";
			break;
		case 151:
			$message = "Conflicts saved successfully.";
			break;
		case 152:
			$message = "Paper updated successfully.";
			break;
		case 153:
			$message = "Review saved successfully.";
			break;
		case 154:
			$message = "Review updated successfully.";
			break;
		case 155:
			$message = "Papers accepted for conference successfully.";
			break;
		case 156:
			$message = $error. "Conference is inactive. This action is not allowed.";
			break;
		case 157:
			$message = $error . "Form was not submitted normally.";
			break;
		case 158:
			$message = $error . "Authors are not allowed to submit their manuscripts.";
			break;
		case 159:
			$message = $error . "Authors are not allowed to update their manuscripts.";
			break;
		case 160:
			$message = $error . "Reviewers are not allowed to enter their levels of interest and conflicts.";
			break;
		case 161:
			$message = $error . "Reviewers are not allowed to download their assigned papers and review them.";
			break;
		case 162:
			$message = "Reviewer(s) assigned successfully.";
			break;
		case 163:
			$message = "Reviewers assignments updated successfully.";
			break;
		default:
			$message = "";
			break;
	}//switch
	echo $message;
}//VariousMessages($flg)

#########################
#########################

function new_user()
{
	session_destroy();
	Redirects(1,"","");
}//new_user()

#########################
#########################

function find_month_from_month_no($month_no)
{
	$month_name = "";
	switch ($month_no)
	{
		case 1:
		case 01:
			$month_name = "January";
			break;
		case 2:
		case 02:
			$month_name = "February";
			break;		
		case 3:
		case 03:
			$month_name = "March";
			break;		
		case 4:
		case 04:
			$month_name = "April";
			break;		
		case 5:
		case 05:
			$month_name = "May";
			break;		
		case 6:
		case 06:
			$month_name = "June";
			break;		
		case 7:
		case 07:
			$month_name = "July";
			break;		
		case 8:
		case 08:
			$month_name = "August";
			break;		
		case 9:
		case 09:
			$month_name = "September";
			break;		
		case 10:
			$month_name = "October";
			break;		
		case 11:
			$month_name = "November";
			break;		
		case 12:
			$month_name = "December";
			break;		
		default:
			$month_name = "";
			break;		
	}//switch
	return $month_name;
}//find_month_from_month_no($month_no)

#########################
#########################

function dateDifference($dformat, $endDate, $beginDate)
{
	$date_parts1 = explode($dformat, $beginDate);
	$date_parts2 = explode($dformat, $endDate);
	$start_date = gregoriantojd($date_parts1[0], $date_parts1[1], $date_parts1[2]);
	$end_date = gregoriantojd($date_parts2[0], $date_parts2[1], $date_parts2[2]);

	return $start_date - $end_date;
}//dateDifference($dformat, $endDate, $beginDate)

#########################
#########################

//$ar_values --> array with values
//$from_str --> change this string
//$to_str -->with this
//example: convert_ar_vals($ar_values, "NULL", "*unspecified*")
function convert_ar_vals($ar_values, $from_str, $to_str)
{
	reset ($ar_values);
	while(list($key, $val) = each ($ar_values))
	{
		if($val == strtoupper($from_str) || $val == strtolower($from_str)) 
		{
			$val = $to_str; 
			$ar_values[$key] = $val;
		}
	}
	//print_r($ar_values);
	return $ar_values;
}//convert_ar_vals($ar_values, $from_str, $to_str)

##########################
##########################

//loads the different dates to sessions in the wanted date format
//$deadline_date --> the source date that will be used
//$deadline_type --> can be deadline, abstracts_deadline, manuscripts_deadline, camera_ready_deadline, preferencies_deadline, reviews_deadline 
//CAUTION DATE FORMAT HAS TO BE yyyy-mm-dd
function load_conference_dates_to_sessions($deadline_date, $deadline_type)
{
	$_SESSION[$deadline_type . "_year"] = intval(substr($deadline_date, 0, 4));
	$_SESSION[$deadline_type . "_month_no"] = intval(substr($deadline_date, 5, 2));
	$_SESSION[$deadline_type . "_month"] = find_month_from_month_no (intval(substr($deadline_date, 5, 2)));
	$_SESSION[$deadline_type . "_day"] = intval(substr($deadline_date, 8, 2));
	$_SESSION[$deadline_type] = intval(substr($deadline_date, 0, 4)) . "-" . intval(substr($deadline_date, 5, 2)) . "-" . intval(substr($deadline_date, 8, 2)) ; //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)			
	
	$_SESSION["g_" . $deadline_type] = $_SESSION[$deadline_type . "_day"] . " " . $_SESSION[$deadline_type . "_month"] . " " . $_SESSION[$deadline_type . "_year"];

}//load_conference_dates_to_sessions($deadline_date, $deadline_type)

###########################
###########################

//$parent_dir_path --> the path of the directory inside of which the new directory will be created.
//$child_dir_name --> the name of the new directory.
function create_directory($parent_dir_path,$child_dir_name)
{
	$directory_mask = 0700;
	//check if a directory with this name already exists in the $parent_dir_path
	if(is_dir($parent_dir_path . $child_dir_name))
	{
		//echo "The directory ".$parent_dir_path . $child_dir_name." exists.";
		$full_path = $parent_dir_path . $child_dir_name . "/";	
	}//
	else
	{
		$full_path = $parent_dir_path . $child_dir_name . "/";	
		mkdir($full_path, $directory_mask);
		//echo $parent_dir_path . $child_dir_name." is not a valid directory.";
		//Create directory with name $child_dir_name
	}//
	return ($full_path);
}//create_directory($parent_dir_path,$child_dir_name)

############################
############################

//find_file_extension($filename)
function find_file_extension($filename)
{
	$filename = strtolower($filename) ;
	$file_extension = split("[/\\.]", $filename) ;
	$n = count($file_extension)-1;
	$file_extension = $file_extension[$n];
	return $file_extension;
}//find_file_extension($filename)

############################
############################

function load_conference_dates_to_array($deadline_date, $deadline_type)
{
	global $cvalues;

	$temp_cvalues[$deadline_type . "_year"] = intval(substr($deadline_date, 0, 4));
	$temp_cvalues[$deadline_type . "_month_no"] = intval(substr($deadline_date, 5, 2));
	$temp_cvalues[$deadline_type . "_month"] = find_month_from_month_no (intval(substr($deadline_date, 5, 2)));
	$temp_cvalues[$deadline_type . "_day"] = intval(substr($deadline_date, 8, 2));
	$temp_cvalues[$deadline_type] = intval(substr($deadline_date, 5, 2)) . "-" . intval(substr($deadline_date, 8, 2)) . "-" . intval(substr($deadline_date, 0, 4)) ; //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)			
		
	$temp_cvalues["g_" . $deadline_type] = $temp_cvalues[$deadline_type . "_day"] . " " . $temp_cvalues[$deadline_type . "_month"] . " " . $temp_cvalues[$deadline_type . "_year"];
	return ($temp_cvalues["g_" . $deadline_type]);
}//load_conference_dates_to_array($deadline_date, $deadline_type)

############################
############################

//$x and $y are arrays
function avgCompare($x, $y)
{
	if( $x["avg"] == $y["avg"]){return 0;}
	elseif ($x["avg"] < $y["avg"]){return 1;}
	else {return -1;}
}//avgCompare($x, $y)

//$x and $y are arrays
function discrCompare($x, $y)
{
	if( $x["discr"] == $y["discr"]){return 0;}
	elseif ($x["discr"] < $y["discr"]){return 1;}
	else {return -1;}
}//discrCompare($x, $y)

//$x and $y are arrays
function wavgCompare($x, $y)
{
	if( $x["wavg"] == $y["wavg"]){return 0;}
	elseif ($x["wavg"] < $y["wavg"]){return 1;}
	else {return -1;}
}//wavgCompare($x, $y)

############################
############################

function ap_getColor($rating)
{
    $bracket_reject = 2.5;
    $bracket_wreject = 3.5;
    $bracket_neutral = 4.5;
    $bracket_waccept = 5.5;

	if ($rating < $bracket_reject) { return ("berry_pink"); } //reject
	else if ($rating < $bracket_wreject) { return ("im_so_sorry"); } //weak reject
	else if ($rating < $bracket_neutral) { return ("neutral_blue"); } //neutral
	else if ($rating < $bracket_waccept) { return ("merky_blue"); } //weak accept
	else { return ("air_born"); } //accept

}//ap_getColor()

############################
############################

function organize_conf_actions_timetable($coptions2D)
{
	/*
	All the conference actions from the timetable
	*/
	$conf_options_timetable = array("create_new_conference"=>"focused", "assign_chairmen"=>"focused",
								"define_system_accepted_fformats"=>"focused", "post_announcements_1"=>"focused",
								"register"=>"strikethrough", "create_review_committee"=>"focused","select_conf_fformats"=>"focused", 
								"post_announcements_2"=>"focused","submit_p_abstracts"=>"focused", "post_announcements_3"=>"focused",
								"submit_interestlevels"=>"focused", 
								"submit_conflicts_with_reviewers"=>"focused","force_interesdt_levels"=>"focused", 
								"assign_papers_to_reviewers"=>"focused",
								"post_announcements_4"=>"focused", "submit_p_manuscripts"=>"focused","review_papers"=>"focused", 
								"accept_papers_for_conference"=>"focused",
								"post_announcements_5"=>"focused", "submit_camera-ready_papers"=>"focused",
								"inactivate_conf_functions"=>"focused", "post_announcements_6"=>"focused",
								"conf_users_download_n_read_all_papers"=>"focused");
		
	reset($conf_options_timetable);
	if($coptions2D["chairman"]["CIA"] == 0)
	{
		while (list($key_01, $val_01) = each ($conf_options_timetable))
		{
			if ( ($key_01 == "conf_users_download_n_read_all_papers") || ($key_01 == "create_new_conference")
				|| ($key_01 == "assign_chairmen") || ($key_01 == "define_system_accepted_fformats")
				|| ($key_01 == "post_announcements_1") || ($key_01 == "register")
				|| ($key_01 == "create_review_committee") || ($key_01 == "select_conf_fformats")
				|| ($key_01 == "post_announcements_2") || ($key_01 == "post_announcements_3")
				|| ($key_01 == "force_interesdt_levels") || ($key_01 == "assign_papers_to_reviewers")
				|| ($key_01 == "post_announcements_4") || ($key_01 == "accept_papers_for_conference")
				|| ($key_01 == "post_announcements_5") || ($key_01 == "inactivate_conf_functions")
				|| ($key_01 == "post_announcements_6") )
				{ continue; }
			else	
				{
					$conf_options_timetable[$key_01] = "strikethrough";
				}
			
			if( (($coptions2D["author"]["UVP"] == 0) && ($coptions2D["author"]["UDP"] == 0))
				&& (($coptions2D["author"]["UVAP"] == 0) && ($coptions2D["author"]["UDAP"] == 0)) )
				{ $conf_options_timetable["conf_users_download_n_read_all_papers"] = "strikethrough"; }
			
		}//outer while
	}//if
	elseif($coptions2D["chairman"]["CIA"] == 1)
	{
		if( ($coptions2D["author"]["ASA"] == 0) && ($coptions2D["author"]["AUA"] == 0) ){ $conf_options_timetable["submit_p_abstracts"] = "strikethrough"; }
		if( ($coptions2D["author"]["ASM"] == 0 ) && ($coptions2D["author"]["AUM"] == 0) ) { $conf_options_timetable["submit_p_manuscripts"] = "strikethrough"; }
		if( ($coptions2D["author"]["ASCRP"] == 0 ) && ($coptions2D["author"]["AUCRP"] == 0) ) { $conf_options_timetable["submit_camera-ready_papers"] = "strikethrough"; }
		if($coptions2D["author"]["ACR"] == 0) { $conf_options_timetable["submit_conflicts_with_reviewers"] = "strikethrough"; }
		if($coptions2D["reviewer"]["RELIC"] == 0) { $conf_options_timetable["submit_interestlevels"] = "strikethrough"; }
		if($coptions2D["reviewer"]["RDPR"] == 0) { $conf_options_timetable["review_papers"] = "strikethrough"; }
		
		if( (($coptions2D["author"]["UVP"] == 0) && ($coptions2D["author"]["UDP"] == 0))
			&& (($coptions2D["author"]["UVAP"] == 0) && ($coptions2D["author"]["UDAP"] == 0)) )
			{ $conf_options_timetable["conf_users_download_n_read_all_papers"] = "strikethrough"; }
	}//elseif
	
	
	return $conf_options_timetable;
}//organize_conf_actions_timetable()


############################
############################

function display_conf_timetable($conf_options_timetable)
{
	$even = "bgcolor=\"#FFFFFF\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='odd'\"";
	$odd = "bgcolor=\"#F5F0EA\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='even'\"";
		
	if(isset($_SESSION["administrator"]) || (isset($_SESSION["chairman"])))
	{
		?>
        <table id="help" cellpadding="2" cellspacing="2" border="0">
            <thead>
            <tr>
                <th scope="col" class="no"></th>
                <th scope="col" class="administrator">administrator</th>
                <th scope="col" class="chairman">chairman</th>
                <th scope="col" class="reviewer">reviewer</th>
                <th scope="col" class="author">author</th>
            </tr>
            </thead>

            <tbody>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td rowspan="4" bgcolor="#FFFFFF" >1</td>
                    <td><span class="<?=$conf_options_timetable["create_new_conference"];?>">create new conference.</span></td>
                    <td rowspan="4" colspan="3" bgcolor="#FFFFFF"><center><span class="<?=$conf_options_timetable["register"]?>">register in the system.</span></center></td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td><span class="<?=$conf_options_timetable["assign_chairmen"]?>">assign chairmen to conference.</span></td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td><span class="<?=$conf_options_timetable["define_system_accepted_fformats"]?>">define the accepted file formats for the system.</span></td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td><span class="<?=$conf_options_timetable["post_announcements_1"]?>">post announcement regarding new conference.</span></td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td rowspan="3" bgcolor="#FFFFFF">2</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["create_review_committee"]?>">create review committee.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["select_conf_fformats"]?>">select accepted file formats  for conference.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["post_announcements_2"]?>">post announcements.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td bgcolor="#FFFFFF" >3</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["submit_p_abstracts"]?>">submit paper abstracts.</span></td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td bgcolor="#FFFFFF" >4</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["post_announcements_3"]?>">post announcements.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td bgcolor="#FFFFFF">5</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["submit_interestlevels"]?>">submit interest levels for papers, and conflicts with authors.</span></td>
                    <td><span class="<?=$conf_options_timetable["submit_conflicts_with_reviewers"]?>">submit conflicts with reviewers.</span></td>
              </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td bgcolor="#FFFFFF" >6</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["post_announcements_3"]?>">post announcements.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td rowspan="3" bgcolor="#FFFFFF">7</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["force_interesdt_levels"]?>">execute &quot;Force interest levels and conflicts&quot; action.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["assign_papers_to_reviewers"]?>">assign papers to reviewers.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["post_announcements_4"]?>">post announcements.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td bgcolor="#FFFFFF">8</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["submit_p_manuscripts"]?>">submit paper manuscripts.</span></td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td bgcolor="#FFFFFF">9</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["review_papers"]?>">review papers.</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td bgcolor="#FFFFFF">10</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["accept_papers_for_conference"]?>">accept papers for conference.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                 <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td bgcolor="#FFFFFF">11</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["post_announcements_5"]?>">post announcements.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                 <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                    <td bgcolor="#FFFFFF">12</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["submit_camera-ready_papers"]?>">submit camera-ready versions of papers.</span></td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td bgcolor="#FFFFFF">13</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["inactivate_conf_functions"]?>">inactivate conference functions.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#FFFFFF" onMouseOver="this.className='highlight'" onMouseOut="this.className='odd'">
                <td bgcolor="#FFFFFF">14</td>
                    <td>&nbsp;</td>
                    <td><span class="<?=$conf_options_timetable["post_announcements_6"]?>">post announcements.</span></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr bgcolor="#F5F0EA" onMouseOver="this.className='highlight'" onMouseOut="this.className='even'">
                    <td bgcolor="#FFFFFF">15</td>
                    <td>&nbsp;</td>
                    <td colspan="3"><span class="<?=$conf_options_timetable["conf_users_download_n_read_all_papers"]?>">conference users can download and read all the papers.</span></td>
                </tr>
            </tbody>
        </table>
        
    <?php
	}
	elseif(isset($_SESSION["reviewer"]))
	{
		echo "<table id=\"help\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"no\"></th>
                <th scope=\"col\" class=\"reviewer\">reviewer</th>
                <th scope=\"col\" class=\"author\">author</th>
            </tr>
            </thead>

            <tbody>
                <tr $even>
                    <td>1</td>
                    <td colspan=\"2\"><center><span class=\"" . $conf_options_timetable["register"] . "\">register in the system.</span></center></td>
                </tr>
                <tr $odd>
                    <td>2</td>
                    <td>&nbsp;</td>
                    <td><span class=\"" . $conf_options_timetable["submit_p_abstracts"] . "\">submit paper abstracts.</span></td>
                </tr>
                <tr $even>
                    <td>3</td>
                    <td><span class=\"" . $conf_options_timetable["submit_interestlevels"] . "\">submit interest levels for papers, and conflicts with authors.</span></td>
                    <td><span class=\"" . $conf_options_timetable["submit_conflicts_with_reviewers"] . "\">submit conflicts with reviewers.</span></td>
                </tr>
                <tr $odd>
                    <td>4</td>
                    <td>&nbsp;</td>
                    <td><span class=\"" . $conf_options_timetable["submit_p_manuscripts"] . "\">submit paper manuscripts.</span></td>
                </tr>
                <tr $even>
                    <td>5</td>
                    <td><span class=\"" . $conf_options_timetable["review_papers"] . "\">review papers.</span></td>
                    <td>&nbsp;</td>
                </tr>
                <tr $odd>
                    <td>6</td>
                    <td>&nbsp;</td>
                    <td><span class=\"" . $conf_options_timetable["submit_camera-ready_papers"] . "\">submit camera-ready versions of papers.</span></td>
                </tr>
                <tr $even>
                    <td>7</td>
                    <td colspan=\"2\"><center><span class=\"" . $conf_options_timetable["conf_users_download_n_read_all_papers"] . "\">conference users can download and read all the papers.</span></center></td>
                </tr>
            </tbody>
        </table>";
	}
	elseif(isset($_SESSION["author"]))
	{
		echo "<table id=\"help\" cellpadding=\"2\" cellspacing=\"2\" border=\"0\">
            <thead>
            <tr>
                <th scope=\"col\" class=\"no\"></th>
                <th scope=\"col\" class=\"author\">author</th>
            </tr>
            </thead>
			
            <tbody>
                <tr $even>
                	<td>1</td>
                    <td><span class=\"" . $conf_options_timetable["register"] . "\">register in the system.</span></td>
                </tr>
                <tr $odd>
                    <td>2</td>
                    <td><span class=\"" . $conf_options_timetable["submit_p_abstracts"] . "\">submit paper abstracts.</span></td>
                </tr>
                <tr $even>
                    <td>3</td>
                    <td><span class=\"" . $conf_options_timetable["submit_conflicts_with_reviewers"] . "\">submit conflicts with reviewers.</span></td>
                </tr>
                <tr $odd>
                    <td>4</td>
                    <td><span class=\"" . $conf_options_timetable["submit_p_manuscripts"] . "\">submit paper manuscripts.</span></td>
                </tr>
                <tr $even>
                    <td>5</td>
                    <td><span class=\"" . $conf_options_timetable["submit_camera-ready_papers"] . "\">submit camera-ready versions of papers.</span></td>
                </tr>
                <tr $odd>
                    <td>6</td>
                    <td><span class=\"" . $conf_options_timetable["conf_users_download_n_read_all_papers"] . "\">download and read all the papers.</span></td>
                </tr>
            </tbody>
        </table>";
	}//
	
}//display_conf_timetable()

?>