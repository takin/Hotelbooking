<?php

//Admin Setting page

$themename = "Site";
$shortname = "aj";

$currenciesArray="";

global $aubergedb;
$currenciesArray = new Db_currencies($aubergedb);
$currenciesArray = $currenciesArray->getCurrencyCodesArray();

require_once (get_template_directory().'/ci/db_wp_country.php');

$cityArray = new Db_country($aubergedb);
$countryArray = $cityArray->get_country_array();
$cityArray    = $cityArray->get_city_array();

$options = array (


	array(	"type" => "open"),

	array(	"name" => "Basic Options",
			"type" => "title"),

	array(	"name" => "booking API",
			"desc" => "Please choose what API you want to use for this site",
			"id" => $shortname."_api_site_data",
			"std" => "hw",
			"type" => "selectplus",
     	"options" => array(array("HostelWorld.com","hw"),array("HostelBookers.com","hb"))),

	array(	"name" => "Enable Mobile Site",
			"desc" => "Check the box to enable the mobile specific design for this site",
			"id" => $shortname."_enable_mobile",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Full site SMS reminders",
			"desc" => "Check the box to enable the SMS reminder on the booking page for this site",
			"id" => $shortname."_enable_sms_reminder",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Test site for Dev",
			"desc" => "Please determine if site is a development site or a live site for bookers",
			"id" => $shortname."_dev_site",
			"std" => 0,
			"type" => "selectplus",
     	"options" => array(array("Live",0),array("Dev site",1))),

	array(	"name" => "Type of site",
			"desc" => "Please choose what type of site this is",
			"id" => $shortname."_type_site",
			"std" => "Youth Hostels",
			"type" => "select",
     	"options" => array("Youth Hostels", "Hotels")),

	array(	"name" => "Velaro Department ID",
			"desc" => "enter the department ID just the number no space and no special characters.",
			"id" => $shortname."_velaro_id",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Date Format",
			"desc" => "Choose the date format to use across the site",
			"id" => $shortname."_date_format",
			"std" => "%m-%d-%Y",
			"type" => "selectplus",
     	"options" => array(array("mm-dd-yyyy","%m-%d-%Y"),array("dd-mm-yyyy","%d-%m-%Y"),array("yyyy/mm/dd","%Y/%m/%d"),array("l F Y (3 october 2010)","%e %B %Y"),array("mm.dd.yyyy","%m.%d.%Y"),array("dd.mm.yyyy","%d.%m.%Y"))),

  array(	"name" => "Date Format - city search",
			"desc" => "Choose the date format to use on city search page property list",
			"id" => $shortname."_date_format_city_search",
			"std" => "%Y-%m-%d",
			"type" => "selectplus",
     	"options" => array(array("yyyy-mm-dd","%Y-%m-%d"),array("mm-dd-yyyy","%m-%d-%Y"),array("dd-mm-yyyy","%d-%m-%Y"),array("yyyy/mm/dd","%Y/%m/%d"),array("l F Y (3 october 2010)","%e %B %Y"),array("mm.dd.yyyy","%m.%d.%Y"),array("dd.mm.yyyy","%d.%m.%Y"))),

	array(	"name" => "Show the Stamp",
			"desc" => "Check the box to show the guarantee booking or no booking fee stamp",
			"id" => $shortname."_show_stamp",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Show Only Guide Home Page",
			"desc" => "Check the box to show only code on the homepage",
			"id" => $shortname."_only_guide",
			"std" => "",
			"type" => "checkbox"),



	array(	"name" => "Show the page excerpt for the guide on home page",
			"desc" => "Use only this setting for languages with no spacing between words (e.g: chinese)",
			"id" => $shortname."_guide_excerpt",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Show Currency Widget",
			"desc" => "Check the box to have the currency widget showing",
			"id" => $shortname."_currency_widget",
			"std" => "checked",
			"type" => "checkbox"),

	array(	"name" => "Show CoursParticulier Widget",
			"desc" => "Check the box to have the cours particulier widget showing",
			"id" => $shortname."_cours_widget",
			"std" => "checked",
			"type" => "checkbox"),

	array(	"name" => "Youtube Promo Video ID",
			"desc" => "Enter the Youtube video ID",
			"id" => $shortname."_promo_video",
			"std" => "",
			"type" => "text"),

	array( "name" => "Choose site default Country",
      "desc" => "Choose country",
      "id" => $shortname."_default_country",
      "std" => "",
      "type" => "selectplus",
      "options" => $countryArray),

	array( "name" => "Choose site default City",
      "desc" => "Choose city",
      "id" => $shortname."_default_city",
      "std" => "",
      "type" => "selectplus",
      "options" => $cityArray),

	array(	"name" => "Choose site default Currency",
			"desc" => "Choose currency",
			"id" => $shortname."_default_currency",
			"std" => "EUR",
			"type" => "selectplus",
      "options" => $currenciesArray),

	array(	"name" => "Choose site default Settle Currency",
			"desc" => "Choose currency",
			"id" => $shortname."_default_settle_currency",
			"std" => "EUR",
			"type" => "selectplus",
      "options" => array(array("Euro","EUR"), array("US Dollar","USD"), array("British Pound","GBP"))),

	array(	"name" => "Show English Cities",
			"desc" => "Check the box show the english cities and countries with the foreign language",
			"id" => $shortname."_show_encity",
			"std" => "checked",
			"type" => "checkbox"),

	array(	"name" => "Don't Show English version in the confirmation email",
			"desc" => "Check the box to disable the english version in the confirmation email.",
			"id" => $shortname."_email_english",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Don't bottom information in red in the email",
			"desc" => "Check the box to disable the red color in the email.",
			"id" => $shortname."_red_email",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Site Title as in API side",
			"desc" => "Enter the name of the site as in the API version of the site",
			"id" => $shortname."_api_name",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Site Name ASCII compatible",
			"desc" => "Enter the compatible name with ASCII compatible characters(important for loading logos and images)",
			"id" => $shortname."_api_ascii",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Language code 4 letters for this site",
			"desc" => "Enter language code",
			"id" => $shortname."_lang_code",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Language code 2 letters for this site",
			"desc" => "Enter language code",
			"id" => $shortname."_lang_code2",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Server locale for this site",
			"desc" => "Enter locale",
			"id" => $shortname."_set_locale",
			"std" => "",
			"type" => "text"),

	array(	"name" => "PO/MO filename",
			"desc" => "Enter the po/mo filename",
			"id" => $shortname."_lang_pomo",
			"std" => "",
			"type" => "text"),

	array(	"name" => "URl for the comments",
			"desc" => "Please enter the url of the hostel api site",
			"id" => $shortname."_comment_url",
			"std" => "http://www.auberges.com/info",
			"type" => "text"),

	array(	"name" => "URl of the hostel API site",
			"desc" => "Please enter the url of the hostel api site",
			"id" => $shortname."_api_url",
			"std" => "",
			"type" => "text"),

	array(	"name" => "SSL URL",
			"desc" => "Please enter the ssl url that will be the link for the ssl image in the booking",
			"id" => $shortname."_ssl_url",
			"std" => "",
			"type" => "text"),

	array(	"name" => "URl of the hostel API base search page",
			"desc" => "Please enter the url of the hostel api search",
			"id" => $shortname."_api_search",
			"std" => "",
			"type" => "text"),

	array(	"name" => "URl the group booking site",
			"desc" => "Please enter the url for the group booking site",
			"id" => $shortname."_group_url",
			"std" => "",
			"type" => "text"),

	array(	"name" => "URL of the about us page",
			"desc" => "URL of the page about us",
			"id" => $shortname."_page_about",
			"type" => "text"),

	array(	"name" => "URL of the page Contact/Help/FAQ page",
			"desc" => "URL of the page for the contact/help/faq",
			"id" => $shortname."_page_faq",
			"type" => "text"),

	array(	"name" => "URL of the page for Confidentiality",
			"desc" => "Choose the page for the Confidentiality",
			"id" => $shortname."_page_conf",
			"type" => "text"),

	array(	"name" => "URL of the page Terms and Conditions",
			"desc" => "Choose the page for the terms and conditions",
			"id" => $shortname."_page_cond",
			"type" => "text"),

	array(	"name" => "URL of the page for the events",
			"desc" => "Choose the page for the events",
			"id" => $shortname."_page_events",
			"type" => "text"),

	array(	"name" => "URL of the page for the guide",
			"desc" => "Choose the page for the guide",
			"id" => $shortname."_page_guides",
			"type" => "text"),

	array(	"name" => "URL of the page for the contest",
			"desc" => "enter the url of the contest page",
			"id" => $shortname."_page_contest",
			"type" => "text"),

	array(	"name" => "ID of the post related to the hostel ratings",
			"desc" => "Please enter the id of the post where the hostel rating will be associated with",
			"id" => $shortname."_post_rating",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Category ID to exclude from Search and RSS",
			"desc" => "Please enter the id of the categoriues to exclude from search and RSS",
			"id" => $shortname."_cat_exclude",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Category ID to exclude from latest article home page",
			"desc" => "Please enter the id of the categories to exclude from latest article home page (put a - in front)",
			"id" => $shortname."_cathome_exclude",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Name of the vedette section on the home page",
			"desc" => "Please enter the name of the page to show on the home page in the vedette section",
			"id" => $shortname."_home_vedette",
			"std" => "",
			"type" => "text"),

	array(	"name" => "ID of the pages to exclude from the search engine indexing",
			"desc" => "Please enter the id of the pages to exclude from the indexing. Separated ",
			"id" => $shortname."_no_seo",
			"std" => "",
			"type" => "text"),


	array(	"type" => "close"),

  array(	"type" => "open"),

  array(	"name" => "HB API specific options",
  			  "type" => "title"),

  array(	"name" => "URL to latest HB static feed zip file",
    			"desc" => "Please enter the FULL URL of latest static feed. Example: http://feeds.hostelbookers.com/affiliate/mcweb/20110601.zip",
    			"id" => $shortname."_hb_static_feed_url",
    			"std" => "",
    			"type" => "text"),

  array(	"type" => "close"),

	array(	"type" => "open"),

	array(	"name" => "Caching Options",
			    "type" => "title"),

	array(	"name" => "AJ Caching enable",
			"desc" => "Herre you can enable/disable html caching of site pages",
			"id" => $shortname."_cache_enable",
			"std" => 0,
			"type" => "selectplus",
     	"options" => array(array("Disable",0),array("Enable",1))),

	array(	"name" => "Caching free space limit",
			"desc" => "enter the amount of free space to stop caching in MB",
			"id" => $shortname."_cache_space_limit",
			"std" => "2000",
			"type" => "text"),

	array(	"name" => "Caching time for auberge page (world map)",
			"desc" => "enter the number of minutes you wish the page to remain cached between refreshes",
			"id" => $shortname."_cache_time_ci_home",
			"std" => "3600",
			"type" => "text"),

	array(	"name" => "Caching time for continent & country pages",
			"desc" => "enter the number of minutes you wish the page to remain cached between refreshes",
			"id" => $shortname."_cache_time_country_pages",
			"std" => "3600",
			"type" => "text"),

	array(	"name" => "Caching time for city landing pages",
			"desc" => "enter the number of minutes you wish the page to remain cached between refreshes",
			"id" => $shortname."_cache_time_city_landing_pages",
			"std" => "3600",
			"type" => "text"),

	array(	"name" => "Caching time for city availability pages",
			"desc" => "enter the number of minutes you wish the page to remain cached between refreshes",
			"id" => $shortname."_cache_time_city_avail_pages",
			"std" => "0",
			"type" => "text"),

	array(	"name" => "Caching time for property pages",
			"desc" => "enter the number of minutes you wish the page to remain cached between refreshes",
			"id" => $shortname."_cache_time_property_pages",
			"std" => "25200",
			"type" => "text"),

	array(	"type" => "close"),

	array(	"type" => "open"),

	array(	"name" => "Home Page Settings",
			"type" => "title"),

	array(	"name" => "Block Search Engine",
			"desc" => "Check the box to block search engine",
			"id" => $shortname."_block_bot",
			"std" => "",
			"type" => "checkbox"),

	array(	"name" => "Authorization Code for Search Engine",
			"desc" => "Enter the meta code provided by the search engine.",
			"id" => $shortname."_special_meta",
			"std" => "",
			"type" => "textarea"),

	array(	"name" => "Description Meta Home Page",
			"desc" => "Please enter the text for the meta description of the home page",
			"id" => $shortname."_desc_block",
			"std" => "",
			"type" => "textarea"),

	array(	"name" => "Google analytic account number",
			"desc" => "Please enter the google analytic account here",
			"id" => $shortname."_google_analytic_account_no",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Google analytic code",
			"desc" => "Please enter the google analytic code here",
			"id" => $shortname."_google_analytic",
			"std" => "",
			"type" => "textarea"),

	array(	"name" => "Google adword conversion code",
			"desc" => "Please enter the Google adword conversion code here",
			"id" => $shortname."_google_adword",
			"std" => "",
			"type" => "textarea"),


	array(	"name" => "Twitter Link",
			"desc" => "Please enter the twitter link",
			"id" => $shortname."_social_twitter",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Facebook Link",
			"desc" => "Please enter the facebook link",
			"id" => $shortname."_social_facebook",
			"std" => "",
			"type" => "text"),

	array(	"name" => "Flickr",
			"desc" => "Please enter the flickr link",
			"id" => $shortname."_social_flickr",
			"std" => "",
			"type" => "text"),

	array(	"type" => "close")

);

function mytheme_add_admin() {

    global $themename, $shortname, $options, $wpdb;

    if ( $_GET['page'] == basename(__FILE__) ) {

        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], stripslashes($_REQUEST[ $value['id'] ])  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=settings.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=settings.php&reset=true");
            die;

        }
    }

    add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');

}

function mytheme_admin() {

    global $themename, $shortname, $options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';

?>
<div class="wrap">
<h2><?php echo $themename; ?> settings</h2>

<form method="post">



<?php foreach ($options as $value) {

	switch ( $value['type'] ) {

		case "open":
		?>
        <table width="100%" border="0" style="padding:10px;">



		<?php break;

		case "close":
		?>

        </table>


		<?php break;

		case "title":
		?>
		<tr>
        	<td colspan="2"><h3><?php echo $value['name']; ?></h3></td>
            <tr><td colspan="2" style="margin-bottom:5px;border-bottom:1px solid #dddddd;">&nbsp;</td></tr><tr><td colspan="2">&nbsp;</td></tr>
        </tr>


		<?php break;

		case 'text':
		?>

        <tr>
            <td valign="top" width="20%" rowspan="2" ><?php echo $value['name']; ?></td>
            <td width="80%"><input style="width:400px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" /></td>
        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php
		break;

		case 'textarea':
		?>

        <tr>
            <td valign="top" width="20%" rowspan="2" ><?php echo stripslashes($value['name']); ?></td>
            <td width="80%"><textarea name="<?php echo $value['id']; ?>" style="width:400px; height:200px;" type="<?php echo $value['type']; ?>" cols="" rows=""><?php if ( get_settings( $value['id'] ) != "") { echo get_settings( stripslashes($value['id']) ); } else { echo stripslashes($value['std']); } ?></textarea></td>

        </tr>

        <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
        </tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php
		break;

		case 'select':
		?>
        <tr>
            <td valign="top" width="20%" rowspan="2"><?php echo $value['name']; ?></td>
            <td width="80%"><select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option><?php } ?></select></td>
       </tr>

       <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
       </tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php
        break;

		case 'selectplus':
		?>
        <tr>
            <td valign="top" width="20%" rowspan="2"><?php echo $value['name']; ?></td>
            <td width="80%"><select style="width:240px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>"><?php foreach ($value['options'] as $option) { ?><option<?php if ( get_settings( $value['id'] ) == $option[1]) { echo ' selected="selected"'; } elseif ($option[1] == $value['std']) { echo ' selected="selected"'; } ?> value="<?php echo $option[1];?>"><?php echo $option[0]; ?></option><?php } ?></select></td>
       </tr>

       <tr>
            <td><small><?php echo $value['desc']; ?></small></td>
       </tr><tr><td colspan="2">&nbsp;</td></tr>

		<?php
        break;

		case "checkbox":
		?>
            <tr>
            <td width="20%" rowspan="2" valign="middle"><?php echo $value['name']; ?></td>
                <td width="80%"><? if(get_settings($value['id'])){ $checked = "checked=\"checked\""; }else{ $checked = ""; } ?>
                        <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
                        </td>
            </tr>

            <tr>
                <td><small><?php echo $value['desc']; ?></small></td>
           </tr><tr><td colspan="2">&nbsp;</td></tr>

        <?php 		break;

		case "page":
		?>
            <tr>
            <td width="20%" rowspan="2" valign="top"><?php echo $value['name']; ?></td>
                <td width="80%">
									<?php wp_dropdown_pages("name=".$value['id']."&show_option_none=- Select -&selected=" .get_settings($value['id']) ); ?>
                </td>
            </tr>

            <tr>
                <td><small><?php echo $value['desc']; ?></small></td>
           </tr><tr><td colspan="2">&nbsp;</td></tr>

        <?php 		break;




}
}
?>

<!--</table>-->

<p class="submit">
<input class="button-primary" name="save" type="submit" value="Save changes" />
<input type="hidden" name="action" value="save" />
</p>
</form>
<!--<form method="post">
<p class="submit">
<input name="reset" type="submit" value="Reset" />
<input type="hidden" name="action" value="reset" />
</p>
</form>-->

<?php
}

add_action('admin_menu', 'mytheme_add_admin');

remove_action ('wp_head', 'wp_generator');

?>