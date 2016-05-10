<?php
####################################################
/*
	empty_conference_form(),
	empty_file_format_form(),
	empty_papers_form(),
	unset_conference_sessions(),
	empty_conference_sessions(),
	empty_fileformat_sessions(),
	empty_user_info_sessions(),
	empty_upated_user_info_sessions(),
	empty_view_conference_info_sessions(),
	change_password_empty_sessions(),
	empty_assignment_sessions(),
	empty_announcement_sessions(),
	empty_reviewers_assignment_sessions(),
	empty_conference_options(),
	empty_paper_sessions(),
	empty_paper_interest_level_sessions(),
	empty_assign_reviewers_to_paper_sessions(),
	empty_paper_body_sessions(),
	empty_review_sessions()
*/
####################################################

//function that empties the sessions for the form of page conferences.php
function empty_conference_form() {
		empty_conference_sessions();
		$_SESSION["updateconference"] = "no";
		
		unset($_SESSION["conf_id"]);
		unset($_SESSION["conf_name"]);
				
		Redirects(7,"","");
}//empty_image_form

function empty_fileformat_form() {
	empty_fileformat_sessions();
	$_SESSION["updatefileformat"] = "no";
	Redirects(34,"","");
}//empty_fileformat_form

function empty_papers_form() {
	empty_paper_sessions();
	$_SESSION["updatepaper"] = "no";
	Redirects(38,"","");
}//empty_papers_form


//unset_conference_sessions()
function unset_conference_sessions()
{
	unset($_SESSION["id"]);
	unset($_SESSION["name"]);
	unset($_SESSION["place"]);
	unset($_SESSION["date_conference_held"]);
	unset($_SESSION["contact_email"]);
	unset($_SESSION["contact_phone"]);
	unset($_SESSION["website"]);
	unset($_SESSION["comments"]);
	unset($_SESSION["date_of_creation"]);
	unset($_SESSION["deadline_year"]);
	unset($_SESSION["deadline_month_no"]);
	unset($_SESSION["deadline_month"]);
	unset($_SESSION["deadline_day"]);
}//unset_conference_sessions

//function that empties all sessions
function empty_conference_sessions()
{
	unset($_SESSION["alias"]);
	unset($_SESSION["name"]);
	unset($_SESSION["conference_name"]);
	unset($_SESSION["conference_id"]);
	unset($_SESSION["place"]);
	unset($_SESSION["date_conference_held"]);
	unset($_SESSION["contact_email"]);
	unset($_SESSION["contact_phone"]);
	unset($_SESSION["website"]);
	unset($_SESSION["comments"]);
	unset($_SESSION["date_of_creation"]);
	unset($_SESSION["deadline"]);

	unset($_SESSION["deadline_year"]);
	unset($_SESSION["deadline_month_no"]);
	unset($_SESSION["deadline_month"]);
	unset($_SESSION["deadline_day"]);

	unset($_SESSION["abstracts_deadline_year"]);
	unset($_SESSION["abstracts_deadline_month_no"]);
	unset($_SESSION["abstracts_deadline_month"]);
	unset($_SESSION["abstracts_deadline_day"]);

	unset($_SESSION["manuscripts_deadline_year"]);
	unset($_SESSION["manuscripts_deadline_month_no"]);
	unset($_SESSION["manuscripts_deadline_month"]);
	unset($_SESSION["manuscripts_deadline_day"]);

	unset($_SESSION["camera_ready_deadline_year"]);
	unset($_SESSION["camera_ready_deadline_month_no"]);
	unset($_SESSION["camera_ready_deadline_month"]);
	unset($_SESSION["camera_ready_deadline_day"]);

	unset($_SESSION["preferencies_deadline_year"]);
	unset($_SESSION["preferencies_deadline_month_no"]);
	unset($_SESSION["preferencies_deadline_month"]);
	unset($_SESSION["preferencies_deadline_day"]);

	unset($_SESSION["reviews_deadline_year"]);
	unset($_SESSION["reviews_deadline_month_no"]);
	unset($_SESSION["reviews_deadline_month"]);
	unset($_SESSION["reviews_deadline_day"]);

	unset($_SESSION["abstracts_deadline"]);
	unset($_SESSION["manuscripts_deadline"]);
	unset($_SESSION["camera_ready_deadline"]);
	unset($_SESSION["preferencies_deadline"]);
	unset($_SESSION["reviews_deadline"]);

	unset($_SESSION["g_deadline"]);
	unset($_SESSION["g_abstracts_deadline"]);
	unset($_SESSION["g_manuscripts_deadline"]);
	unset($_SESSION["g_camera_ready_deadline"]);
	unset($_SESSION["g_preferencies_deadline"]);
	unset($_SESSION["g_reviews_deadline"]);

	unset($_SESSION["conf_id"]);
	unset($_SESSION["conf_name"]);
}//empty_conference_sessions

//empty_fileformat_sessions()
function empty_fileformat_sessions()
{
	unset($_SESSION["extension"]);
	unset($_SESSION["description"]);
	unset($_SESSION["mime_type"]);
	unset($_SESSION["file_format_id"]);

	$_SESSION["updatefileformat"] = "no";

}//empty_fileformat_sessions()

function empty_user_info_sessions()
{
	unset($_SESSION["user_info_id"]);
	unset($_SESSION["user_info_fname"]);
	unset($_SESSION["user_info_lname"]);
	unset($_SESSION["user_info_email"]);
	unset($_SESSION["user_info_address_01"]);
	unset($_SESSION["user_info_address_02"]);
	unset($_SESSION["user_info_address_03"]);
	unset($_SESSION["user_info_city"]);
	unset($_SESSION["user_info_country"]);
	unset($_SESSION["user_info_phone_01"]);	
	unset($_SESSION["user_info_phone_02"]);
	unset($_SESSION["user_info_fax"]);
	unset($_SESSION["user_info_website"]);
}//empty_user_info_sessions

function empty_upated_user_info_sessions()
{
	unset($_SESSION["email"]);
	unset($_SESSION["fname"]);
	unset($_SESSION["lname"]);
	unset($_SESSION["user_updated"]);
	unset($_SESSION["address_01"]);
	unset($_SESSION["address_02"]);
	unset($_SESSION["address_03"]);
	unset($_SESSION["city"]);
	unset($_SESSION["country"]);
	unset($_SESSION["phone_01"]);
	unset($_SESSION["phone_02"]);
	unset($_SESSION["fax"]);		
	unset($_SESSION["website"]);
	unset($_SESSION["birthday_month"]);
	unset($_SESSION["birthday_month_no"]);
	unset($_SESSION["birthday_day"]);
	unset($_SESSION["birthday_year"]);
	unset($_SESSION["security_question"]);
	unset($_SESSION["security_answer"]);
	unset($_SESSION["birthday"]);
}//empty_upated_user_info_sessions()

function empty_view_conference_info_sessions()
{
	unset($_SESSION["conference_id"]);
	unset($_SESSION["updateconference"]);
	empty_conference_sessions();
}//empty_view_conference_info_sessions()

function change_password_empty_sessions()
{
	session_unset();
	// Clear the session cookie
	unset($_COOKIE[session_name()]);
	// Destroy session data
	session_destroy();
}//change_password_empty_sessions()

function empty_assignment_sessions()
{
	unset($_SESSION["user_id"]); 
	unset($_SESSION["conference_id"]); 
	unset($_SESSION["type"]);
	unset($_SESSION["conference_name"]);
	unset($_SESSION["name"]);
	//unset($_SESSION["id"]);
	unset($_SESSION["deadline"]);
	unset($_SESSION["date_of_creation"]);
}//empty_assignment_sessions()

function empty_announcement_sessions()
{
	unset($_SESSION["post_date"]);
	unset($_SESSION["message"]);

	unset($_SESSION["regardschairmen"]);
	unset($_SESSION["regardsreviewers"]);
	unset($_SESSION["regardsauthors"]);
}//empty_announcement_sessions()

//empty_reviewers_assignment_sessions()
function empty_reviewers_assignment_sessions()
{
	unset($_SESSION["user_id"]);
	unset($_SESSION["conference_id"]);
	unset($_SESSION["type"]);
}//empty_reviewers_assignment_sessions()

function empty_conference_options()
{
	unset($_SESSION["CIA"]);
	unset($_SESSION["ASA"]);
	unset($_SESSION["AUA"]);
	unset($_SESSION["ASM"]);
	unset($_SESSION["AUM"]);
	unset($_SESSION["ASCRP"]);
	unset($_SESSION["AUCRP"]);
	unset($_SESSION["AVP"]);
	unset($_SESSION["ACR"]);
	unset($_SESSION["NORPC"]);
	unset($_SESSION["RELIC"]);
	unset($_SESSION["RDPR"]);
	unset($_SESSION["RVRP"]);
	unset($_SESSION["UVP"]);
	unset($_SESSION["UDP"]);
	unset($_SESSION["UVAP"]);
	unset($_SESSION["UDAP"]);
}//empty_conference_options()

function empty_paper_sessions()
{
	//empty the forms sessions 
	unset($_SESSION["user_id"]);
	unset($_SESSION["conference_id"]);
	unset($_SESSION["title"]);
	unset($_SESSION["abstract"]);
	unset($_SESSION["authors"]);
	unset($_SESSION["status_code"]);
	unset($_SESSION["subject"]);
	unset($_SESSION["submition_date"]);

	//when a paper is selected from the papers combo box, to upload
	//that papers info, so that they would be updated, the following
	//values are stored in sessions. Here we unset them. 
	
	unset($_SESSION["paper_id"]);
	unset($_SESSION["paper_title"]);
	unset($_SESSION["title"]);
	unset($_SESSION["abstract"]);
	unset($_SESSION["authors"]);
	unset($_SESSION["subject"]);

	unset($_SESSION["update_authors"]);
	$_SESSION["updatepaper"]="no";
	//		unset($_SESSION["updatepaper"]);
	
}//empty_paper_sessions()

function empty_paper_interest_level_sessions()
{
	unset($_SESSION["paper_id"]);
	unset($_SESSION["user_id"]);
	unset($_SESSION["level_of_interest"]);
	unset($_SESSION["conflict"]);

	unset($_SESSION["updatepaper_interestlevel"]);
}//empty_paper_interest_level_sessions

function empty_assign_reviewers_to_paper_sessions()
{
	unset($_SESSION["paper_id"]);
	unset($_SESSION["temp_paper_id"]);
	unset($_SESSION["conference_id"]);

	unset($_SESSION["update_reviewer_assignment"]);
}//empty_paper_interest_level_sessions

function empty_paper_body_sessions()
{
	unset($_SESSION["paper_type"]);
	unset($_SESSION["date_of_submition"]);
	unset($_SESSION["upload_type"]);
	unset($_SESSION["filename"]);
	unset($_SESSION["filesize"]);
	unset($_SESSION["filecontent"]);
	unset($_SESSION["fileurl"]);
	unset($_SESSION["format_id"]);

}//empty_paper_body_sessions()

function empty_review_sessions()
{
	unset($_SESSION["user_id"]);
	unset($_SESSION["paper_id"]);
	unset($_SESSION["conference_id"]);
	unset($_SESSION["date_of_submition"]);
	unset($_SESSION["referee_name"]);
	unset($_SESSION["originality"]);
	unset($_SESSION["significance"]);
	unset($_SESSION["quality"]);
	unset($_SESSION["relevance"]);
	unset($_SESSION["presentation"]);
	unset($_SESSION["overall"]);
	unset($_SESSION["expertise"]);
	unset($_SESSION["confidential"]);
	unset($_SESSION["contributions"]);
	unset($_SESSION["positive"]);
	unset($_SESSION["negative"]);
	unset($_SESSION["further"]);

	unset($_SESSION["updatereview"]);
	unset($_SESSION["flg"]);
}//empty_review_sessions()
?>