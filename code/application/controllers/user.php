<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends UserRestricted
{
  function User()
  {
    parent::UserRestricted();

    $this->load->library('form_validation');
    $this->load->model('Db_model');
    $this->load->model('Db_reviews');

    //Currency initialization
    $this->load->library('get_config');
    $this->load->model('Db_currency');

    $currency_validated = $this->input->get("currency", TRUE);
    $currency_posted    = $this->input->post('favorite_currency',TRUE);

    if(!empty($currency_validated))
    {
      $currency_validated = $this->Db_currency->validate_currency($currency_validated);
    }
    elseif(!empty($currency_posted))
    {
      $currency_validated = $this->Db_currency->validate_currency($currency_posted);
      $_GET['currency'] = $currency_validated;
    }
    elseif($this->tank_auth->is_logged_in())
    {
      $currency_validated = $this->config->item('site_currency_default');

      $user_id = $this->tank_auth->get_user_id();

      if($user_id !== false)
      {
        $user_info = array();
        $user_info = $this->Db_model->get_user_profile($user_id);
        $currency_validated = $this->Db_currency->get_currency_code($user_info['favorite_currency']);
      }
    }
    else
    {
      $currency_validated = $this->config->item('site_currency_default');
    }
    $this->get_config->set_config_from_get("currency","site_currency_selected",TRUE,'currency_selected',$currency_validated);
  }

  function index()
  {
    $this->carabiner->load_group_assets('search_box_scripts');

    if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
    {
      $data['user_profile'] = $this->tank_auth->get_profile($this->user_info['id']);

      $data['current_view_dir'] = "mobile/user/";
      $data['current_view'] = "user_view";

      $this->carabiner->load_group_assets('mobile_main_menu');

      $this->load->view('mobile/includes/template',$data);
    }
    else
    {
      $data['current_view_dir'] = $this->api_view_dir;
      $data['current_view'] = "restricted/user_view";
      $this->load->view('includes/template',$data);
    }
  }

  function profile()
  {

    $this->carabiner->load_group_assets('search_box_scripts');

    $this->form_validation->set_rules('firstname', _('Prénom'), 'trim|xss_clean');
    $this->form_validation->set_rules('lastname', _('Nom'), 'trim|xss_clean');
    $this->form_validation->set_rules('nationality', _('Nationalité'), 'trim|xss_clean');
    $this->form_validation->set_rules('language', _('Langue de correspondance'), 'trim|xss_clean');
    $this->form_validation->set_rules('gender', _('Sexe'), 'trim|xss_clean');
    $this->form_validation->set_rules('favorite_currency', _('Devise'), 'trim|xss_clean');
    $this->form_validation->set_rules('phone_number', _('Téléphone'), 'trim|xss_clean');
	$profile_change='';
    if ($this->form_validation->run() == FALSE) {                // validation not ok

    }
    else
    {
      $this->load->model('Db_currency');
      $profile_data = array(
                      'first_name' => $this->input->post('firstname',TRUE),
                      'last_name' => $this->input->post('lastname',TRUE),
                      'gender_id' => $this->Db_model->get_gender_id($this->input->post('gender',TRUE)),
                      'phone_number' => $this->input->post('phone_number',TRUE),
                      'home_country' => $this->input->post('nationality',TRUE),
                      'favorite_lang_id' => $this->input->post('language',TRUE),
                      'favorite_currency' => $this->Db_currency->get_currency_id($this->input->post('favorite_currency',TRUE)),
                      'mail_subscription' => $this->input->post('mail_subscribe',TRUE),
                      );

      if($this->user_profiles->set_profile_data($this->user_info['id'],$profile_data,false)==false)
      {
        $data['warning']         = true;
        $data['warning_message'] = _('Erreur lors de la mise à jour de votre profil.');
      }
      else
      {
        $data['warning']         = true;
        $data['warning_message'] = _('Une mise à jour de votre profil a été effectuée. Merci!');
      }
	  $data['changes_success']='Change Successfuly';
	  $profile_change='success';
    }
//    $this->load->view('auth/change_password_form', $data);


    $data['user'] = $this->user_info;
    $data['user_profile'] = $this->tank_auth->get_profile($this->user_info['id']);

    if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
    {
      $data['current_view_dir'] = "mobile/user/";
      if($profile_change!='')
	  {
	  	$data['current_view'] = "user_view";
	  }
	  else
	  {
	  	$data['current_view'] = "user_profile";	
	  }
      $this->carabiner->load_group_assets('mobile_main_menu');

      $this->load->view('mobile/includes/template',$data);
    }
    else
    {
      $data['current_view_dir'] = $this->api_view_dir;
      if($profile_change!='')
	  {
	  	$data['current_view'] = "restricted/user_view";
	  }
	  else
	  {
	  	$data['current_view'] = "restricted/user_profile";	
	  }
      $this->load->view('includes/template',$data);
    }
  }

  function favorite_properties() {
      $data['current_view'] = "restricted/user_favorite_properties";

      $this->load->view('includes/template', $data);
  }

  function favorite_properties_list() {
      $data = array();

      $this->load->model('Db_favorite_hostels');

      header('Content-type: application/json');
      $this->load->view('includes/template-json', array('json_data' => json_encode($this->Db_favorite_hostels->getAll(13)), true));
  }

  function bookings()
  {

    $this->carabiner->load_group_assets('search_box_scripts');

    $data['user'] = $this->user_info;
    $email_user = $this->user_info['email'];

    $data['bookings'] = $this->Db_model->get_user_bookings($email_user);

    if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
    {
      $this->load->library('mobile');
      $data['current_view_dir'] = "mobile/user/";
      $data['current_view'] = "user_bookings";

      $this->carabiner->load_group_assets('mobile_main_menu');

      $this->load->view('mobile/includes/template',$data);
    }
    else
    {
      $data['current_view_dir'] = $this->api_view_dir;
      $data['current_view'] = "restricted/user_bookings";
      $this->load->view('includes/template',$data);
    }
  }

  function comments()
  {
    $this->carabiner->load_group_assets('search_box_scripts');

    $data['user'] = $this->user_info;
    $data['user_comments'] = $this->Db_reviews->get_user_reviews($this->user_info['email']);

    if($this->user_agent_mobile && !$this->user_agent_mobile_bypass)
    {
      $data['current_view_dir'] = "mobile/user/";
      $data['current_view'] = "user_ratings";

      $this->carabiner->load_group_assets('mobile_main_menu');

      $this->load->view('mobile/includes/template',$data);
    }
    else
    {
      $data['current_view_dir'] = $this->api_view_dir;
      $data['current_view'] = "restricted/user_ratings";
      $this->load->view('includes/template',$data);
    }
  }
}
