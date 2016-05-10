<?php
##################################################
################selectdbrevinc.php################
##################################################
/*
This file includes all the functions that include
select queries to the DataBase, that refer to reviewers.
This file doesn't include some functions that are common to
all the users of the system, (these are included in the
selectdbcommoninc.php file).
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
display_reviews_r(),
lpapersInterests(),
lpapersInterests2(),
show_assigned_papers(),
select_review(),
*/

function display_reviews_r()
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
		or dbErrorHandler("display_reviews_r()","selectdbrevinc.php",41,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("display_reviews_r()","selectdbrevinc.php",42,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//show only the paper reviews that are assigned to him
	$query01 = "SELECT paper.id, paper.title "
				. "FROM paper "
				. "WHERE conference_id = '" . $conference_id . "' ORDER BY (paper.id) ASC;";
	//Get all the reviewers and what paper they are assigned to review for this conference
	$query02 = "SELECT papertoreviewer.paper_id, user.id, user.fname, user.lname "
				. "FROM user, papertoreviewer "
				. "WHERE user.id=papertoreviewer.user_id AND papertoreviewer.conference_id = '" . $conference_id . "' ORDER BY (papertoreviewer.paper_id) ASC;";

	//get the papers that the logged_in user is reviewer for.
	$query03 = "SELECT papertoreviewer.paper_id, user.id, user.fname, user.lname "
				. "FROM user, papertoreviewer "
				. "WHERE user.id=papertoreviewer.user_id AND papertoreviewer.conference_id = '" . $conference_id . "' AND user.id = '" . $user_id . "' ORDER BY (papertoreviewer.paper_id) ASC;";

	$result01 = @mysql_query($query01) or dbErrorHandler("display_reviews_r()","selectdbrevinc.php",59,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
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

	$result02 = @mysql_query($query02) or dbErrorHandler("display_reviews_r()","selectdbrevinc.php",76,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
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

	$result03 = @mysql_query($query03) or dbErrorHandler("display_reviews_r()","selectdbrevinc.php",94,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query03);
	$num03 = @mysql_num_rows($result03);//num

	if($num03 == 0)
	{
		//he has no papers assigned to him, to review
	}//if
	else
	{
		//fill the array with values
		for($z=0; $z<$num03; $z++)
		{
			$assigned_papers[$z]["paper_id"] = mysql_result($result03,$z,"paper_id");
			$assigned_papers[$z]["reviewer_id"]  = mysql_result($result03,$z,"id");
			$assigned_papers[$z]["reviewer_name"]  = mysql_result($result03,$z,"fname") . " " . $reviewers[$z]["reviewer_lname"]  = mysql_result($result03,$z,"lname");
		}//for
	}//else

	//COMBINE THE DATA OF arrays: 'reviewers' and $assigned_papers
	for($i=0; $i<count($reviewers); $i++)
	{
		for($j=0; $j<count($reviewers); $j++)
		{
			if($reviewers[$i]["paper_id"] == $assigned_papers[$j]["paper_id"])
			{
				//this paper should be printed when the $user_type = 'reviewer'
				//because in that case we only want the papers that are assigned to the reviewer who is currently logged_in
				$p_id = $reviewers[$i]["paper_id"];
				$papers_to_print[$p_id]["title"] = $papers[$p_id]["title"];

				//this array will contain all the reviewers assigned for that paper
				$reviewers_to_print[$i . " " . $j]["paper_id"] = $reviewers[$i]["paper_id"];
				$reviewers_to_print[$i . " " . $j]["reviewer_id"] = $reviewers[$i]["reviewer_id"];
				$reviewers_to_print[$i . " " . $j]["reviewer_name"] = $reviewers[$i]["reviewer_name"];
			}
		}//inner for
	}//outer for

	if(count($papers_to_print) == 0)
	{
		//no papers have been submitted for this conference
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"3\" align=\"center\">Currently there are no reviews for any paper.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($papers_to_print) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the arrays
		reset($papers_to_print);

		$count=0;
		while (list($key01, $val01) = each($papers_to_print))
		{
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
			echo "\n\t\t\t<td class=\"name\"><a href=\"./paper_info.php?paperid=" . $key01 . "\">" . $papers_to_print[$key01]["title"] . "</a>" . "</td>";

			echo "\n\t\t\t<td class=\"reviewers\">";
			echo "\n\t\t\t\t<ul>";
			reset($reviewers_to_print);
			while (list($key02, $val02) = each($reviewers_to_print))
			{
				if($key01 == $reviewers_to_print[$key02]["paper_id"])
				{
					//if the user is the administrator or a chairman, make the reviewers name a link
					echo "<li>" . $reviewers_to_print[$key02]["reviewer_name"] . "</li>";
				}//if
			}//for
			echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";

			echo "\n\t\t\t<td class=\"view\">" . "<a href=\"./paper_reviews_info.php?paperid=" . $key01 . "\" class=\"simple\">" . "view" . "</a>" . "</td>";

			echo "\n\t\t</tr>";

			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//else
	@mysql_close();//closes the connection to the DB
}//display_reviews_r()

##################################
##################################

//load a table with all the papers of a conference
//next to each paper, show level of interest and conflict of logged-in reviewer
function lpapersInterests()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$user_id = $_SESSION["logged_user_id"];//$user_id is the id of the logged user
	$conference_id = $_SESSION["conf_id"];//$conference_id is the id of the present conference

	$papers_ar = array();
	$interests_ar = array();

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"interests_and_conflicts\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"interest\">Interest</th>
		<th scope=\"col\" class=\"conflict\">Conflict</th>
		<th scope=\"col\" class=\"abstract\">Abstract</th>
		<th scope=\"col\" class=\"edit\"></th>
		</tr>\n</thead>";

	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	$logged_user_name_1 = strtolower($_SESSION["logged_user_fname"] . " " . $_SESSION["logged_user_lname"]);
	$logged_user_name_2 = strtolower($_SESSION["logged_user_lname"] . " " . $_SESSION["logged_user_fname"]);

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("lpapersInterests()","selectdbrevinc.php",225,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("lpapersInterests()","selectdbrevinc.php",226,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$query01 = "SELECT id, user_id, title, authors, subject, abstract "
			 . "FROM paper "
			 . " WHERE conference_id = '" . $conference_id . "';";
	$result01 = @mysql_query($query01) or dbErrorHandler("lpapersInterests()","selectdbrevinc.php",232,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//There are no papers for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{

			//don't show the papers submitted by the reviewer (check user_id with logged_user_id)
			if(mysql_result($result01,$i,"user_id") == $user_id)
			{
				continue;
			}//if
			//don't show the papers that list the logged user as an author
			//first check as "first name <blank> last name"
			if (strchr(mysql_result($result01,$i,"authors"), $logged_user_name_1))
			{
				continue;
			}//if
			//then check as "last name <blank> first name"
			if (strchr(mysql_result($result01,$i,"authors"), $logged_user_name_2))
			{
				continue;
			}//if

			//ALL OK
			$paper_id = mysql_result($result01,$i,"id");

			$papers_ar[$paper_id]["user_id"] = mysql_result($result01,$i,"user_id");
			$papers_ar[$paper_id]["title"] = mysql_result($result01,$i,"title");
			$papers_ar[$paper_id]["authors"] = mysql_result($result01,$i,"authors");
			$papers_ar[$paper_id]["subject"] = mysql_result($result01,$i,"subject");
			$papers_ar[$paper_id]["abstract"] = mysql_result($result01,$i,"abstract");
		}//for
	}//else

	//Get the interest levels and conflicts, for all the papers of this user, for this conference.
	$query02 = "SELECT paper_id, level_of_interest, conflict "
			 . "FROM interest"
			 . " WHERE conference_id = '" . $conference_id . "' AND user_id = '" . $user_id . "';";
	$result02 = @mysql_query($query02) or dbErrorHandler("lpapersInterests()","selectdbrevinc.php",277,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	if($num02 == 0)
	{
		//This user hasn't entered any interest or conflict for any paper of this conference.
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num02; $i++)
		{
			$paper_id = mysql_result($result02,$i,"paper_id");

			$interests_ar[$paper_id]["level_of_interest"] = mysql_result($result02,$i,"level_of_interest");
			$interests_ar[$paper_id]["conflict"] = mysql_result($result02,$i,"conflict");
		}//for
	}//else

	if(count($papers_ar) == 0)
	{
		//there aren't any papers that he is allowed to enter his interest levels
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"6\" align=\"center\">Currently there are no papers for this conference.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($papers_ar) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the arrays
		reset($papers_ar);
		reset($interests_ar);

		$count=0;
		while (list($key01, $val01) = each($papers_ar))
		{
			// $key01 is the paper_id for this iteration
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

			echo "\n\t\t\t<td class=\"title\">" . $papers_ar[$key01]["title"] . "</td>";

			$papers_ar[$key01]["authors"] = $papers_ar[$key01]["authors"] . " ";
			$authors = explode(", ", $papers_ar[$key01]["authors"]);
			unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it

			echo "\n\t\t\t<td class=\"authors\">";
				echo "\n\t\t\t\t<ul>";
				for($k=0; $k<count($authors); $k++)
				{
					echo "<li>" . $authors[$k] . "</li>";
				}//for
				echo "\n\t\t\t\t</ul>";
			echo "\n\t\t\t</td>";

			//echo "\n\t\t\t<td class=\"authors\" title=\"" . $papers_ar[$key01]["authors"] . "\">" . substr($papers_ar[$key01]["authors"], 0, 8) . "..." . "</td>";


			echo "\n\t\t\t<td class=\"interest\">";
			if( isset($interests_ar[$key01]["level_of_interest"]) ) { echo $interests_ar[$key01]["level_of_interest"]; }
			else {
				//echo nothing
				//reviewer hasn't entered any interest for this paper yet
			}
			echo "\n\t\t\t</td>";

			if( $interests_ar[$key01]["conflict"] == 0) { echo "\n\t\t\t<td class=\"conflict\">" . "no" . "\n\t\t\t</td>"; }//if
			else { echo "\n\t\t\t<td class=\"conflict\">" . "yes" . "\n\t\t\t</td>";}//else

			echo "\n\t\t\t<td class=\"button\"><a onClick=\"toggle_hidden_content('" . "v" . $key01 . "', this, 'preferencies');\" >view</a></td>";

			if( isset($interests_ar[$key01]["level_of_interest"]) ) //no interest level or conflict
			{
				echo "<td><a href=\"./include/functionsinc.php?type=55&paperid=" . $key01 . "\" class=\"simple\">update</a></td>";
			}//else
			else
			{
				echo "<td><a href=\"./include/functionsinc.php?type=54&paperid=" . $key01 . "\" class=\"simple\">insert</a></td>";
			}//else if

			echo "\n\t\t</tr>";


			//print the abstract
			echo "\n\t\t<tr>";
				echo "\n\t\t\t<td colspan=\"6\"  title=\"Paper Abstract.\">";
					echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "v" . $key01 . "\">";
						echo "\n\t\t\t<div class=\"a_of_p\">Abstract for: <span class=\"red\">" . $papers_ar[$key01]["title"] . "</span></div>";
						echo  "<div class=\"abst\">" . $papers_ar[$key01]["abstract"] . "</div>";
					echo "\n\t\t\t\t</div>";
				echo "</td>";
			echo "\n\t\t</tr>";


			$count++;
		}//outer while
		echo $str_05;
		echo $str_06;
	}//elseif

	@mysql_close();//closes the connection to the DB

}//lpapersInterests()

##################################
##################################

//load a table with all the papers of a conference
//next to each paper, show level of interest and conflict of logged-in reviewer
function lpapersInterests2()
{
	$user_id = $_SESSION["logged_user_id"];//$user_id is the id of the logged user
	$conference_id = $_SESSION["conf_id"];//$conference_id is the id of the present conference

	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$papers_ar = array();
	$interests_ar = array();

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"interests_and_conflicts\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"authors\">Authors</th>
		<th scope=\"col\" class=\"interest\">Interest</th>
		<th scope=\"col\" class=\"conflict\">Conflict</th>
		<th scope=\"col\" class=\"abstract\">Abstract</th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	$logged_user_name_1 = strtolower($_SESSION["logged_user_fname"] . " " . $_SESSION["logged_user_lname"]);
	$logged_user_name_2 = strtolower($_SESSION["logged_user_lname"] . " " . $_SESSION["logged_user_fname"]);

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("lpapersInterests2()","selectdbrevinc.php",426,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("lpapersInterests2()","selectdbrevinc.php",427,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//Get all the papers of this conference
	$query01 = "SELECT id, user_id, title, authors, subject, abstract "
			 . "FROM paper"
			 . " WHERE conference_id = '" . $conference_id . "';";
	$result01 = @mysql_query($query01) or dbErrorHandler("lpapersInterests2()","selectdbrevinc.php",434,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$num01 = @mysql_num_rows($result01);//num

	if($num01 == 0)
	{
		//There are no papers for this conference
	}//if
	else
	{
		//fill the array with values
		for($i=0; $i<$num01; $i++)
		{
			//don't show the papers submitted by the reviewer (check user_id with logged_user_id)
			if(mysql_result($result01,$i,"user_id") == $user_id)
			{
				continue;
			}//if
			//don't show the papers that list the logged user as an author
			//first check as "first name <blank> last name"
			if (strchr(mysql_result($result01,$i,"authors"), $logged_user_name_1))
			{
				continue;
			}//if
			//then check as "last name <blank> first name"
			if (strchr(mysql_result($result01,$i,"authors"), $logged_user_name_2))
			{
				continue;
			}//if

			//ALL OK
			$paper_id = mysql_result($result01,$i,"id");

			$papers_ar[$paper_id]["user_id"] = mysql_result($result01,$i,"user_id");
			$papers_ar[$paper_id]["title"] = mysql_result($result01,$i,"title");
			$papers_ar[$paper_id]["authors"] = mysql_result($result01,$i,"authors");
			$papers_ar[$paper_id]["subject"] = mysql_result($result01,$i,"subject");
			$papers_ar[$paper_id]["abstract"] = mysql_result($result01,$i,"abstract");
		}//for
	}//else

		//Get the interest levels and conflicts, for all the papers of this user, for this conference.
		$query02 = "SELECT paper_id, level_of_interest, conflict "
				 . "FROM interest"
				 . " WHERE conference_id = '" . $conference_id . "' AND user_id = '" . $user_id . "';";
		$result02 = @mysql_query($query02) or dbErrorHandler("lpapersInterests2()","selectdbrevinc.php",478,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
		$num02 = @mysql_num_rows($result02);//num

		if($num02 == 0)
		{
			//This user hasn't entered any interest or conflict for any paper of this conference.
		}//if
		else
		{
			//fill the array with values
			for($i=0; $i<$num02; $i++)
			{
				$paper_id = mysql_result($result02,$i,"paper_id");

				$interests_ar[$paper_id]["level_of_interest"] = mysql_result($result02,$i,"level_of_interest");
				$interests_ar[$paper_id]["conflict"] = mysql_result($result02,$i,"conflict");
			}//for
		}//else

		if(count($papers_ar) == 0)
		{
			//there aren't any papers that he is allowed to enter his interest levels
			echo $str_01;
			echo $str_03;
			echo $str_04;
			echo "\n\t\t<tr><td colspan=\"6\" align=\"center\">Currently there are no papers for this conference.</td>";
			echo $str_05;
			echo $str_06;
		}//if
		elseif(count($papers_ar) != 0)
		{
			echo $str_01;
			echo $str_03;
			echo $str_04;

			//reset the arrays
			reset($papers_ar);
			reset($interests_ar);

			$count=0;
			while (list($key01, $val01) = each($papers_ar))
			{
				// $key01 is the paper_id for this iteration

				if (($count%2) == 0) {
					$bgColor = "#FFFFFF";
					$trClass = "odd";
				} else {
					$bgColor = "#F5F0EA";
					$trClass = "even";
				}//else

				echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

				echo "\n\t\t\t<td class=\"title\">" . $papers_ar[$key01]["title"] . "</td>";

				$papers_ar[$key01]["authors"] = $papers_ar[$key01]["authors"] . " ";
				$authors = explode(", ", $papers_ar[$key01]["authors"]);
				unset($authors[count($authors)-1]);	//the last cell of this array contains just an empty space, so we unset it

				echo "\n\t\t\t<td class=\"authors\">";
					echo "\n\t\t\t\t<ul>";
					for($k=0; $k<count($authors); $k++)
					{
						echo "<li>" . $authors[$k] . "</li>";
					}//for
					echo "\n\t\t\t\t</ul>";
				echo "\n\t\t\t</td>";

				//echo "\n\t\t\t<td class=\"authors\" title=\"" . $papers_ar[$key01]["authors"] . "\">" . substr($papers_ar[$key01]["authors"], 0, 8) . "..." . "</td>";

				echo "\n\t\t\t<td class=\"interest\">";
				//print the 'interest level' combo box
				//each combo box has as name: 'i_$key01' which means: 'i_paper_id'.
				echo "<select name=\"" . "i_" . $key01 . "\" id=\"" . "i_" . $key01 . "\" title=\"Select Interest Level.\" style=\"width:75px\">";
				if( isset($interests_ar[$key01]["level_of_interest"]) )
				{
					echo "<option value=\"" . $interests_ar[$key01]["level_of_interest"] . "\">" . $interests_ar[$key01]["level_of_interest"] . "</option>";
					echo "<option value=\"1\"></option>";
				}
				else
				{
					echo "<option value=\"1\">[Interest]</option>";
					echo "<option value=\"1\"></option>";
				}
				echo "<option value=\"1\">1</option><option value=\"2\">2</option><option value=\"3\">3</option>"
					  . "<option value=\"4\">4</option><option value=\"5\">5</option><option value=\"6\">6</option>"
					  . "<option value=\"7\">7</option>"
					  . "</select>";

				echo "\n\t\t\t</td>";

				//print the conflict check box
				//each check box has as name: 'c_$key01' which means: 'c_paper_id'.
				if( $interests_ar[$key01]["conflict"] == 0)
				{
					echo "\n\t\t\t<td class=\"conflict\">";
					echo "<input type=\"checkbox\" id=\"" . "c_" . $key01 . "\" name=\"" . "c_" . $key01 . "\">";
					echo "\n\t\t\t</td>";
				}//if
				else
				{
					echo "\n\t\t\t<td class=\"conflict\">";
					echo "<input type=\"checkbox\" id=\"" . "c_" . $key01 . "\" name=\"" . "c_" . $key01 . "\" checked>";
					echo "\n\t\t\t</td>";
				}//else
				echo "\n\t\t\t<td class=\"button\"><a onClick=\"toggle_hidden_content('" . "v" . $key01 . "', this, 'preferencies');\" class=\"simple\">view</a></td>";
				echo "\n\t\t</tr>";

				//print the abstract
				echo "\n\t\t<tr>";
				echo "\n\t\t\t<td colspan=\"5\"  title=\"Paper Abstract.\">";
					echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "v" . $key01 . "\">";
						echo "\n\t\t\t<div class=\"a_of_p\">Abstract for: <span class=\"red\">" . $papers_ar[$key01]["title"] . "</span></div>";
						echo  "<div class=\"abst\"><pre>" . $papers_ar[$key01]["abstract"] . "</pre></div>";
					echo "\n\t\t\t\t</div>";
				echo "</td>";
				echo "\n\t\t</tr>";

				$count++;
			}//outer while
			echo $str_05;
			echo $str_06;
		}//else

	@mysql_close();//closes the connection to the DB
}//lpapersInterests2()

##################################
##################################

//Shows all the papers that are assigned to a reviewer for a conference
function show_assigned_papers()
{
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	$str_01 = "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"assigned_papers\">";
	$str_02 = "<caption>papers list</caption>";
	$str_03 = "\n<thead>\n\t<tr>
		<th scope=\"col\" class=\"title\">Paper Title</th>
		<th scope=\"col\" class=\"review\">Review</th>
		<th scope=\"col\" class=\"option\"></th>
		</tr>\n</thead>";
	$str_04 = "<tbody>";
	$str_05 = "</tbody>";
	$str_06 = "\n\t</table>";

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("show_assigned_papers()","selectdbrevinc.php",626,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("show_assigned_papers()","selectdbrevinc.php",627,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	$conference_id = $_SESSION["conf_id"];
	$reviewer_id = $_SESSION["logged_user_id"];

	//Get all the papers of this conference assigned to this reviewer
	$query01 = "SELECT paper.id, paper.title"
			 . " FROM papertoreviewer, paper"
			 . " WHERE paper.id = papertoreviewer.paper_id "
			 . " AND papertoreviewer.conference_id = '" . $conference_id . "' "
			 . " AND papertoreviewer.user_id = '" . $reviewer_id . "' ;";
	$result01 = @mysql_query($query01) or dbErrorHandler("show_assigned_papers()","selectdbrevinc.php",629,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
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
			$assigned_papers[$paper_id]["title"] = mysql_result($result01,$i,"title");
		}//for
	}//else

	//Get all the reviews of this reviewer for this conference
	$query02 = "SELECT id, user_id, paper_id, referee_name, originality, significance, quality, relevance, presentation, overall, expertise, confidential, contributions, positive, negative, further"
			 . " FROM review"
			 . " WHERE user_id = '" . $reviewer_id . "' "
			 . " AND conference_id = '" . $conference_id . "' ;";
	$result02 = @mysql_query($query02) or dbErrorHandler("show_assigned_papers()","selectdbrevinc.php",663,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$num02 = @mysql_num_rows($result02);//num

	for($j=0; $j<$num02; $j++)
	{
		$paper_id = mysql_result($result02,$j,"paper_id");

		$reviews[$paper_id]["review_id"] = mysql_result($result02,$j,"id");
		$reviews[$paper_id]["referee_name"] = mysql_result($result02,$j,"referee_name");
		$reviews[$paper_id]["originality"] = mysql_result($result02,$j,"originality");
		$reviews[$paper_id]["significance"] = mysql_result($result02,$j,"significance");
		$reviews[$paper_id]["quality"] = mysql_result($result02,$j,"quality");
		$reviews[$paper_id]["relevance"] = mysql_result($result02,$j,"relevance");
		$reviews[$paper_id]["presentation"] = mysql_result($result02,$j,"presentation");
		$reviews[$paper_id]["overall"] = mysql_result($result02,$j,"overall");
		$reviews[$paper_id]["expertise"] = mysql_result($result02,$j,"expertise");
		$reviews[$paper_id]["confidential"] = mysql_result($result02,$j,"confidential");
		$reviews[$paper_id]["contributions"] = mysql_result($result02,$j,"contributions");
		$reviews[$paper_id]["positive"] = mysql_result($result02,$j,"positive");
		$reviews[$paper_id]["negative"] = mysql_result($result02,$j,"negative");
		$reviews[$paper_id]["further"] = mysql_result($result02,$j,"further");

	}//for

	if(count($assigned_papers) != 0)
	{
		//reset the arrays
		reset($assigned_papers);
		while (list($key01, $val01) = each($assigned_papers))
		{
			$array[$key01]["title"] = $assigned_papers[$key01]["title"];
			if(count($reviews) != 0)
			{
				reset($reviews);
				while (list($key02, $val02) = each($reviews))
				{
					if($key01 == $key02)
					{
						$review_id = $reviews[$key01]["review_id"];

						$array[$key01]["review"]["referee_name"] = $reviews[$key01]["referee_name"];
						$array[$key01]["review"]["originality"] = $reviews[$key01]["originality"];
						$array[$key01]["review"]["significance"] = $reviews[$key01]["significance"];
						$array[$key01]["review"]["quality"] = $reviews[$key01]["quality"];
						$array[$key01]["review"]["relevance"] = $reviews[$key01]["relevance"];
						$array[$key01]["review"]["presentation"] = $reviews[$key01]["presentation"];
						$array[$key01]["review"]["overall"] = $reviews[$key01]["overall"];
						$array[$key01]["review"]["expertise"] = $reviews[$key01]["expertise"];
						$array[$key01]["review"]["confidential"] = $reviews[$key01]["confidential"];
						$array[$key01]["review"]["contributions"] = $reviews[$key01]["contributions"];
						$array[$key01]["review"]["positive"] = $reviews[$key01]["positive"];
						$array[$key01]["review"]["negative"] = $reviews[$key01]["negative"];
						$array[$key01]["review"]["further"] = $reviews[$key01]["further"];

						break;
					}//if
					else
					{
						continue;
					}//else
				}//inner while

		}//inner if
			}//outer while

	}//outer if

	if(count($array) == 0)
	{
		//no papers have been assigned for him to review
		echo $str_01;
		echo $str_03;
		echo $str_04;
		echo "\n\t\t<tr><td colspan=\"6\" align=\"center\">No papers assigned to review.</td>";
		echo $str_05;
		echo $str_06;
	}//if
	elseif(count($array) != 0)
	{
		echo $str_01;
		echo $str_03;
		echo $str_04;

		//reset the arrays
		reset($array);

		$count=0;
		while (list($key01, $val01) = each($array))
		{
			if (($count%2) == 0) {
				$bgColor = "#FFFFFF";
				$trClass = "odd";
			} else {
				$bgColor = "#F5F0EA";
				$trClass = "even";
			}//else

			echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";

			echo "\n\t\t\t<td class=\"name\"><a href=\"./paper_info.php?paperid=" . $key01 . "\">" . $array[$key01]["title"] . "</a>" . "</td>";

			if(isset($array[$key01]["review"]))
			{
				echo "\n\t\t\t<td class=\"button\">";
				echo "\n\t\t\t\t<a onClick=\"toggle_hidden_content('" . "r" . $key01 . "', this, 'reviews');\">View</a>";
				echo "\n\t\t\t</td>";

				echo "\n\t\t\t<td class=\"enter\">";
					echo "<a href=\"" . "./include/functionsinc.php?type=51&paperid=" . $key01 . "\" class=\"simple\">update</a>";
				echo "\n\t\t\t</td>";

				echo "\n\t\t</tr>";

				//print the deadlines
				echo "\n\t\t<tr>";
				echo "\n\t\t\t<td colspan=\"5\"  title=\"Conference Deadlines.\">";
					echo "\n\t\t\t\t<div class=\"hidden_content\" id=\"" . "r" . $key01 . "\">";
						echo "\n\t\t\t\t\t<div class=\"paper_title\">Review for Paper: " . "<span class=\"red\">" .  $array[$key01]["title"] . "</span>" . "</div>";
						echo "\n\t\t\t\t<ul>";
							echo "\n\t\t\t\t<li>Referee Name: " . "<span class=\"num\">" .  $array[$key01]["review"]["referee_name"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Originality: " . "<span class=\"num\">" . $array[$key01]["review"]["originality"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Significance: " . "<span class=\"num\">" . $array[$key01]["review"]["significance"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Quality: " . "<span class=\"num\">" . $array[$key01]["review"]["quality"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Relevance: " . "<span class=\"num\">" . $array[$key01]["review"]["relevance"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Presentation: " . "<span class=\"num\">" . $array[$key01]["review"]["presentation"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Overall: " . "<span class=\"num\">" . $array[$key01]["review"]["overall"] . "</span>" . "</li>";
							echo "\n\t\t\t\t<li>Expertise: " . "<span class=\"num\">" . $array[$key01]["review"]["expertise"] . "</span>" . "</li>";
							echo "<br>";
							echo "\n\t\t\t\t<li>Confidential: " . "<div class=\"text\">" . $array[$key01]["review"]["confidential"] . "</div>" . "</li>";
							echo "\n\t\t\t\t<li>Contributions: " . "<div class=\"text\">" . $array[$key01]["review"]["contributions"] . "</div>" . "</li>";
							echo "\n\t\t\t\t<li>Positive: " . "<div class=\"text\">" . $array[$key01]["review"]["positive"] . "</div>" . "</li>";
							echo "\n\t\t\t\t<li>Negative: " . "<div class=\"text\">" . $array[$key01]["review"]["negative"] . "</div>" . "</li>";
							echo "\n\t\t\t\t<li>Further: " . "<div class=\"text\">" . $array[$key01]["review"]["further"] . "</div>" . "</li>";
						echo "\n\t\t\t\t</ul>";
					echo "\n\t\t\t\t</div>";
				echo "</td>";
				echo "\n\t\t</tr>";

			}//if
			elseif(!isset($array[$key01]["review"]))
			{
				echo "\n\t\t\t<td class=\"button\">-</td>";
				echo "\n\t\t\t<td class=\"enter\">";
					echo "<a href=\"" . "./include/functionsinc.php?type=49&paperid=" . $key01 . "\" class=\"simple\">review</a>";
				echo "\n\t\t\t</td>";

				echo "\n\t\t</tr>";
			}//elseif

			$count++;
		}//while
		echo $str_05;
		echo $str_06;
	}//else

	@mysql_close();//closes the connection to the DB
}//show_assigned_papers()

##################################
##################################

//loads review data in session to be used for update
function select_review()
{
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	//check if %_POST is set. If it's not set, then the form was not submitted normaly.
	if(!isset($_POST)){ Redirects(51,"?flg=157","");}
	if((!isset($_SESSION["reviewer"])) ||($_SESSION["reviewer"] != TRUE) ){ Redirects(0,"",""); }

	//the array $arVals stores the names of all the values of the form
	$arVals = array( "paper_id"=>"", "logged_user_id"=>"", "conference_id"=>"");
	//the array $arValsRequired stores the name of the values of the form that are required for the registration
	$arValsRequired = array( "paper_id"=>"", "logged_user_id"=>"", "conference_id"=>"");
	//the array $arValsValidations stores the names of the fields and the regular expression their values have to much with.
	$arValsValidations = array( "paper_id"=>"/^[0-9]([0-9]*)/", "logged_user_id"=>"/^[0-9]([0-9]*)/", "conference_id"=>"/^[0-9]([0-9]*)/");

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
	variablesSet($arValsRequired,51,"");//send 51 because the page we want is assigned_papers.php
	// check if the form variables have something in them...
	variablesFilled($arValsRequired,51,"");//send 51 because the page we want is assigned_papers.php
	// make sure the variables match the corresponding regular expressions
	variablesValidate($arValsValidations,51,"");

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("select_review()","selectdbrevinc.php",830,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or dbErrorHandler("select_review()","selectdbrevinc.php",831,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");


	//Get all the papers of this conference
	$query = "SELECT id, referee_name, originality, significance, quality, relevance, presentation, overall, expertise, confidential, contributions, positive, negative, further"
			 . " FROM review"
			 . " WHERE user_id = " . $arVals["logged_user_id"] . "" //this is the reviewer id
			 . " AND conference_id = " . $arVals["conference_id"] . ""
			 . " AND paper_id = " . $arVals["paper_id"] . " ";
	$result = @mysql_query($query) or dbErrorHandler("select_review()","selectdbrevinc.php",842,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	$_SESSION["referee_name"] = mysql_result($result,0,"referee_name");
	$_SESSION["originality"] = mysql_result($result,0,"originality");
	$_SESSION["significance"] = mysql_result($result,0,"significance");
	$_SESSION["quality"] = mysql_result($result,0,"quality");
	$_SESSION["relevance"] = mysql_result($result,0,"relevance");
	$_SESSION["presentation"] = mysql_result($result,0,"presentation");
	$_SESSION["overall"] = mysql_result($result,0,"overall");
	$_SESSION["expertise"] = mysql_result($result,0,"expertise");
	$_SESSION["confidential"] = mysql_result($result,0,"confidential");
	$_SESSION["contributions"] = mysql_result($result,0,"contributions");
	$_SESSION["positive"] = mysql_result($result,0,"positive");
	$_SESSION["negative"] = mysql_result($result,0,"negative");
	$_SESSION["further"] = mysql_result($result,0,"further");

	$_SESSION["updatereview"] = "yes";

	@mysql_close();//closes the connection to the DB

	redirects(50,"?paperid=" . $_POST["paper_id"],"");
}//select_review()

##################################
##################################

?>
