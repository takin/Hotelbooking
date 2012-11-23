<?php
header("content-type: application/x-javascript");
?>

var  site = new Site('<?php echo $_GET["lang"]; ?>');

function Site(lang)
{
	this.lang      = lang;
	this.base_url  = window.location.protocol+'//'+window.location.host;

	this.make_url = function(link) {
		return this.base_url+'/'+link;
	};
}

