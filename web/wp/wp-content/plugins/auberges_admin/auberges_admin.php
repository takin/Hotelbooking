<?php
/*
Plugin Name: auberges admin
Plugin URI:
Description: Add admin menu for auberges website outsite wp
Version: 1.0
Author: Louis-Michel Raynauld
Author URI: graphem.ca
License: Commercial
*/

//If form posted to this page process data
if (is_admin())
{
  if ($_POST['csv_transactions'] == "Télécharger")
  {
    include('auberges_csv_transactions.php');
    exit;
  }
  if ($_POST['csv_usagers'] == "Télécharger")
  {
    include('auberges_csv_users.php');
    exit;
  }

  if ($_POST['csv_adword_pays'] == "Télécharger")
  {
    include('auberges_csv_adword_pays.php');
    exit;
  }

  if ($_POST['csv_adword_villes'] == "Télécharger")
  {
    include('auberges_csv_adword.php');
    exit;
  }

  if ($_POST['csv_adword_hostels'] == "Télécharger")
  {
    include('auberges_csv_adword_hostels.php');
    exit;
  }
  if ($_POST['csv_adword_districts'] == "Télécharger")
  {
    include('auberges_csv_adword_districts.php');
    exit;
  }
  if ($_POST['csv_adword_landmarks'] == "Télécharger")
  {
    include('auberges_csv_adword_landmarks.php');
    exit;
  }

  if (isset($_POST['POtranslate']))
  {
    include('pogtranslate.php');
    exit;
  }
}



// create custom plugin settings menu
add_action('admin_menu', 'auberges_admin_menu');

function auberges_admin_menu() {

  //create new top-level menu
  add_menu_page('Auberges administration', 'Auberges', 'administrator', __FILE__, 'auberges_home_page');

  //Create langage sub menu
  add_submenu_page( __FILE__, "Auberges Langages", "Langages", "administrator", "auberges_lang_menu", "auberges_lang_page");
  add_submenu_page( __FILE__, "Auberges Sitemap", "Sitemap", "administrator", "auberges_sitemap_menu", "auberges_sitemap_page");
  add_submenu_page( __FILE__, "Auberges DB", "Database", "administrator", "auberges_db_menu", "auberges_db_page");

  //call register settings function
//  add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
  //register our settings
  register_setting( 'baw-settings-group', 'new_option_name' );
  register_setting( 'baw-settings-group', 'some_other_option' );
  register_setting( 'baw-settings-group', 'option_etc' );
}

function auberges_home_page() {
  include('auberges_admin_menu_view.php');
}

function auberges_lang_page() {
  include('auberges_admin_lang_view.php');
}

function auberges_sitemap_page() {
  include('auberges_sitemap.php');
}

function auberges_db_page() {
  include('auberges_admin_db.php');
}
?>