<?php
##################################################################################################
##############This php file loads the inc.php files that include all the functions################
/*
	Redirects($r_id,$flags,$more_flags),
*/
##################################################################################################

session_start();

//load functions
//require("initset.php");
require("loginlogoutinc.php"); 
require("commonfunctionsinc.php");
require("checkvariablesinc.php");
require("insertdbinc.php");
require("sessioninitinc.php");
require("updatedbinc.php");
require("selectdbinc.php");
require("selectdbcommoninc.php");
require("selectdbadmininc.php");
require("selectdbchairinc.php");
require("selectdbrevinc.php");
require("selectdbauthinc.php");
require("selectdbcomboboxesinc.php");
require("emptysessionsinc.php");
require("uploaddeleteinc.php");
require("emailinc.php");
require("usersactionsloginc.php");
require("loadconfoptionsinc.php");

//require("errorreportinc.php"); do not use here

//function with different headers for redirection purposes
function Redirects($r_id,$flags,$more_flags)
{
	if(!preg_match("/^[0-9]([0-9]*)/",$r_id)){ $r_id = NULL; }
	else {}//do nothing. ALL OK

	switch($r_id)
	{
		case 0:
			header("Location: ./index.php".$flags);
			exit;
			break;
		case 1:
			header("Location: ../user_registration.php".$flags);
			exit;
			break;
		case 2:
			header("Location: ../user_registration_complete.php");
			exit;
			break;
		case 3:
			header("Location: ../login.php".$flags);
			exit;
			break;
		case 4:				
			header("Location: ../change_password.php".$flags);
			exit;
			break;
		case 5:
			header("Location: ../ulounge.php".$flags);
			exit;
			break;
		case 6:
			//for update user data
			break;
		case 7:
			header("Location: ../conferences.php".$flags);
			exit;
			break;
		case 8:
			header("Location: ../conferences.php".$flags);
			exit;
			break;				
		case 9:
			header("Location: ../choose_conference.php".$flags.$more_flags);
			exit;
			break;
		case 10:
			header("Location: ../update_conference.php".$flags);
			exit;
			break;
		case 11:
			header("Location: ../assign_conferences.php".$flags.$more_flags);
			exit;
			break;
		case 12:
			header("Location: ../update_assignments.php");
			exit;
			break;
		case 13:
			header("Location: ../login_choose_conference.php".$flags.$more_flags);
			exit;
			break;
		case 14:
			header("Location: ../update_user_profile.php".$flags.$more_flags);
			exit;
			break;
		case 15:
			header("Location: ../update_user_profile_complete.php");
			exit;
			break;
		case 16:
			header("Location: ../conference_control_panel.php".$flags.$more_flags);
			exit;
			break;
		case 17:
			header("Location: ../update_conference_options_complete.php");
			exit;
			break;
		case 18:
			header("Location: ../create_review_committee.php".$flags.$more_flags);
			exit;
			break;
		case 19:
			header("Location: ../update_review_committee.php");
			exit;
			break;
		case 20:
			header("Location: ../user_info.php");
			exit;
			break;
		case 21:
			header("Location: ../select_conference.php".$flags.$more_flags);
			exit;
			break;
		case 22:
			header("Location: ./chairmen_assignments.php".$flags.$more_flags);
			exit;
			break;
		case 23:
			header("Location: ../chairmen_assignments.php".$flags.$more_flags);
			exit;
			break;
		case 24: 
			header("Location: ../reviewers_assignments.php".$flags.$more_flags);
			exit;
			break;
		case 25:
			header("Location: ./conference_control_panel.php".$flags.$more_flags);
			exit;
			break;
		case 26:
			header("Location: ../chairman_conference_control_panel.php".$flags.$more_flags);
			exit;
			break;
		case 27:
			header("Location: ../users_list.php".$flags.$more_flags);
			exit;
			break;
		case 28:
			header("Location: ../display_users_again.php".$flags.$more_flags);
			exit;
			break;
		case 29:
			header("Location: ../conference_info.php".$flags.$more_flags);
			exit;
			break;
		case 30:
			header("Location: ../display_users.php".$flags.$more_flags);
			exit;
			break;
		case 31:
			header("Location: ../conferences.php".$flags.$more_flags);
			exit;
			break;
		case 32: 
			header("Location: ../announcements.php".$flags.$more_flags);
			exit;
			break;
		case 33:
			header("Location: ./administrator_announcements.php".$flags.$more_flags);
			exit;
			break;
		case 34:
			header("Location: ../file_formats.php".$flags.$more_flags);
			exit;
			break;
		case 35:
			header("Location: ../administrator_announcements.php".$flags.$more_flags);
			exit;
			break;
		case 36:
			header("Location: ../user_info.php".$flags.$more_flags);
			exit;
			break;
		case 37:
			header("Location: ../update_conference.php".$flags.$more_flags);
			exit;
			break;
		case 38:
			header("Location: ../papers.php".$flags.$more_flags);
			exit;
			break;
		case 39:
			header("Location: ../paper_interest_level.php".$flags.$more_flags);
			exit;
			break;
		case 40:
			header("Location: ../set_interest_levels.php".$flags.$more_flags);
			exit;
			break;
		case 41:
			header("Location: ../pi_interest_level.php".$flags.$more_flags);
			exit;
			break;
		case 42:
			header("Location: ../assign_reviewers.php".$flags.$more_flags);
			exit;
			break;
		case 43:
			header("Location: ../view_assignments.php".$flags.$more_flags);
			exit;
			break;
		case 44:
			header("Location: ../paper_body.php".$flags.$more_flags);
			exit;
			break;
		case 45:
			header("Location: ../conflicts.php".$flags.$more_flags);
			exit;
			break;
		case 46:
			header("Location: ../force_interest_levels.php".$flags.$more_flags);
			exit;
			break;
		case 47:
			header("Location: ../chairman_file_formats.php".$flags.$more_flags);
			exit;
			break;
		case 48:
			header("Location: ../assign_reviewersf.php".$flags.$more_flags);
			exit;
			break;
		case 49:
			header("Location: ../set_interest_levelsf.php".$flags.$more_flags);
			exit;
			break;
		case 50:
			header("Location: ../reviews.php".$flags.$more_flags);
			exit;
			break;
		case 51:
			header("Location: ../assigned_papers.php".$flags.$more_flags);
			exit;
			break;
		case 52:
			//pay attention to this one
			//in the page 'reviews' there can only be one $_Get value ('paperid')
			//Because in this case we want to redirect to the 'reviews' page and send '?flg=error_code' which
			//exists in the $flags variable, we explode $flags and take the error_code.
			//then we save this error code in a Session (this session is later destroyed)
			$temp = explode("=",$flags);
			$_SESSION["flg"] = $temp[1];
			header("Location: ../reviews.php".$more_flags."");
			exit;
			break;
		case 53:
			header("Location: ../accept_papers.php".$flags.$more_flags);
			exit;
			break;
		case 54:
			header("Location: ../display_papers_ad.php".$flags.$more_flags);
			exit;
			break;
		case 55:
			//if(@header("Location: ../errors.php".$flags.$more_flags)){ exit;}
			//else { return 0; }			
			//break;
			header("Location: ./errors.php".$flags.$more_flags);
			exit;			
			break;
		case 56:
		 	header("Location: ../users_actions_log_db.php".$flags.$more_flags);
			exit;
			break;
		case 57:
		 	header("Location: ../users_actions_log_file.php".$flags.$more_flags);
			exit;
			break;
		case 58:
			header("Location: ../administrator_help.php".$flags.$more_flags);
			exit;
			break;
		default:
			//do nothing
			break;
	}
}//end Redirects

reset($_GET); //resets the pointer to the $_GET table
if(isset($_GET["type"]))
{ 
	if(!preg_match("/^[0-9]([0-9]*)/",$_GET["type"])){$_GET["type"] = NULL; }
	else{$get_type = $_GET["type"]; }
}else { $get_type = NULL; }

switch($get_type)
{
	case 1:
		login();
		break;
	case 2:
		logout();
		break;
	case 3:
		user_registration();
		break;
	case 4:
		new_user();
		break;
	case 5:
		change_password();
		break;
	case 6:
		//update_user_data(); //THIS FUNCTION DOESN'T EXIST ANYMORE
		break;
	case 7:
		create_conference();
		break;
	case 8:
		update_conference($_GET["user_type"]);
		break;
	case 9:
		search_conference($_GET["action"]);
		break;
	case 10:
		if(isset($_GET["user_type"])){ assign_chairmen_to_conferences($_GET["user_type"]); }
		else {Redirects(0,"","");}
		break;
	case 11:
		if(isset($_GET["id"]))
		{ 
			$_POST["chairman_id"] = $_GET["id"];
			$_POST["conference_id"] = $_SESSION["conf_id"];
			remove_chairman_from_conference(); 
		}
		else {Redirects(0,"","");}
		break;
	case 12:
		// update_assignments(); //THIS FUNCTION DOESN'T EXIST ANYMORE
		break;
	case 13:
		//find_user_type($action); //FUNCTION EXISTS BUT THIS IS NOT USED ANYWHERE
		Redirects(0,"","");
		break;
	case 14:
		find_user_data();
		break;
	case 15:
		update_user_profile();
		break;
	case 16:
		update_conference_options();
		break;
	case 17:
		if(isset($_GET["user_type"])){ create_review_committee($_GET["user_type"]); }
		else{ Redirects(0,"",""); }
		break;
	case 18:
		if(isset($_GET["id"]))
		{
			$_POST["reviewer_id"] = $_GET["id"];
			$_POST["conference_id"] = $_SESSION["conf_id"];
			remove_reviewer_from_committee($_GET["id"], $_SESSION["conf_id"]);
		}
		else { Redirects(0,"",""); }
		break;		
	case 19:
		//view_user_info($_GET["user_id"],$_GET["redirect_to"]); //FUNCTION EXISTS BUT THIS IS NOT USED ANYWHERE
		Redirects(0,"","");
		break;
	case 20:
		empty_conference_form();
		break;
	case 21:
		// find_conference(); //FUNCTION EXISTS, BUT LOOK AT CASE 22!
		break;
	case 22:
		find_conference();
		break;
	case 23:
		find_user();
		break;
	case 24:
		create_announcement();
		break;
	case 25:
		insert_new_file_format();
		break;
	case 26:
		update_file_format();
		break;
	case 27:
		find_file_format();
		break;
	case 28:
		empty_fileformat_form();
		break;
	case 29:
		create_paper();
		break;
	case 30:
		find_paper("papers");
		break;
	case 31:
		empty_papers_form();
		break;
	case 32:
		update_paper();
		break;
	case 33:
		insert_paper_interest_level();
		break;
	case 35:
		update_paper_interest_level();
		break;
	case 37:
		assign_reviewers_to_paper();
		break;
	case 38:
		update_assign_reviewers_to_paper();
		break;
	case 39:
		find_paper("paper_body");
		break;
	case 40:
		insert_paper_body();
		break;
	case 41:
		find_paper("conflicts");
		break;
	case 42:
		enter_conflicts_with_reviewers();
		break;
	case 43:
		force_interest_levels();
		break;
	case 44:
		insert_conference_file_format();
		break;
	case 45:
		if(isset($_GET["id"]))
		{
			$_POST["file_format_id"] = $_GET["id"];
			$_POST["conference_id"] = $_SESSION["conf_id"];
			remove_file_format_from_conference();
		}
		else { Redirects(0,"",""); }
		break;
	case 46:
		assign_reviewers_to_paper2();
		break;
	case 47:
		insert_paper_interest_level2();
		break;
	case 48:
		if(isset($_GET["c_id"]))
		{
			$_POST["conference_name"] = $_GET["c_id"];
			search_conference("user_login");
		}else{ Redirects(0,"",""); }
		break;
	case 49:
		$_SESSION["updatereview"] = "no";
		unset($_SESSION["flg"]);
		unset($_SESSION["rev_varcheck"]);
		Redirects(50,"?paperid=" . $_GET["paperid"],"");
		break;
	case 50:
		review_paper();
		break;
	case 51:
		if(isset($_GET["paperid"]))
		{
			$_SESSION["updatereview"] = "yes";
			unset($_SESSION["flg"]);
			unset($_SESSION["rev_varcheck"]);
			
			$_POST["paper_id"] = $_GET["paperid"];
			$_POST["logged_user_id"] = $_SESSION["logged_user_id"];
			$_POST["conference_id"] = $_SESSION["conf_id"];
			select_review();
		}else { Redirects(0,"",""); }
		break;
	case 52:
		$_SESSION["update_reviewer_assignment"] = "no";
		Redirects(42,"?paperid=" . $_GET["paperid"],"");
		break;
	case 53:
		$_SESSION["update_reviewer_assignment"] = "yes";
		Redirects(42,"?paperid=" . $_GET["paperid"],"");
		break;
	case 54:
		empty_paper_interest_level_sessions();
		$_SESSION["updatepaper_interestlevel"] = "no";
		Redirects(39,"?paperid=" . $_GET["paperid"],"");
		//Redirects(41,"?paperid=" . $_GET["paperid"],"");
		break;
	case 55:
		$_SESSION["updatepaper_interestlevel"] = "yes";
		Redirects(39,"?paperid=" . $_GET["paperid"],"");
		//Redirects(41,"?paperid=" . $_GET["paperid"],"");
		break;
	case 56:
		//if the sessions regarding a paper are filled, empty them
		empty_paper_sessions();
		Redirects(38,"","");
		break;
	case 57:
		$_SESSION["updateconference"] = "no";
		//if the sessions regarding the conference are filled, empty them.
		empty_conference_sessions();
		Redirects(8,"","");
		break;
	case 58:
		$_SESSION["updatefileformat"] = "no";
		Redirects(34,"","");
		break;
	case 59:
		accept_papers_for_conference();
		break;
	case 60:
		//if the sessions regarding a paper are filled, empty them
		empty_paper_sessions();
		Redirects(44,"","");
		break;
	case 61:
		//if the sessions regarding a paper are filled, empty them
		unset($_SESSION["temp_token"]);
		empty_paper_sessions();
		Redirects(45,"","");
		break;
	default:
		//IMPORTANT TO HAVE NO ACTION
		return -1;
		break;
}//switch

?>