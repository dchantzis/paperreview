function makeSlider () {	
	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('track');
	Element.hide('level_of_interest');
	
	$('value').innerHTML = document.getElementById('level_of_interest').value;
	$('level_of_interest').value = document.getElementById('level_of_interest').value;
	
	var timeout;	
	
	new Control.Slider('handle', 'track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			$('value').innerHTML = getValue(v);
			$('level_of_interest').value = getValue(v);
		}//onChange
	});
				
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue

}//makeSlider

Event.observe(window, 'load', makeSlider, false);
