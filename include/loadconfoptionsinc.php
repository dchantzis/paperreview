<?php
	//session_start();

	### The following commented lines of code should not be checked in this file EVER.
	### These checks are being handled in the other pages which require() this file.
	/*
	if ($_SESSION["user_logged_in"] != TRUE ||
		!isset($_SESSION["logged_user_password"]) ||
		$_SESSION["logged_user_password"] == "")
	{
			header("Location: login.php");
			exit;
	}//if
	*/

	//1-D array that will store all the conference options
	global $coptions1D;
	//2-D array that will store the conference option and the user it refers to
	//example; $coptions2D["author"]["ASA"]=1;
	global $coptions2D;

	if(isset($_SESSION["conf_id"])){ $conference_id = $_SESSION["conf_id"];}
	else { $conference_id = ""; }

	if (!isset($_SESSION["SESSION"])) require ( "sessioninitinc.php");

	#####CONFERENCE OPTIONS CODES#################
	/*
	1. Conference is active. ==> CODE: CIA
	2. Let authors submit abstracts. ==> CODE: ASA
	3. Let authors update abstracts. ==> CODE: AUA
	4. Let authors submit manuscripts. ==> CODE: ASM
	5. Let authors update manuscripts. ==> CODE: AUM
	6. Let authors submit camera_ready papers. ==> CODE: ASCRP
	7. Let authors update camera_ready papers. ==> CODE: AUCRP
	8. Let authors view reviews for their papers. ==> CODE: AVP
	9. Let authors enter conflicts with reviewers. ==> CODE: ACR
	10. How many reviewers for each paper in this conference? ==> CODE: NORPC
	11. Let reviewer view papers and enter level of interest and conflicts. ==> CODE: RELIC
	12. Let reviewer download his assigned papers and review them. ==> CODE: RDPR
	13. Let reviewer view reviews of his assigned papers by other reviewers. ==> CODE: RVRP
	14. Let users view all conference papers. ==> CODE: UVP
	15. Let users download all conference papers.(manuscripts and camera-ready versions). ==> CODE: UDP
	16. Let users view ONLY the accepted papers. ==> CODE: UVAP
	17. Let users download ONLY the accepted papers (only camera-ready versions) ==> CODE: UDAP
	*/

	@mysql_connect($db_host,$db_common_user,$db_common_password)
		or dbErrorHandler("loadconfoptionsinc.php","loadconfoptionsinc.php",54,"Unable to connect to SQL server using: username: " . $db_common_user . ", password: " . $db_common_password);

	@mysql_select_db($database) or dbErrorHandler("loadconfoptionsinc.php","loadconfoptionsinc.php",56,"Unable to select database: " . $database);
   	//@mysql_query("SET NAMES greek");

	$query = "SELECT * FROM options WHERE conference_id='" . $conference_id . "'";

	$result = @mysql_query($query) or dbErrorHandler("loadconfoptionsinc.php","loadconfoptionsinc.php",61,"Invalid query: " . mysql_error() . "<br /><br />" . " Executed query: " . "<br /><br />" . $query);
	$num = @mysql_num_rows($result);//num

	for($i=0; $i<$num; $i++)
	{
		$coptions1D["CIA"] = @mysql_result($result,$i,"CIA");
		$coptions1D["ASA"] = @mysql_result($result,$i,"ASA");
		$coptions1D["AUA"] = @mysql_result($result,$i,"AUA");
		$coptions1D["ASM"] = @mysql_result($result,$i,"ASM");
		$coptions1D["AUM"] = @mysql_result($result,$i,"AUM");
		$coptions1D["ASCRP"] = @mysql_result($result,$i,"ASCRP");
		$coptions1D["AUCRP"] = @mysql_result($result,$i,"AUCRP");
		$coptions1D["AVP"] = @mysql_result($result,$i,"AVP");
		$coptions1D["ACR"] = @mysql_result($result,$i,"ACR");
		$coptions1D["NORPC"] = @mysql_result($result,$i,"NORPC");
		$coptions1D["RELIC"] = @mysql_result($result,$i,"RELIC");
		$coptions1D["RDPR"] = @mysql_result($result,$i,"RDPR");
		$coptions1D["RVRP"] = @mysql_result($result,$i,"RVRP");
		$coptions1D["UVP"] = @mysql_result($result,$i,"UVP");
		$coptions1D["UDP"] = @mysql_result($result,$i,"UDP");
		$coptions1D["UVAP"] = @mysql_result($result,$i,"UVAP");
		$coptions1D["UDAP"] = @mysql_result($result,$i,"UDAP");
 	}//for

	//load the 2-D array $coptions2D
	//these conference options refer to the authors
	$coptions2D["author"]["ASA"] = $coptions1D["ASA"];
	$coptions2D["author"]["AUA"] = $coptions1D["AUA"];
	$coptions2D["author"]["ASM"] = $coptions1D["ASM"];
	$coptions2D["author"]["AUM"] = $coptions1D["AUM"];
	$coptions2D["author"]["ASCRP"] = $coptions1D["ASCRP"];
	$coptions2D["author"]["AUCRP"] = $coptions1D["AUCRP"];
	$coptions2D["author"]["AVP"] = $coptions1D["AVP"];
	$coptions2D["author"]["ACR"] = $coptions1D["ACR"];
	//these conference options are used for both authors and reviewers
	$coptions2D["author"]["UVP"] = $coptions1D["UVP"];
	$coptions2D["author"]["UDP"] = $coptions1D["UDP"];
	$coptions2D["author"]["UVAP"] = $coptions1D["UVAP"];
	$coptions2D["author"]["UDAP"] = $coptions1D["UDAP"];

	//these conference options refer to the reviewers
	$coptions2D["reviewer"]["RELIC"] = $coptions1D["RELIC"];
	$coptions2D["reviewer"]["RDPR"] = $coptions1D["RDPR"];
	$coptions2D["reviewer"]["RVRP"] = $coptions1D["RVRP"];
	//these conference options are used for both authors and reviewers
	$coptions2D["reviewer"]["UVP"] = $coptions1D["UVP"];
	$coptions2D["reviewer"]["UDP"] = $coptions1D["UDP"];
	$coptions2D["reviewer"]["UVAP"] = $coptions1D["UVAP"];
	$coptions2D["reviewer"]["UDAP"] = $coptions1D["UDAP"];

	//these conference options refer to the chairmen
	$coptions2D["chairman"]["CIA"] = $coptions1D["CIA"];
	$coptions2D["chairman"]["NORPC"] = $coptions1D["NORPC"];

	@mysql_close();//closes the connection to the DB
?>
