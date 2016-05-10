<?php
###############################################################
/*	
	resendToForm($flags, $type, $more_flags),
	variablesSet($arrayValues,$type,$more_flags), 
	variablesFilled($arrayValues,$type,$more_flags),
	variablesCheckRange($arrayValues,$type,$more_flags),
	variablesCheckRangeCutExtra($arrayValues),
	checkGetVariable($variablesnumber,$redirectpage,$variablestype)
*/
###############################################################
function resendToForm($flags, $type, $more_flags)
{
	reset ($_POST);
	// store variables in session...
	while (list ($key, $val) = each ($_POST)) {
		$_SESSION[$key] = $val;
	}	
	// go back to the form...
	//echo $flags;
	Redirects($type,$flags,$more_flags);
}//resendToForm
// function that checks to see if these variables have been set...
function variablesSet($arrayValues,$type,$more_flags)
{
	while (list($key, $val) = each ($arrayValues))
	{
		if ( (!isset($_SESSION[$key])) ){
			resendToForm("?flg=103",$type,$more_flags);
		}
	}//while
}//variablesSet
// function that checks if the form variables have something in them...
function variablesFilled($arrayValues,$type,$more_flags)
{
	while (list($key, $val) = each ($arrayValues))
	{
		if ($_SESSION[$key] == "" ){
			resendToForm("?flg=103",$type,$more_flags);
		}
	}//while
}//variablesFilled()
//function that checks if the fields are within the proper range...
function variablesCheckRange($arrayValues,$type,$more_flags)
{
	while (list($key, $val) = each($arrayValues))
	{
		if (strlen($_SESSION[$key]) > $val){
			resendToForm("?flg=106",$type,$more_flags);
		}
	}//while
}//variable
//function that makes sure fields are within the proper range... else cuts off any extra...
function  variablesCheckRangeCutExtra($arrayValues)
{	
	while (list($key, $val) = each($arrayValues))
	{
		if (strlen($_SESSION[$key]) > $val) { 
			$_SESSION[$key] = substr($_SESSION[$key],0,$val);
		}
	}//while
}//variablesCheckRangeCutExtra
function variablesValidate($arrayValues,$type,$more_flags)
{
	while (list($key, $val) = each($arrayValues))
	{
		if($_SESSION[$key] == NULL){}
		else
		{
			//echo $key . "-->" . $arrayValues[$key] . " compare with " . $_SESSION[$key] . " ";
			//if($_SESSION[$key] == "NULL" || !preg_match($arrayValues[$key],$_SESSION[$key]))
			if(!preg_match($arrayValues[$key],$_SESSION[$key]))
			{
				resendToForm("?flg=108",$type,$more_flags);
				//echo $key . " = " . $_SESSION[$key] . " ========> ERROR" . "<br>";
			}
			else
			{
				//echo $key . " = " . $_SESSION[$key] . " ========> ok" . "<br>";
			}
		}
	}//while
}//variablesValidate
//variablesnumber: how many variable should the $_GET array have for this page
//on error redirect to $redirectpage 
//$variablestype: what type of variable should each $_GET be
function checkGetVariable($variablesnumber,$redirectpage,$variablestype)
{
	######################
	######################
	if(count($_GET) == $variablesnumber)
	{
		//OK
		reset($_GET);
		while(list($key, $val) = each ($_GET))
		{
			if(!isset($_GET[$key]))
			{ 
				Redirects($redirectpage,"","");
				//echo "value is not set";
			}//if
			else
			{
				//OK
				if($_GET[$key] == "")
				{
					Redirects($redirectpage,"","");
					//echo "value is empty";
				}//if
				else
				{
					//OK
					if ( preg_match($variablestype[$key],$_GET[$key]))
					{
						Redirects($redirectpage,"","");
						//echo "value is not of wanted type";
					}//if
					else
					{
						//ALL OK
						//return the value trimmed
						$validated_vars[$key] = trim($_GET[$key]);
						return $validated_vars;
					}//
				}//
			}//else
		}//while
	}//if
	else
	{
		Redirects(0,"","");
		//echo "more values than the ones we want in the query string";
	}//
	######################
	######################
}//checkGetVariable
?>