<?php
function domain_name_replace($string) 
{
	$current_domain = ucfirst(str_replace("www.", "", $_SERVER['SERVER_NAME']));
	$CI =& get_instance();
	$bad_domain_names = $CI->config->item('companies_strings_to_replace'); // Array defined in config file that has list of string to look for
	$correct_string = $string;
	foreach($bad_domain_names as $name) 
	{
		$correct_string = str_ireplace($name, $current_domain, $correct_string);
	}
	 return $correct_string;
}
?>