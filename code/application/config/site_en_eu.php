<?php  //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Wordpress database variable name
|--------------------------------------------------------------------------
|
| variable name of database set in database.php
|
| Be SURE that a corresponding database variable is set in database.php!!!
|
*/
$config['wp_db_tag'] = "wpblog_en_eu";
/*
|--------------------------------------------------------------------------
| HW booking test mode
|--------------------------------------------------------------------------
|
| if this is 1, bookings will be send in test mode.
| For normal mode keep it 0.
|
*/
$config['booking_test_mode'] = 0;
/*
|--------------------------------------------------------------------------
| HW booking test mode
|--------------------------------------------------------------------------
|
| if this is 1, HostelWorld will not send a confirmation email to customer on
| a succesful booking.
|
|
*/
$config['HW_no_confirmation_email'] = 1;
/*
|--------------------------------------------------------------------------
| Ad words cookie expiration time
|--------------------------------------------------------------------------
|
| default adwords cookies last 30 days = 60*60*24*30 = 2592000 
| 
| Set to zero for no expiration as long as the browser is open
|
|
*/
$config['adword_cookie_expiration'] = 2592000;
/*
|--------------------------------------------------------------------------
| Ad words cookie prefix
|--------------------------------------------------------------------------
|
| prefix for adword cookie 
|
*/
$config['adword_cookie_prefix'] = "adword_";
/*
|--------------------------------------------------------------------------
| Authentication library email address
|--------------------------------------------------------------------------
|
| Email to sent authentication information from.
|
| Contact email used for emails sent by authentication library.
|
*/
$config['email_users_admin'] = "info@youth-hostels.eu";
/*
|--------------------------------------------------------------------------
| Information email address
|--------------------------------------------------------------------------
|
| Contact email address for clients
|
|
*/
$config['contact_info_email'] = "info@youth-hostels.eu";
/*
|--------------------------------------------------------------------------
| Booking email address from
|--------------------------------------------------------------------------
|
| Email address to send booking confirmation emails from
|
|
*/
$config['admin_booking_email'] = "info@youth-hostels.eu";
/*
|--------------------------------------------------------------------------
| Booking confirmation email to admin
|--------------------------------------------------------------------------
|
| 1 for a confirmation will be sent to admin booking email address
| 0 for no confirmation on a new booking
|
*/
$config['admin_booking_conf'] = 1;
/*
|--------------------------------------------------------------------------
| admin email for booking confirmation 
|--------------------------------------------------------------------------
|
| Admin email address to send a new bookings confirmations
|
*/
$config['admin_booking_conf_email'] = "info@youth-hostels.eu";
/*
|--------------------------------------------------------------------------
| Review alert to admin
|--------------------------------------------------------------------------
|
| 1 to send an email alert to admin on a new review
| 0 no new review email alert
|
*/
$config['admin_review_conf'] = 1;
/*
|--------------------------------------------------------------------------
| admin email for vew review alert
|--------------------------------------------------------------------------
|
| Admin email address to send new review alerts
|
*/
$config['admin_review_conf_email'] = "info@youth-hostels.eu";
/*
|--------------------------------------------------------------------------
| Default site currency
|--------------------------------------------------------------------------
|
| ISO code of default site currency
|
|
*/
$config['site_currency_default'] = "EUR";
/*
|--------------------------------------------------------------------------
| HostelWorld.com Affiliate UserID
|--------------------------------------------------------------------------
|
| User Id given to use with Hostelworld.com affiliate program
|
| ex: testaffiliate.com
|
*/
$config['hostelworld_userID'] = "aubergesdejeunesse.com";
/*
|--------------------------------------------------------------------------
| HostelWorld.com API url
|--------------------------------------------------------------------------
|
| User Id given to use with Hostelworld.com affiliate program
|
| ex: testaffiliate.com
|
*/
$config['hostelworld_API_url'] = "http://reservations.bookhostels.com/xmlapi/";
/*
|--------------------------------------------------------------------------
| HostelWorld.com API url
|--------------------------------------------------------------------------
|
| User Id given to use with Hostelworld.com affiliate program
|
| ex: testaffiliate.com
|
*/
$config['hostelworld_API_url_secure_booking'] = "https://secure.hostelworld.com/bookhostels/xmlapi/";

?>