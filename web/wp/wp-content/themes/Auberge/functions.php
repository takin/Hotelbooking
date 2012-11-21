<?php
//  TABLE OF CONTENTS

//  Localization Initialize
//  SEO Stuff
//  The 'Read More' link
//  Nav Animation
//  wp_page_menu Filter
//  Archive Pagination
//  Search Highlighting
//	Dynamic Titles
//  Widgets
//  Get the Image
//  Comment output
error_reporting(0);
define('EXT', '.php');
define('CI_APPPATH', CI_ABSPATH.'system/application/');

/** mobile detection **/
$mobile_enable = get_option('aj_enable_mobile');
if($mobile_enable == TRUE)
{
  mobile_detection();
}
function mobile_detection()
{
  require_once ('ci/User_agent.php');
  $user_agent  = new CI_User_agent();

  $site_pref = FALSE;
  if(!empty($_GET['site']))
  {
    if($_GET['site'] == 'full')
    {
      $site_pref = 'full';
      setcookie("site", $site_pref, time()+3600,"/");
    }
    else
    {
      setcookie("site", '', time()-3600,"/");
    }
  }
  elseif(!empty($_COOKIE['site']) && ($_COOKIE['site'] == 'full'))
  {
    $site_pref = 'full';
  }
  elseif(!empty($_COOKIE['site']))
  {
    setcookie("site", '', time()-3600,"/");
  }

  if($user_agent->is_mobile() && !$user_agent->is_tablet() && ($site_pref === FALSE)&&($_GET['print']!='mobile'))
  {
    header("Location: ".get_option('aj_api_search'));
    exit;
  }

}

global $aubergedb;
$aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
$aubergedb->hide_errors();

/* Localization Initialize ********************************************/
// This sets the basename of the theme for localization.
//setlocale(LC_ALL, get_option('aj_set_locale'));

global $locale;
global $dev_site;

$locale = get_option('aj_set_locale');
$dev_site = (get_option('aj_dev_site') == true);

$mofile = get_template_directory()."/".get_option('aj_lang_pomo').".mo";
load_textdomain('auberge', $mofile);

setlocale(LC_ALL, $locale);

/** option optimization **/
/***** include librairies ***/


require_once ('ci/db_wp_currencies.php');
require_once ('admin/settings.php');
require_once ('admin/widgets.php');

require_once (CI_ABSPATH.'system/application/helpers/misc_tools_helper.php');

global $current_aj_user;
get_user_info();

/* Enable Taxonomies */

add_action( 'init', 'create_my_taxonomies', 0 );

add_filter('language_attributes', 'remove_utf8_from_lang');
function remove_utf8_from_lang($language_attributes)
{
    return preg_replace('/ lang=\"(.)+\.utf8"/', ' lang="'.get_option('aj_lang_code').'"', $language_attributes);
}

function create_my_taxonomies() {
	register_taxonomy( __('ville','auberge'), 'post', array( 'hierarchical' => false, 'label' => __('Ville','auberge'), 'query_var' => true, 'rewrite' => true ) );
	register_taxonomy( __('pays','auberge'), 'post', array( 'hierarchical' => false, 'label' => __('Pays','auberge'), 'query_var' => true, 'rewrite' => true ) );
	register_taxonomy( __('attraits','auberge'), 'post', array( 'hierarchical' => false, 'label' => __('Attraits','auberge'), 'query_var' => true, 'rewrite' => true ) );
	register_taxonomy( __('date_evenements','auberge'), 'post', array( 'hierarchical' => false, 'label' => __('Date Événements','auberge'), 'query_var' => true, 'rewrite' => true ) );
}

remove_filter( 'pre_term_description', 'wp_filter_kses' );


/* SEO Stuff ********************************************/
// This converts the tags associated with a post into SEO-friendly keywords

function keyword_tags() {
	$posttags = get_the_tags();
	foreach((array)$posttags as $tag) {
		$keyword_tags .= $tag->name . ',';
	}
	echo '<meta name="keywords" content="'.$keyword_tags.'" />';
}


/* Limit excerpt */

function string_limit_words($string, $word_limit)
{
$words = explode(' ', $string, ($word_limit + 1));
if(count($words) > $word_limit)
array_pop($words);
return implode(' ', $words);
}

function string_limit_char( $str, $len, $cut = true ) {
    if ( mb_strlen ( $str ,'UTF-8') <= $len ) return $str;
    $string = mb_substr( $str, 0, $len ,'UTF-8');
    return ( $cut ? $string : mb_substr( $str, 0, strrpos( $string, ' ' ),'UTF-8' ) ) . ' ...';
}


function string_limit_words_content($string, $limit) {
  $content = explode(' ',  strip_tags($string), $limit);
  if (count($content)>=$limit) {
    array_pop($content);
    $content = implode(" ",$content).'...';
  } else {
    $content = implode(" ",$content);
  }
  $content = preg_replace('/\[.+\]/','', $content);

  $content = str_replace(']]>', ']]&gt;', $content);
  return $content;
}

/* check if page */

function is_type_page() {
global $post;
if ($post->post_type == 'page') {
return true;
} else {
return false;
}}

/* The 'More' Link ********************************************/
// This is a filter for styling the "Read More" link that appears when creating excerpts

add_filter( 'the_content_more_link', 'my_more_link', 10, 2 );

function my_more_link( $more_link, $more_link_text ) {
	return str_replace( $more_link_text, __('Lire la suite','auberge'), $more_link );
}

/* wp_page_menu Filter ********************************************/
// This is a filter that allows a custom ID to be added to your nav

function add_menuclass($ulclass) {
return preg_replace('/<ul>/', '<ul id="nav">', $ulclass, 1);
}
add_filter('wp_page_menu','add_menuclass');



/* Archive Pagination ********************************************/
// This adds next/previous pagination to the custom Archives page

function my_post_limit($limit) {
	global $paged, $myOffset, $postsperpage;
	if(empty($paged)) {
		$paged = 1;
	}
	$pgstrt = ((intval($paged) -1) * $postsperpage) + $myOffset . ', ';
	$limit = 'LIMIT '.$pgstrt.$postsperpage;
	return $limit;
}


/* Search Highlighting ********************************************/
// This highlights search terms in both titles, excerpts and content

function search_excerpt_highlight() {
	$excerpt = get_the_excerpt();
	$keys = implode('|', explode(' ', get_search_query()));
	$excerpt = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $excerpt);

	echo '<p class="excerpt">' . $excerpt . '</p>';
}


function search_title_highlight() {
	$title = get_the_title();
	$keys = implode('|', explode(' ', get_search_query()));
	$title = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $title);

	echo $title;
}



/* Dynamic Titles ********************************************/
// This sets your <title> depending on what page you're on, for better formatting and for SEO

function dynamictitles() {

	if ( is_single() ) {
      wp_title('');
      echo (' | ');
      bloginfo('name');

} else if ( is_page() || is_paged() ) {
      bloginfo('name');
      wp_title('|');

} else if ( is_author() ) {
      bloginfo('name');
      wp_title(' | '.__('Author','auberge').'');

} else if ( is_category() ) {
      bloginfo('name');
      wp_title(' | '.__('Archive pour','auberge').'');
      ('');

} else if ( is_tag() ) {
      bloginfo('name');
      echo (' | '.__('Archive mot clé pour','auberge').'');
      wp_title('');

} else if ( is_archive() ) {
      bloginfo('name');
      echo (' | '.__('Archive pour','auberge').'');
      wp_title('');

} else if ( is_search() ) {
      bloginfo('name');
      echo (' | '.__('Résultat de recherche','auberge').'');

} else if ( is_404() ) {
      bloginfo('name');
      echo (' | '.__('Erreur 404 page non trouvée','auberge').'');

} else if ( is_home() ) {
      bloginfo('name');
      echo (' | ');
      bloginfo('description');

} else {
      bloginfo('name');
      echo (' | ');
      echo (''.$blog_longd.'');
}
}



/**
* Register scripts to script loader
*/
function register_scripts() {
// Register jQuery
	wp_deregister_script('jquery');
	wp_register_script('jquery', get_option('aj_api_url').'js/jquery-1.3.2.min.js', '', '1.3.2');
	wp_register_script('jtools', get_option('aj_api_url').'js/jtools.js',array('jquery'));
	wp_register_script('translate',get_option('aj_api_url').'js/jquery.translate-1.3.9.js',array('jquery'));
}


/*
Post Thumbnail feature
*/

add_theme_support('post-thumbnails');


if ( function_exists('add_theme_support') ) {
    add_theme_support('post-thumbnails');
}


/* Get the thumbnail URL */

function get_thumb_url ($post_id) {

	global $wpdb;
	$mainurl = get_bloginfo('url');

	//$thumb_url = $wpdb->get_var("SELECT guid FROM $wpdb->posts WHERE post_parent = '$post_id' AND ID IN (SELECT meta_value FROM wp_postmeta WHERE post_id = '$post_id' AND meta_key = '_thumbnail_id')");
	$thumb_url = $wpdb->get_var("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND post_id IN (SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key = '_thumbnail_id')");
	if (!empty($thumb_url)){
	if(get_option( 'upload_path' )==''){
		//$fullurl =	$thumb_url;
		$fullurl =	'http://'.$_SERVER['HTTP_HOST'].'/wp-content/uploads/'.$thumb_url;
		return $fullurl;
	}else{
		$fullurl =	'http://'.$_SERVER['HTTP_HOST'].'/'.get_option( 'upload_path' ).'/'.$thumb_url;
		//$fullurl =	$thumb_url;
		return $fullurl;
	}}

}


/* Get Post Image ********************************************/

/*
To retrieve a post image and resize it with TimThumb:
<?php echo get_post_image (get_the_id(), '', '', '' .get_bloginfo('template_url') .'/scripts/timthumb.php?zc=1&amp;w=105&amp;h=85&amp;src='); ?></a>
*/


function get_post_image ($post_id=0, $width=0, $height=0, $img_script='') {
	global $wpdb;
	if($post_id > 0) {

		 // select the post content from the db

		 $sql = 'SELECT post_content FROM ' . $wpdb->posts . ' WHERE id = ' . $wpdb->escape($post_id);
		 $row = $wpdb->get_row($sql);
		 $the_content = $row->post_content;
		 if(strlen($the_content)) {

			  // use regex to find the src of the image

			preg_match("/<img src\=('|\")(.*)('|\") .*( |)\/>/", $the_content, $matches);
			if(!$matches) {
				preg_match("/<img class\=\".*\" title\=\".*\" src\=('|\")(.*)('|\") .*( |)\/>/U", $the_content, $matches);
			}
			$the_image = '';
			$the_image_src = $matches[2];
			$frags = preg_split("/(\"|')/", $the_image_src);
			if(count($frags)) {
				$the_image_src = $frags[0];
			}

			  // if src found, then create a new img tag

			  if(strlen($the_image_src)) {
				   if(strlen($img_script)) {

					    // if the src starts with http/https, then strip out server name

					    if(preg_match("/^(http(|s):\/\/)/", $the_image_src)) {
						     $the_image_src = preg_replace("/^(http(|s):\/\/)/", '', $the_image_src);
						     $frags = split("\/", $the_image_src);
						     array_shift($frags);
						     $the_image_src = '/' . join("/", $frags);
					    }
					    $the_image = '<img alt="" src="' . $img_script . $the_image_src . '" />';
				   }
				   else {
					    $the_image = '<img alt="" src="' . $the_image_src . '" width="' . $width . '" height="' . $height . '" />';
				   }
			  }
			  return $the_image;
		 }
	}
}



/* Comments Callback ********************************************/
// This code abstracts out comment code and makes the markup editable

function mytheme_comment($comment, $args, $depth) {

	$GLOBALS['comment'] = $comment;
?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div class="comment-wrap" id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
            	<?php $siteurl = get_bloginfo('template_url');?>
				<?php echo get_avatar($comment,$size='60',$default=$siteurl.'/images/gravatar.jpg' ); ?>
				<div class="commentmetadata">

					<div class="comment-date">
						<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
							<?php echo get_comment_date('j F'); ?>
						</a>
						<?php edit_comment_link(__('(Modifier)','auberge')) ?>
					</div>
				</div>
			</div>
<?php
	if ($comment->comment_approved == '0') {
?>
			<em><?php _e('Votre commentaire est en attente d\'approbation.','auberge') ?></em>
			<br />
<?php }?>
	<div class="single-comment">
   <cite class="fn"><?php echo get_comment_author_link(); ?></cite>
	<?php comment_text(); ?>
    </div>
			<div class="reply">

<?php
	comment_reply_link(
		array_merge( $args, array(
			'depth' => $depth,
			'reply_text' => __('Répondre','auberge'),
			'login_text' => __('Vous connecter pour répondre','auberge'),
			'max_depth' => $args['max_depth'])
		)
	);
?>

            </div>
		</div>
<?php
}


function comment_add_microid($classes) {
	$c_email=get_comment_author_email();
	$c_url=get_comment_author_url();
	if (!empty($c_email) && !empty($c_url)) {
		$microid = 'microid-mailto+http:sha1:' . sha1(sha1('mailto:'.$c_email).sha1($c_url));
		$classes[] = $microid;
	}
	return $classes;
}
add_filter('comment_class','comment_add_microid');

/* Subscription cookie functions */


 function get_page_name($page_id)
{
	global $wpdb;
	$page_name = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$page_id."'");
	return $page_name;
}

function get_page_url($page_id)
{
	global $wpdb;
	$page_url = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE ID = '".$page_id."'");
	return $page_url;
}

function get_page_id($page_name)
{
	global $wpdb;
	$page_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_title = '".$page_name."'");
	return $page_id;
}

function get_id_out() {
global $wp_query;
$thePostID = $wp_query->post->ID;
return $thePostID;
}

/* Ad words set cookie */

function adword_init()
{

  global $aubergedb;
  $keywords = $aubergedb->get_results("SELECT keyword FROM refer_keywords");

  if(!$keywords)
  {
    error_log("Problem with SQL query on ".DB_NAME_AUBERGE,0);
  }
  else
  {
    foreach ($keywords as $row) {
        if(!is_null($_GET[$row->keyword]))
        {
          //If a cookie with same name exist delete it
          if(!is_null($_COOKIE["adword_".$row->keyword]))
          {
            setcookie ("adword_".$row->keyword, "", time() - 3600);
            unset($_COOKIE["adword_".$row->keyword]);
          }

          //Set Cookie. Time: 30 days=60*60*24*30=2592000
          setcookie("adword_".$row->keyword, $_GET[$row->keyword], time()+2592000,"/");
        }
    }
  }

}

/* Currency cookie */
currency_init();
function currency_init()
{
  if(!empty($_GET['currency']))
  {
    global $aubergedb;
    $currency = new Db_currencies($aubergedb);
    $currency = $currency->validate_currency($_GET['currency']);

    if(!empty($currency))
    {
       setcookie("currency_selected", $currency, time()+2592000,"/");
    }
  }
}
city_init();
function city_init()
{
  if(!empty($_GET['country']))
  {
    setcookie("country_selected", $_GET['country'], time()+2592000,"/");

    if(!empty($_GET['city']))
    {
       setcookie("city_selected", $_GET['city'], time()+2592000,"/");
    }
    return;
  }
/*
 * presets country and city to most popular
 * should be put in option
 *
/*
  $top = get_top_cities("",1);
  if(!empty($top[0]))
  {
    $default_country = $top[0]->property_country;
    $default_city    = $top[0]->property_city;
    if(!empty($default_country))
    {
      setcookie("country_selected", $default_country, time()+2592000,"/");
      if(!empty($default_city))
      {
         setcookie("city_selected", $default_city, time()+2592000,"/");
      }
      return;
    }
  }
*/
  if((empty($_COOKIE['country_selected'])) && (empty($_COOKIE['city_selected'])))
  {
    $default_country = get_option('aj_default_country');
    $default_city    = get_option('aj_default_city');

    if(!empty($default_country))
    {
      setcookie("country_selected", $default_country, time()+2592000,"/");
      if(!empty($default_city))
      {
         setcookie("city_selected", $default_city, time()+2592000,"/");
      }

    }
    return;
  }
}

function get_selected_currency()
{
  global $current_aj_user;

  if(!empty($_GET['currency']))
  {
    global $aubergedb;
    $currency = new Db_currencies($aubergedb);

    return $currency->validate_currency($_GET['currency']);
  }
  elseif(!empty($_COOKIE['currency_selected']))
  {
    global $aubergedb;
    $currency = new Db_currencies($aubergedb);

    return $currency->validate_currency($_COOKIE['currency_selected']);
  }
  elseif(is_logged_in())
  {
    return get_user_currency(get_user_id());
  }
  elseif(!empty($current_aj_user->CurrencyCode))
  {
    return $current_aj_user->CurrencyCode;
  }
  else
  {
    return get_option('aj_default_currency');
  }

}
/* Validate currency -> eventually to be moved to a CI folder file*/
function validate_currency($currency)
  {
    switch(strtolower($currency))
    {
      case strtolower("EUR"):
        return "EUR";
      case strtolower("USD"):
        return "USD";
      case strtolower("GBP"):
        return "GBP";
      default:
        return NULL;
    }

    return NULL;
  }

/* Check if user is logged in */

function is_logged_in()
{

  $cisess_cookie = $_COOKIE['aj_session'];
  $cisess_cookie = stripslashes($cisess_cookie);
  $cisess_cookie = unserialize($cisess_cookie);

  global $aubergedb;
  $cisess_session_id = $aubergedb->escape($cisess_cookie['session_id']);
  $ci_res = $aubergedb->get_row("SELECT user_data FROM ci_sessions WHERE session_id = '$cisess_session_id' LIMIT 1");


  //If user_data is set
  if($ci_res == false)
  {
    return false;
  }
  else
  {
    $cisess_data = unserialize($ci_res->user_data);
    return $cisess_data["status"]==1;
  }

  return false;
}

function get_user_id()
{

  $cisess_cookie = $_COOKIE['aj_session'];
  $cisess_cookie = stripslashes($cisess_cookie);
  $cisess_cookie = unserialize($cisess_cookie);

  global $aubergedb;
  $cisess_session_id = $aubergedb->escape($cisess_cookie['session_id']);
  $ci_res = $aubergedb->get_row("SELECT user_data FROM ci_sessions WHERE session_id = '$cisess_session_id' LIMIT 1");


  //If user_data is set
  if($ci_res == false)
  {
    return false;
  }
  else
  {
    $cisess_data = unserialize($ci_res->user_data);
    return $cisess_data["user_id"];
  }

  return NULL;
}

function get_user_currency($user_id)
{
  if(!empty($user_id))
  {
    global $aubergedb;
    $query = "SELECT currencies.currency_code AS favorite_currency_code FROM user_profiles";
    $query.= " LEFT JOIN currencies ON currencies.currency_id = user_profiles.favorite_currency";
    $query.= " WHERE user_id = $user_id";
    return $aubergedb->get_var($query);
  }
  return NULL;
}
/* Get last booking info */
function get_last_booking_info()
{
  global $aubergedb;

  $api_letters = get_option('aj_api_site_data');
  if(empty($api_letters))
  {
    $api_letters = "hw";
  }

  $sql_query = "SELECT last_booking.property_name, last_booking.property_number, num_nights, SUM(beds) AS guests, IFNULL(property_type,'property') as property_type ";
  $sql_query.= " FROM";
  $sql_query.= " (";
  $sql_query.= "     SELECT transaction_id, property_name, property_number, num_nights";
  $sql_query.= "     FROM transactions_hostelworld ";
  $sql_query.= "     WHERE customer_booking_reference NOT LIKE'%TEST%' AND (test_booked != 1 OR (test_booked IS NULL)) ";
  if($api_letters == "hb")
  {
    $sql_query.= ' AND API_booked = "HB"';
  }
  else
  {
    $sql_query.= ' AND API_booked = "HW"';
  }
  $sql_query.= "     ORDER BY booking_time DESC";
  $sql_query.= "     LIMIT 1";
  $sql_query.= " ) AS last_booking";
  $sql_query.= " LEFT JOIN rooms_per_transactions ON last_booking.transaction_id = rooms_per_transactions.transaction_id ";

  if($api_letters == "hb")
  {
    $sql_query.= " LEFT JOIN hb_hostel ON last_booking.property_number = hb_hostel.property_number";
  }
  else
  {
    $sql_query.= " LEFT JOIN hw_hostel ON last_booking.property_number = hw_hostel.property_number";
  }

  $sql_query.= " GROUP BY rooms_per_transactions.transaction_id";

  return $aubergedb->get_row($sql_query);
}

/* translate country via database */
function translate_country($country_search, $lang = "en")
{
  global $aubergedb;

  $lang           = $aubergedb->escape($lang);
  $country_search = $aubergedb->escape($country_search);

  $sql_query = "SELECT country_".$lang." FROM cities2";
  $sql_query.= " WHERE country_en LIKE '$country_search'";
  $sql_query.= " OR country_fr LIKE '$country_search'";
  $sql_query.= " GROUP BY country_en";

  $translation = $aubergedb->get_var($sql_query);

  if(is_null($translation))
  {
      $translation = $country_search;
  }
  return $translation;
}

function get_ci_link($keyword)
{
  global $aubergedb;

  $host    = $aubergedb->escape($_SERVER['HTTP_HOST']);
  $keyword = $aubergedb->escape($keyword);

  $query = "SELECT * FROM site_links";
  $query.= " JOIN site_domains ON site_links.site_domain_id = site_domains.site_domain_id";
  $query.= " JOIN links ON site_links.link_id = links.link_id";
  $query.= " WHERE site_domain LIKE '%".$host."'";
  $query.= " AND keyword LIKE '".$keyword."'";

  $query = $aubergedb->get_row($query);

  if (!empty($query))
  {
    $link = $query->link;
    if(strcmp(substr($link, -5, 5),"/:any")==0) $link = substr($link, 0, -5);
    return $link;
  }

  return "";
}

function get_ajax_url()
{
  return get_option('aj_api_url').get_site_api();
}
function get_site_api()
{
  $api_letters = get_option('aj_api_site_data');
  if(empty($api_letters))
  {
    $api_letters = "hw";
  }
  return $api_letters;
}

function get_site_lang($domain = NULL)
{
  if(empty($domain)) $domain = $_SERVER['HTTP_HOST'];

  require_once ('ci/db_wp_links.php');
  global $aubergedb;
  $lang = new Db_links($aubergedb,$domain);
  return $lang->get_lang_from_domain($domain);
}

function select_currency($select_id,
                         $select_name,
                         $currency_selected = "",
                         $otherAttributes = "",
                         $text_lang = "en",
                         $no_selection_text = NULL)
{
  global $aubergedb;
  $currencies = new Db_currencies($aubergedb);
  $currencies->select_currency($select_id, $select_name, $currency_selected, $otherAttributes, $text_lang, $no_selection_text);
}

function build_property_page_link($property_type, $property_name, $property_number, $domain = NULL)
{
  if(empty($domain)) $domain = $_SERVER['HTTP_HOST'];

  global $aubergedb;
  require_once("ci/db_wp_links.php");

  $wp_links  = new Db_links($aubergedb,$domain);
  $property_type = $wp_links->get_property_type_link($property_type, get_site_lang($domain));


  return "http://".$domain."/".$property_type."/".url_title($property_name)."/$property_number";
}

function get_top_hostels()
{
  global $aubergedb;
  global $current_aj_user;
  global $dev_site;
  require_once("ci/db_wp_hostels.php");

  $hostels  = new Db_hostels($aubergedb);

  $countrycode = "";
  if(!empty($current_aj_user->CountryCode))
  {
    $countrycode = $current_aj_user->CountryCode;
  }

  //fourth argument true/false decide whether the test bookings are included in the top cities calculations
  $top_hostels = $hostels->get_top_hostels(get_site_api(), $countrycode, get_selected_currency(), get_site_lang(), $dev_site);

  require_once("ci/db_wp_translation.php");
  $translate = new Db_translate($aubergedb);
  $top_hostels = $translate->translate_top_hostels(get_site_api(), $top_hostels, get_site_lang());

  return $top_hostels;
}

function get_top_cities($continent_en = "", $count = 4)
{
  global $aubergedb;
  global $current_aj_user;
  global $dev_site;
  require_once("ci/db_wp_hostels.php");

  $hostels  = new Db_hostels($aubergedb);

  $countrycode = "";
  if(!empty($current_aj_user->CountryCode))
  {
    $countrycode = $current_aj_user->CountryCode;
  }

  //sixth argument true/false decide whether the test bookings are included in the top cities calculations
  return $hostels->get_top_cities(get_site_api(), "xx", get_selected_currency(), get_site_lang(), $continent_en, $dev_site, "", $count);
}

function get_user_info()
{
  global $current_aj_user;
  global $aubergedb;
  require_once("ci/db_wp_currencies.php");

  $currencies  = new Db_currencies($aubergedb);

  $current_aj_user = freeGeoFromIP($_SERVER["REMOTE_ADDR"]);

  if(!empty($current_aj_user) && !empty($current_aj_user->CountryCode))
  {
    $current_aj_user->CurrencyCode = $currencies->get_currency_of_country($current_aj_user->CountryCode);
  }
}

if ( ! function_exists('url_title'))
{
	function url_title($str, $separator = 'dash', $lowercase = FALSE)
	{
		if ($separator == 'dash')
		{
			$search		= '_';
			$replace	= '-';
		}
		else
		{
			$search		= '-';
			$replace	= '_';
		}

		$trans = array(
						'&\#\d+?;'				=> '',
						'&\S+?;'				=> '',
						'\s+'					=> $replace,
						'[^a-z0-9\-\._]'		=> '',
						$replace.'+'			=> $replace,
						$replace.'$'			=> $replace,
						'^'.$replace			=> $replace,
						'\.+$'					=> ''
					  );

		$str = strip_tags($str);

		foreach ($trans as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		if ($lowercase === TRUE)
		{
			$str = strtolower($str);
		}

		return trim(stripslashes($str));
	}
}
?>