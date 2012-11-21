<?php
/*
Plugin Name: auberges term translate
Plugin URI:
Description: Add admin menu for term translator
Version: 1.0
Author: Louis-Michel Raynauld
Author URI: pweb.ca
License: Open source but do not share
*/

require_once("libs/wp_plugin.php");
class term_translation_plugin extends WP_plugin
{
  private $translator = NULL;
  private $plugindb = NULL;
  public function term_translation_plugin()
  {
    $plugindb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);

    require_once("libs/db_translator.php");
    $this->translator = new DB_translator($plugindb);
  }
  public function csv_import()
  {
    return $this->translator->importCSV($_FILES['CSVtrans_file']['tmp_name']);
  }
}

if (is_admin())
{
  $message = "";
  $plugin = new term_translation_plugin();

  if ($_POST['translateCSV'] == "TranslateCSVimport")
  {
    $message = "Errors happened importing CSV";
    if($plugin->csv_import());
    {
      $message = "Imported with success";
      echo $message;
    }

  }
}

// create custom plugin settings menu
add_action('admin_menu', 'plugin_admin_menu');

function plugin_admin_menu() {

  //create new top-level menu
  add_menu_page('Auberges administration', 'Translation', 'administrator', __FILE__, 'setting_page');

  //Create langage sub menu
//   add_submenu_page( __FILE__, "Auberges Langages", "Langages", "administrator", "auberges_lang_menu", "auberges_lang_page");
//   add_submenu_page( __FILE__, "Auberges Sitemap", "Sitemap", "administrator", "auberges_sitemap_menu", "auberges_sitemap_page");
//   add_submenu_page( __FILE__, "Auberges DB", "Database", "administrator", "auberges_db_menu", "auberges_db_page");

  //call register settings function
  //  add_action( 'admin_init', 'register_mysettings' );
}

function setting_page() {
  include('settings/settings_view.php');

}

?>