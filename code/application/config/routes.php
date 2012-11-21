<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved
| routes must come before any wildcard or regular expression routes.
|
*/

$route['default_controller'] = "cmain";
$route['scaffolding_trigger'] = "";

//Routes are handle custom via database to support multiple languages

//Routes error controller
$route['error_controller'] = "cmain/error404";

$route['hw/citylistdb'] = "chostel/citylistdb";
$route['hb/citylistdb'] = "chostelbk/citylistdb";

$route['guarantee'] = "cmain/guarantee";

// $route['s']        = "cmain/site_search";
// $route['s/(:any)'] = "cmain/site_search/$1";
$route['s']        = "cmain/error404";
$route['s/(:any)'] = "cmain/error404";
$route['group']        = "cmain/group_request";
$route['group/(:any)'] = "cmain/group_request";

//Admin request
$route['group_quote']        = "cadmin/generate_quote";
$route['new_group_quote']    = "cadmin/group_quote";

//Ajax requests
$route['ax/group_request']        = "cform_ajax/group_request";
$route['ax/group_request/(:any)'] = "cform_ajax/group_request/$1";

$route['suggest']        = "cmain/site_search_suggest";
$route['suggest/(:any)'] = "cmain/site_search_suggest/$1";

$route['location_avail']        = "cmain/ajax_location_avail";
$route['location_avail/(:any)'] = "cmain/ajax_location_avail/$1";

$route['reviews_map/(:any)']      = "cmain/reviews_map/$1";
$route['property_reviews/(:any)'] = "cmain/property_reviews/$1";

$route['rooms_avail']        = "cmain/property_rooms_avail/";
$route['rooms_avail/(:any)'] = "cmain/property_rooms_avail/$1";

$route['prop_pics']        = "cmain/property_images/";
$route['prop_pics/(:any)'] = "cmain/property_images/$1";

$route['prop_infos']        = "cmain/property_infos/";
$route['prop_infos/(:any)'] = "cmain/property_infos/$1";

//Mobile routes
$route['m'] = "cmain/mobile_search";

$route['map'] = "cmain/mobile_map";
$route['map/(:any)'] = "cmain/mobile_map/$1";

$route['ma']        = "cmain/mobile_avail_check/";
$route['ma/(:any)'] = "cmain/mobile_avail_check/$1";

$route['mbooking_confirmation'] = "cmain/mobile_booking_confirmation";
$route['mbooking']              = "cmain/mobile_booking";

$route['mbooking_try']       = "cmain/mobile_booking_try";
$route['mbooking_completed'] = "cmain/mobile_booking_complete";



/* End of file routes.php */
/* Location: ./system/application/config/routes.php */