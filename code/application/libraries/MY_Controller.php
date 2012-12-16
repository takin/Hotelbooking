<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

define('HW_API', 0);
define('HB_API', 1);

define('LINK_PROPERTY', 0);
define('LINK_CITY', 1);
define('LINK_COUNTRY', 2);


class MY_Controller extends Controller
{
  function MY_Controller()
  {
    parent::Controller();
  }
}

class I18n_site extends MY_Controller
{
  var $site_conf = "site_default";
  var $site_lang = "fr";
  var $html_lang_code = "fr-FR";

  var $link_hostel_page = "";

  var $api_used   = HW_API;
  var $api_forced = false;  //true when a HW site is force to HB by get parameter
  var $hostel_controller = "chostel";

  var $site_user;
  var $user_agent = NULL;
  var $user_agent_mobile = FALSE;
  var $user_agent_mobile_bypass = FALSE;

  var $user_id = 0;

  var $translation_source_slug = NULL;

  function I18n_site()
  {
    parent::MY_Controller();

    $this->site_user = new stdClass();

    $this->load_lang_context($_SERVER['HTTP_HOST']);
    $this->get_site_user_info();

    //Ouput class might need to check for mobile properties for cache purpose
    //If output class has already set the mobile properties take it else get it
    if($this->wordpress->get_option("aj_enable_mobile") == TRUE)
    {
      if(!isset($this->output->user_agent_mobile)||empty($this->output->user_agent))
      {
        $this->get_user_agent_info();
      }
      else
      {
        $this->user_agent        = $this->output->user_agent;
        $this->user_agent_mobile = $this->output->user_agent_mobile;
      }
      if(!isset($this->output->user_agent_mobile_bypass))
      {
        $this->set_user_agent_pref('site');
      }
      else
      {
        $this->user_agent_mobile_bypass = $this->output->user_agent_mobile_bypass;
      }
    }

    //Carabiner must be load after de lang context initialization in order to get the correct base url
    $this->load->library('carabiner');
    $this->load->model('Db_term_translate');
    $this->js_init();
  }

  function js_init()
  {
    $this->carabiner->js('site_init.php?lang='.$this->site_lang,'site_init.php?lang='.$this->site_lang,FALSE,TRUE);
  }

  function set_user_agent_pref($var_name, $cookie_time = 3600)
  {

    $site_pref = $this->input->get($var_name, TRUE);
    if(empty($site_pref))
    {
      $site_pref = $this->input->cookie($var_name, TRUE);
      if($site_pref != 'full')
      {
        $site_pref = false;
      }
    }
    else
    {
      $this->load->helper('cookie');
      if($site_pref == 'full')
      {
        $cookie = array('name'   => $var_name,
                        'value'  => 'full',
                        'expire' => $cookie_time);
        set_cookie($cookie);
      }
      else
      {
        delete_cookie($var_name);
      }
    }

    if($site_pref == 'full')
    {
      $this->user_agent_mobile_bypass = true;
    }
    else
    {
      $this->user_agent_mobile_bypass = false;
    }
  }

  function get_user_agent_info()
  {
    $this->load->library('user_agent');

    $this->user_agent = $this->agent->agent_string();

    if($this->agent->is_mobile() && !$this->agent->is_tablet())
    {
      $this->user_agent_mobile = TRUE;
    }
  }

  function get_site_user_info()
  {
    $this->load->library('session');

    //To limit 3rd party IP detection on every page store info in session cookies
    $user_ip_address = $this->session->userdata('user_ip_address');
    if(empty($user_ip_address) || ($user_ip_address != $_SERVER["REMOTE_ADDR"]))
    {

      $this->site_user = freeGeoFromIP($_SERVER["REMOTE_ADDR"]);

      if(empty($this->site_user))
      {
        $this->site_user = new stdClass();
        $this->site_user->CountryCode = "";
        $this->site_user->RegionCode = "";
        $this->site_user->CountryName = "";
        $this->site_user->City = "";
        $this->site_user->Latitude = "";
        $this->site_user->Longitude = "";
      }

      if(!isset($this->site_user->CountryCode))
      {
        $this->site_user->CountryCode = "";
      }
      if(!isset($this->site_user->RegionCode))
      {
        $this->site_user->RegionCode = "";
      }
      if(!isset($this->site_user->CountryName))
      {
        $this->site_user->CountryName = "";
      }
      if(!isset($this->site_user->City))
      {
        $this->site_user->City = "";
      }
      if(!isset($this->site_user->Latitude))
      {
        $this->site_user->Latitude = "";
      }
      if(!isset($this->site_user->Longitude))
      {
        $this->site_user->Longitude = "";
      }

      $userdata = array(
                     'user_ip_address'    => $_SERVER["REMOTE_ADDR"],
                     'user_country_code'  => $this->site_user->CountryCode,
                     'user_region_code'   => $this->site_user->RegionCode,
                     'user_country_name'  => $this->site_user->CountryName,
                     'user_city_name'     => $this->site_user->City,
                     'user_latitude'      => $this->site_user->Latitude,
                     'user_longitude'     => $this->site_user->Longitude
                 );

      $this->session->set_userdata($userdata);
    }
    else
    {
      $this->site_user->Ip         = $_SERVER["REMOTE_ADDR"];
      $this->site_user->CountryCode = $this->session->userdata('user_country_code');
      $this->site_user->CountryName = $this->session->userdata('user_country_name');
      $this->site_user->RegionCode  = $this->session->userdata('user_region_code');
      $this->site_user->City        = $this->session->userdata('user_city_name');
      $this->site_user->Latitude    = $this->session->userdata('user_latitude');
      $this->site_user->Longitude   = $this->session->userdata('user_longitude');
    }

    if(!empty($this->site_user) && !empty($this->site_user->CountryCode))
    {
      $this->load->model('Db_currency');
      $this->site_user->CurrencyCode = $this->Db_currency->get_currency_of_country($this->site_user->CountryCode);
    }

  }

  function load_lang_context($host)
  {
    $_SERVER['HTTP_HOST'] = $host;

    if(!is_null($domain = $this->Db_links->get_domain($host)))
    {
      $this->config->set_item('base_url',"http://". $domain->site_domain);

      if(!empty($domain->secure_site_domain))
      {
        $this->config->set_item('secure_base_url',"https://". $domain->secure_site_domain);
      }
      else
      {
        $this->config->set_item('secure_base_url',"http://". $domain->site_domain);
      }

      $this->site_lang      = $domain->lang;
      $this->site_domain_id = $domain->site_domain_id;
      $this->html_lang_code = $domain->HTML_lang_code;
      $this->translation_source_slug = $domain->translation_key_slug;
      $this->translation_quota = $domain->translation_quota;

      //For better performance, load all links in variables
      $this->link_hostel_page = $this->Db_links->get_link("info");

      //Set CI langage
      $this->config->set_item('language',$domain->CI_lang);

      //Load domain config file
      /* TONOTICE config file cannot be loaded twice
       *
       * If a config file is loaded a second time the changes have no effect.
       * Therefore, the previous config file data is kept until a new config file is loaded again
       *
       */
      if(!empty($domain->conf_filename)&&file_exists(APPPATH."config/".$domain->conf_filename.".php"))
      {
        $this->site_conf = $domain->conf_filename;
      }
      else
      {
        $this->site_conf = "site_default";
      }

      $this->config->load($this->site_conf,TRUE);

      if (ISWINDOWS)
      {
         putenv('LANG='.$domain->locale);
      }

      //locale settings for gettext and datetime format
      setlocale(LC_ALL, $domain->locale);

//       setlocale(LC_ALL, "fr_FR.utf8");

      //Keep US number for numeric values
      //prevent number in SQL queries to use comma and bug the SQL queries
      setlocale(LC_NUMERIC, "en_US.utf8");

      $this->i18n->set_lang_filename($domain->mo_filename);
      $this->i18n->load_gettext();

//      $this->site_lang = "fr";

      $this->wordpress->load_wordpress_db($this->config->item("wp_db_tag",$this->site_conf));

      //get wordpress theme options
      $this->config->set_item('site_title',$this->wordpress->get_option("blogname"));
      $this->config->set_item('site_name',$this->wordpress->get_option("aj_api_name"));
      $this->config->set_item('wp_base_url',$this->wordpress->get_option("siteurl"));
      $this->config->set_item('site_currency_default',$this->wordpress->get_option("aj_default_currency"));
      $this->config->set_item('site_settle_currency_default',$this->wordpress->get_option("aj_default_settle_currency"));

      //This is for backward compatibility
      //To avoid conflict when multiple site config file is loaded the site config is now loaded in the index
      $this->config->set_item('wp_db_tag',$this->config->item("wp_db_tag",$this->site_conf));
      $this->config->set_item('booking_test_mode',$this->config->item("booking_test_mode",$this->site_conf));
      $this->config->set_item('HW_no_confirmation_email',$this->config->item("HW_no_confirmation_email",$this->site_conf));
      $this->config->set_item('adword_cookie_expiration',$this->config->item("adword_cookie_expiration",$this->site_conf));
      $this->config->set_item('adword_cookie_prefix',$this->config->item("adword_cookie_prefix",$this->site_conf));
      $this->config->set_item('email_users_admin',$this->config->item("email_users_admin",$this->site_conf));
      $this->config->set_item('contact_info_email',$this->config->item("contact_info_email",$this->site_conf));
      $this->config->set_item('admin_booking_email',$this->config->item("admin_booking_email",$this->site_conf));
      $this->config->set_item('admin_booking_conf',$this->config->item("admin_booking_conf",$this->site_conf));
      $this->config->set_item('admin_booking_conf_email',$this->config->item("admin_booking_conf_email",$this->site_conf));
      $this->config->set_item('admin_review_conf',$this->config->item("admin_review_conf",$this->site_conf));
      $this->config->set_item('admin_review_conf_email',$this->config->item("admin_review_conf_email",$this->site_conf));
      $this->config->set_item('hostelworld_userID',$this->config->item("hostelworld_userID",$this->site_conf));
      $this->config->set_item('hostelworld_API_url',$this->config->item("hostelworld_API_url",$this->site_conf));
      $this->config->set_item('hostelworld_API_url_secure_booking',$this->config->item("hostelworld_API_url_secure_booking",$this->site_conf));

      $this->api_forced = false;
      if(strcmp($this->wordpress->get_option("aj_api_site_data",$this->api_used),"hb")==0)
      {
        $this->api_used = HB_API;
        $this->hostel_controller = "chostelbk";
      }

    }
    else
    {
      log_message('error', 'Invalid host: '. $host);
      show_error('Invalid host or server capacity reached. Server administrators have been advised.');
    }

  }
}

class UserRestricted extends I18n_site
{
  protected $user_info = array("id" => 0,
                               "email" => "",
                               "level" => null);
  protected $api_view_dir = "";

  public function UserRestricted()
  {
    parent::__construct();

    $this->load->config('tank_auth', TRUE);
    $this->load->library('tank_auth');

    if (!$this->tank_auth->is_logged_in())
    {
      redirect('/'.$this->Db_links->get_link("connect"), 'refresh');
    }

    $this->load->model('tank_auth/user_profiles');
    $this->user_info['email'] = $this->tank_auth->get_email();
    $this->user_info['id']    = $this->tank_auth->get_user_id();
    $this->user_info['level'] = $this->user_profiles->get_user_level($this->user_info['id']);

    $this->load->model('Db_country');
  }

}
?>
