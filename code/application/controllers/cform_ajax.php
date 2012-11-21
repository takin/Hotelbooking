<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CForm_ajax extends I18n_site
{

  function CForm_ajax()
  {
    parent::I18n_site();
  }

  function group_request()
  {
    $this->load->library('email');

    foreach($_POST as $index => $val)
    {
      $data[$index] = $this->input->post($index, TRUE);
    }
    $dateStart = new DateTime($data['datecal']);
    $date_us = $dateStart->format('F j, Y');

    $data["translated_country"] = $data["search_co"];
    $data["translated_city"]    = $data["search_ci"];
    $data["search_country"]     = $data["search_co"];
    $data["search_city"]        = $data["search_ci"];

    if($this->api_used == HB_API)
    {
      $this->load->model('Db_hb_country');
      $city    = $this->Db_hb_country->get_city($data["search_co"],$data["search_ci"],"en");
      if(!empty($city))
      {
        $data["search_country"] = $city->display_country;
        $data["search_city"]    = $city->display_city;
      }
    }
    else
    {
      $this->load->model('Db_country');
      $this->load->model('Db_hw_city');
      $city = $this->Db_hw_city->get_city($data["search_co"],$data["search_ci"],"en");

      if(!empty($city))
      {
        $data["search_country"] = $city->country_name_translated;
        $data["search_city"]    = $city->city_name_translated;
      }

    }


    //Send confirmation email
    $emailsent = FALSE;
    $totalpeople = $data["nb_male_gp"] + $data["nb_female_gp"] + $data["nb_person_staff"];

    $emailcontent = $this->load->view("email/admin/new_group_request",$data , true);
    $this->email->from($this->config->item('admin_booking_email'),$this->config->item('site_name'));
    $this->email->to($this->config->item('admin_booking_email'));
    $this->email->bcc("louismichel@pweb.ca");
    $this->email->subject("Request for Group booking - ".$data["search_city"].', '.$data["search_country"]." - $totalpeople people - ". $date_us ." - ". $data["search_night"] ." night(s) - ".$this->config->item('site_name'));
    $this->email->message($emailcontent);
    $emailsent = $this->email->send();

    if(! $emailsent)
    {
      log_message('error',"Error sending Group Booking Request Email to ".$admin_email." -> ".$this->email->print_debugger());
    }

    //clear email data for next email to be sent ok
    $this->email->clear();
    $this->email->_bcc_array = array();
  }
}
?>