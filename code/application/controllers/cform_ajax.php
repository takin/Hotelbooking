<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CForm_ajax extends I18n_site {

    function CForm_ajax() {
        parent::I18n_site();
    }

    function group_request() {
        
        echo $this->config->item('admin_booking_email');
        
        $this->load->library('email');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('search_co', 'search_co', 'required');
        $this->form_validation->set_rules('search_ci', 'search_ci', 'required');
        $this->form_validation->set_rules('datepick', 'datepick', 'required');
        $this->form_validation->set_rules('search_night', 'search_night', 'required');
        $this->form_validation->set_rules('nb_male_gp', 'nb_male_gp', 'required');
        $this->form_validation->set_rules('nb_female_gp', 'nb_female_gp', 'required');
        $this->form_validation->set_rules('lower_age', 'lower_age', 'required');
        $this->form_validation->set_rules('first_name', 'first_name', 'required');
        $this->form_validation->set_rules('last_name', 'last_name', 'required');
        $this->form_validation->set_rules('email', 'email', 'required');
        $this->form_validation->set_rules('confirm-email', 'confirm-email', 'required');
        $this->form_validation->set_rules('phone_number', 'phone_number', 'required');
        $this->form_validation->set_rules('demand', 'demand', 'required');

        $this->form_validation->set_error_delimiters('<br /><span class="error">', '</span>');

        if ($this->form_validation->run() == FALSE) { // validation hasn't been passed
            log_message('error', "Validation error : Group Booking Request Email");
        } else { // passed validation proceed to post success logic
            foreach ($_POST as $index => $val) {
                $data[$index] = $this->input->post($index, TRUE);
            }
            $dateStart = new DateTime($data['datecal']);
            $date_us = $dateStart->format('F j, Y');

            $data["translated_country"] = $data["search_co"];
            $data["translated_city"] = $data["search_ci"];
            $data["search_country"] = $data["search_co"];
            $data["search_city"] = $data["search_ci"];

            if ($this->api_used == HB_API) {
                $this->load->model('Db_hb_country');
                $city = $this->Db_hb_country->get_city($data["search_co"], $data["search_ci"], "en");
                if (!empty($city)) {
                    $data["search_country"] = $city->display_country;
                    $data["search_city"] = $city->display_city;
                }
            } else {
                $this->load->model('Db_country');
                $this->load->model('Db_hw_city');
                $city = $this->Db_hw_city->get_city($data["search_co"], $data["search_ci"], "en");

                if (!empty($city)) {
                    $data["search_country"] = $city->country_name_translated;
                    $data["search_city"] = $city->city_name_translated;
                }
            }


            //Send confirmation email
            $emailsent = FALSE;
            $totalpeople = $data["nb_male_gp"] + $data["nb_female_gp"] + $data["nb_person_staff"];

            if ($totalpeople > 0) {

                $emailcontent = $this->load->view("email/admin/new_group_request", $data, true);
                $this->email->from($this->config->item('admin_booking_email'), $this->config->item('site_name'));
                $this->email->to($this->config->item('admin_booking_email'));
                $this->email->bcc("technical@mcwebmanagement.com");
                $this->email->subject($this->config->item('site_name') . " - " . sprintf(gettext("Group Booking")) . " - " . sprintf(gettext("People:")) . " " . $totalpeople . " - " . $data["search_city"] . " - " . $data["datepick"]);
                $this->email->message($emailcontent);
                $emailsent = $this->email->send();

                if (!$emailsent) {
                    log_message('error', "Error sending Group Booking Request Email to " . $admin_email . " -> " . $this->email->print_debugger());
                }

                //clear email data for next email to be sent ok
                $this->email->clear();
                $this->email->_bcc_array = array();
            } else {
                log_message('error', "Error sending Group Booking Request Email - Total persons can not be 0");
            }
        }
    }

}

?>
