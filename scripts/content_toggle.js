/*
*/

function prepareHiddenInformation() {
	if (!document.getElementsByTagName) return false;	// Checks to make sure this function exists. Halts execution of script if not
	if (!document.getElementById) return false;	// Checks to make sure this function exists. Halts execution of script if not
	if (!document.getElementsByClassName('hidden_content')) return false;	// Checks to make sure this function exists. Halts execution of script if not

	var $hidden_content_boxes = document.getElementsByClassName('hidden_content');	// Gets list of all elements with class of 'hidden_content'
	for (var $i=0; $i<$hidden_content_boxes.length; $i++) {	// loops through the whole list...
		var $hidden_content_box = $hidden_content_boxes[$i];	// allocates each element to a variable (for easier reading)
	}
}

// Executes the prepareHiddenInformation function when page loads
window.onload = function() {
	prepareHiddenInformation();
}


// Function to return a list of elements with a specific class attribute
document.getElementsByClassName = function($class) {
	var $results = Array();
	var $elements = document.getElementsByTagName("*");
	for (var $i=0; $i<$elements.length; $i++) {
		var $classes = $elements[$i].className.split(" ");
		for (var $j=0; $j<$classes.length; $j++) {
			if ($classes[$j] == $class) {
				$results[$results.length] = $elements[$i];
			}
		}
	}
	return $results;
}

function toggle_hidden_content($i, $button, $page)
{
	if( (document.getElementById($i).style.display == 'none') || (document.getElementById($i).style.display == ''))
	{
		if($page == 'preferencies'){ $button.innerHTML = 'hide'; }
		else if($page == 'loggin'){ $button.innerHTML = '.::Hide All::.'; }
		else if($page == 'reviews'){ $button.innerHTML = 'hide'; }
		else if($page == 'papers'){ $button.innerHTML = 'hide'; }
		else if($page == 'abstracts'){ $button.innerHTML = 'hide'; }
		else if($page == 'review_texts'){ $button.innerHTML = 'hide'; }
		else if($page == 'errors'){ $button.innerHTML = 'hide'; }
		document.getElementById($i).style.display = 'block';
	}
	else
	{
		if($page == 'preferencies'){ $button.innerHTML = 'view'; }
		else if($page == 'loggin'){ $button.innerHTML = '.::View All::.'; }
		else if($page == 'reviews'){ $button.innerHTML = 'view'; }
		else if($page == 'papers'){ $button.innerHTML = 'view'; }
		else if($page == 'abstracts'){ $button.innerHTML = 'view'; }
		else if($page == 'review_texts'){ $button.innerHTML = 'read'; }
		else if($page == 'errors'){ $button.innerHTML = 'view'; }
		document.getElementById($i).style.display = 'none';	
	}
}