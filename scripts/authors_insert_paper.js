// JavaScript Document

//NOTE: this is used WITH authors_insert_paper.js
//Please include this file in the <head> tag of 'papers.php'

var num_of_items = 0;

//removes spaces from the left and the right of the string
String.prototype.trim = function() 
{
	return this.replace(/^\s+|\s+$/g,"");
}//trim

//removes spaces from the left of the string\
String.prototype.ltrim = function() 
{
	return this.replace(/^\s+/,"");
}//ltrim

//removes spaces from the right of the string
String.prototype.rtrim = function() 
{
	return this.replace(/\s+$/,"");
}//rtrim
  

//addAuthor()
function addAuthor()
{
	document.lastfrm.authorslist.disabled = false;

	//get the text values
	author_first_name = document.getElementById("authorfname").value.toLowerCase();
	author_first_name = author_first_name.trim(); //trim the spaces from beginning and ending of string
	author_last_name = document.getElementById("authorlname").value.toLowerCase();
	author_last_name = author_last_name.trim(); //trim the spaces from beginning and ending of string

	//check if both text fields are filled
	empty=0;
	if( (author_first_name == "") || (author_last_name == "")){ empty=1; }

	if(empty == 1)
	{
		//do nothing
		return false;
	}
	else if(empty == 0)
	{
		//ok!
			
		//check if an author with this name already exists in the authors textarea and hidden input
		old_authors_list = document.getElementById("authors").value;
		brokenstring=old_authors_list.split(", ");
	
		author_full_name_type01 = author_first_name + " " + author_last_name; //eg. john doe
		author_full_name_type02 = author_last_name + " " + author_first_name; //eg. doe john
		
		flag = 0;
		for(i=0; i<brokenstring.length; i++)
		{
	
			if(author_full_name_type01 == brokenstring[i]){ flag=1; }
			if(author_full_name_type02 == brokenstring[i]){ flag=1; }
	
			if(flag == 1)
			{ 
				alert ("This person is already an author.");
				break;
			}//if
		}//for
	
		if(flag == 0)
		{
			//add this author to the authors list

			var txt = author_full_name_type01; //what would be saved in the elementID and thus in the DB

			//set the value of the hidden field which will be sent with the form to the DB
			document.getElementById("authors").value = old_authors_list + txt + ", ";
			//trim the spaces from beginning and ending of string
			document.getElementById("authors").value = 	document.getElementById("authors").value.trim(); 
			
			
			//load the 'authorslist' select box with all the authors names
			loadAuthList();
			
			//clear the fields
			document.getElementById("authorfname").value = "";
			document.getElementById("authorlname").value = "";

			return true;
		}//if
		else if (flag == 1)
		{
			//clear the fields
			document.getElementById("authorfname").value = "";
			document.getElementById("authorlname").value = "";
			return true;
		}//else if
	}//else if
}//addAuthor()

//deleteAuthor()
function deleteAuthor() {
	var contSearch = 1;
	var selIndex;
			  
	// check to see if there is at least one item selected
	if (document.lastfrm.authorslist.selectedIndex == -1) {
		alert("No entries selected for deletion.");
		return true;
	}
			  
	// loop through all selected items and delete selected
	while (contSearch > 0) {
		selIndex = document.lastfrm.authorslist.selectedIndex;
	
		if (selIndex >= 0) { 
			old_authors = document.getElementById("authors").value;
			author_to_remove =  document.lastfrm.authorslist.options[selIndex].value + ", "; 
			//remove from combo box
			document.lastfrm.authorslist.options[selIndex] = null;
			
			
			//remove from hidden field
			//call function 'remove' to remove the deleted authorname from the hidden field 'authors'

			//the author_to_remove string is like this "1 james doe", so in order to remove it from the hidden element 'authors',
			//we first have to remove the 2 first characters from it, which include the 'number' and the 'space' characters.
			//If these 2 characters are not removed, then 'author_to_remove' will not be found in the old_authors list to be 
			// removed. This happens in the 'remove' function.						
			author_to_remove = author_to_remove.substring(2,350);

			new_authors = remove(old_authors, author_to_remove);
			document.getElementById("authors").value = new_authors;
			--num_of_items;
		}//if
		else 
			contSearch = 0;
		}//else
	
	placeAuthorsInOrder(new_authors);	
	
	return true;              
}//deleteAuthor()

//clearAuthors()
function clearAuthors()
{
	document.lastfrm.authorslist.length = 0;
	num_of_items = 0;
	document.lastfrm.authorslist.value = "";
	document.getElementById("authors").value = "";   
	return true;
}//clearAuthors()

//remove(old_string, string_to_remove)
function remove(old_string, string_to_remove)
{
	pointer = old_string.indexOf(string_to_remove); //find the first index of the token string to be removed.
//alert (pointer);
	new_string = "";
	if (pointer == -1){ return old_string ; }//if token string wasn't found in old_string
	else  
	{
		part01 = old_string.substring(0,pointer);
		part02 = old_string.substring(pointer+string_to_remove.length);
		part03 = remove(part02, string_to_remove);
		new_string += part01 + part03;
	}//else
	
	return new_string;
}//remove(old_string, string_to_remove)


function placeAuthorsInOrder(new_authors_list)
{

		clearAuthors(); //delete all the auhtors list, and the authors hidden field 

		brokenstring=new_authors_list.split(", ");

		for(i=0; i<brokenstring.length; i++)
		{				
				if( i == (brokenstring.length-1) )
				{
					break;
				}//if
				else
				{
					var txt = (i+1) + " " + brokenstring[i];
					
					addOption = new Option(txt,txt);
					document.lastfrm.authorslist.options[num_of_items++] = addOption;
				}//else
		}//for
		document.getElementById("authors").value = new_authors_list;
}//placeAuthorsInOrder(new_authors)

//this function is used to load all the authors from the 'authors' hidden field to the 'authorslist' select box 
function loadAuthList() 
{
	//clear the select authorslist
	document.lastfrm.authorslist.length = 0;
	num_of_items = 0;
	document.lastfrm.authorslist.value = "";
	
	//first, explode the  string from 'authors' hidden field
	old_authors_list = document.getElementById("authors").value + " !ghostvalue!";

	brokenstring=old_authors_list.split(", ");
	//then insert each author in the 'authorslist' selection box
	for(i=0; i<brokenstring.length; i++)
	{
		var txt = brokenstring[i];
		
		if(txt == "!ghostvalue!")
		{
			document.getElementById("authors").value = remove(old_authors_list, "!ghostvalue!");
			continue;
		}//if
	
		var txt = (i+1) + " " +txt;

		addOption = new Option(txt,txt);
		document.lastfrm.authorslist.options[num_of_items++] = addOption;
	}//
	document.getElementById("update_authors").value = 0;
}//loadAuthLIst();

//////VERY IMPORTANT FUNCTION//////////
function loadAuthors() 
{
		var num_of_items = 0;
		
		//if the user is creating a new paper, then this value is 0 and the 'authors' hidden field is empty
		
		if (document.getElementById("update_authors").value == 0 || document.getElementById("update_authors").value == "")
		{
			document.lastfrm.authorslist.disabled = true;
			//do nothing
			
			//the following lines of code, are used to make to add automatically the logged_in_author, as an
			//author of the paper. His name is added in the "authors" hidden field, as well as the "authorslist"
			document.getElementById("authors").value = document.getElementById("logged_in_author").value + ", "; 
			var txt = "1 " + document.getElementById("logged_in_author").value;
			addOption = new Option(txt,txt);
			document.lastfrm.authorslist.options[num_of_items++] = addOption;
		}//if
		
		//if the user is updating a paper, then this value is 1 and the 'authors' hidden field is not empty.
		
		//So we have to fill the 'authorslist' selection box the first time the page is loaded ONLY.
		else if(document.getElementById("update_authors").value == 1) 
		{
			loadAuthList();		
		}//else if
		
}//loadAuthors()


//the value "direction" can either be 'up' or 'down'
function moveAuthor(direction)
{
	var contSearch = 1;
	var selIndex;
	var old_authors_order;
	var number_of_authors_in_list;
	var temp;
	var temp_auth_list;
	var new_selIndex;
			 
	//alert (document.lastfrm.authorslist.options[document.lastfrm.authorslist.selectedIndex].value); 
	// check to see if there is at least one item selected
	if (document.lastfrm.authorslist.selectedIndex == -1) {
		alert("No entries selected for deletion.");
		return true;
	}
	  
	// loop through all selected items and delete selected
	while (contSearch > 0) 
	{
		//this is the selected authors' position in the list
		selIndex = document.lastfrm.authorslist.selectedIndex;
			
		old_authors_order = document.getElementById("authors").value;
		author_to_moveA =  document.lastfrm.authorslist.options[selIndex].value + ", "; 
		author_to_moveB =  document.lastfrm.authorslist.options[selIndex].value; 
		brokenstring=old_authors_order.split(", ");			
		number_of_authors_in_list = brokenstring.length-1;
			
		if (selIndex >= 0) 
		{ 
			switch (direction)
			{
				case "up":
					if(number_of_authors_in_list == 0){return -1;}//the are no authors in the list so we can't select nor move anything
					else if(selIndex == 0){return -1;}//the selected author to move, is already the first in order
					else {
						new_selIndex = (selIndex-1);
						
						//swaping authors in brokenstring array
						//the result of this swap is the authors in wanted order
									
						temp = brokenstring[(selIndex-1)];
						brokenstring[(selIndex-1)] = author_to_moveB.substring(2,350);
						brokenstring[selIndex] = temp;
									
						//now copy the authors in their correct order to the 'authorslist' and the 'authors' hidden field
						//first clear both 'authorslist' and 'authors'
						clearAuthors();

						//load the values in the 'brokenstring' array to the 'authors' hidden field													
						for(i=0; i<(brokenstring.length-1); i++)
						{
							document.getElementById("authors").value += brokenstring[i] + ", ";
						}//for											
									
						//trim the spaces from beginning and ending of string
						document.getElementById("authors").value = 	document.getElementById("authors").value.trim(); 							
								
						loadAuthList();
					}//else												
					break;
				case "down": 
					if(number_of_authors_in_list == 0){return -1;}//the are no authors in the list so we can't select nor move anything
					else if(selIndex == (number_of_authors_in_list-1)){return -1;}//the selected author to move, is already the last in order												
					else {
						new_selIndex = (selIndex+1);
									
						//swaping authors in brokenstring array
						//the result of this swap is the authors in wanted order
						temp = brokenstring[(selIndex+1)];
						brokenstring[(selIndex+1)] = author_to_moveB.substring(2,350);
						brokenstring[selIndex] = temp;
 
						//now copy the authors in their correct order to the 'authorslist' and the 'authors' hidden field
						//first clear both 'authorslist' and 'authors'
						clearAuthors();

						//load the values in the 'brokenstring' array to the 'authors' hidden field													
						for(i=0; i<(brokenstring.length-1); i++)
						{
							document.getElementById("authors").value += brokenstring[i] + ", ";
						}//for											
									
						//trim the spaces from beginning and ending of string
						document.getElementById("authors").value = 	document.getElementById("authors").value.trim(); 
								
						loadAuthList();
					}//else							
					break;
				default:
					//do nothing
					break;
			}//switch
					
			return -1; 					
					
		}//if
		else 
		{
			contSearch = 0;
		}//else
	}//while
}//moveAuthor(direction)