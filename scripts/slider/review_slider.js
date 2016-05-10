// JavaScript Document

function makeOriginalitySlider () {	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('originality_track');
	Element.hide('originality');
	
	$('originality_value').innerHTML = document.getElementById('originality').value;
	$('originality').value = document.getElementById('originality').value;
	
	var timeout;
	
	new Control.Slider('originality_handle', 'originality_track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('originality_value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			
			$('originality_value').innerHTML = getValue(v);
			$('originality').value = getValue(v);
				
		}//onChange
	});
	
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue
}//makeOriginalitySlider

function makeSignificanceSlider () {	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('significance_track');
	Element.hide('significance');

	$('significance_value').innerHTML = document.getElementById('significance').value;
	$('significance').value = document.getElementById('significance').value;

	var timeout;
	
	new Control.Slider('significance_handle', 'significance_track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('significance_value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			
			$('significance_value').innerHTML = getValue(v);
			$('significance').value = getValue(v);
				
		}//onChange
	});
	
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue
}//makeSignificanceSlider


function makeQualitySlider () {	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('quality_track');
	Element.hide('quality');
	
	$('quality_value').innerHTML = document.getElementById('quality').value;
	$('quality').value = document.getElementById('quality').value;
	
	var timeout;
	
	new Control.Slider('quality_handle', 'quality_track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('quality_value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			
			$('quality_value').innerHTML = getValue(v);
			$('quality').value = getValue(v);
				
		}//onChange
	});
	
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue
}//makeQualitySlider

function makeRelevanceSlider () {	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('relevance_track');
	Element.hide('relevance');

	$('relevance_value').innerHTML = document.getElementById('relevance').value;
	$('relevance').value = document.getElementById('relevance').value;

	var timeout;
	
	new Control.Slider('relevance_handle', 'relevance_track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('relevance_value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			
			$('relevance_value').innerHTML = getValue(v);
			$('relevance').value = getValue(v);
				
		}//onChange
	});
	
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue
}//makeRelevanceSlider

function makePresentationSlider () {	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('presentation_track');
	Element.hide('presentation');
	
	$('presentation_value').innerHTML = document.getElementById('presentation').value;
	$('presentation').value = document.getElementById('presentation').value;
	
	var timeout;
	
	new Control.Slider('presentation_handle', 'presentation_track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('presentation_value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			
			$('presentation_value').innerHTML = getValue(v);
			$('presentation').value = getValue(v);
				
		}//onChange
	});
	
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue
}//makePresentationSlider

function makeOverallSlider () {	
	// Removes input named 'slider' and shows div named 'track'
	Element.show('overall_track');
	Element.hide('overall');

	$('overall_value').innerHTML = document.getElementById('overall').value;
	$('overall').value = document.getElementById('overall').value;

	var timeout;
	
	new Control.Slider('overall_handle', 'overall_track', {
		range: $R(1,7),
		onSlide: function (v) { 
			$('overall_value').innerHTML = getValue(v);
			clearTimeout(timeout);
		},
		onChange: function (v) { 
			
			clearTimeout(timeout);
			timeout = setTimeout( function () {
					//$('callback').innerHTML = "You entered: <b>" + getValue(v) + " <b>";
				}, 500);
			
			$('overall_value').innerHTML = getValue(v);
			$('overall').value = getValue(v);
				
		}//onChange
	});
	
	function getValue (input) {
		return Math.round(input); //return Math.round(input)/2;
	}//getValue
}//makePresentationSlider

Event.observe(window, 'load', makeOriginalitySlider, false);
Event.observe(window, 'load', makeSignificanceSlider, false);
Event.observe(window, 'load', makeQualitySlider, false);
Event.observe(window, 'load', makeRelevanceSlider, false);
Event.observe(window, 'load', makePresentationSlider, false);
Event.observe(window, 'load', makeOverallSlider, false);

