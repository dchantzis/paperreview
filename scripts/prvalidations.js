// JavaScript Document

	function SubmitForm(id) {
		var form = document.forms[id];
		var bRequired = true;
		if ((form.fname.value.length < 1) ||
		(form.lname.value.length < 1) ||
		(form.email.value.length < 1) ||
		(form.password.value.length < 1) ||
		(form.repassword.value.length < 1) ||
		(form.address_01.value.length < 1) ||
		(form.phone_01.value.length < 1) ||
		(form.security_question.selectedIndex < 1) ||
		(form.security_answer.value.length<1) ||
		(form.birthday_month.selectedIndex<1) ||
		(form.birthday_day.value.length<1) ||
		(form.birthday_year.value.length<1)) {
			alert("Please fill out all the required fields.");
			bRequired = false;
		}
		//
		if (!bRequired) return false;
		
		if(passwordValidation(form.password.value, form.fname.value, form.lname.value, form.security_answer.value)!=-1)
		{
			form.submit();
		}
	}//
	
	
function passwordValidation(password, fname, lname, security_answer){
 if(password.length == 0){
    alert("Please choose a password.");
    return -1;
  } else if (password.length > 15) {
    alert("Your new password must be no greater than 15 characters.");
    return -1;
  }else if (password.length < 6) {
    alert("Your new password must be at least 6 characters.");
    return  -1;
  }else if ((fname.length > 2) && (password.indexOf(fname) >= 0) ){
    alert("Your new password is too similar to your first name.");
	return -1;
  } else if ( (lname.length > 2) && (password.indexOf(lname) >=0) ){
    alert("Your new password is too similar to your last name.");
    return -1;
  } else if ( security_answer == password ){
    alert("Your Security Answer is too similar to your password.\nPlease choose a different answer for your Security Question.");
    return -1;
  } else if ( password.value != repassword.value ) {
  	alert("You didn't retype your password correctly.");
	return -1;
  } else {
    return 0;
  }
}

function emailValidation(email)
{
	var regexp = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
	var matches = regexp.exec(email.value);
	if (!matches && (email.value.length >= 1)) {
			email.value="";
			alert("This in not a valid email address.");
			document.refresh;
		} 
}


function yearValidation(year)
{
	var regexp = /[^0-9]/;
	var matches = regexp.exec(year.value);
	if (matches)
	{
		year.value="";
		alert("Type only numbers");
		document.refresh;
	}
	if ((year.value.length > 1) && (year.value.length < 4)) {
			year.value="";
			alert("Enter 4 digits for the year");
			document.refresh;
	}

}

function dayValidation(day)
{
	var regexp = /[^0-9]/;
	var matches = regexp.exec(day.value);
	if (matches)
	{
		day.value="";
		alert("Type only numbers");
		document.refresh;
	}
}

function phoneValidation(phone_number)
{
	var regexp = /[^0-9]/;
	var matches = regexp.exec(phone_number.value);
	if (matches)
	{
		phone_number.value="";
		alert("Type only numbers");
		document.refresh;
	}
}

function websiteValidation(website){
		var regexp = /(http:\/\/)?([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
		var matches = regexp.exec(website.value)
		if (!matches && (website.value.length > 1)) {
				website.value="";
				alert("This is not a valid website address");
				document.refresh;
		}
}

function SAnswerValidation()
{
	var form = document.forms[0];
	if((form.security_answer.value.length > 1) && (form.security_answer.value.length < 4))
	{
		form.website.value="";
		alert("Your answer must be at least 4 characters long.");
		document.refresh;
	}
}


function confirmChoice() {
	question = confirm("Are you sure you want to decline the Terms of Service?\nClick Cancel to continue with registration."); 
	if (question == true) { 
		var newlocation = './login.php';
		//newlocation+='';
		//newlocation+=''; 
		location = newlocation;
	}
}

function check_password(password)
{
	  if(password.length == 0){
		alert("Please choose a password.");
		return -1;
	  } else if (password.length > 15) {
		alert("Your new password must be no greater than 15 characters.");
		return -1;
	  }else if (password.length < 6) {
		alert("Your new password must be at least 6 characters.");
		return  -1;
	  }
}

function SubmitForm2(form) {

	var bRequired = true;
	if ((form.fname.value.length < 1) ||
		(form.lname.value.length < 1) ||
		(form.address_01.value.length < 1) ||
		(form.phone_01.value.length < 1) ||
		(form.security_answer.value.length<1)) 
	{
		alert("Please fill out all the required fields.");
		bRequired = false;
	}//
	if (!bRequired) return false;

	form.submit();
}//

function numberValidation(NORPC)
{
	var regexp = /[^0-9]/;
	var matches = regexp.exec(NORPC.value);
	if (matches)
	{
		NORPC.value="";
		alert("Type only numbers");
		document.refresh;
	}
}//

function conference_options_restriction(checkbox)
{
	switch(checkbox)
	{
		case "UDP":
				if(document.getElementById('UDP').checked == false){} //do nothing
				else if(document.getElementById("UDP").checked == true){document.getElementById("UVP").checked = true; }
			break;
		case "UDAP":
				if(document.getElementById('UDAP').checked == false){} //do nothing
				else if(document.getElementById("UDAP").checked == true){document.getElementById("UVAP").checked = true; }
			break;
	}//switch
}//
