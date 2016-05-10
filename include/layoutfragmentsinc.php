<?php
##################################################
################layoutfragmentsinc.php############
##################################################
/*
This file includes 2 functions which are used to display
html code that is common to all the pages of the system.
Additional functions are included in the file 'layoutinc.php',
but are separated for clarity purposes.
*/

//INCLUDES THE FOLLOWING FUNCTIONS
/*
layout_fragment_start(),
layout_fragment_end()
*/
?>
<?php function layout_fragment_start() { ?>	
	<?php layout_conf_search_form(); ?>
	<div id="wrapper">
		<div id="skiplink"><a href="#content" title="skip to content of page" accesskey="q">skip to content</a></div>
		<div id="masthead">PAPER <div class="red">REVIEW</div></div>	
		
			<div id="user">
				<div id="userData">
					<?php echo "USER ~ " . strtoupper($_SESSION["logged_user_fname"]) . "\t" . strtoupper($_SESSION["logged_user_lname"]); ?>
				</div><!--userData-->
                                
                                <?php
                                    if(isset($_SESSION["administrator"])) {
                                ?>
                                    <div id="userOptions" class="userOptionsAdmin">
                                        <ul>
                                            <li><a href="./include/functionsinc.php?type=2" title="logout">logout</a></li>
                                        </ul>
                                    </div>
                                <?php
                                    } elseif(!isset($_SESSION["administrator"])) {
                                ?>
                                <div id="userOptions">
                                    <ul>
                                        <li id='profilelink'>
                                            <a href="./include/functionsinc.php?type=14" title="User Profile">profile</a>
                                        </li>
                                        <li><a href="./include/functionsinc.php?type=2" title="logout">logout</a></li>
                                    </ul><!--userOptions-->
                                </div><!--userOptions-->
                                <?php 
                                    }
                                ?>
			</div><!--user-->
	
			<!--image wrapper background bars -->
			<div id="topbar"></div>	
			<div id="leftbar"></div>
			<div id="rightbar"></div>
			<!--image wrapper background bars -->
	
			<div id="logo">logo</div>
		
			<!--call to function displayUserMenus to display the menus for the user -->
			<?php displayUserMenus(); ?>
	
		<div id="content">
			<!--image content background bars -->
			<div id="contenttopbar"></div>
			<div id="contentleftbar"></div>
	
			<div id="datetime"><? echo date('l\, dS \of F Y'); ?><!--example: Thursday, 06th of September 2007--></div>
<?php }//layout_fragment_start() ?>
<?php function layout_fragment_end() { ?>
		<div id="separator"></div>
		<div id="extraColumn">extraColumn</div>
	</div><!--content-->

	<div id="footer">footer</div>
</div><!--wrapper-->
<?php }//layout_fragment_end() ?>