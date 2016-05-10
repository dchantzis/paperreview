<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	###################################################################################

	whereUgo(0);
	whereUgo(1);

	global $referee;
	global $score;
	global $confidential;
	global $contributions;
	global $positive;
	global $negative;
	global $further;
	global $author_id;

	//load DataBase info
	if (!isset($_SESSION["SESSION"])) require ( "./include/sessioninitinc.php");

	######################
	//check if the $_GET table has only the value we want,
	//and the value is of the type we want
	//returns the value we want trimmed
	//$paper_id = checkGetVariable("paperid",0,"([^0-9]+)");
	if(!isset($_GET["paperid"])){ header("Location: ./ulounge.php"); exit;}
	$get_var_type["paperid"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$paper_id = $validated_vars["paperid"];
	######################

	$conference_id = $_SESSION["conf_id"];//current conference
	$loggedUserIsReviewer = 0;

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("findpaperreviewsinc.php","",40,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);
	@mysql_select_db($database) or errorHandler("findpaperreviewsinc.php","",41,"Unable to select database: " . $database);
	//@mysql_query("SET NAMES greek");

	//get the reviewers names, the paper title, of this paper for this confernece
	$query01 = "SELECT user.id AS reviewer_id, user.fname AS reviewer_fname, user.lname AS reviewer_lname, "
					. "paper.id, paper.title, paper.user_id AS author_id "
				. "FROM user, paper, papertoreviewer "
				. "WHERE user.id = papertoreviewer.user_id AND paper.id = papertoreviewer.paper_id AND "
					. "papertoreviewer.conference_id='" . $conference_id . "' AND papertoreviewer.paper_id='" . $paper_id . "' ORDER BY (user.id) ASC ";

	$query02 = "SELECT user.id AS reviewer_id, "
						. "review.referee_name, review.originality, review.significance, "
						. "review.quality, review.relevance, review.presentation, review.overall, "
						. "review.expertise, review.confidential, review.contributions, review.positive, "
						. "review.negative, review.further "
				. "FROM user, review "
				. "WHERE user.id = review.user_id AND review.conference_id='" . $conference_id . "' AND review.paper_id='" . $paper_id . "' ORDER BY (user.id) ASC ";

	//Execute guery01
	$result01 = @mysql_query($query01) or dbErrorHandler("findpaperreviewsinc.php","",60,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query01);
	$row01 = mysql_fetch_row($result01);
	$num01 = mysql_num_rows($result01);

	if($num01 == 0)
	{
		//echo "ERROR! A paper with this id doesn't exist.";
		Redirects(0,"","");
	}//if
	else{
		for($i=0; $i<$num01; $i++)
		{
			$author_id = mysql_result($result01, $i, "author_id");  //id of the user that submitted the paper. For the system he is an autho

			//store all the DB values in array $cvalues
			$reviewer_id = mysql_result($result01,$i,"reviewer_id");
			$array01[$reviewer_id]["reviewer_name"] = ucwords(mysql_result($result01,$i,"reviewer_fname") . " " . mysql_result($result01,$i,"reviewer_lname"));

			$paper_id = mysql_result($result01,$i,"id");
			$paper_title = mysql_result($result01,$i,"title");
		}//for
	}//else

	//Execute query02
	$result02 = @mysql_query($query02) or dbErrorHandler("findpaperreviewsinc.php","",84,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query02);
	$row02 = mysql_fetch_row($result02);
	$num02 = mysql_num_rows($result02);

	if($num02 == 0)
	{
		//no reviews written for this paper yet.
	}//if
	else
	{
		for($j=0; $j<$num02; $j++)
		{
			//store all the DB values in array $cvalues
			$reviewer_id = mysql_result($result02,$j,"reviewer_id");
			$array02[$reviewer_id]["referee_name"] = ucwords(mysql_result($result02,$j,"referee_name"));
			$array02[$reviewer_id]["originality"] = mysql_result($result02,$j,"originality");
			$array02[$reviewer_id]["significance"] = mysql_result($result02,$j,"significance");
			$array02[$reviewer_id]["quality"] = mysql_result($result02,$j,"quality");
			$array02[$reviewer_id]["relevance"] = mysql_result($result02,$j,"relevance");
			$array02[$reviewer_id]["presentation"] = mysql_result($result02,$j,"presentation");
			$array02[$reviewer_id]["expertise"] = mysql_result($result02,$j,"expertise");
			$array02[$reviewer_id]["overall"] = mysql_result($result02,$j,"overall");

			$array02[$reviewer_id]["confidential"] = mysql_result($result02,$j,"confidential");
			$array02[$reviewer_id]["contributions"] = mysql_result($result02,$j,"contributions");
			$array02[$reviewer_id]["positive"] = mysql_result($result02,$j,"positive");
			$array02[$reviewer_id]["negative"] = mysql_result($result02,$j,"negative");
			$array02[$reviewer_id]["further"] = mysql_result($result02,$j,"further");
		}//for
	}//else

	//Combine the data from $array01 and $array02
	reset($array01);
	while (list($key01, $val01) = each($array01))
	{
		//$key01 is the reviewer_id
		$reviewer_name = $array01[$key01]["reviewer_name"];

		if($key01 == $_SESSION["logged_user_id"]){ $loggedUserIsReviewer=1; }//this means, that the user that is trying to view the review $paper_id is one of the reviewers of that paper

		if(count($array02)!=0)
		{

			$reviewer_name = $array01[$key01]["reviewer_name"];

			$referee[$reviewer_name]["referee_name"] = $array02[$key01]["referee_name"];

			$score[$reviewer_name]["originality"] = $array02[$key01]["originality"];
			$score[$reviewer_name]["significance"] = $array02[$key01]["significance"];
			$score[$reviewer_name]["quality"] = $array02[$key01]["quality"];
			$score[$reviewer_name]["relevance"] = $array02[$key01]["relevance"];
			$score[$reviewer_name]["presentation"] = $array02[$key01]["presentation"];
			$score[$reviewer_name]["expertise"] = $array02[$key01]["expertise"];
			$score[$reviewer_name]["overall"] = $array02[$key01]["overall"];

			$confidential[$reviewer_name] = $array02[$key01]["confidential"];
			$contributions[$reviewer_name] = $array02[$key01]["contributions"];
			$positive[$reviewer_name] = $array02[$key01]["positive"];
			$negative[$reviewer_name] = $array02[$key01]["negative"];
			$further[$reviewer_name] = $array02[$key01]["further"];

		}//if
	}//while



	//if the logged user is an AUTHOR, Then he should be able to view the reviews
	//of ONLY his papers. So first we check if the user is an AUTHOR, and then if he is
	//the one who wrote the paper (because generally we do want reviewers to view all
	//the reviews
	//THE ABOVE ALWAYS DEPEND BY THE CONFERENCE OPTIONS

	if($loggedUserIsReviewer == 0)
	{
		if( (isset($_SESSION["author"])) && ($_SESSION["logged_user_id"] != $author_id) && (!isset($_SESSION["chairman"])))
		{
			unset($array02); //we destroy the result
		}//inner if
	}//outer if
	@mysql_close();//closes the connection to the DB

	//echo $loggedUserIsReviewer;
?>
