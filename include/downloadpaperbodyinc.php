<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	###################################################################################

	session_start();
	require("functionsinc.php");

	whereUgo(0);

	global $pvalues; //initialized in sessioninitinc.php
	global $papers_folder; //initialized in sessioninitinc.php

	$ok_tmp_var = 0;

	//load DataBase info
	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	######################
	//check if the $_GET table has only the value we want,
	//and the value is of the type we want
	//returns the value we want trimmed
	//$paper_id = checkGetVariable("paperid",0,"([^0-9]+)");

	$get_var_type["pbodyid"] = "([^0-9]+)";
	$validated_vars = checkGetVariable(1,0,$get_var_type);
	$pbodyid = $validated_vars["pbodyid"];

	######################

	@mysql_connect($db_host,$db_common_user,$db_common_password)
			or die("DATABASE ERROR: Unable to connect to SQL server");
	@mysql_select_db($database) or die("DATABASE ERROR: Unable to select database");
	//@mysql_query("SET NAMES greek");

	$query = "SELECT pa.id, pa.user_id, pa.conference_id, pb.filename, f.mime_type, pb.filesize, pb.filecontent, pb.fileurl, pb.paper_type "
			 . "FROM fileformat f, paperbody pb, paper pa "
			 . "WHERE f.id = pb.format_id AND pb.paper_id = pa.id AND pb.id='" . $pbodyid . "';";
	$result = @mysql_query($query) or die('Query failed: ' . mysql_error());
	$num = @mysql_num_rows($result);

	if($num == 0)
	{
		echo "ERROR! A paper body with this id doesn't exist.";
		Redirects(55,"","");
	}//if
	else{
		$filename = mysql_result($result,0,"filename");
		$mime_type = mysql_result($result,0,"mime_type");
		$filesize = mysql_result($result,0,"filesize");
		$filecontent = mysql_result($result,0,"filecontent");
		$fileurl = mysql_result($result,0,"fileurl");
		$paper_type = mysql_result($result,0,"paper_type");

		$author_id = mysql_result($result,0,"user_id");
		$conference_id = mysql_result($result,0,"conference_id");


		if( (isset($_SESSION["administrator"])) && ($_SESSION["administrator"] == TRUE)) { $ok_tmp_var = 1;}//he can download all papers
		elseif($conference_id == $_SESSION["conf_id"])//if the paper belong to the conference that the user is currently logged-in
		{
			if((isset($_SESSION["chairman"])) && ($_SESSION["chairman"] == TRUE)){ $ok_tmp_var = 1;}
			elseif((isset($_SESSION["reviewer"])) && ($_SESSION["reviewer"] == TRUE)){ $ok_tmp_var = 1;}
			elseif($author_id == $_SESSION["logged_user_id"]){ $ok_tmp_var = 1;}//if the user is the author of the paper
			else{ $ok_tmp_var = 0;}
		}//elseif()
		else{ $ok_tmp_var = 0;}


		if($ok_tmp_var == 1)
		{
			if($paper_type == "manuscript")
			{
				$papers_upload_dir = $papers_upload_dir . "ConferenceID_" . $_SESSION["conf_id"] . "/" . "Manuscripts/";
			}
			elseif($paper_type == "camera_ready")
			{
				$papers_upload_dir = $papers_upload_dir . "ConferenceID_" . $_SESSION["conf_id"] . "/" . "Camera_Ready/";
			}

			$filename =  str_replace(" ", "_", $filename);

			header("Content-length: " . $filesize);
			header("Content-type: " . $mime_type);
			header("Content-Disposition: attachment; filename=" . $filename);

			if($fileurl == "NULL")
			{
				echo $filecontent;
			}//if
			else if($filecontent == "NULL")
			{
				readfile ($papers_upload_dir . $fileurl);
			}//else if
		}
		elseif($ok_tmp_var == 0)
		{
			//unauthorized user tried to download the paper
			//do nothing
			Redirects(0,"","");
		}

	}//else

	@mysql_close();//closes the connection to the DB

?>
