<?php  //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default backup directory
|--------------------------------------------------------------------------
|
| Directory to write databases backup
|
*/
$config['db_backup_dir'] = "/srv/d_mcweb1/dbbackups";
/*
|--------------------------------------------------------------------------
| Databases keywords to backup
|--------------------------------------------------------------------------
|
| Databases keyword found in databases.php of DB to backup
|
| DEPRECATED: Now we use a list of DB with a prefix to backup
*/
$config['dbKeysToBackup'][0] = 'default';
/*
|--------------------------------------------------------------------------
| Number of files to keep of the same database backup
|--------------------------------------------------------------------------
|
| 
|
*/
$config['DbFilesQty'] = 15;
?>