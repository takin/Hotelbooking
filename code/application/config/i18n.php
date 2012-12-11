<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Langage gettext file
|--------------------------------------------------------------------------
|
|
*/
$config['gettext_filename'] = "auberge_lang";
/*
|--------------------------------------------------------------------------
| Langage locale dir
|--------------------------------------------------------------------------
|
|
*/
if (ISWINDOWS) {
	$config['gettext_filedir'] = "c:/GitHub/source/languages/ci/locale";
}
else
{
	$config['gettext_filedir'] = "/opt/languages/ci/locale";
}
?>
