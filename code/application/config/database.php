<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (ISWINDOWS)
{
        $username = "dev_aj_site";
	$password = "data2016";
	$DBHostname = "127.0.0.1:4040";
	$translationDBHost = "127.0.0.1:4041";
}
else
{
	$username = "aj_site";
	$password = "2bVHhwjCGQrRnGW2";
	$DBHostname = "92.243.25.30";
	$translationDBHost = "95.142.167.244";
}

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "default";
$active_record = TRUE;

$db['default']['hostname'] = $DBHostname;
$db['default']['username'] = $username;
$db['default']['password'] = $password;
$db['default']['database'] = "aj_ci";
$db['default']['dbdriver'] = "mysqli";
$db['default']['dbprefix'] = "";
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = FCPATH."cache_queries/aj_ci";
$db['default']['char_set'] = "utf8";
$db['default']['dbcollat'] = "utf8_general_ci";
$db['default']['ssl_enable'] = FALSE;
$db['default']['ssl_ca']   = "/srv/d_mcweb1/mysql-ssl/ca-cert.pem";
$db['default']['ssl_cert'] = "/srv/d_mcweb1/mysql-ssl/client-cert.pem";
$db['default']['ssl_key']  = "/srv/d_mcweb1/mysql-ssl/client-key.pem";

//Database for cache translation
$db['translation']['hostname'] = $translationDBHost;
$db['translation']['username'] = $username;
$db['translation']['password'] = $password;
$db['translation']['database'] = "aj_translation";
$db['translation']['dbdriver'] = "mysqli";
$db['translation']['dbprefix'] = "";
$db['translation']['pconnect'] = FALSE;
$db['translation']['db_debug'] = FALSE;
$db['translation']['cache_on'] = FALSE;
$db['translation']['cachedir'] = FCPATH."cache";
$db['translation']['char_set'] = "utf8";
$db['translation']['dbcollat'] = "utf8_general_ci";

//Wordpress database for reviews
$db['wpblog_reviews']['hostname'] = $DBHostname;
$db['wpblog_reviews']['username'] = $username;
$db['wpblog_reviews']['password'] = $password;
$db['wpblog_reviews']['database'] = "aj_wp_main_fr";
$db['wpblog_reviews']['dbdriver'] = "mysqli";
$db['wpblog_reviews']['dbprefix'] = "";
$db['wpblog_reviews']['pconnect'] = FALSE;
$db['wpblog_reviews']['db_debug'] = FALSE;
$db['wpblog_reviews']['cache_on'] = FALSE;
$db['wpblog_reviews']['cachedir'] = "";
$db['wpblog_reviews']['char_set'] = "utf8";
$db['wpblog_reviews']['dbcollat'] = "utf8_general_ci";

//French wordpress
$db['wpblog_fr']['hostname'] = $DBHostname;
$db['wpblog_fr']['username'] = $username;
$db['wpblog_fr']['password'] = $password;
$db['wpblog_fr']['database'] = "aj_wp_main_fr";
$db['wpblog_fr']['dbdriver'] = "mysqli";
$db['wpblog_fr']['dbprefix'] = "";
$db['wpblog_fr']['pconnect'] = FALSE;
$db['wpblog_fr']['db_debug'] = FALSE;
$db['wpblog_fr']['cache_on'] = FALSE;
$db['wpblog_fr']['cachedir'] = "";
$db['wpblog_fr']['char_set'] = "utf8";
$db['wpblog_fr']['dbcollat'] = "utf8_general_ci";

//Italian wordpress
$db['wpblog_it']['hostname'] = $DBHostname;
$db['wpblog_it']['username'] = $username;
$db['wpblog_it']['password'] = $password;
$db['wpblog_it']['database'] = "aj_wp_it";
$db['wpblog_it']['dbdriver'] = "mysqli";
$db['wpblog_it']['dbprefix'] = "";
$db['wpblog_it']['pconnect'] = FALSE;
$db['wpblog_it']['db_debug'] = FALSE;
$db['wpblog_it']['cache_on'] = FALSE;
$db['wpblog_it']['cachedir'] = "";
$db['wpblog_it']['char_set'] = "utf8";
$db['wpblog_it']['dbcollat'] = "utf8_general_ci";

//Spanish aj wordpress
$db['wpblog_aj_es']['hostname'] = $DBHostname;
$db['wpblog_aj_es']['username'] = $username;
$db['wpblog_aj_es']['password'] = $password;
$db['wpblog_aj_es']['database'] = "aj_wp_es";
$db['wpblog_aj_es']['dbdriver'] = "mysqli";
$db['wpblog_aj_es']['dbprefix'] = "";
$db['wpblog_aj_es']['pconnect'] = FALSE;
$db['wpblog_aj_es']['db_debug'] = FALSE;
$db['wpblog_aj_es']['cache_on'] = FALSE;
$db['wpblog_aj_es']['cachedir'] = "";
$db['wpblog_aj_es']['char_set'] = "utf8";
$db['wpblog_aj_es']['dbcollat'] = "utf8_general_ci";

//Spanish ht wordpress
$db['wpblog_ht_es']['hostname'] = $DBHostname;
$db['wpblog_ht_es']['username'] = $username;
$db['wpblog_ht_es']['password'] = $password;
$db['wpblog_ht_es']['database'] = "aj_wp_ht_es";
$db['wpblog_ht_es']['dbdriver'] = "mysqli";
$db['wpblog_ht_es']['dbprefix'] = "";
$db['wpblog_ht_es']['pconnect'] = FALSE;
$db['wpblog_ht_es']['db_debug'] = FALSE;
$db['wpblog_ht_es']['cache_on'] = FALSE;
$db['wpblog_ht_es']['cachedir'] = "";
$db['wpblog_ht_es']['char_set'] = "utf8";
$db['wpblog_ht_es']['dbcollat'] = "utf8_general_ci";

//Portuguese wordpress
$db['wpblog_pt']['hostname'] = $DBHostname;
$db['wpblog_pt']['username'] = $username;
$db['wpblog_pt']['password'] = $password;
$db['wpblog_pt']['database'] = "aj_wp_pt";
$db['wpblog_pt']['dbdriver'] = "mysqli";
$db['wpblog_pt']['dbprefix'] = "";
$db['wpblog_pt']['pconnect'] = FALSE;
$db['wpblog_pt']['db_debug'] = FALSE;
$db['wpblog_pt']['cache_on'] = FALSE;
$db['wpblog_pt']['cachedir'] = "";
$db['wpblog_pt']['char_set'] = "utf8";
$db['wpblog_pt']['dbcollat'] = "utf8_general_ci";

//Japanese wordpress
$db['wpblog_ja']['hostname'] = $DBHostname;
$db['wpblog_ja']['username'] = $username;
$db['wpblog_ja']['password'] = $password;
$db['wpblog_ja']['database'] = "aj_wp_ja";
$db['wpblog_ja']['dbdriver'] = "mysqli";
$db['wpblog_ja']['dbprefix'] = "";
$db['wpblog_ja']['pconnect'] = FALSE;
$db['wpblog_ja']['db_debug'] = FALSE;
$db['wpblog_ja']['cache_on'] = FALSE;
$db['wpblog_ja']['cachedir'] = "";
$db['wpblog_ja']['char_set'] = "utf8";
$db['wpblog_ja']['dbcollat'] = "utf8_general_ci";

//Japanese Hostels wordpress
$db['wpblog_ja_ho']['hostname'] = $DBHostname;
$db['wpblog_ja_ho']['username'] = $username;
$db['wpblog_ja_ho']['password'] = $password;
$db['wpblog_ja_ho']['database'] = "aj_wp_ja_ho";
$db['wpblog_ja_ho']['dbdriver'] = "mysqli";
$db['wpblog_ja_ho']['dbprefix'] = "";
$db['wpblog_ja_ho']['pconnect'] = FALSE;
$db['wpblog_ja_ho']['db_debug'] = FALSE;
$db['wpblog_ja_ho']['cache_on'] = FALSE;
$db['wpblog_ja_ho']['cachedir'] = "";
$db['wpblog_ja_ho']['char_set'] = "utf8";
$db['wpblog_ja_ho']['dbcollat'] = "utf8_general_ci";

//English wordpress
$db['wpblog_en']['hostname'] = $DBHostname;
$db['wpblog_en']['username'] = $username;
$db['wpblog_en']['password'] = $password;
$db['wpblog_en']['database'] = "aj_wp_en";
$db['wpblog_en']['dbdriver'] = "mysqli";
$db['wpblog_en']['dbprefix'] = "";
$db['wpblog_en']['pconnect'] = FALSE;
$db['wpblog_en']['db_debug'] = FALSE;
$db['wpblog_en']['cache_on'] = FALSE;
$db['wpblog_en']['cachedir'] = "";
$db['wpblog_en']['char_set'] = "utf8";
$db['wpblog_en']['dbcollat'] = "utf8_general_ci";

//German wordpress
$db['wpblog_de']['hostname'] = $DBHostname;
$db['wpblog_de']['username'] = $username;
$db['wpblog_de']['password'] = $password;
$db['wpblog_de']['database'] = "aj_wp_de";
$db['wpblog_de']['dbdriver'] = "mysqli";
$db['wpblog_de']['dbprefix'] = "";
$db['wpblog_de']['pconnect'] = FALSE;
$db['wpblog_de']['db_debug'] = FALSE;
$db['wpblog_de']['cache_on'] = FALSE;
$db['wpblog_de']['cachedir'] = "";
$db['wpblog_de']['char_set'] = "utf8";
$db['wpblog_de']['dbcollat'] = "utf8_general_ci";

//German wordpress
$db['wpblog_de_he']['hostname'] = $DBHostname;
$db['wpblog_de_he']['username'] = $username;
$db['wpblog_de_he']['password'] = $password;
$db['wpblog_de_he']['database'] = "aj_wp_de_he";
$db['wpblog_de_he']['dbdriver'] = "mysqli";
$db['wpblog_de_he']['dbprefix'] = "";
$db['wpblog_de_he']['pconnect'] = FALSE;
$db['wpblog_de_he']['db_debug'] = FALSE;
$db['wpblog_de_he']['cache_on'] = FALSE;
$db['wpblog_de_he']['cachedir'] = "";
$db['wpblog_de_he']['char_set'] = "utf8";
$db['wpblog_de_he']['dbcollat'] = "utf8_general_ci";

//Korean wordpress
$db['wpblog_ko']['hostname'] = $DBHostname;
$db['wpblog_ko']['username'] = $username;
$db['wpblog_ko']['password'] = $password;
$db['wpblog_ko']['database'] = "aj_wp_ko";
$db['wpblog_ko']['dbdriver'] = "mysqli";
$db['wpblog_ko']['dbprefix'] = "";
$db['wpblog_ko']['pconnect'] = FALSE;
$db['wpblog_ko']['db_debug'] = FALSE;
$db['wpblog_ko']['cache_on'] = FALSE;
$db['wpblog_ko']['cachedir'] = "";
$db['wpblog_ko']['char_set'] = "utf8";
$db['wpblog_ko']['dbcollat'] = "utf8_general_ci";

$db['wpblog_ko_ho']['hostname'] = $DBHostname;
$db['wpblog_ko_ho']['username'] = $username;
$db['wpblog_ko_ho']['password'] = $password;
$db['wpblog_ko_ho']['database'] = "aj_wp_ko_ho";
$db['wpblog_ko_ho']['dbdriver'] = "mysqli";
$db['wpblog_ko_ho']['dbprefix'] = "";
$db['wpblog_ko_ho']['pconnect'] = FALSE;
$db['wpblog_ko_ho']['db_debug'] = FALSE;
$db['wpblog_ko_ho']['cache_on'] = FALSE;
$db['wpblog_ko_ho']['cachedir'] = "";
$db['wpblog_ko_ho']['char_set'] = "utf8";
$db['wpblog_ko_ho']['dbcollat'] = "utf8_general_ci";

//Portuguese Brazil wordpress
$db['wpblog_pt_br']['hostname'] = $DBHostname;
$db['wpblog_pt_br']['username'] = $username;
$db['wpblog_pt_br']['password'] = $password;
$db['wpblog_pt_br']['database'] = "aj_wp_pj_pt";
$db['wpblog_pt_br']['dbdriver'] = "mysqli";
$db['wpblog_pt_br']['dbprefix'] = "";
$db['wpblog_pt_br']['pconnect'] = FALSE;
$db['wpblog_pt_br']['db_debug'] = FALSE;
$db['wpblog_pt_br']['cache_on'] = FALSE;
$db['wpblog_pt_br']['cachedir'] = "";
$db['wpblog_pt_br']['char_set'] = "utf8";
$db['wpblog_pt_br']['dbcollat'] = "utf8_general_ci";

//Portuguese Brazil wordpress
$db['wpblog_pt_hi']['hostname'] = $DBHostname;
$db['wpblog_pt_hi']['username'] = $username;
$db['wpblog_pt_hi']['password'] = $password;
$db['wpblog_pt_hi']['database'] = "aj_wp_hi";
$db['wpblog_pt_hi']['dbdriver'] = "mysqli";
$db['wpblog_pt_hi']['dbprefix'] = "";
$db['wpblog_pt_hi']['pconnect'] = FALSE;
$db['wpblog_pt_hi']['db_debug'] = FALSE;
$db['wpblog_pt_hi']['cache_on'] = FALSE;
$db['wpblog_pt_hi']['cachedir'] = "";
$db['wpblog_pt_hi']['char_set'] = "utf8";
$db['wpblog_pt_hi']['dbcollat'] = "utf8_general_ci";

//Portuguese Brazil wordpress
$db['wpblog_hi']['hostname'] = $DBHostname;
$db['wpblog_hi']['username'] = $username;
$db['wpblog_hi']['password'] = $password;
$db['wpblog_hi']['database'] = "aj_wp_hi";
$db['wpblog_hi']['dbdriver'] = "mysqli";
$db['wpblog_hi']['dbprefix'] = "";
$db['wpblog_hi']['pconnect'] = FALSE;
$db['wpblog_hi']['db_debug'] = FALSE;
$db['wpblog_hi']['cache_on'] = FALSE;
$db['wpblog_hi']['cachedir'] = "";
$db['wpblog_hi']['char_set'] = "utf8";
$db['wpblog_hi']['dbcollat'] = "utf8_general_ci";

//English UK wordpress
$db['wpblog_en_uk']['hostname'] = $DBHostname;
$db['wpblog_en_uk']['username'] = $username;
$db['wpblog_en_uk']['password'] = $password;
$db['wpblog_en_uk']['database'] = "aj_wp_en_uk";
$db['wpblog_en_uk']['dbdriver'] = "mysqli";
$db['wpblog_en_uk']['dbprefix'] = "";
$db['wpblog_en_uk']['pconnect'] = FALSE;
$db['wpblog_en_uk']['db_debug'] = FALSE;
$db['wpblog_en_uk']['cache_on'] = FALSE;
$db['wpblog_en_uk']['cachedir'] = "";
$db['wpblog_en_uk']['char_set'] = "utf8";
$db['wpblog_en_uk']['dbcollat'] = "utf8_general_ci";

//Spanish Spanin .es wordpress
$db['wpblog_aj_es_es']['hostname'] = $DBHostname;
$db['wpblog_aj_es_es']['username'] = $username;
$db['wpblog_aj_es_es']['password'] = $password;
$db['wpblog_aj_es_es']['database'] = "aj_wp_es_es";
$db['wpblog_aj_es_es']['dbdriver'] = "mysqli";
$db['wpblog_aj_es_es']['dbprefix'] = "";
$db['wpblog_aj_es_es']['pconnect'] = FALSE;
$db['wpblog_aj_es_es']['db_debug'] = FALSE;
$db['wpblog_aj_es_es']['cache_on'] = FALSE;
$db['wpblog_aj_es_es']['cachedir'] = "";
$db['wpblog_aj_es_es']['char_set'] = "utf8";
$db['wpblog_aj_es_es']['dbcollat'] = "utf8_general_ci";

//Spanish Spanin pension wordpress
$db['wpblog_ap_es']['hostname'] = $DBHostname;
$db['wpblog_ap_es']['username'] = $username;
$db['wpblog_ap_es']['password'] = $password;
$db['wpblog_ap_es']['database'] = "aj_wp_ap_es";
$db['wpblog_ap_es']['dbdriver'] = "mysqli";
$db['wpblog_ap_es']['dbprefix'] = "";
$db['wpblog_ap_es']['pconnect'] = FALSE;
$db['wpblog_ap_es']['db_debug'] = FALSE;
$db['wpblog_ap_es']['cache_on'] = FALSE;
$db['wpblog_ap_es']['cachedir'] = "";
$db['wpblog_ap_es']['char_set'] = "utf8";
$db['wpblog_ap_es']['dbcollat'] = "utf8_general_ci";

//Frecnh Canadian Wordpress
$db['wpblog_fr_ca']['hostname'] = $DBHostname;
$db['wpblog_fr_ca']['username'] = $username;
$db['wpblog_fr_ca']['password'] = $password;
$db['wpblog_fr_ca']['database'] = "aj_wp_fr_ca";
$db['wpblog_fr_ca']['dbdriver'] = "mysqli";
$db['wpblog_fr_ca']['dbprefix'] = "";
$db['wpblog_fr_ca']['pconnect'] = FALSE;
$db['wpblog_fr_ca']['db_debug'] = FALSE;
$db['wpblog_fr_ca']['cache_on'] = FALSE;
$db['wpblog_fr_ca']['cachedir'] = "";
$db['wpblog_fr_ca']['char_set'] = "utf8";
$db['wpblog_fr_ca']['dbcollat'] = "utf8_general_ci";

//Chinese Spanin pension wordpress
$db['wpblog_zh']['hostname'] = $DBHostname;
$db['wpblog_zh']['username'] = $username;
$db['wpblog_zh']['password'] = $password;
$db['wpblog_zh']['database'] = "aj_wp_zh";
$db['wpblog_zh']['dbdriver'] = "mysqli";
$db['wpblog_zh']['dbprefix'] = "";
$db['wpblog_zh']['pconnect'] = FALSE;
$db['wpblog_zh']['db_debug'] = FALSE;
$db['wpblog_zh']['cache_on'] = FALSE;
$db['wpblog_zh']['cachedir'] = "";
$db['wpblog_zh']['char_set'] = "utf8";
$db['wpblog_zh']['dbcollat'] = "utf8_general_ci";

//English Canadian Wordpress
$db['wpblog_en_ca']['hostname'] = $DBHostname;
$db['wpblog_en_ca']['username'] = $username;
$db['wpblog_en_ca']['password'] = $password;
$db['wpblog_en_ca']['database'] = "aj_wp_en_ca";
$db['wpblog_en_ca']['dbdriver'] = "mysqli";
$db['wpblog_en_ca']['dbprefix'] = "";
$db['wpblog_en_ca']['pconnect'] = FALSE;
$db['wpblog_en_ca']['db_debug'] = FALSE;
$db['wpblog_en_ca']['cache_on'] = FALSE;
$db['wpblog_en_ca']['cachedir'] = "";
$db['wpblog_en_ca']['char_set'] = "utf8";
$db['wpblog_en_ca']['dbcollat'] = "utf8_general_ci";

//Polish wordpress
$db['wpblog_pl']['hostname'] = $DBHostname;
$db['wpblog_pl']['username'] = $username;
$db['wpblog_pl']['password'] = $password;
$db['wpblog_pl']['database'] = "aj_wp_pl";
$db['wpblog_pl']['dbdriver'] = "mysqli";
$db['wpblog_pl']['dbprefix'] = "";
$db['wpblog_pl']['pconnect'] = FALSE;
$db['wpblog_pl']['db_debug'] = FALSE;
$db['wpblog_pl']['cache_on'] = FALSE;
$db['wpblog_pl']['cachedir'] = "";
$db['wpblog_pl']['char_set'] = "utf8";
$db['wpblog_pl']['dbcollat'] = "utf8_general_ci";

//PRussian wordpress
$db['wpblog_ru_yh']['hostname'] = $DBHostname;
$db['wpblog_ru_yh']['username'] = $username;
$db['wpblog_ru_yh']['password'] = $password;
$db['wpblog_ru_yh']['database'] = "aj_wp_ru_yh";
$db['wpblog_ru_yh']['dbdriver'] = "mysqli";
$db['wpblog_ru_yh']['dbprefix'] = "";
$db['wpblog_ru_yh']['pconnect'] = FALSE;
$db['wpblog_ru_yh']['db_debug'] = FALSE;
$db['wpblog_ru_yh']['cache_on'] = FALSE;
$db['wpblog_ru_yh']['cachedir'] = "";
$db['wpblog_ru_yh']['char_set'] = "utf8";
$db['wpblog_ru_yh']['dbcollat'] = "utf8_general_ci";

//Polish SM wordpress
$db['wpblog_pl_sm']['hostname'] = $DBHostname;
$db['wpblog_pl_sm']['username'] = $username;
$db['wpblog_pl_sm']['password'] = $password;
$db['wpblog_pl_sm']['database'] = "aj_wp_pl_sm";
$db['wpblog_pl_sm']['dbdriver'] = "mysqli";
$db['wpblog_pl_sm']['dbprefix'] = "";
$db['wpblog_pl_sm']['pconnect'] = FALSE;
$db['wpblog_pl_sm']['db_debug'] = FALSE;
$db['wpblog_pl_sm']['cache_on'] = FALSE;
$db['wpblog_pl_sm']['cachedir'] = "";
$db['wpblog_pl_sm']['char_set'] = "utf8";
$db['wpblog_pl_sm']['dbcollat'] = "utf8_general_ci";

//French AJ wordpress
$db['wpblog_fr_aj']['hostname'] = $DBHostname;
$db['wpblog_fr_aj']['username'] = $username;
$db['wpblog_fr_aj']['password'] = $password;
$db['wpblog_fr_aj']['database'] = "aj_wp_fr_aj";
$db['wpblog_fr_aj']['dbdriver'] = "mysqli";
$db['wpblog_fr_aj']['dbprefix'] = "";
$db['wpblog_fr_aj']['pconnect'] = FALSE;
$db['wpblog_fr_aj']['db_debug'] = FALSE;
$db['wpblog_fr_aj']['cache_on'] = FALSE;
$db['wpblog_fr_aj']['cachedir'] = "";
$db['wpblog_fr_aj']['char_set'] = "utf8";
$db['wpblog_fr_aj']['dbcollat'] = "utf8_general_ci";

//Hungarian Wordpress
$db['wpblog_hu']['hostname'] = $DBHostname;
$db['wpblog_hu']['username'] = $username;
$db['wpblog_hu']['password'] = $password;
$db['wpblog_hu']['database'] = "aj_wp_hu";
$db['wpblog_hu']['dbdriver'] = "mysqli";
$db['wpblog_hu']['dbprefix'] = "";
$db['wpblog_hu']['pconnect'] = FALSE;
$db['wpblog_hu']['db_debug'] = FALSE;
$db['wpblog_hu']['cache_on'] = FALSE;
$db['wpblog_hu']['cachedir'] = "";
$db['wpblog_hu']['char_set'] = "utf8";
$db['wpblog_hu']['dbcollat'] = "utf8_general_ci";

//Hungarian Wordpress
$db['wpblog_hu_if']['hostname'] = $DBHostname;
$db['wpblog_hu_if']['username'] = $username;
$db['wpblog_hu_if']['password'] = $password;
$db['wpblog_hu_if']['database'] = "aj_wp_hu_if";
$db['wpblog_hu_if']['dbdriver'] = "mysqli";
$db['wpblog_hu_if']['dbprefix'] = "";
$db['wpblog_hu_if']['pconnect'] = FALSE;
$db['wpblog_hu_if']['db_debug'] = FALSE;
$db['wpblog_hu_if']['cache_on'] = FALSE;
$db['wpblog_hu_if']['cachedir'] = "";
$db['wpblog_hu_if']['char_set'] = "utf8";
$db['wpblog_hu_if']['dbcollat'] = "utf8_general_ci";

//Russian Wordpress
$db['wpblog_ru']['hostname'] = $DBHostname;
$db['wpblog_ru']['username'] = $username;
$db['wpblog_ru']['password'] = $password;
$db['wpblog_ru']['database'] = "aj_wp_ru";
$db['wpblog_ru']['dbdriver'] = "mysqli";
$db['wpblog_ru']['dbprefix'] = "";
$db['wpblog_ru']['pconnect'] = FALSE;
$db['wpblog_ru']['db_debug'] = FALSE;
$db['wpblog_ru']['cache_on'] = FALSE;
$db['wpblog_ru']['cachedir'] = "";
$db['wpblog_ru']['char_set'] = "utf8";
$db['wpblog_ru']['dbcollat'] = "utf8_general_ci";

//Finnish Wordpress
$db['wpblog_fi']['hostname'] = $DBHostname;
$db['wpblog_fi']['username'] = $username;
$db['wpblog_fi']['password'] = $password;
$db['wpblog_fi']['database'] = "aj_wp_fi";
$db['wpblog_fi']['dbdriver'] = "mysqli";
$db['wpblog_fi']['dbprefix'] = "";
$db['wpblog_fi']['pconnect'] = FALSE;
$db['wpblog_fi']['db_debug'] = FALSE;
$db['wpblog_fi']['cache_on'] = FALSE;
$db['wpblog_fi']['cachedir'] = "";
$db['wpblog_fi']['char_set'] = "utf8";
$db['wpblog_fi']['dbcollat'] = "utf8_general_ci";

//Finnish Wordpress
$db['wpblog_fi_ho']['hostname'] = $DBHostname;
$db['wpblog_fi_ho']['username'] = $username;
$db['wpblog_fi_ho']['password'] = $password;
$db['wpblog_fi_ho']['database'] = "aj_wp_fi_ho";
$db['wpblog_fi_ho']['dbdriver'] = "mysqli";
$db['wpblog_fi_ho']['dbprefix'] = "";
$db['wpblog_fi_ho']['pconnect'] = FALSE;
$db['wpblog_fi_ho']['db_debug'] = FALSE;
$db['wpblog_fi_ho']['cache_on'] = FALSE;
$db['wpblog_fi_ho']['cachedir'] = "";
$db['wpblog_fi_ho']['char_set'] = "utf8";
$db['wpblog_fi_ho']['dbcollat'] = "utf8_general_ci";

//Czech Wordpress
$db['wpblog_cs']['hostname'] = $DBHostname;
$db['wpblog_cs']['username'] = $username;
$db['wpblog_cs']['password'] = $password;
$db['wpblog_cs']['database'] = "aj_wp_cs";
$db['wpblog_cs']['dbdriver'] = "mysqli";
$db['wpblog_cs']['dbprefix'] = "";
$db['wpblog_cs']['pconnect'] = FALSE;
$db['wpblog_cs']['db_debug'] = FALSE;
$db['wpblog_cs']['cache_on'] = FALSE;
$db['wpblog_cs']['cachedir'] = "";
$db['wpblog_cs']['char_set'] = "utf8";
$db['wpblog_cs']['dbcollat'] = "utf8_general_ci";

//Czech Wordpress
$db['wpblog_cs_ml']['hostname'] = $DBHostname;
$db['wpblog_cs_ml']['username'] = $username;
$db['wpblog_cs_ml']['password'] = $password;
$db['wpblog_cs_ml']['database'] = "aj_wp_cs_ml";
$db['wpblog_cs_ml']['dbdriver'] = "mysqli";
$db['wpblog_cs_ml']['dbprefix'] = "";
$db['wpblog_cs_ml']['pconnect'] = FALSE;
$db['wpblog_cs_ml']['db_debug'] = FALSE;
$db['wpblog_cs_ml']['cache_on'] = FALSE;
$db['wpblog_cs_ml']['cachedir'] = "";
$db['wpblog_cs_ml']['char_set'] = "utf8";
$db['wpblog_cs_ml']['dbcollat'] = "utf8_general_ci";

//Russian Wordpress xo
$db['wpblog_ru_xo']['hostname'] = $DBHostname;
$db['wpblog_ru_xo']['username'] = $username;
$db['wpblog_ru_xo']['password'] = $password;
$db['wpblog_ru_xo']['database'] = "aj_wp_ru_xo";
$db['wpblog_ru_xo']['dbdriver'] = "mysqli";
$db['wpblog_ru_xo']['dbprefix'] = "";
$db['wpblog_ru_xo']['pconnect'] = FALSE;
$db['wpblog_ru_xo']['db_debug'] = FALSE;
$db['wpblog_ru_xo']['cache_on'] = FALSE;
$db['wpblog_ru_xo']['cachedir'] = "";
$db['wpblog_ru_xo']['char_set'] = "utf8";
$db['wpblog_ru_xo']['dbcollat'] = "utf8_general_ci";

//Portuguese Wordpress Ho
$db['wpblog_pt_ho']['hostname'] = $DBHostname;
$db['wpblog_pt_ho']['username'] = $username;
$db['wpblog_pt_ho']['password'] = $password;
$db['wpblog_pt_ho']['database'] = "aj_wp_pt_ho";
$db['wpblog_pt_ho']['dbdriver'] = "mysqli";
$db['wpblog_pt_ho']['dbprefix'] = "";
$db['wpblog_pt_ho']['pconnect'] = FALSE;
$db['wpblog_pt_ho']['db_debug'] = FALSE;
$db['wpblog_pt_ho']['cache_on'] = FALSE;
$db['wpblog_pt_ho']['cachedir'] = "";
$db['wpblog_pt_ho']['char_set'] = "utf8";
$db['wpblog_pt_ho']['dbcollat'] = "utf8_general_ci";

//Italian Wordpress Ho
$db['wpblog_it_ho']['hostname'] = $DBHostname;
$db['wpblog_it_ho']['username'] = $username;
$db['wpblog_it_ho']['password'] = $password;
$db['wpblog_it_ho']['database'] = "aj_wp_it_ho";
$db['wpblog_it_ho']['dbdriver'] = "mysqli";
$db['wpblog_it_ho']['dbprefix'] = "";
$db['wpblog_it_ho']['pconnect'] = FALSE;
$db['wpblog_it_ho']['db_debug'] = FALSE;
$db['wpblog_it_ho']['cache_on'] = FALSE;
$db['wpblog_it_ho']['cachedir'] = "";
$db['wpblog_it_ho']['char_set'] = "utf8";
$db['wpblog_it_ho']['dbcollat'] = "utf8_general_ci";


//Ireland Wordpress Ho
$db['wpblog_en_ie']['hostname'] = $DBHostname;
$db['wpblog_en_ie']['username'] = $username;
$db['wpblog_en_ie']['password'] = $password;
$db['wpblog_en_ie']['database'] = "aj_wp_en_ie";
$db['wpblog_en_ie']['dbdriver'] = "mysqli";
$db['wpblog_en_ie']['dbprefix'] = "";
$db['wpblog_en_ie']['pconnect'] = FALSE;
$db['wpblog_en_ie']['db_debug'] = FALSE;
$db['wpblog_en_ie']['cache_on'] = FALSE;
$db['wpblog_en_ie']['cachedir'] = "";
$db['wpblog_en_ie']['char_set'] = "utf8";
$db['wpblog_en_ie']['dbcollat'] = "utf8_general_ci";

//New Zealand Wordpress Ho
$db['wpblog_en_nz']['hostname'] = $DBHostname;
$db['wpblog_en_nz']['username'] = $username;
$db['wpblog_en_nz']['password'] = $password;
$db['wpblog_en_nz']['database'] = "aj_wp_en_nz";
$db['wpblog_en_nz']['dbdriver'] = "mysqli";
$db['wpblog_en_nz']['dbprefix'] = "";
$db['wpblog_en_nz']['pconnect'] = FALSE;
$db['wpblog_en_nz']['db_debug'] = FALSE;
$db['wpblog_en_nz']['cache_on'] = FALSE;
$db['wpblog_en_nz']['cachedir'] = "";
$db['wpblog_en_nz']['char_set'] = "utf8";
$db['wpblog_en_nz']['dbcollat'] = "utf8_general_ci";

//Europe Wordpress
$db['wpblog_en_eu']['hostname'] = $DBHostname;
$db['wpblog_en_eu']['username'] = $username;
$db['wpblog_en_eu']['password'] = $password;
$db['wpblog_en_eu']['database'] = "aj_wp_en_eu";
$db['wpblog_en_eu']['dbdriver'] = "mysqli";
$db['wpblog_en_eu']['dbprefix'] = "";
$db['wpblog_en_eu']['pconnect'] = FALSE;
$db['wpblog_en_eu']['db_debug'] = FALSE;
$db['wpblog_en_eu']['cache_on'] = FALSE;
$db['wpblog_en_eu']['cachedir'] = "";
$db['wpblog_en_eu']['char_set'] = "utf8";
$db['wpblog_en_eu']['dbcollat'] = "utf8_general_ci";

//Asia Wordpress
$db['wpblog_en_asia']['hostname'] = $DBHostname;
$db['wpblog_en_asia']['username'] = $username;
$db['wpblog_en_asia']['password'] = $password;
$db['wpblog_en_asia']['database'] = "aj_wp_en_asia";
$db['wpblog_en_asia']['dbdriver'] = "mysqli";
$db['wpblog_en_asia']['dbprefix'] = "";
$db['wpblog_en_asia']['pconnect'] = FALSE;
$db['wpblog_en_asia']['db_debug'] = FALSE;
$db['wpblog_en_asia']['cache_on'] = FALSE;
$db['wpblog_en_asia']['cachedir'] = "";
$db['wpblog_en_asia']['char_set'] = "utf8";
$db['wpblog_en_asia']['dbcollat'] = "utf8_general_ci";

//Europe Wordpress
$db['wpblog_zh_cn']['hostname'] = $DBHostname;
$db['wpblog_zh_cn']['username'] = $username;
$db['wpblog_zh_cn']['password'] = $password;
$db['wpblog_zh_cn']['database'] = "aj_wp_zh_cn";
$db['wpblog_zh_cn']['dbdriver'] = "mysqli";
$db['wpblog_zh_cn']['dbprefix'] = "";
$db['wpblog_zh_cn']['pconnect'] = FALSE;
$db['wpblog_zh_cn']['db_debug'] = FALSE;
$db['wpblog_zh_cn']['cache_on'] = FALSE;
$db['wpblog_zh_cn']['cachedir'] = "";
$db['wpblog_zh_cn']['char_set'] = "utf8";
$db['wpblog_zh_cn']['dbcollat'] = "utf8_general_ci";

//Mobi Wordpress
$db['wpblog_en_mobi']['hostname'] = $DBHostname;
$db['wpblog_en_mobi']['username'] = $username;
$db['wpblog_en_mobi']['password'] = $password;
$db['wpblog_en_mobi']['database'] = "aj_wp_en_mobi";
$db['wpblog_en_mobi']['dbdriver'] = "mysqli";
$db['wpblog_en_mobi']['dbprefix'] = "";
$db['wpblog_en_mobi']['pconnect'] = FALSE;
$db['wpblog_en_mobi']['db_debug'] = FALSE;
$db['wpblog_en_mobi']['cache_on'] = FALSE;
$db['wpblog_en_mobi']['cachedir'] = "";
$db['wpblog_en_mobi']['char_set'] = "utf8";
$db['wpblog_en_mobi']['dbcollat'] = "utf8_general_ci";

//NofeeHostels Wordpress
$db['wpblog_en_nf']['hostname'] = $DBHostname;
$db['wpblog_en_nf']['username'] = $username;
$db['wpblog_en_nf']['password'] = $password;
$db['wpblog_en_nf']['database'] = "aj_wp_en_nf";
$db['wpblog_en_nf']['dbdriver'] = "mysqli";
$db['wpblog_en_nf']['dbprefix'] = "";
$db['wpblog_en_nf']['pconnect'] = FALSE;
$db['wpblog_en_nf']['db_debug'] = FALSE;
$db['wpblog_en_nf']['cache_on'] = FALSE;
$db['wpblog_en_nf']['cachedir'] = "";
$db['wpblog_en_nf']['char_set'] = "utf8";
$db['wpblog_en_nf']['dbcollat'] = "utf8_general_ci";

//Wordpress database for reviews
$db['wpblog_hb']['hostname'] = $DBHostname;
$db['wpblog_hb']['username'] = $username;
$db['wpblog_hb']['password'] = $password;
$db['wpblog_hb']['database'] = "aj_wp_hb";
$db['wpblog_hb']['dbdriver'] = "mysqli";
$db['wpblog_hb']['dbprefix'] = "";
$db['wpblog_hb']['pconnect'] = FALSE;
$db['wpblog_hb']['db_debug'] = FALSE;
$db['wpblog_hb']['cache_on'] = FALSE;
$db['wpblog_hb']['cachedir'] = "";
$db['wpblog_hb']['char_set'] = "utf8";
$db['wpblog_hb']['dbcollat'] = "utf8_general_ci";

//Wordpress database for reviews
$db['wpblog_hw']['hostname'] = $DBHostname;
$db['wpblog_hw']['username'] = $username;
$db['wpblog_hw']['password'] = $password;
$db['wpblog_hw']['database'] = "aj_wp_hw";
$db['wpblog_hw']['dbdriver'] = "mysqli";
$db['wpblog_hw']['dbprefix'] = "";
$db['wpblog_hw']['pconnect'] = FALSE;
$db['wpblog_hw']['db_debug'] = FALSE;
$db['wpblog_hw']['cache_on'] = FALSE;
$db['wpblog_hw']['cachedir'] = "";
$db['wpblog_hw']['char_set'] = "utf8";
$db['wpblog_hw']['dbcollat'] = "utf8_general_ci";



/* End of file database.php */
/* Location: ./application/config/database.php */