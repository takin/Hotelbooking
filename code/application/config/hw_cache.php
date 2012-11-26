<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| HW cache Log file
|--------------------------------------------------------------------------
|
| Filename of log file
|
*/
$config['hw_log_filename'] = "hw_cache";
/*
|--------------------------------------------------------------------------
| HW Connection details
|--------------------------------------------------------------------------
|
| hw_api_max_tries: indicate the number of attempts to try reaching HW API.
|
| hw_api_time_delay: Indicate the number of time (in seconds) to wait before
|                    sending another request to HW API after a previous request failed.
|
| hw_sec_between_req: Indicate the number of time (in seconds) to wait between
|                     normal requests
*/
$config['hw_api_max_tries'] = 5;
$config['hw_api_time_delay'] = 2;
$config['hw_sec_between_req'] = 1;
/*
|--------------------------------------------------------------------------
| HW Cache emails
|--------------------------------------------------------------------------
|
| email_to_report: email to send report of API DB Cache update
|
*/
$config['email_to_report_city']   = "technical@mcwebmanagement.com";
$config['email_to_report_hostel'] = "";
/*
|--------------------------------------------------------------------------
| HW Cache details
|--------------------------------------------------------------------------
|
| target_time: in hours, it is the time expected by the script to update the
|                        hostels of all city in DB
|
| cron_job_freq_by_hour: Nomber of time per hours that the location search
|                        cache script will be run.
|
*/
$config['target_time'] = 96; //7 X 24 hours -> 1 week
$config['cron_job_freq_by_hour'] = 1;
/*
|--------------------------------------------------------------------------
| HW Cache langage of API location search request
|--------------------------------------------------------------------------
|
| Array of langages to get for location search requests caching
| English must be first
|
*/
$config['hw_api_langages'][0] = 'English';
$config['hw_api_langages'][1] = 'French';
$config['hw_api_langages'][2] = 'Spanish';
$config['hw_api_langages'][3] = 'German';
$config['hw_api_langages'][4] = 'Italian';
/*
|--------------------------------------------------------------------------
| HW Cache Currencies of API location search request
|--------------------------------------------------------------------------
|
| Array of currencies to get for location search requests caching
|
*/
$config['hw_api_currencies'][0] = 'EUR';
$config['hw_api_currencies'][1] = 'USD';
$config['hw_api_currencies'][2] = 'GBP';
/*
|--------------------------------------------------------------------------
| HW City to execute API location search request for all currency
|--------------------------------------------------------------------------
|
| English name of a city and its country
|
*/
$config['hw_currency_country'] = 'France';
$config['hw_currency_city']    = 'Paris';
/*
|--------------------------------------------------------------------------
| HW Hostels booing info cache
|--------------------------------------------------------------------------
|
|
| hostel_info_cron_per_day: number of times the script will be run each day
|
|
| hostel_info_avail_tries: Number of availability check to try
*/
$config['target_days_to_cache_all'] = 30;
$config['hostel_info_cron_per_day'] = 4;
$config['hostel_info_avail_tries']  = 5;
?>
