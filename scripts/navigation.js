var persisteduls=new Object()
var ddtreemenu=new Object()

ddtreemenu.closefolder="./images/navigation/plus.gif" //set image path to "closed" folder image
ddtreemenu.openfolder="./images/navigation/minus.gif" //set image path to "open" folder image

function new_window(destination)
{
	window.open("./help.php","_blank","fullscreen=no, width=900, height=430, status=no, toolbar=no,menubar=no,resizable=no,scrollbars=yes");
}

//////////No need to edit beyond here///////////////////////////



ddtreemenu.createTree=function(treeid, enablepersist, persistdays){
var ultags=document.getElementById(treeid).getElementsByTagName("ul")
if (typeof persisteduls[treeid]=="undefined")
persisteduls[treeid]=(enablepersist==true && ddtreemenu.getCookie(treeid)!="")? ddtreemenu.getCookie(treeid).split(",") : ""
for (var i=0; i<ultags.length; i++)
ddtreemenu.buildSubTree(treeid, ultags[i], i)
	if (enablepersist==true){ //if enable persist feature
		var durationdays=(typeof persistdays=="undefined")? 1 : parseInt(persistdays)
		ddtreemenu.dotask(window, function(){ddtreemenu.rememberstate(treeid, durationdays)}, "unload") //save opened UL indexes on body unload
	}//if
}//ddtreemenu.createTree

ddtreemenu.buildSubTree=function(treeid, ulelement, index){
	ulelement.parentNode.className="submenu"
		if (typeof persisteduls[treeid]=="object"){ //if cookie exists (persisteduls[treeid] is an array versus "" string)
			if (ddtreemenu.searcharray(persisteduls[treeid], index)){
				ulelement.setAttribute("rel", "open")
				ulelement.style.display="block"
				ulelement.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
			}//if
			else
				ulelement.setAttribute("rel", "closed")
		} //end cookie persist code
		else if (ulelement.getAttribute("rel")==null || ulelement.getAttribute("rel")==false) //if no cookie and UL has NO rel attribute explicted added by user
			ulelement.setAttribute("rel", "closed")
		else if (ulelement.getAttribute("rel")=="open") //else if no cookie and this UL has an explicit rel value of "open"
			ddtreemenu.expandSubTree(treeid, ulelement) //expand this UL plus all parent ULs (so the most inner UL is revealed!)
			ulelement.parentNode.onclick=function(e){
			var submenu=this.getElementsByTagName("ul")[0]
			if (submenu.getAttribute("rel")=="closed"){
				submenu.style.display="block"
				submenu.setAttribute("rel", "open")
				ulelement.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
			}//if
		else if (submenu.getAttribute("rel")=="open"){
			submenu.style.display="none"
			submenu.setAttribute("rel", "closed")
			ulelement.parentNode.style.backgroundImage="url("+ddtreemenu.closefolder+")"
		}//if
		ddtreemenu.preventpropagate(e)
	}
	ulelement.onclick=function(e){
		ddtreemenu.preventpropagate(e)
	}//ulelement.onclick
}//ddtreemenu.buildSubTree

ddtreemenu.expandSubTree=function(treeid, ulelement){ //expand a UL element and any of its parent ULs
	var rootnode=document.getElementById(treeid)
	var currentnode=ulelement
	currentnode.style.display="block"
	currentnode.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
	while (currentnode!=rootnode){
		if (currentnode.tagName=="UL"){ //if parent node is a UL, expand it too
			currentnode.style.display="block"
			currentnode.setAttribute("rel", "open") //indicate it's open
			currentnode.parentNode.style.backgroundImage="url("+ddtreemenu.openfolder+")"
		}//if
		currentnode=currentnode.parentNode
	}//while
}//ddtreemenu.expandSubTree

ddtreemenu.flatten=function(treeid, action){ //expand or contract all UL elements
	var ultags=document.getElementById(treeid).getElementsByTagName("ul")
	for (var i=0; i<ultags.length; i++){
		ultags[i].style.display=(action=="expand")? "block" : "none"
		var relvalue=(action=="expand")? "open" : "closed"
		ultags[i].setAttribute("rel", relvalue)
		ultags[i].parentNode.style.backgroundImage=(action=="expand")? "url("+ddtreemenu.openfolder+")" : "url("+ddtreemenu.closefolder+")"
	}//for
	if (action == 'expand')
	{
		document.getElementById('expand').style.visibility = "hidden";
		document.getElementById('contract').style.visibility = "visible";
	}//if
	else if (action == 'contract')
	{
		document.getElementById('expand').style.visibility = "visible";
		document.getElementById('contract').style.visibility = "hidden";
	}//else if
}//ddtreemenu.flatten

ddtreemenu.rememberstate=function(treeid, durationdays){ //store index of opened ULs relative to other ULs in Tree into cookie
	var ultags=document.getElementById(treeid).getElementsByTagName("ul")
	var openuls=new Array()
		for (var i=0; i<ultags.length; i++){
			if (ultags[i].getAttribute("rel")=="open")
				openuls[openuls.length]=i //save the index of the opened UL (relative to the entire list of ULs) as an array element
			}//if
		if (openuls.length==0) //if there are no opened ULs to save/persist
			openuls[0]="none open" //set array value to string to simply indicate all ULs should persist with state being closed
			ddtreemenu.setCookie(treeid, openuls.join(","), durationdays) //populate cookie with value treeid=1,2,3 etc (where 1,2... are the indexes of the opened ULs)
}//ddtreemenu.rememberstate

////A few utility functions below//////////////////////

ddtreemenu.getCookie=function(Name){ //get cookie value
	var re=new RegExp(Name+"=[^;]+", "i"); //construct RE to search for target name/value pair
	if (document.cookie.match(re)) //if cookie found
	return document.cookie.match(re)[0].split("=")[1] //return its value
	return ""
}//ddtreemenu.getCookie

ddtreemenu.setCookie=function(name, value, days){ //set cookei value
var expireDate = new Date()
//set "expstring" to either future or past date, to set or delete cookie, respectively
var expstring=expireDate.setDate(expireDate.getDate()+parseInt(days))
document.cookie = name+"="+value+"; expires="+expireDate.toGMTString()+"; path=/";
}

ddtreemenu.searcharray=function(thearray, value){ //searches an array for the entered value. If found, delete value from array
var isfound=false
for (var i=0; i<thearray.length; i++){
if (thearray[i]==value){
isfound=true
thearray.shift() //delete this element from array for efficiency sake
break
}
}
return isfound
}

ddtreemenu.preventpropagate=function(e){ //prevent action from bubbling upwards
if (typeof e!="undefined")
e.stopPropagation()
else
event.cancelBubble=true
}

ddtreemenu.dotask=function(target, functionref, tasktype){ //assign a function to execute to an event handler (ie: onunload)
	var tasktype=(window.addEventListener)? tasktype : "on"+tasktype
	if (target.addEventListener)
	target.addEventListener(tasktype, functionref, false)
	else if (target.attachEvent)
	target.attachEvent(tasktype, functionref)
}

function timer() {
	var TimerID = null;
	var dn = null;	
	var now = new Date();
	var hours = now.getHours();
	var months = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
	var days = new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
	var now = new Date();
	var daynumber = now.getDay();
	var dayname = days[daynumber];
	var monthnumber = now.getMonth();
	var monthname = months[monthnumber];
	var monthday = now.getDate();
	var year = now.getYear();

	if(year < 2000) { year = year + 1900; }
	
	if( monthday == 1 || monthday == 21 || monthday ==31 ){monthday = monthday+'st';}
	else if( monthday == 2 || monthday == 3 || monthday ==22 || monthday == 23 ) {monthday = monthday+'nd';}
	else {monthday = monthday+'th';}
	
	var dateString = dayname + ', ' + monthday + ' of ' + monthname
					+ ' ' + year;
	
	if(hours>12) { hours = hours-12; }
	if(hours<10) { hours = "0"+hours; }

	if(hours>12) {
		dn="PM";
	}else {
		dn="AM";
	}

	var minutes = now.getMinutes();
	if(minutes<10) {
		minutes = "0"+minutes;
	}

	var seconds = now.getSeconds();
	if(seconds<10) {
		seconds = "0"+seconds;
	}

	TimerID = setTimeout("timer()",1000);

	var times = hours+":"+minutes+":"+seconds+" "+dn
	document.getElementById('datetime').innerHTML = dateString + ' ' + times;
}