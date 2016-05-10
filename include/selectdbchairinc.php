<?php
##################################################
################selectdbchairinc.php############
##################################################
/*
This file includes all the functions that include
select queries to the DataBase, that refer to chairmen.
This file doesn't include some functions that are common to
all the users of the system, (these are included in the
selectdbcommoninc.php file).
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
load_assigned_papers(),
show_candidate_reviewers_of_paper($paper_id),
display_all_papers_ch(),
display_all_abstracts_ch(),
display_reviews_ch(),
find_conference(),
display_incomplete_reviewers(),
display_papers_and_reviews($order_by)
*/

//loads all the conference papers as well as the reviewers that are assigned to them.
//$conf_id: the id of the selected conference that has the papers.
//used for view_assignments.php
function load_assigned_papers()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"papers_and_reviewers\">";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"papers\">Paper</th>
		<th scope=\"col\" class=\"reviewers\">Reviewers</th>
		<th scope=\"col\" class=\"edit\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	$papers = array();
	$paper_to_reviewers = array();

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("load_assigned_papers()","selectdbchairinc.php",48,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("load_assigned_papers()","selectdbchairinc.php",49,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT id, user_id, title, authors, subject "
			 . "FROM paper "
			 . "WHERE conference_id = '" . $conference_id . "' ORDER BY title ASC; ";
	$result01 = @mysql_query($query01) or dbErrorHandler("load_assigned_papers()","selectdbchairinc.php",55,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//echo "\n<div>There are no papers for this conference.</div>";
	}//if
	else
	{
		//fill the array with values of all the papers of that conference
		for($i=0; $i<$num01; $i++)
		{
			$papers[$i]["id"] = mysql_result($result01,$i,"id");
			$papers[$i]["authors"] = mysql_result($result01,$i,"authors");//the authors that wrote this paper
			$papers[$i]["user_id"] = mysql_result($result01,$i,"user_id");//the user that submitted this paper
			$papers[$i]["title"] = mysql_result($result01,$i,"title");
		}//for
	}//else

	//select all the assigned reviewers of this conference
	$query02 = "SELECT papertoreviewer.paper_id, user.id, user.lname, user.fname"
			 . " FROM user, papertoreviewer "
			 . " WHERE user.id = papertoreviewer.user_id ; ";
	$result02 = @mysql_query($query02) or dbErrorHandler("load_assigned_papers()","selectdbchairinc.php",78,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	//fill the array $paper_to_reviewers
	for($i=0; $i<$num02; $i++)
	{
		//ALL OK
		$paper_id = mysql_result($result02,$i,"paper_id");
		$user_id = mysql_result($result02,$i,"id");

		$paper_to_reviewers[$paper_id][$user_id]["lname"] = mysql_result($result02,$i,"lname");
		$paper_to_reviewers[$paper_id][$user_id]["fname"] = mysql_result($result02,$i,"fname");
	}//for

	//combine the data from arrays $papers, $paper_to_reviewers to $array!!
	if(isset($papers)){reset($papers);}
	if(isset($paper_to_reviewers)){reset($paper_to_reviewers);}

	for($key2=0; $key2<count($papers); $key2++)
	{
		$array[$key2]["paper_id"] = $papers[$key2]["id"];
		$array[$key2]["paper_title"] = $papers[$key2]["title"];
		$array[$key2]["paper_authors"] = $papers[$key2]["authors"];//the authors that wrote this paper
		$array[$key2]["paper_user_id"] = $papers[$key2]["user_id"] ;//the user that submitted this paper

		reset($paper_to_reviewers);
		while (list($key, $val) = each ($paper_to_reviewers))
		{
			if ($key == $papers[$key2]["id"])
			{
				//echo $key . "<br>";
				while (list($key3, $val3) = each ($paper_to_reviewers[$key]))
				{
					$array[$key2][$key3]["reviewer_lname"] = $paper_to_reviewers[$key][$key3]["lname"];
					$array[$key2][$key3]["reviewer_fname"] = $paper_to_reviewers[$key][$key3]["fname"];
				}//inner while
			}//if
		}//outer while
	}//
	//print the $array
	//print_r($array) . "<br>";

	echo $str_01;
	echo $str_03;
	echo $str_04;

	if(count($array)==0)
	{
		echo "<tr><td colspan=\"3\" align=\"center\">There are no papers for this conference</td></tr>";
	}//if
	else
	{
		reset($array);

		for($i=0; $i<count($array); $i++)
		{
			//echo "paper title: " . $array[$i]["paper_title"] . ": ";
			if (($i%2) == 0) {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			} else {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			}//else

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

			echo "\n\t\t\t<td class=\"paper_title\"><a href=\"paper_info.php?paperid=" . $array[$i]["paper_id"] . "\" title=\"Click for paper info.\">" . $array[$i]["paper_title"] . "</a></td>";
			echo "\n\t\t\t<td class=\"reviewers_list\">";

			echo "<ul>";
			if(count($array[$i])!=4)
			{
				while (list($key, $val) = each ($array[$i]))
				{
					if($key == "paper_id" || $key == "paper_title" || $key == "paper_authors" || $key == "paper_user_id"){continue;}
					else
					{
						echo "<li>";
						echo "<a href=\"user_info.php?userid=" . $key . "\" title=\"Click for user info.\">";
						echo $array[$i][$key]["reviewer_lname"] . " " . $array[$i][$key]["reviewer_fname"];
						echo "</a>";
						echo "<li>";
						$update_reviewer_assignment = "yes";
					}//else
				}//while
			}//if
			else if(count($array[$i])==4)
			{
				echo "-";
				$update_reviewer_assignment = "no";
			}//else if

			echo "</ul>";
			echo "</td>";

			//output the edit button
			if( $update_reviewer_assignment == "no" ){
				echo "<td class=\"edit_button\"><a href=\"./include/functionsinc.php?type=52&paperid=" . $array[$i]["paper_id"] . "\" title=\"Click to edit assignment.\" class=\"simple\">edit</a></td>";
			}//no
			else{
				echo "<td class=\"edit_button\"><a href=\"./include/functionsinc.php?type=53&paperid=" . $array[$i]["paper_id"] . "\" title=\"Click to edit assignment.\" class=\"simple\">edit</a></td>";
			}//yes
			echo "<tr>\n";
			//echo "<br>";
		}//for
	}//else


	echo $str_05;
	echo $str_06;

	@mysql_close();//closes the connection to the DB
}//load_assigned_papers()

##################################
##################################

//used for the assign_reviewers.php page to load the reviewers who want to review tha paper with id $paper_id
function show_candidate_reviewers_of_paper($paper_id)
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"candidate_reviewers\">";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"\" title=\"Click for Reviewers Info.\">reviewer</th>
		<th scope=\"col\" class=\"\" title=\"Conflict that the author has with this reviewer.\">conflict by author</th>
		<th scope=\"col\" class=\"\" title=\"Level of interest of the reviewer to review this paper.\">interest</th>
		<th scope=\"col\" class=\"checkbox\"></th>
		</tr>\n</thead>";
			//<th scope=\"col\" class=\"\" title=\"Conflict that the reviewer has with one of the author(s) of this paper.\">conflict by reviewer</th>
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	$user_ar = array ();
	$paper_ar = array();
	$paper_to_reviewer = array();
	$checkbox_status = "";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("show_candidate_reviewers_of_paper()","selectdbchairinc.php",219,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("show_candidate_reviewers_of_paper()","selectdbchairinc.php",220,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//get all the papers
	$query01 = "SELECT id, user_id, title, authors, subject "
			 . "FROM paper "
			 . "WHERE id='" . $paper_id . "'; ";
	$result01 = @mysql_query($query01) or dbErrorHandler("show_candidate_reviewers_of_paper()","selectdbchairinc.php",227,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//no results
	}//if
	else
	{
		//fill the array with values of all the papers of that conference
		for($i=0; $i<$num01; $i++)
		{
			//ALL OK
			$paper_ar["id"] = mysql_result($result01,$i,"id");
			$paper_ar["user_id"] = mysql_result($result01,$i,"user_id");
			$paper_ar["title"] = mysql_result($result01,$i,"title");
			$paper_ar["authors"] = mysql_result($result01,$i,"authors");
			$paper_ar["subject"] = mysql_result($result01,$i,"subject");
		}//for
	}//else

	//get all the users and their interests for each paper
	$query02 = "SELECT user.id, user.fname, user.lname, interest.level_of_interest, interest.conflict, interest.conflict_by_author "
				. "FROM user, usertype, interest "
				. "WHERE user.id = usertype.user_id AND usertype.user_id = interest.user_id "
					. "AND usertype.type = 'reviewer' "
					. "AND usertype.conference_id='" . $_SESSION["conf_id"] . "' "
					. "AND interest.paper_id='" . $paper_id . "' ORDER BY interest.level_of_interest DESC; ";
	$result02 = @mysql_query($query02) or dbErrorHandler("show_candidate_reviewers_of_paper()","selectdbchairinc.php",255,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//no results
	}
	else
	{
		//fill the array $user_ar
		for($j=0; $j<$num02; $j++)
		{
			if (mysql_result($result02,$j,"conflict") == "1"){continue;}
			else
			{
				$user_ar[$j]["id"] = mysql_result($result02,$j,"id");
				$user_ar[$j]["fname"] = mysql_result($result02,$j,"fname");
				$user_ar[$j]["lname"] = mysql_result($result02,$j,"lname");
				$user_ar[$j]["level_of_interest"] = mysql_result($result02,$j,"level_of_interest");

				if (mysql_result($result02,$j,"conflict") == "1")
				{
					$user_ar[$j]["conflict"] = "yes";
				}//if
				else if (mysql_result($result02,$j,"conflict") == "0")
				{
					$user_ar[$j]["conflict"] = "no";
				}//if

				if(mysql_result($result02,$j,"conflict_by_author") == "")
				{
					$user_ar[$j]["conflict_by_author"] = "-";
				}else
				{
					if (mysql_result($result02,$j,"conflict_by_author") == "1")
					{
						$user_ar[$j]["conflict_by_author"] = "yes";
					}//if
					else if (mysql_result($result02,$j,"conflict_by_author") == "0")
					{
						$user_ar[$j]["conflict_by_author"] = "no";
					}//if
				}//
			}
		}//for
	}//else

	$query03 = "SELECT user_id "
				. "FROM papertoreviewer "
				. "WHERE conference_id='" . $_SESSION["conf_id"] . "' AND paper_id='" . $paper_id . "'; ";
	$result03 = @mysql_query($query03) or dbErrorHandler("show_candidate_reviewers_of_paper()","selectdbchairinc.php",305,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$num03 = @mysql_num_rows($result03);//num

	if($num03 == 0)
	{
		//no results
	}//if
	else
	{
		//fill the array $paper_to_reviewer
		for($z=0; $z<$num03; $z++)
		{
			$paper_to_reviewer[$z]["user_id"] = mysql_result($result03,$z,"user_id");
		}//for
	}//else

	//print the contents of the array
	$string_to_echo = $str_01;
	$string_to_echo = $string_to_echo . $str_03;
	$string_to_echo = $string_to_echo . $str_04;

	reset($user_ar);

	if(count($user_ar) == 0)
	{
		$string_to_echo = $string_to_echo .  "<tr>" . "<td colspan=\"3\">" . "No reviewer has entered his interest levels and conflicts for this paper" . "</td>" . "</tr>";
	}
	else
	{
		while (list($t, $val) = each($user_ar))
		{
			if (($t%2) == 0) {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			} else {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			}//else

			$string_to_echo = $string_to_echo .   "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

			$string_to_echo = $string_to_echo . "\n\t\t\t<td class=\"reviewer\"><a href=\"user_info.php?userid=" . $user_ar[$t]["id"] . "\" title=\"Click for user info.\">" . $user_ar[$t]["lname"] . " " . $user_ar[$t]["fname"] . "</a></td>";
			$string_to_echo = $string_to_echo . "\n\t\t\t<td class=\"conflict\">" . $user_ar[$t]["conflict"] . "</td>";
			//$string_to_echo = $string_to_echo . "\n\t\t\t<td class=\"conflict_by_author\">" . $user_ar[$t]["conflict_by_author"] . "</td>";
			$string_to_echo = $string_to_echo . "\n\t\t\t<td class=\"level_of_interest\">" . $user_ar[$t]["level_of_interest"] . "</td>";
			for($z=0; $z<count($paper_to_reviewer); $z++)
			{
				if($paper_to_reviewer[$z]["user_id"] == $user_ar[$t]["id"])
				{
					$checkbox_status = "checked";
					break;
				}//if
				else
				{
					$checkbox_status = "";
					continue;
				}//else
			}//for
			$string_to_echo = $string_to_echo .  "\n\t\t\t<td class=\"\">" . "<input type=\"checkbox\" id=\"" . $user_ar[$t]["id"] . "\" name=\"" . $user_ar[$t]["id"] . "\" " . $checkbox_status . " >" . "</td>";
			$string_to_echo = $string_to_echo .  "\n\t\t<tr>";
		}//for
	}//if

	$string_to_echo = $string_to_echo . $str_05;
	$string_to_echo = $string_to_echo . $str_06;

	@mysql_close();//closes the connection to the DB

	$_SESSION["temp_paper_title"] = $paper_ar["title"];
	return ($string_to_echo);

}//show_candidate_reviewers_of_paper($paper_id)

##################################
##################################

//loads all the conference papers as well as the reviewers that are assigned to them.
//$conf_id: the id of the selected conference that has the papers.
//used for assign_reviewersf.php
function display_reviewers_for_papers()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];

	global $coptions2D; //so that we can use any of the conference_options' variables

	$conf_candidate_reviewers = array();

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"papers_and_reviewers\">";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"papers\">Paper</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"reviewers\">Reviewers</th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_reviewers_for_papers()","selectdbchairinc.php",405,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_reviewers_for_papers()","selectdbchairinc.php",406,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT id, user_id, title, authors, subject "
			 . "FROM paper "
			 . "WHERE conference_id = '" . $conference_id . "' ORDER BY title ASC; ";
	$result01 = @mysql_query($query01) or dbErrorHandler("display_reviewers_for_papers()","selectdbchairinc.php",412,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//echo "\n<div>There are no papers for this conference.</div>";
	}//if
	else
	{
		//fill the array with values of all the papers of that conference
		for($i=0; $i<$num01; $i++)
		{
			$papers[$i]["id"] = mysql_result($result01,$i,"id");
			$papers[$i]["authors"] = mysql_result($result01,$i,"authors");//the authors that wrote this paper
			$papers[$i]["user_id"] = mysql_result($result01,$i,"user_id");//the user that submitted this paper
			$papers[$i]["title"] = mysql_result($result01,$i,"title");
		}//for
	}//else

	$query02 = "SELECT papertoreviewer.paper_id, user.id, user.lname, user.fname"
			 . " FROM user, papertoreviewer "
			 . " WHERE user.id = papertoreviewer.user_id AND conference_id='" . $conference_id . "';";
	$result02 = @mysql_query($query02) or dbErrorHandler("display_reviewers_for_papers()","selectdbchairinc.php",434,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	//fill the array $paper_to_reviewers
	for($i=0; $i<$num02; $i++)
	{
		//ALL OK
		$paper_id = mysql_result($result02,$i,"paper_id");
		$user_id = mysql_result($result02,$i,"id");

		$paper_to_reviewers[$paper_id][$user_id]["lname"] = mysql_result($result02,$i,"lname");
		$paper_to_reviewers[$paper_id][$user_id]["fname"] = mysql_result($result02,$i,"fname");
	}//for


	//get how many papers have been assigned to each reviewer for this paper
	$query04 = "SELECT user_id, count(*) AS count FROM papertoreviewer WHERE conference_id='" . $conference_id . "' GROUP BY user_id;";
	$result04 = @mysql_query($query04) or dbErrorHandler("display_reviewers_for_papers()","selectdbchairinc.php",451,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query04);
	$num04 = @mysql_num_rows($result04);//num03

	for($i=0; $i<$num04; $i++)
	{
		$reviewer_id = mysql_result($result04,$i,"user_id");
		$arp[$reviewer_id]["assigned_papers_no"] = mysql_result($result04,$i,"count");
	}//for


	//get all the candidate reviewers of this confernece, sorted by level of interest
	$query03 = "SELECT user.id, user.fname, user.lname, interest.paper_id, interest.level_of_interest, interest.conflict, interest.conflict_by_author "
				. "FROM user, usertype, interest "
				. "WHERE user.id = usertype.user_id AND usertype.user_id = interest.user_id "
					. "AND usertype.type = 'reviewer' "
					. "AND usertype.conference_id='" . $conference_id . "' ORDER BY interest.level_of_interest DESC;";
	$result03 = @mysql_query($query03) or dbErrorHandler("create_conference","selectdbchairinc.php",467,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$num03 = @mysql_num_rows($result03);//num03

	//fill the array $paper_to_reviewers
	for($i=0; $i<$num03; $i++)
	{
		//we want to exclude the reviewers that have conflicts with the author(s) of the paper.
		if(mysql_result($result03,$i,"conflict") == 1){ continue; }

		$paper_id = mysql_result($result03,$i,"paper_id");
		$reviewer_id = mysql_result($result03,$i,"id");

		$conf_candidate_reviewers[$paper_id][$reviewer_id]["lname"] = mysql_result($result03,$i,"lname");
		$conf_candidate_reviewers[$paper_id][$reviewer_id]["fname"] = mysql_result($result03,$i,"fname");
		$conf_candidate_reviewers[$paper_id][$reviewer_id]["level_of_interest"] = mysql_result($result03,$i,"level_of_interest");

		if(mysql_result($result03,$i,"conflict_by_author") == 0) {$conf_candidate_reviewers[$paper_id][$reviewer_id]["conflict_by_author"] = "NO";}
		elseif(mysql_result($result03,$i,"conflict_by_author") == 1){$conf_candidate_reviewers[$paper_id][$reviewer_id]["conflict_by_author"] = "YES";}

		if(!isset($arp[$reviewer_id]["assigned_papers_no"])){ $conf_candidate_reviewers[$paper_id][$reviewer_id]["assigned_papers_no"] = 0; }
		else{ $conf_candidate_reviewers[$paper_id][$reviewer_id]["assigned_papers_no"] = $arp[$reviewer_id]["assigned_papers_no"]; }

	}//for

	//combine the data from arrays $papers, $paper_to_reviewers, $arp to one array named '$array'
	if( (count($papers) !=0) && (count($paper_to_reviewers) !=0) )
	{
		reset($papers);
		reset($paper_to_reviewers);

		while (list($key, $val) = each ($paper_to_reviewers))
		{
			for($key2=0; $key2<count($papers); $key2++)
			{
				if ($key == $papers[$key2]["id"])
				{
					$array[$key2]["paper_id"] = $papers[$key2]["id"];
					$array[$key2]["paper_title"] = $papers[$key2]["title"];
					$array[$key2]["paper_authors"] = $papers[$key2]["authors"];//the authors that wrote this paper
					$array[$key2]["paper_user_id"] = $papers[$key2]["user_id"] ;//the user that submitted this paper
					while (list($key3, $val3) = each ($paper_to_reviewers[$key]))
					{
						$array[$key2][$key3]["reviewer_lname"] = $paper_to_reviewers[$key][$key3]["lname"];
						$array[$key2][$key3]["reviewer_fname"] = $paper_to_reviewers[$key][$key3]["fname"];
					}//while
				}//if
				else
				{
					$array[$key2]["paper_id"] = $papers[$key2]["id"];
					$array[$key2]["paper_title"] = $papers[$key2]["title"];
					$array[$key2]["paper_authors"] = $papers[$key2]["authors"];//the authors that wrote this paper
					$array[$key2]["paper_user_id"] = $papers[$key2]["user_id"] ;//the user that submitted this paper
					//this case means that there are no reviewers assigned for this paper.
				}//else
			}//for
		}//while
	}//if( (count($papers) !=0) && (count($paper_to_reviewers) !=0) )
	else
	{	//this case is for when there are no reviewers assigned to any papers
		for($key2=0; $key2<count($papers); $key2++)
		{
			$array[$key2]["paper_id"] = $papers[$key2]["id"];
			$array[$key2]["paper_title"] = $papers[$key2]["title"];
			$array[$key2]["paper_authors"] = $papers[$key2]["authors"];//the authors that wrote this paper
			$array[$key2]["paper_user_id"] = $papers[$key2]["user_id"] ;//the user that submitted this paper
			//this case means that there are no reviewers assigned for this paper.
		}//for
	}//else

	#########################################
	#########################################

	//print the $array
	//print_r($array) . "<br>";
	echo $str_01;
	echo $str_03;
	echo $str_04;

	if(count($array) ==0)
	{
		echo "<tr><td colspan=\"3\" align=\"center\">There are no papers for this conference.</tr>";
		$temp_value = 0;
	}
	else
	{
		reset($array);
		for($i=0; $i<count($array); $i++)
		{
			//echo "paper title: " . $array[$i]["paper_title"] . ": ";
			if (($i%2) == 0) {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			} else {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			}//else

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

			echo "\n\t\t\t<td class=\"paper_title\"><a href=\"paper_info.php?paperid=" . $array[$i]["paper_id"] . "\" title=\"Click for paper info.\">" . $array[$i]["paper_title"] . "</a></td>";

			$array[$i]["paper_authors"] = $array[$i]["paper_authors"] . " ";

			$authors = explode(", ", $array[$i]["paper_authors"]);
			unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it

			echo "\n\t\t\t<td class=\"paper_authors\">";
				echo "\n\t\t\t\t<ul>";
				for($k=0; $k<count($authors); $k++)
				{
					echo "<li>" . $authors[$k] . "</li>";
				}//for
				echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";

			//echo "\n\t\t\t<td class=\"paper_authors\">" . $array[$i]["paper_authors"] . "</td>";

			echo "\n\t\t\t<td class=\"reviewers_list\">";

			$combo_box_no=0;

			if(count($array[$i])!=4)
			{
				while (list($key0, $val0) = each ($array[$i]))
				{
					if($key0 == "paper_id" || $key0 == "paper_title" || $key0 == "paper_authors" || $key0 == "paper_user_id"){continue;}
					else
					{
						//echo "<a href=\"user_info.php?userid=" . $key . "\" title=\"Click for user info.\">";
						//echo $array[$i][$key]["reviewer_lname"] . " " . $array[$i][$key]["reviewer_fname"];
						//echo "</a>";
						reset($conf_candidate_reviewers);
						$combo_box_no++;
							while (list($key, $val) = each ($conf_candidate_reviewers))
							{
								if($array[$i]["paper_id"] == $key)
								{
									//echo "<br>" . count($conf_candidate_reviewers[$key]);
									echo "\n\t<select name=\"" . $key . "-" . $combo_box_no . "\" id=\"\" style=\"width:280px\">";
										//print the reviewer who is already assigned to this paper, and then a blank option.
										echo "\n\t\t<option value=\"" . $key0 . "\" >";//reviewer_id
										echo "<b>" . $conf_candidate_reviewers[$key][$key0]["level_of_interest"] . "</b> - ";
										echo strtoupper($conf_candidate_reviewers[$key][$key0]["lname"]) . " " . strtoupper($conf_candidate_reviewers[$key][$key0]["fname"]) . " - ";
										echo "<b>" . "(" . $conf_candidate_reviewers[$key][$key0]["assigned_papers_no"] . ")" . "</b> - ";
										echo "<b>" . $conf_candidate_reviewers[$key][$key0]["conflict_by_author"] . "</b>";
										echo "</option>";
										echo "\n\t\t<option value=\"\"></option>";
										//unset($conf_candidate_reviewers[$key][$key0]);

									while (list($key2, $val2) = each ($conf_candidate_reviewers[$key]))
									{
										echo "\n\t\t<option value=\"" .$key2 . "\" >";//reviewer_id
										echo "<b>" . $conf_candidate_reviewers[$key][$key2]["level_of_interest"] . "</b> - ";
										echo strtoupper($conf_candidate_reviewers[$key][$key2]["lname"]) . " " . strtoupper($conf_candidate_reviewers[$key][$key2]["fname"]) . " - ";
										echo "<b>" . "(" . $conf_candidate_reviewers[$key][$key2]["assigned_papers_no"] . ")" . "</b> - ";
										echo "<b>" . $conf_candidate_reviewers[$key][$key2]["conflict_by_author"] . "</b>";
										echo "</option>";
									}//inner while
									echo "\n\t</select>\n";
								}//if
							}//outer while
						//candidate_paper_reviewers_combo_box($array[$i]["paper_id"],$key,$combo_box_no);
						$update_reviewer_assignment = "yes";
					}//else
				}//while

				$num_of_unassigned_reviewers = ($coptions2D["chairman"]["NORPC"] - $combo_box_no);

				for($r=0; $r<$num_of_unassigned_reviewers; $r++)
				{
					reset($conf_candidate_reviewers);
					$combo_box_no++;
					while (list($key, $val) = each ($conf_candidate_reviewers))
					{
						if($array[$i]["paper_id"] == $key)
						{
							//echo "<br>" . count($conf_candidate_reviewers[$key]);
							echo "\n\t<select name=\"" . $key . "-" . $combo_box_no . "\" id=\"\" style=\"width:280px\">";
							echo "\n\t\t<option value=\"\">[Interest - Name - (No of Papers) - Author Conflict]</option>";
							while (list($key2, $val2) = each ($conf_candidate_reviewers[$key]))
							{
								echo "\n\t\t<option value=\"" . $key2 . "\" >";//reviewer_id
								echo "<b>" . $conf_candidate_reviewers[$key][$key2]["level_of_interest"] . "</b> - ";
								echo strtoupper($conf_candidate_reviewers[$key][$key2]["lname"]) . " " . strtoupper($conf_candidate_reviewers[$key][$key2]["fname"]) . " - ";
								echo "<b>" . "(" . $conf_candidate_reviewers[$key][$key2]["assigned_papers_no"] . ")" . "</b> - ";
								echo "<b>" . $conf_candidate_reviewers[$key][$key2]["conflict_by_author"] . "</b>";
								echo "</option>";
							}//inner while
							echo "\n\t</select>\n";
						}//if
					}//outer while
				}//for
			}//if
			else if(count($array[$i])==4)
			{
				//no reviewers assigned
				$combo_box_no=0;
				//$coptions2D["chairman"]["NORPC"] is the number of reviewers for each paper (allowed in this conference)
				for($h=0; $h<$coptions2D["chairman"]["NORPC"]; $h++)
				{
					reset($conf_candidate_reviewers);
					$combo_box_no++;
					while (list($key, $val) = each ($conf_candidate_reviewers))
					{
						if($array[$i]["paper_id"] == $key)
						{
							//echo "<br>" . count($conf_candidate_reviewers[$key]);
							echo "\n\t<select name=\"" . $key . "-" . $combo_box_no . "\" id=\"\" style=\"width:280px\">";
							echo "\n\t\t<option value=\"\">[Interest - Name - (No of Papers) - Author Conflict]</option>";
							while (list($key2, $val2) = each ($conf_candidate_reviewers[$key]))
							{
								echo "\n\t\t<option value=\"" . $key2 . "\" >";//reviewer_id
								echo "<b>" . $conf_candidate_reviewers[$key][$key2]["level_of_interest"] . "</b> - ";
								echo strtoupper($conf_candidate_reviewers[$key][$key2]["lname"]) . " " . strtoupper($conf_candidate_reviewers[$key][$key2]["fname"]) . " - ";
								echo "<b>" . "(" . $conf_candidate_reviewers[$key][$key2]["assigned_papers_no"] . ")" . "</b> - ";
								echo "<b>" . $conf_candidate_reviewers[$key][$key2]["conflict_by_author"] . "</b>";
								echo "</option>";
							}//inner while
							echo "\n\t</select>\n";
						}//if
					}//outer while
					//candidate_paper_reviewers_combo_box($array[$i]["paper_id"],"",$combo_box_no);
				}
				//echo "-";
			}//else if
			echo "</td>";

			//output the edit button
			//echo "<td class=\"edit_button\"><a href=\"./include/functionsinc.php?type=36&paperid=" . $array[$i]["paper_id"] . "&update_reviewer_assignment=" . $update_reviewer_assignment . "\" title=\"Click to edit assignment.\">.::edit::.</a></td>";
			echo "<tr>\n";
			//echo "<br>";
		}//
		$temp_value = 1;
	}//else

	echo $str_05;
	echo $str_06;

	return $temp_value;

	@mysql_close();//closes the connection to the DB
}//display_reviewers_for_papers()

##################################
##################################

function display_all_papers_ch()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];
	$papers = array();
	$paper_id = "";

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"conference_papers\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"view\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_all_papers_ch()","selectdbchairinc.php",731,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_all_papers_ch()","selectdbchairinc.php",732,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Get all the papers of this conference
	$query01 = "SELECT id, title, authors, status_code "
				. "FROM paper "
				. "WHERE conference_id = '" . $conference_id . "' ORDER BY (status_code) DESC;";

	$result01 = @mysql_query($query01) or dbErrorHandler("display_all_papers_ch()","selectdbchairinc.php",740,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//no papers have been assigned for him to review
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			$paper_id = mysql_result($result01,$i,"id");
			$papers[$paper_id]["title"] = mysql_result($result01,$i,"title");
			$papers[$paper_id]["authors"] = mysql_result($result01,$i,"authors");
			$papers[$paper_id]["status_code"] = mysql_result($result01,$i,"status_code");
		}//for
	}//else

	if(count($papers[$paper_id]) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"3\" align=\"center\">No papers submitted for this conference.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($papers[$paper_id]) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the array
		reset($papers);

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

			//accepted papers have red titles
			if($papers[$key01]["status_code"] == 1){echo "\n\t\t\t<td class=\"name\">" . "<span class=\"red\">" . $papers[$key01]["title"] . "</span>" . "</td>";}
			else{echo "\n\t\t\t<td class=\"name\">" . $papers[$key01]["title"] . "</td>";}

			$papers[$key01]["authors"] = $papers[$key01]["authors"] . " ";
			$authors = explode(", ", $papers[$key01]["authors"]);
			unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it

			echo "\n\t\t\t<td class=\"authors\">";
				echo "\n\t\t\t\t<ul>";
				for($k=0; $k<count($authors); $k++)
				{
					echo "<li>" . $authors[$k] . "</li>";
				}//for
				echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";

			echo "\n\t\t\t<td class=\"view\"><a href=\"./paper_info.php?paperid=" . $key01 . " \" class=\"simple\">view</a></td>";

			echo "\n\t\t</tr>";
			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//elseif

	@mysql_close();//closes the connection to the DB
}//display_all_papers_ch

##################################
##################################

function display_all_abstracts_ch()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"conference_abstracts\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"user\">Submitted By</th>
		<th scope=\"col\" class=\"download\">Download</th>
		<th scope=\"col\" class=\"abstract\">Abstract</th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_all_abstracts_ch()","selectdbchairinc.php",843,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_all_abstracts_ch()","selectdbchairinc.php",844,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Get all the papers of this conference
	$query01 = "SELECT paper.id, paper.user_id, paper.title, paper.abstract, paper.authors, paper.status_code, user.fname, user.lname "
				. "FROM paper, user "
				. "WHERE conference_id = '" . $conference_id . "' AND user.id=paper.user_id ORDER BY (paper.status_code) DESC;";

	$result01 = @mysql_query($query01) or dbErrorHandler("display_all_abstracts_ch()","selectdbchairinc.php",854,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//no papers have been assigned for him to review
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			$paper_id = mysql_result($result01,$i,"id");
			$papers[$paper_id]["title"] = mysql_result($result01,$i,"title");
			$papers[$paper_id]["submitted_by_id"] = mysql_result($result01,$i,"user_id");
			$papers[$paper_id]["submitted_by_name"] = mysql_result($result01,$i,"fname") . " " . mysql_result($result01,$i,"lname");
			$papers[$paper_id]["authors"] = mysql_result($result01,$i,"authors");
			$papers[$paper_id]["abstract"] = mysql_result($result01,$i,"abstract");
			$papers[$paper_id]["status_code"] = mysql_result($result01,$i,"status_code");
		}//for
	}//else


	//Get all the papers of this conference
	$query02 = "SELECT paperbody.id AS paperbody_id, paperbody.paper_id, paperbody.filename, paperbody.filesize, paperbody.paper_type "
				. "FROM paper, paperbody "
				. "WHERE paper.id = paperbody.paper_id AND paper.conference_id = '" . $conference_id . "' ORDER BY (paper_id) ASC;";
	$result02 = @mysql_query($query02) or dbErrorHandler("display_all_abstracts_ch()","selectdbchairinc.php",881,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//no paper bodies uploaded yet
	}//if
	else
	{
		//fill the array with values
		for($j=0; $j<$num02; $j++)
		{
			$paperbodies[$j]["paperbody_id"] = mysql_result($result02,$j,"paperbody_id");
			$paperbodies[$j]["paper_id"] = mysql_result($result02,$j,"paper_id");
			$paperbodies[$j]["filename"] = mysql_result($result02,$j,"filename");
			$paperbodies[$j]["filesize"] = mysql_result($result02,$j,"filesize");
			$paperbodies[$j]["paper_type"] = mysql_result($result02,$j,"paper_type");
		}//for
	}//else

	if(count($papers) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"6\" align=\"center\">No papers submitted for this conference.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($papers) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the array
		reset($papers);

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

			//accepted papers have red titles
			if($papers[$key01]["status_code"] == 1){echo "\n\t\t\t<td class=\"name\">" . "<span class=\"red\">" . $papers[$key01]["title"] . "</span>" . "</td>";}
			else{echo "\n\t\t\t<td class=\"name\">" . $papers[$key01]["title"] . "</td>";}

			/**/
			$papers[$key01]["authors"] = $papers[$key01]["authors"] . " ";
			$authors = explode(", ", $papers[$key01]["authors"]);
			unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it
			echo "\n\t\t\t<td class=\"authors\">";
				echo "\n\t\t\t\t<ul>";
				for($k=0; $k<count($authors); $k++)
				{
					echo "<li>" . $authors[$k] . "</li>";
				}//for
				echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";
			/**/

			echo "\n\t\t\t<td class=\"user\">" . "<a href=\"./user_info.php?userid=" . $papers[$key01]["submitted_by_id"] . "\">" . $papers[$key01]["submitted_by_name"] . "</a>" . "</td>";

			/**/
			//find if there are is a manuscript and a paper-body for this paper.
			//if there is, echo a 'download' option for it.
			$manuscript = "-";
			$camera_ready = "-";

			for($j=0; $j<count($paperbodies); $j++)
			{
				if($key01 == $paperbodies[$j]["paper_id"])
				{
					if($paperbodies[$j]["paper_type"] == "manuscript")
					{
						$manuscript = "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paperbodies[$j]["paperbody_id"] . "\" title=\"" . $paperbodies[$j]["filename"] . " (size: " . $paperbodies[$j]["filesize"] . " bytes)\">" . "manuscript" . "</a>";
					}//if
					elseif($paperbodies[$j]["paper_type"] == "camera_ready")
					{
						$camera_ready = "<a href=\"./include/downloadpaperbodyinc.php?pbodyid=" . $paperbodies[$j]["paperbody_id"] . "\" title=\"" . $paperbodies[$j]["filename"] . " (size: " . $paperbodies[$j]["filesize"] . " bytes)\">" . "camera-ready" . "</a>";
					}//elseif
				}//if
			}//for
			echo "\n\t\t\t<td class=\"download\">";
			echo $manuscript . "<br>" . $camera_ready;
			echo "\n\t\t\t</td>";
			/**/

			echo "\n\t\t\t<td class=\"button\"><a onClick=\"toggle_hidden_content('" . "a" . $key01 . "', this, 'abstracts');\" class=\"simple\">view</a></td>";
			echo "\n\t\t</tr>";

			//print the abstract
			echo "\n\t\t<tr>";
				echo "\n\t\t\t<td colspan=\"6\"  title=\"Paper Abstract.\">";
					echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "a" . $key01 . "\">";
						echo "\n\t\t\t<div class=\"a_of_p\">Abstract for: <span class=\"red\">" . $papers[$key01]["title"] . "</span></div>";
						echo  "<div class=\"abst\"><pre>" . $papers[$key01]["abstract"] . "</pre></div>";
					echo "\n\t\t\t\t</div>";
				echo "</td>";
			echo "\n\t\t</tr>";

			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//elseif

	@mysql_close();//closes the connection to the DB
}//display_all_abstracts_ch()

##################################
##################################

function display_reviews_ch()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];
	$user_id = $_SESSION["logged_user_id"];

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"paper_reviews\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper</th>
		<th scope=\"col\" class=\"reviewers\">Reviewers</th>
		<th scope=\"col\" class=\"view\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_reviews_ch()","selectdbchairinc.php",1022,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_reviews_ch()","selectdbchairinc.php",1023,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//show all paper reviews this conference

	//Get all the papers of this conference
	$query01 = "SELECT paper.id, paper.title "
				. "FROM paper "
				. "WHERE conference_id = '" . $conference_id . "' ORDER BY (paper.id) ASC;";
	//Get all the reviewers of this conference and what paper they are assigned to review
	$query02 = "SELECT papertoreviewer.paper_id, user.id, user.fname, user.lname "
				. "FROM user, papertoreviewer "
				. "WHERE user.id=papertoreviewer.user_id AND papertoreviewer.conference_id = '" . $conference_id . "' ORDER BY (papertoreviewer.paper_id) ASC;";

	$result01 = @mysql_query($query01) or dbErrorHandler("display_reviews_ch()","selectdbchairinc.php",1037,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
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

	$result02 = @mysql_query($query02) or dbErrorHandler("display_reviews_ch()","selectdbchairinc.php",1054,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
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
		echo "\n\t\t<tr><td colspan=\"3\" align=\"center\">No results.</td>";
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

			echo "\n\t\t\t<td class=\"reviewers\">";
			echo "\n\t\t\t\t<ul>";
			for($j=0; $j<count($reviewers); $j++)
			{
				if($key01 == $reviewers[$j]["paper_id"])
				{
					//if the user is the administrator or a chairman, make the reviewers name a link
					echo "<li>" . "<a href=\"./user_info.php?userid=" . $reviewers[$j]["reviewer_id"] . "\">" . $reviewers[$j]["reviewer_name"] . "</a>" . "</li>";
					$flag=1;
				}//if
			}//for
			echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";

			//if there are no reviewers don't echo a link to a paper review
			if($flag == 0)
			{
				echo "\n\t\t\t<td class=\"view\">" . "" . "</td>";

			}//if
			elseif($flag == 1)
			{
				echo "\n\t\t\t<td class=\"view\">" . "<a href=\"./paper_reviews_info.php?paperid=" . $key01 . "\" class=\"simple\">view</a>" . "</td>";
			}//elseif
			echo "\n\t\t</tr>";

			$flag=0;
			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//else
	@mysql_close();//closes the connection to the DB
}//display_reviews_ch()

##################################
##################################

//function that is used to load a conference info for the update_conference.php of the chairman
function find_conference()
{

	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];
	if((!isset($_SESSION["chairman"])) ||($_SESSION["chairman"] != TRUE) ){ Redirects(0,"",""); }

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("find_conference()","selectdbchairinc.php",1164,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("find_conference()","selectdbchairinc.php",1165,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query = "SELECT id, name, alias, place, date_conference_held, contact_email, contact_phone, website, comments, "
			. "deadline, abstracts_deadline, manuscripts_deadline, camera_ready_deadline, "
			. "preferencies_deadline, reviews_deadline "
		. " FROM conference WHERE id='" . $conference_id . "';";
	$result = @mysql_query($query) or dbErrorHandler("find_conference()","selectdbchairinc.php",1184,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);

	$row = mysql_fetch_row($result);
	$num = mysql_num_rows($result);//num

	if($num == 0)
	{
		empty_conference_sessions();//empty conference sessions
		$_SESSION["updateconference"] = "no";
	}//if
	else{
		for($i=0; $i<$num; $i++)
		{
			if($conference_id != "")
			{
				$cvalues["conf_id"] = mysql_result($result,$i,"id");
			}//

			//store all the DB values in array $cvalues

			$cvalues["name"] = mysql_result($result,$i,"name");
			$cvalues["alias"] = mysql_result($result,$i,"alias");
			$cvalues["place"] = mysql_result($result,$i,"place");
			$cvalues["date_conference_held"] = mysql_result($result,$i,"date_conference_held");
			$cvalues["contact_email"] = mysql_result($result,$i,"contact_email");
			$cvalues["contact_phone"] = mysql_result($result,$i,"contact_phone");
			$cvalues["website"] = mysql_result($result,$i,"website");
			$cvalues["comments"] = mysql_result($result,$i,"comments");

			$cvalues["deadline"] = mysql_result($result,$i,"deadline");
			$cvalues["abstracts_deadline"] = @mysql_result($result,$i,"abstracts_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["manuscripts_deadline"] = @mysql_result($result,$i,"manuscripts_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["camera_ready_deadline"] = @mysql_result($result,$i,"camera_ready_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["preferencies_deadline"] = @mysql_result($result,$i,"preferencies_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)
			$cvalues["reviews_deadline"] = @mysql_result($result,$i,"reviews_deadline"); //deadline in YEAR-MONTH-DAY format (YYYY-MM-DD)

			//convert all NULL of the $uvalues to '-'
			$cvalues = convert_ar_vals($cvalues, "NULL", "");

			//save all values of $uvalues to sessions of the same name
			reset ($cvalues);
			while(list($key, $val) = each ($cvalues))
			{
				$_SESSION[$key] = strtolower($val);
			}//while

			### for all the different conference deadlines, load the appropriate deadline values to sessions
			//for conference deadline
			load_conference_dates_to_sessions($cvalues["deadline"], "deadline");
			//for papers deadline
			load_conference_dates_to_sessions($cvalues["abstracts_deadline"], "abstracts_deadline");
			//for manuscripts deadline
			load_conference_dates_to_sessions($cvalues["manuscripts_deadline"], "manuscripts_deadline");
			//for camera-ready deadline
			load_conference_dates_to_sessions($cvalues["camera_ready_deadline"], "camera_ready_deadline");
			//for preferencies deadline
			load_conference_dates_to_sessions($cvalues["preferencies_deadline"], "preferencies_deadline");
			//find reviews deadline
			load_conference_dates_to_sessions($cvalues["reviews_deadline"], "reviews_deadline");

			$_SESSION["updateconference"] = "yes";

		}//for
	}//else

	@mysql_close();//closes the connection to the DB

	//we want a different page to redirect depending on the $conference_id value
	Redirects(10,"","");
}//find_conference()

##################################
##################################

//function that returns a table with all the reviews that are not completed
function display_incomplete_reviewers()
{

	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$papers_to_reviewers = array();
	$paper_titles = array();
	$active_reviewers = array();

	$conference_id = $_SESSION["conf_id"];
	$user_id = $_SESSION["logged_user_id"];

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"paper_reviews\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper</th>
		<th scope=\"col\" class=\"reviewers\">Reviewers</th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_incomplete_reviewers()","selectdbchairinc.php",1287,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_incomplete_reviewers()","selectdbchairinc.php",1288,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//get all the papers
	$query00 = "SELECT id AS paper_id, title AS paper_title "
									. " FROM paper "
									. " WHERE conference_id = '" . $conference_id . "' ORDER BY paper_id ASC;";

	//get all the papers that have reviewers assigned to them
	$query01 = "SELECT papertoreviewer.paper_id, paper.title AS paper_title, papertoreviewer.user_id AS reviewer_id, user.fname AS reviewer_fname, user.lname AS reviewer_lname"
									. " FROM user, paper, papertoreviewer "
									. " WHERE paper.id = papertoreviewer.paper_id AND user.id = papertoreviewer.user_id  AND papertoreviewer.conference_id='" . $conference_id . "' ORDER BY paper_id ASC ;";

	//get all the papers that have been reviewed
	$query02 = "SELECT id, paper_id, user_id AS reviewer_id "
									. " FROM review WHERE conference_id='" . $conference_id . "' ORDER BY paper_id ASC;";

	$result00 = @mysql_query($query00) or dbErrorHandler("display_incomplete_reviewers()","selectdbchairinc.php",1305,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query00);
	$num00 = @mysql_num_rows($result00);//num

	if($num00 == 0)
	{
		//no papers submitted for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num00; $i++)
		{
			$paper_id = mysql_result($result00,$i,"paper_id");

			$paper_titles[$paper_id] = mysql_result($result00,$i,"paper_title");

			$papers_to_reviewers[$paper_id] = array ();
		}//for
	}//else

	$result01 = @mysql_query($query01) or dbErrorHandler("display_incomplete_reviewers()","selectdbchairinc.php",1325,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
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
			$paper_id = mysql_result($result01,$i,"paper_id");
			$reviewer_id = mysql_result($result01,$i,"reviewer_id");

			$paper_titles[$paper_id] = mysql_result($result01,$i,"paper_title");

			$papers_to_reviewers[$paper_id][$reviewer_id]["fname"] = mysql_result($result01,$i,"reviewer_fname");
			$papers_to_reviewers[$paper_id][$reviewer_id]["lname"] = mysql_result($result01,$i,"reviewer_lname");
			$papers_to_reviewers[$paper_id][$reviewer_id]["review_status"] = "review pending";
		}//for
	}//else

	$result02 = @mysql_query($query02) or dbErrorHandler("display_incomplete_reviewers()","selectdbchairinc.php",1349,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//no reviewers have submitted reviews for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num02; $i++)
		{
			$paper_id = mysql_result($result02,$i,"paper_id");
			$reviewer_id = mysql_result($result02,$i,"reviewer_id");

			$active_reviewers[$paper_id][$reviewer_id]["review_status"] = "review submitted";
		}//for
	}//else

	//This resets the cursor of the array.
	reset ($papers_to_reviewers);
	reset ($paper_titles);
	reset ($active_reviewers);

	//combine the date of the arrays '$papers_to_reviewers" and "$active_reviewers"
	while (list($key01, $val01) = each ($active_reviewers))
	{
		while (list($key02, $val02) = each ($active_reviewers[$key01]))
		{
			$papers_to_reviewers[$key01][$key02]["review_status"] = $active_reviewers[$paper_id][$reviewer_id]["review_status"];
		}//
	}//

	//print the results
	if(count($paper_titles) == 0 || count($papers_to_reviewers) == 0)
	{
		//no papers have been submitted for this conference

		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"3\" align=\"center\">Currently, no paper have been submitted to this conference.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($paper_titles) != 0 || count($papers_to_reviewers) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the arrays
		reset ($papers_to_reviewers);
		reset ($paper_titles);
		reset ($active_reviewers);

		$count=0;
		while (list($key01, $val01) = each($papers_to_reviewers))
		{
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else


			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
			echo "\n\t\t\t<td class=\"name\"><a href=\"./paper_info.php?paperid=" . $key01 . "\">" . $paper_titles[$key01] . "</a>" . "</td>";

			echo "\n\t\t\t<td class=\"reviewers\">";
			echo "\n\t\t\t\t<ul>";

			while (list($key02, $val02) = each ($papers_to_reviewers[$key01]))
			{
				if($papers_to_reviewers[$key01] == "")
				{
					echo "1";
					continue;
				}
				echo "<li>";
				echo "<a href=\"./user_info.php?userid=" . $key02 . "\">" . $papers_to_reviewers[$key01][$key02]["fname"] . " " . $papers_to_reviewers[$key01][$key02]["lname"] .  "</a>";

				if( $papers_to_reviewers[$key01][$key02]["review_status"] == "review pending" )
				{
					echo "<div class=\"red\">" . " (" . $papers_to_reviewers[$key01][$key02]["review_status"] . ")" . "</div>";
				}//if
				else
				{
					echo " (" . $papers_to_reviewers[$key01][$key02]["review_status"] . ")";
				}//
				echo "</li>";
			}//

			echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";

			echo "\n\t\t</tr>";
			$count++;
		}//while

		echo $str_05;
		echo $str_06;
	}//else

		@mysql_close();//closes the connection to the DB
}//display_incomplete_reviewers()

##################################
##################################

//function that displays all the reviews of the papers, sorted by paper, in order to accept/reject them
//for each conferencehe
//$order_by can have 3 values 0 = "average", 1 = "paper_id", 2 = "discrepancy"
function display_papers_and_reviews($order_by)
{

	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$conference_id = $_SESSION["conf_id"];
	$user_id = $_SESSION["logged_user_id"];

	global $coptions2D; //so that we can use any of the conference_options' variables
	//$coptions2D["chairman"]["NORPC"] are the number of reviewers per paper for this conference

	$paper_titles = array();
	$papers_n_reviewers = array();
	$scores_array = array();
	$tmp_tr_str_01 = "";
	$tmp_tr_str_02 = "";
	$sum = 0;

	for($i=1; $i<=$coptions2D["chairman"]["NORPC"]; $i++)
	{
			$tmp_tr_str_01 .= "<th scope=\"col\" class=\"reviewers\" colspan=\"7\">" . "Reviewer " . $i . "</th>";

			$tmp_tr_str_02 .= "<th scope=\"col\" class=\"Originality\" title=\"originality\">Or</th>"
			. "<th scope=\"col\" class=\"significance\" title=\"significance\">S</th>"
			. "<th scope=\"col\" class=\"quality\" title=\"quality\">Q</th>"
			. "<th scope=\"col\" class=\"relevance\" title=\"relevance\">R</th>"
			. "<th scope=\"col\" class=\"presentation\" title=\"presentation\">P</th>"
			. "<th scope=\"col\" class=\"overall\" title=\"overall\">Ov</th>"
			. "<th scope=\"col\" class=\"expertise\" title=\"expertise\">E</th>";
	}//for


	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"acceptpapers\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n\n\t<tr>"
		//<th scope=\"col\" class=\"rank\" rowspan=\"2\">Rank</th>
		. "<th scope=\"col\" class=\"paper_id\" rowspan=\"2\">Paper ID</th>"
		. $tmp_tr_str_01
		. "<th scope=\"col\" class=\"overall_rating\" colspan=\"4\">Overall Rating</th>"
		. "<th scope=\"col\" class=\"status\" rowspan=\"2\">Status</th>"
		. "</tr>" . "\n"
		. "<tr>"
				. $tmp_tr_str_02
				. "<th scope=\"col\">Min</th><th scope=\"col\">Max</th><th scope=\"col\">Avg</th><th scope=\"col\">WAvg</th>"
		. "</tr>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";


	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("display_papers_and_reviews()","selectdbchairinc.php",1515,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_papers_and_reviews()","selectdbchairinc.php",1516,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//get all the papers
	$query00 = "SELECT id AS paper_id, title AS paper_title, status_code "
					. " FROM paper "
					. " WHERE conference_id = '" . $conference_id . "' ORDER BY paper_id ASC;";

	//get all the papers tha have reviewers assigned to them
	$query01 = "SELECT papertoreviewer.paper_id, paper.title AS paper_title, papertoreviewer.user_id AS reviewer_id, user.fname AS reviewer_fname, user.lname AS reviewer_lname"
					. " FROM user, paper, papertoreviewer "
					. " WHERE paper.id = papertoreviewer.paper_id AND user.id = papertoreviewer.user_id  AND papertoreviewer.conference_id='" . $conference_id . "' ORDER BY paper_id ASC ;";

	//get all the papers that have been reviewed
	$query02 = "SELECT papertoreviewer.paper_id, papertoreviewer.user_id AS reviewer_id, review.referee_name, review.originality, review.significance, review.quality, review.relevance, review.presentation, review.overall, review.expertise, review.confidential, review.contributions, review.positive, review.negative, review.further "
					. " FROM papertoreviewer, review "
					. " WHERE papertoreviewer.conference_id = '" . $conference_id . "' AND papertoreviewer.paper_id = review.paper_id AND papertoreviewer.user_id = review.user_id "
					. " ORDER BY (papertoreviewer.paper_id) ASC";

	//get all the possible acceptance status for this conference that where first defined in "sessioninitinc.php"
	$query10 = "SELECT status_code, status_description "
				. " FROM paperacceptancestatus "
				. " WHERE conference_id = '" . $conference_id . "' ";

	$result00 = @mysql_query($query00) or dbErrorHandler("display_papers_and_reviews()","selectdbchairinc.php",1535,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query00);
	$num00 = @mysql_num_rows($result00);//num

	//Fetch all the papers of this conference
	if($num00 == 0)
	{
		//no papers submitted for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num00; $i++)
		{
			$paper_id = mysql_result($result00,$i,"paper_id");
			$paper_status_code = mysql_result($result00,$i,"status_code");

			$paper_titles[$paper_id]["title"] = mysql_result($result00,$i,"paper_title");
			$paper_titles[$paper_id]["status_code"] = $paper_status_code;

			$papers_n_reviewers[$paper_id] = array ();
		}//for
	}//else

	$result01 = @mysql_query($query01) or dbErrorHandler("display_papers_and_reviews()","selectdbchairinc.php",1558,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	//Fetch all the papers of this conference that have reviewers
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
			$reviewer_id = mysql_result($result01,$i,"reviewer_id");

			//$paper_titles[$paper_id] = mysql_result($result01,$i,"paper_title");

			//$papers_n_reviewers[$paper_id][$reviewer_id]["review_status"] = "";
			$papers_n_reviewers[$paper_id][$reviewer_id] = "";

		}//for
	}//else

	//Fetch all the papers of this conference that have reviewers that have reviewes submitted
	$result02 = @mysql_query($query02) or dbErrorHandler("display_papers_and_reviews()","selectdbchairinc.php",1583,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//no reviewers have submitted reviews for this conference
		return(0);
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num02; $i++)
		{

			$paper_id = mysql_result($result02,$i,"paper_id");
			$reviewer_id = mysql_result($result02,$i,"reviewer_id");

			$referee_name = mysql_result($result02,$i,"referee_name");
			$originality = mysql_result($result02,$i,"originality");
			$significance = mysql_result($result02,$i,"significance");
			$quality = mysql_result($result02,$i,"quality");
			$relevance = mysql_result($result02,$i,"relevance");
			$presentation = mysql_result($result02,$i,"presentation");
			$overall = mysql_result($result02,$i,"overall");
			$expertise = mysql_result($result02,$i,"expertise");
			$confidential = mysql_result($result02,$i,"confidential");
			$contributions = mysql_result($result02,$i,"contributions");
			$positive = mysql_result($result02,$i,"positive");
			$negative = mysql_result($result02,$i,"negative");
			$further = mysql_result($result02,$i,"further");

			$papers_n_reviewers[$paper_id][$reviewer_id]["referee_name"] = $referee_name;
			$papers_n_reviewers[$paper_id][$reviewer_id]["originality"] = $originality;
			$papers_n_reviewers[$paper_id][$reviewer_id]["significance"] = $significance;
			$papers_n_reviewers[$paper_id][$reviewer_id]["quality"] = $quality;
			$papers_n_reviewers[$paper_id][$reviewer_id]["relevance"] = $relevance;
			$papers_n_reviewers[$paper_id][$reviewer_id]["presentation"] = $presentation;
			$papers_n_reviewers[$paper_id][$reviewer_id]["overall"] = $overall;
			$papers_n_reviewers[$paper_id][$reviewer_id]["expertise"] = $expertise;

			/* //Don't really need these information about each review
 			$papers_n_reviewers[$paper_id][$reviewer_id]["confidential"] = $confidential;
			$papers_n_reviewers[$paper_id][$reviewer_id]["contributions"] = $contributions;
			$papers_n_reviewers[$paper_id][$reviewer_id]["positive"] = $positive;
			$papers_n_reviewers[$paper_id][$reviewer_id]["negative"] = $negative;
			$papers_n_reviewers[$paper_id][$reviewer_id]["further"] = $further;
			*/
		}//for
	}//else



	//This resets the cursor of the array.
	reset ($paper_titles);
	reset ($papers_n_reviewers);

	//find the Average Ratings for each paper and store it into an array
	//we use this for when the user want the papers to be sorted by average rating
	if(count($paper_titles) != 0 || count($papers_n_reviewers) != 0)
	{
		while (list($key01, $val01) = each($papers_n_reviewers))
		{

			$min = $max = 0;
			$sum = 0;
			$loop_no = 0;
			$reviewer_wavg = 0;
			$reviewer_sum_wavg = 0;
			$reviewer_expertise_sum = 0;

			while (list($key02, $val02) = each($papers_n_reviewers[$key01]))
			{

				//for the average
				if($loop_no == 0 )
				{
					if(isset($papers_n_reviewers[$key01][$key02]["overall"]) && ($papers_n_reviewers[$key01][$key02]["overall"] != null) ) {$min = $max = $papers_n_reviewers[$key01][$key02]["overall"];}
				}
				else {
					if($papers_n_reviewers[$key01][$key02]["overall"] < $min && ($papers_n_reviewers[$key01][$key02]["overall"] != null )){$min = $papers_n_reviewers[$key01][$key02]["overall"];}
					if($papers_n_reviewers[$key01][$key02]["overall"] > $max ){$max = $papers_n_reviewers[$key01][$key02]["overall"];}
				}
					$sum += $papers_n_reviewers[$key01][$key02]["overall"]; //sum of all the overall reviews of a paper
					$loop_no++;

				//for the weighted average
				switch($papers_n_reviewers[$key01][$key02]["expertise"])
				{
					case "high":
						$expertise_of_reviewer = 3;
						break;
					case "medium":
						$expertise_of_reviewer = 2;
						break;
					case "low":
						$expertise_of_reviewer = 1;
						break;
					default:
						//do nothing
						break;
				}//switch
				$reviewer_wavg = $expertise_of_reviewer * $papers_n_reviewers[$key01][$key02]["overall"]; //weighted average of the reviewer
				$reviewer_sum_wavg += $reviewer_wavg; //sum of the weighted averages of the reviewers
				$reviewer_expertise_sum += $expertise_of_reviewer; //sum of the expertise of each reviewer

			}//inner while

			if(sizeof($papers_n_reviewers[$key01]) == 0 )
			{
				$temp_avg = 0;  // if there are no reviewers assigned for a paper, then the $temp_avg is 0
				$temp_wavg = 0; // if there are no reviewers assigned for a paper, then the $temp_wavg is 0
			}else
			{
				$temp_avg = $sum / sizeof($papers_n_reviewers[$key01]); // sizeof($papers_n_reviewers[$key01] returns the number of reviewers assigned to this paper

				if($reviewer_expertise_sum != 0) { $temp_wavg = $reviewer_sum_wavg / $reviewer_expertise_sum; } //
			}

			$scores_array[$key01]["min"] = $min;
			$scores_array[$key01]["max"] = $max;
			$scores_array[$key01]["avg"] = round($temp_avg, 2); //keep only 3 digits of the double
			$scores_array[$key01]["wavg"] = round($temp_wavg, 2); //keep only 3 digits of the double
			$scores_array[$key01]["discr"] = abs($min-$max); //discrepancy

		}//outer while
	}//if

	//the result of the following switch depends from the value stored in the $order_by variable.
	switch($order_by)
	{
		case 0: // 0 = "average"
			uasort($scores_array, avgCompare );  //this works!! (reverseCompare is a function in commonfunctionsinc.php)
			//arsort($scores_array); //this works too!
			break;
		case 1:// 1 = "paper_id"
			//do nothing.
			//the array is already sorted by paper_id from the result of the sql queries
			break;
		case 2: // 2 = "discrepancy"
			uasort($scores_array, discrCompare );
			break;
		case 3: // 3 = "weighted average"
			uasort($scores_array, wavgCompare);
			break;
		default:
			//do nothing
			break;
	}//switch



	//Fetch all the possible acceptance status for this conference
	$result10 = @mysql_query($query10) or dbErrorHandler("display_papers_and_reviews()","selectdbchairinc.php",1701,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query10);
	$num10 = @mysql_num_rows($result10);//num

	if($num10 == 0)
	{
		//no papers submitted for this conference
		$acceptancestatus[0] = "rejected";
		$acceptancestatus[1] = "accepted";
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num10; $i++)
		{
			$status_code = mysql_result($result10,$i,"status_code");
			$status_description = mysql_result($result10,$i,"status_description");

			$acceptancestatus[$status_code] = $status_description;
		}//for
	}//else




	//print the results
	if(count($paper_titles) == 0 || count($papers_n_reviewers) == 0)
	{
		//no papers have been submitted for this conference

		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"29\" align=\"center\">Currently there are no reviews for any paper.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($paper_titles) != 0 || count($papers_n_reviewers) != 0)
	{

		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the arrays
		reset ($scores_array);

		$count=0;
		$accepted_papers_count=0;
		while(list($key00, $val00) = each ($scores_array))
		{
			$disc_avg=0;
			$disc_avg=0;
			$disc_max=0;
			$has_weak_accept='n';

			reset ($papers_n_reviewers);
			reset ($paper_titles);
			while (list($key01, $val01) = each($papers_n_reviewers))
			{
				if($key00 == $key01)
				{
					if (($count%2) == 0) {
						$bgColor = "#FFFFFF";
						$trClass = "odd";
					} else {
						$bgColor = "#F5F0EA";
						$trClass = "even";
					}//else

					echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\" align=\"center\">";
					echo "\n\t\t\t<td class=\"\" title=\"" . strtoupper($paper_titles[$key01]["title"]) . "\"><a href=\"./paper_reviews_info.php?paperid=" . $key01  ."\">" . $key01 . "</a></td>";

					$temp_count=0;
					while (list($key02, $val02) = each($papers_n_reviewers[$key01]))
					{
						echo "\n\t\t\t<td>" . 	$papers_n_reviewers[$key01][$key02]["originality"] . "</td>";
						echo "\n\t\t\t<td>" . 	$papers_n_reviewers[$key01][$key02]["significance"] . "</td>";
						echo "\n\t\t\t<td>" . 	$papers_n_reviewers[$key01][$key02]["quality"] . "</td>";
						echo "\n\t\t\t<td>" . 	$papers_n_reviewers[$key01][$key02]["relevance"] . "</td>";
						echo "\n\t\t\t<td>" . 	$papers_n_reviewers[$key01][$key02]["presentation"] . "</td>";
						echo "\n\t\t\t<td class=" . ap_getColor($papers_n_reviewers[$key01][$key02]["overall"]) . ">" . $papers_n_reviewers[$key01][$key02]["overall"] . "</td>";
						echo "\n\t\t\t<td title=\"" . $papers_n_reviewers[$key01][$key02]["expertise"] . "\">" . 	substr($papers_n_reviewers[$key01][$key02]["expertise"], 0, 1) . "</td>";
						$temp_count++;
					}//inner while 2

					//print blank <td>s
					if($temp_count == $coptions2D["chairman"]["NORPC"]){}//do nothing
					elseif($temp_count != 0)
					{
						for($j=0; $j<($coptions2D["chairman"]["NORPC"] - $temp_count); $j++)
						{
							echo "\n\t\t\t<td colspan=\"7\" bgcolor=\"#F5F0EA\" title=\"No reviewer\">" . "-" . "</td>";
						}
					}
					elseif($temp_count == 0 )
					{
						echo "\n\t\t\t<td colspan=\"" . ($coptions2D["chairman"]["NORPC"] * 7) . "\" bgcolor=\"#F5F0EA\" title=\"No reviewers assigned for this paper\">" . "-" . "</td>";
					}
					////

					if(abs($scores_array[$key01]["wavg"] - $scores_array[$key01]["min"]) >= 2){ echo "\n\t\t\t<td class=\"cinnamint\">" . $scores_array[$key01]["min"] . "</td>"; }
					else { echo "\n\t\t\t<td>" . $scores_array[$key01]["min"] . "</td>"; }

					if(abs($scores_array[$key01]["wavg"] - $scores_array[$key01]["max"]) >= 2){ echo "\n\t\t\t<td class=\"cinnamint\">" . $scores_array[$key01]["max"] . "</td>"; }
					else { echo "\n\t\t\t<td>" . $scores_array[$key01]["max"] . "</td>"; }

					if($scores_array[$key01]["max"] > 4.5){ $has_weak_accept = 'y';}

					if( ($has_weak_accept == 'y') && (($scores_array[$key01]["max"] - $scores_array[$key01]["min"])>3) ){ echo "\n\t\t\t<td class=\"cinnamint\">" . $scores_array[$key01]["avg"] . "</td>"; }
					else { echo "\n\t\t\t<td class=\"" . ap_getColor($scores_array[$key01]["avg"]) . "\">" . $scores_array[$key01]["avg"] . "</td>"; }

					echo "\n\t\t\t<td class=\"" . ap_getColor($scores_array[$key01]["wavg"]) . "\">" . $scores_array[$key01]["wavg"] . "</td>";

					/*
					echo "\n\t\t\t<td>";
					if($paper_titles[$key01]["status_code"] == 0)
						{ echo "<input type=\"checkbox\" id=\"" . $key01 . "\" name=\"" . $key01 . "\" >";}
					elseif($paper_titles[$key01]["status_code"] == 1)
						{ echo "<input type=\"checkbox\" id=\"" . $key01 . "\" name=\"" . $key01 . "\" checked>"; $accepted_papers_count++;}
					echo "</td>";
					*/

					reset($acceptancestatus);
					echo "\n\t\t\t<td>";
						echo "<select id=\"" . $key01 . "\" style=\"width:80px\" name=\"" . $key01 . "\">";
						while (list($key10, $val10) = each($acceptancestatus))
						{
							if($paper_titles[$key01]["status_code"] == $key10){ $astempval = "selected"; }
							else { $astempval = "";}
							echo "<option value=\"" . $key10 . "\" " . $astempval . " >" . $val10 . "</option>";
						}//while
						echo "</select>";
						if($paper_titles[$key01]["status_code"] == 1){$accepted_papers_count++;}
					echo "</td>";

					echo "\n\t\t</tr>";
					$count++;
					break;
				}//inner while 1
			}//if
		}//outer while

		//print the statistics
		echo "\n\t\t<tr>"
			. "\n\t\t\t<td colspan=" . (($coptions2D["chairman"]["NORPC"] * 7)+6) . ">"
			. "Total Papers: " . "<span class='totals'>" . $num00 . "</span>" . ", "
			. "Reviews Completed: " . "<span class='totals'>" . $num02 . "</span>" . ", "
			. "Unavailable Reviews: " . "<span class='totals'>" . ($num01 - $num02) . "</span>" . ", "
			. "Total Accepted Papers: " . "<span class='totals'>" . $accepted_papers_count . "</span>"
			. "\n\t\t\t</td>"
			. "\n\t\t</tr>";
		echo $str_05;
		echo $str_06;

	}//else

	@mysql_close();//closes the connection to the DB
}//display_papers_and_reviews($order_by)



?>
