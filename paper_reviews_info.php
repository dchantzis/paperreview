<?php
	###################################################################################
	header("Expires: Thu, 17 May 2001 10:17:17 GMT");    // Date in the past
  	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
	header ("Pragma: no-cache");                          // HTTP/1.0
	header ("Content-type: text/html; charset=utf-8");
	###################################################################################

	session_start(); //start session
	session_regenerate_id(true); //regenerate session id
	//regenerate session id if PHP version is lower thatn 5.1.0
	if(!version_compare(phpversion(),"5.1.0",">=")){ setcookie( session_name(), session_id(), ini_get("session.cookie_lifetime"), "/" );}

	require("./include/layoutfragmentsinc.php");
	require("./include/layoutinc.php"); 
	require("./include/functionsinc.php");
	require("./include/findpaperreviewsinc.php");
	require("./include/errorreportinc.php");

	whereUgo(0);
	whereUgo(1);
	
	$flg = "";
	$error = "";
	if (isset($_GET["flg"])) {$flg = $_GET["flg"];}
	$temp_msg = "<center>No reviews for this paper yet.</center>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>.::PaperReview::. <?php	if(!isset($_SESSION["administrator"])){echo " - " . strtoupper($_SESSION["conf_name"]);	}?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="./scripts/navigation.js"></script>
<script type="text/javascript" src="./scripts/content_toggle.js"></script>
<noscript><META HTTP-EQUIV="Refresh" CONTENT="1;URL=<?="./browsererrors.php?e=" . hash('sha256', "javascript")?>"></noscript>
<style type="text/css" media="screen">
	@import url(./scripts/allstyles.inc.css);
</style>
<link rel="stylesheet" rev="stylesheet" href="./scripts/print.inc.css" type="text/css" media="print" />
</head>

<body class="ulounge">

<?php layout_fragment_start(); ?>

<div class="paper_reviews_info">

	<div id="page_title">Review Info</div>
	<div id="spacer"></div>
	<div id="paper_reviews_info_content">
		<div class="dataTypeGroup">
		<fieldset> 
			<legend>
				Reviews for Paper: <? echo "<a href=\"./paper_info.php?paperid=" . $paper_id . "\" class=\"simple\">" . $paper_title . "</a>"; ?>
			</legend>
				
                
            <?php
				//if the user is the author of the paper, 
				//check from the conference options, if he is allowed to view reviews of his papers.
				if( ($_SESSION["logged_user_id"] == $author_id) && (!isset($_SESSION["chairman"])) )
				{	
					if($coptions1D["AVP"] == 0)
					{
						echo "<center><span class=\"red\">" . "Error: Authors are not allowed to view reviews of their papers." . "</span></center>";
						unset($array02);
						$temp_msg = "";
					}
				}
				if(isset($_SESSION["reviewer"]))
				{
					if($coptions1D["RVRP"] == 0)
					{
						echo "<center><span class=\"red\">" . "Error: Reviewers are not allowed to view reviews of their assigned papers by other reviewers." . "</span><center>";
						unset($array02);
						$temp_msg = "";
					}
				}
			?>    
			<?php 
				//all the arrays are filled from 'findpaperreviewsinc.php'
				//if '$array02' is not set, that means that none of the reviewers have
				//written their reviews. Naughty reviewers, very naughty indeed!!!
				if(isset($array02)) 
				{ 
			?>
			<?php
				//OK, the chairmen of this conference can view the names of the reviewers
				//and so do all the reviewers of the system. BUT reviewers that are ALSO
				//authors for this conference, ESPECIALLY if they are  the ones who submitted
				//the papers, should NEVER be allowed to view the names of the reviewers.
				//the id of the author is in the '$author_id' value
				if(isset($_SESSION["chairman"]))
				{
					echo "Reviewers: ";
					reset($referee);
					while (list($key01, $val01) = each($referee))
					{
						echo "<span class=\"reviewer\"><span class='red'>" . $key01 . "</span></span>" . ", ";
					}//while
					
					echo "<div class=\"field\">";
					reset($referee);
					echo "<div>" . "Referee Names:" . "</div>";
		
					echo "<ul>";
					$count=1;
					while (list($key01, $val01) = each($referee))
					{
						echo "<li>";
						echo "<span class=\"reviewer\">" . $key01 . "</span>" . ": ";					
						echo $referee[$key01]["referee_name"];
						echo "</li>";
		
						$count++;
					}//while
					echo "</ul>";
					echo "</div>";
				}
				elseif(isset($_SESSION["reviewer"]))
				{
					if( $_SESSION["logged_user_id"] != $author_id )
					{
						echo "Reviewers: ";
						reset($referee);
						while (list($key01, $val01) = each($referee))
						{
							echo "<span class=\"reviewer\"><span class='red'>" . $key01 . "</span></span>" . ", ";
						}//while
						
						echo "<div class=\"field\">";
						reset($referee);
						echo "<div>" . "Referee Names:" . "</div>";
		
						echo "<ul>";
						$count=1;
						while (list($key01, $val01) = each($referee))
						{
							echo "<li>";
							echo "<span class=\"reviewer\">" . $key01 . "</span>" . ": ";					
							echo $referee[$key01]["referee_name"];
							echo "</li>";
		
							$count++;
						}//while
						echo "</ul>";
						echo "</div>";
					}//inner if
				}//outer if
				?>
				<?php
				echo "<div class=\"field\">";
				echo "<div>" . "Score:" . " <center><a href=\"#rguide\" class=\"simple\" >what do these values mean?</a></center>" . "</div>";
				echo "\n\t<table cellpadding=\"2\" cellspacing=\"2\" class=\"score\">";
				echo "\n<thead>\n\t<tr>
							<th scope=\"col\" class=\"reviewer\"></th>
							<th scope=\"col\">Originality</th>
							<th scope=\"col\">significance</th>
							<th scope=\"col\">Quality</th>
							<th scope=\"col\">Relevance</th>
							<th scope=\"col\">Presentation</th>
							<th scope=\"col\">Expertise</th>
							<th scope=\"col\" class=\"overall\">Overall</th>
						</tr>\n</thead>";
				echo "<tbody>";
				reset($score);
				$count=1;
				while (list($key01, $val01) = each($score))
				{
					if (($count%2) == 0) { 
						$bgColor = "#F5F0EA";
						$trClass = "even"; 
					} else { 
						$bgColor = "#FFFFFF";
						$trClass = "odd";
					}//else

					echo "\n\t\t<tr bgcolor=\"" . $bgColor . "\" onMouseOver=\"this.className='highlight'\" onMouseOut=\"this.className='" . $trClass . "'\">";
							
					echo "\n\t\t<td class=\"reviewer\">";
					//show the reviewers name only if the logged_in user is a chairman or a reviewer
					if(isset($_SESSION["chairman"]) || isset($_SESSION["reviewer"])){ echo "<span class=\"reviewer\">" . $key01 . "</span>" . ": ";}
					else{ echo "<span class=\"reviewer\">" . "Reviewer " . $count . "</span>" . ": ";}
					echo "</td>";
							
					echo "\n\t\t<td>" . $score[$key01]["originality"] . "</td>";
					echo "\n\t\t<td>" . $score[$key01]["significance"] . "</td>";
					echo "\n\t\t<td>" . $score[$key01]["quality"] . "</td>";
					echo "\n\t\t<td>" . $score[$key01]["relevance"] . "</td>";
					echo "\n\t\t<td>" . $score[$key01]["presentation"] . "</td>";
					echo "\n\t\t<td>" . $score[$key01]["expertise"] . "</td>";
					echo "\n\t\t<td>" . $score[$key01]["overall"] . "</td>";

					echo "\n\t</tr>";
					$count++;
				}//while
				echo "</tbody>";
				echo "\n\t</table>";
				echo "</div>";
				?>

				<?php
				if(isset($_SESSION["chairman"]))
				{
					echo "<div class=\"field\">";
					reset($confidential);
					echo "<div>" . "Confidential: " . "</div>";
					echo "<center>" . "<a onClick=\"toggle_hidden_content('confidential', this, 'review_texts');\" class=\"simple\">read</a>" . "</center>";
					$count=1;
					echo "<ul class=\"textwrap\" id=\"confidential\">";
					while (list($key01, $val01) = each($confidential))
					{
						echo "<li>";
						//show the reviewers name only if the logged_in user is a chairman or a reviewer
						if(isset($_SESSION["chairman"]) || isset($_SESSION["reviewer"])){ echo "<span class=\"reviewer\">" . $key01 . " wrote" . "</span>" . ": ";}
						else{ echo "<span class=\"reviewer\">" . "Reviewer " . $count . " wrote" . "</span>" . ": "; }
					 
						if(count($confidential[$key01])!=0) {echo "<div class=\"txt\"><pre>" . $confidential[$key01] . "</pre></div>";}				
						$count++;
						echo "</li>";
					}//while
					echo "</ul>";
					echo "</div>"; //field
				}//for
				?>
				<?php
				echo "<div class=\"field\">";
				reset($contributions);
				echo "<div>" . "Contributions: " . "</div>";
				echo "<center>" . "<a onClick=\"toggle_hidden_content('contributions', this, 'review_texts');\" class=\"simple\">read</a>" . "</center>";
				$count=1;
				echo "<ul class=\"textwrap\" id=\"contributions\">";
				while (list($key01, $val01) = each($contributions))
				{
					echo "<li>";
					//show the reviewers name only if the logged_in user is a chairman or a reviewer
					if(isset($_SESSION["chairman"]) || isset($_SESSION["reviewer"])){ echo "<span class=\"reviewer\">" . $key01 . " wrote" . "</span>" . ": ";}
					else{ echo "<span class=\"reviewer\">" . "Reviewer " . $count . " wrote" . "</span>" . ": ";}
				
					if(count($contributions[$key01])!=0) {echo "<div class=\"txt\"><pre>" . $contributions[$key01] . "</pre></div>";}				
					$count++;
					echo "</li>";
				}//while
				echo "</ul>";
				echo "</div>";
				?>

			<?php
			echo "<div class=\"field\">";
				reset($positive);
				echo "<div>" . "Positive: " . "</div>";
				echo "<center>" . "<a onClick=\"toggle_hidden_content('positive', this, 'review_texts');\" class=\"simple\">read</a>" . "</center>";
				$count=1;
				echo "<ul class=\"textwrap\" id=\"positive\">";
				while (list($key01, $val01) = each($positive))
				{
					echo "<li>";
					//show the reviewers name only if the logged_in user is a chairman or a reviewer
					if(isset($_SESSION["chairman"]) || isset($_SESSION["reviewer"])){ echo "<span class=\"reviewer\">" . $key01 . " wrote" . "</span>" . ": ";}
					else{ echo "<span class=\"reviewer\">" . "Reviewer " . $count . " wrote" . "</span>" . ": ";}
				
					if(count($positive[$key01])!=0) {echo "<div class=\"txt\"><pre>" . $positive[$key01] . "</pre></div>";}				
					$count++;
					echo "</li>";
				}//while
				echo "</ul>";
			echo "</div>";
				?>
				<?php
			echo "<div class=\"field\">";
				reset($negative);
				echo "<div>" . "Negative: " . "</div>";
				echo "<center>" . "<a onClick=\"toggle_hidden_content('negative', this, 'review_texts');\" class=\"simple\">read</a>" . "</center>";
				$count=1;
				echo "<ul class=\"textwrap\" id=\"negative\">";
				while (list($key01, $val01) = each($negative))
				{
					echo "<li>";
					//show the reviewers name only if the logged_in user is a chairman or a reviewer
					if(isset($_SESSION["chairman"]) || isset($_SESSION["reviewer"])){ echo "<span class=\"reviewer\">" . $key01 . " wrote" . "</span>" . ": ";}
					else{ echo "<span class=\"reviewer\">" . "Reviewer " . $count . " wrote" . "</span>" . ": ";}
				
					if(count($negative[$key01])!=0) {echo "<div class=\"txt\"><pre>" . $negative[$key01] . "</pre></div>";}				
					$count++;
					echo "</li>";
				}//while
				echo "</ul>";
			echo "</div>";
				?>

				<?
			echo "<div class=\"field\">";
				reset($further);
				echo "<div>" . "Further: " . "</div>";
				echo "<center>" . "<a onClick=\"toggle_hidden_content('further', this, 'review_texts');\" class=\"simple\">read</a>" . "</center>";
				$count=1;
				echo "<ul class=\"textwrap\" id=\"further\">";
				while (list($key01, $val01) = each($further))
				{
					echo "<li>";
					//show the reviewers name only if the logged_in user is a chairman or a reviewer
					if(isset($_SESSION["chairman"]) || isset($_SESSION["reviewer"])){ echo "<span class=\"reviewer\">" . $key01 . " wrote" . "</span>" . ": ";}
					else{ echo "<span class=\"reviewer\">" . "Reviewer " . $count . " wrote" . "</span>" . ": ";}
		
					if(count($further[$key01])!=0) {echo "<div class=\"txt\"><pre>" . $further[$key01] . "</pre></div>";}
					$count++;
					echo "</li>";
				}//while
				echo "</ul>";
				echo "</div>";
			?>
			<? 
				}//if(isset($array02))
				else	
				{
					echo $temp_msg;
				}//else
			?>
		</fieldset>
		</div><!--dataTypeGroup-->
         
        <div id="rguide"> 
            Review guide for fields: 
            <b>originality</b>, <b>significance</b>, <b>quality</b>, 
            <b>relevance</b>, <b>presentation</b>, <b>overall</b>
            <br /><br />
            <ul>
                <li>7: Strong Accept (award quality)</li>
                <li>6: Accept (I will argue for this paper)</li>
                <li>5: Weak Accept (vote accept, but won't object)</li>
                <li>4: Neutral (not impressed, won't object)</li>
                <li>3: Weak Reject (vote reject, but won't object)</li>
                <li>2: Reject (I will argue against this paper)</li>
                <li>1: Strong Reject</li>
            </ul>
        </div>
        <center><div class="print_button" onClick="javascript:if (window.print) window.print();" title="print page"></div></center>
	</div><!--paper_reviews_info_content-->
	<div id="bottomspacer"><a href="#wrapper" title="Return to top">return to top</a></div>
</div><!--paper_reviews_info-->
				
<?php layout_fragment_end(); ?>

<script type="text/javascript">
//ddtreemenu.createTree(treeid, enablepersist, opt_persist_in_days (default is 1))
ddtreemenu.createTree("navi", true);
</script>
</body>
</html>
