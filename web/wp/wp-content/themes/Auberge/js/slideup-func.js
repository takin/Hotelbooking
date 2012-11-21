function addLoadEvent(func) {
var oldonload = window.onload;
if (typeof window.onload != 'function') {
window.onload = func;
} else {
window.onload = function() {
oldonload();
func();
}
}} 

function destroycatfish()
{
 var catfish = document.getElementById('slideup');
 document.body.removeChild(catfish); /* clip catfish off the tree */
 document.getElementsByTagName('html')[0].style.padding= '0'; /* reset the padding at the bottom */
 return false;
}
function closeme()
{
 var closelink = document.getElementById('closeme');
 closelink.onclick = destroycatfish;
} 

addLoadEvent(function() {
closeme();
}); 

var catfish;

function deploycatfish()
// initializing
{
	catfish = document.getElementById('slideup');
	
	catfishheight = 105; // total height of catfish in pixels
	catfishoverlap = 21; // height of the 'overlap' portion only (semi-transparent)
	catfishtimeout = setTimeout(startcatfish, 3500);
}

function startcatfish()
// starts the catfish sliding up
{
	catfishposition = 0; // catfishposition is expressed in percentage points (out of 100)
	catfishtimeout = setInterval(positioncatfish, 50);
}

function positioncatfish()
{
	catfishposition += 10;
	catfish.style.marginBottom = '-' + (((100 - catfishposition) / 100) * catfishheight) + 'px';
	if (catfishposition >= 100)
	{
		clearTimeout(catfishtimeout);
		catfishtimeout = setTimeout(finishcatfish, 1);
	}
}

function finishcatfish()
{
	catfish.style.marginBottom = '0';	
	// jump the bottom of the document to give room for the catfish when scrolled right down
	document.body.parentNode.style.paddingBottom = (catfishheight - catfishoverlap) +'px';
	
	// here you could use AJAX (or similar) to log the popup hit for tracking purposes	
}

addLoadEvent(deploycatfish);
