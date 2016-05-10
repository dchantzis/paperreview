<?php
//////////////////////////////////////
/////////layout functions/////////////
//////////////////////////////////////
###############################################################
/*
displayUserMenus(),
layout_conf_search_form()
*/
###############################################################

function displayUserMenus()
{
	$str_01 = "\n\t\t\t<div id=\"navigation\">";
	$str_02 = "\n\t\t\t\t<ul id=\"navi\" class=\"navi_tree\">";
	$str_03 = "\n\t\t\t\t</ul>";
	$str_04 = "\n\t\t\t\t\t</div>"; 

	echo $str_01 . $str_02;
	//echo "<a href=\"javascript:ddtreemenu.flatten('navi', 'expand')\" id=\"expand\">expand all</a> ";
	//echo "<a href=\"javascript:ddtreemenu.flatten('navi', 'contract')\" id=\"contract\">contract all</a>";
	echo "\n\t\t\t\t\t<li><a href=\"./ulounge.php\" class=\"\" title=\"Home\">Home</a></li>";
	echo "\n\t\t\t\t\t<li><a href=\"./display_announcements.php?flg=0\" class=\"\" title=\"Conference Announcements\" >Announcements</a></li>";
	if(isset($_SESSION["administrator"]))//if the logged user is the system administrator then show his menu
	{
		echo "\n\t\t\t\t\t<li id=\"parent_node_admin\"><a href=\"#\" class=\"\" title=\"Administrator Navigation\">Administrator</a>";
		echo "\n\t\t\t\t\t\t<ul id=\"admin_subnavi\" class=\"subnavi\">";
		echo "\n\t\t\t\t\t\t<li><a href=\"./include/functionsinc.php?type=57\" title=\"Create/update conferences\" >Conferences</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./chairmen_assignments.php\" title=\"Chairman assignment options\" >Chairmen</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./display_users.php\" title=\"User options\" >Users</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./display_papers_ad.php\" title=\"Paper options\">Papers</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./conference_control_panel.php\" title=\"Conference control panel\" >Control Panel</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./administrator_announcements.php\" title=\"Announcements\" >Announcements</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./include/functionsinc.php?type=58\" title=\"File Formats\" >File Formats</a></li>";
		echo "\n\t\t\t\t\t\t<li><a href=\"./users_actions_log.php\" title=\"Users Action Log\" >Users Action Log</a></li>";
		echo "\n\t\t\t\t\t\t</ul>";
		echo "\n\t\t\t\t\t</li>";
	}//if
	elseif(!isset($_SESSION["administrator"])) //if the logged user is NOT the system administrator then don't show his menu
	{
			if(isset($_SESSION["chairman"])) //if the logged user is a chairman then show his menu
			{
				echo "\n\t\t\t\t\t<li id=\"parent_node_ch\"><a href=\"#\" class=\"\" title=\"Chairman Navigation\">Chairman</a>";
				echo "\n\t\t\t\t\t\t<ul id=\"chairman_subnavi\" class=\"subnavi\">";
				echo "\n\t\t\t\t\t\t<li><a href=\"./reviewers_assignments.php\" title=\"Create Review Committee\">Reviewers</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"force_interest_levels.php\" title=\"Force Declaration of Interest Levels and Conflicts.\">Force Interests</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"assign_papers.php\" title=\"Assign Papers To Reviewers\">Assign Papers</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./papers_display_options.php\" title=\"Papers\">Papers</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./reviews_display_options.php\" title=\"Paper Reviews\">Reviews</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./display_users.php\" title=\"User options\">Users</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./include/functionsinc.php?type=22\" title=\"Update Conference\">Conference</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./chairman_conference_control_panel.php\" title=\"Conference control panel\">Control Panel</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./announcements.php\" title=\"Create Announcements\">Announcements</a></li>";
				echo "\n\t\t\t\t\t\t<li><a href=\"./chairman_file_formats.php\" title=\"Select Supported File Formats\">File Formats</a></li>";
				echo "\n\t\t\t\t\t\t</ul>";
				echo "\n\t\t\t\t\t</li>";
			}//if
			if(isset($_SESSION["reviewer"])) //if the logged user is a reviewer then show his menu
			{
				echo "\n\t\t\t\t\t<li id=\"parent_node_rev\"><a href=\"#\" class=\"\" title=\"Reviewer Navigation\">Reviewer</a>";		
				echo "\n\t\t\t\t\t<ul id=\"reviewer_subnavi\" class=\"subnavi\">";
				echo "\n\t\t\t\t\t<li><a href=\"./interest_levels.php\" title=\"Enter levels of interest and conflicts for papers.\">Interest Levels</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./assigned_papers.php\" title=\"Review papers\">Review Papers</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./display_reviews_r.php\" title=\"Display paper reviews\">Display Reviews</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./display_papers.php\" title=\"Display papers\">Display Papers</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./contact_chairmen.php\" title=\"Contact\">Contact Chairmen</a></li>";
				echo "\n\t\t\t\t\t</ul>";
				echo "\n\t\t\t\t\t</li>";	
			}//if
			if(isset($_SESSION["author"]))  //if the logged user is an author then show his menu
			{
				echo "\n\t\t\t\t\t<li id=\"parent_node_auth\"><a href=\"#\" class=\"\" title=\"Author Navigation\">Author</a>";
				echo "\n\t\t\t\t\t<ul id=\"author_subnavi\" class=\"subnavi\">";
				echo "\n\t\t\t\t\t<li><a href=\"./include/functionsinc.php?type=56\" title=\"Create/Update papers\">Papers</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./include/functionsinc.php?type=60\" title=\"Submit paper body\">Submit P. body</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./conflicts.php\" title=\"Enter conflicts with Reviewers\">Conflicts</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./display_reviews_a.php\" title=\"Display paper reviews\">Display Reviews</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./display_papers.php\" title=\"Display papers\">Display Papers</a></li>";
				echo "\n\t\t\t\t\t<li><a href=\"./contact_chairmen.php\" title=\"Contact\">Contact Chairmen</a></li>";
				echo "\n\t\t\t\t\t</ul>";
				echo "\n\t\t\t\t\t</li>";
			}//if
	}//else
	if(isset($_SESSION["administrator"])){	echo "\n\t\t\t\t\t<li><a href=\"./administrator_help.php\" class=\"\" title=\"Help\">Help</a></li>";}
	elseif(!isset($_SESSION["administrator"])){	echo "\n\t\t\t\t\t<li><a href=\"./help.php\" class=\"\" title=\"Help\">Help</a></li>";}
	echo $str_03 . $str_04;
}//displayUserMenus

function layout_conf_search_form()
{
	global $csrf_password_generator;
	if(isset($_SESSION["administrator"])){
		/*echo nothing*/
	}//if
	elseif(!isset($_SESSION["administrator"])){
		echo "\n\t\t<div id=\"conferenceInfo\">";
			echo "\n\t\t\t<div id=\"conferenceTitle\">";
			echo "\n\t\t\t " . "<a href=\"./conference_info.php?confid=" . $_SESSION["conf_id"] . "\" title=\"Conference Info.\">" . $_SESSION["conf_name"] . "</a>";
			echo "\n\t\t\t</div>";//conferecnceTitle
			echo "\n\t\t\t<div id=\"conference_search_form\" title=\"Change Conference.\">";
			echo "\n\t\t\t\t<form id=\"fconfform\" name=\"fconfform\" method=\"post\" action=\"./include/functionsinc.php?type=9&action=user_login\">\n";
				conf_combo_box();
			echo "<input type=\"hidden\" name=\"csrf\" id=\"csrf\" value=\"" . $csrf_password_generator . "\" />";
			echo "\t\t<input type=\"submit\" value=\"GO\" id=\"cc_submit\">";
			echo "</form>";
			echo "\n\t\t\t</div>";//conference_search_form
		echo "\n\t\t</div>";//comferenceInfo
	}//elseif
}//layout_conf_search_form()

?>