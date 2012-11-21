<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cadmin extends UserRestricted
{

  public function Cadmin()
  {
    parent::__construct();

    if ($this->user_info["level"] < 2)
    {
      show_404();
      exit;
    }
  }

  protected function _translate_col_head($colhead)
  {
    switch($colhead)
    {
      case "beds":
        return _("Beds");
      case "rooms":
        return _("Rooms");
      case "people":
        return _("People");
      case "nights":
        return _("Nights");
      default:
        return '';
    }
  }
  public function group_quote()
  {
    $this->carabiner->js('search_box.js');
    $this->load->model('Db_currency');
    $data['current_view'] = "restricted/admin/group_quote";
    $this->load->view('includes/template',$data);
  }

  protected function _setup_quote($api,
                                  $property_number,
                                  $col2head,
                                  $col3head,
                                  $rooms,
                                  $people,
                                  $nights,
                                  $custom_price,
                                  $prop_price,
                                  $inc,
                                  $down_pay_custom,
                                  $total_custom,
                                  $total_custom_cur,
                                  $total_prop,
                                  $total_prop_cur,
                                  $down_pay_prop,
                                  $expiry_date,
                                  $balance_date
                                  )
  {
    $quote->rooms = array();
    $quote->total_people = 0;

    $customer_site_lang = $this->site_lang;
    foreach($rooms as $i => $room)
    {
      $roomtrimmed = trim($rooms[$i]);
      if(empty($roomtrimmed)) continue;

      $quote->rooms[$i]->desc = $roomtrimmed;
      $number = $this->translate_model->extract_number($quote->rooms[$i]->desc);
      //TODO add englsih room type
      $quote->rooms[$i]->trans_desc   = "";
      $quote->rooms[$i]->people       = (int) trim($people[$i]);
      $quote->total_people += $quote->rooms[$i]->people;
      $quote->rooms[$i]->nights       = trim($nights[$i]);
      $quote->rooms[$i]->custom_price = trim($custom_price[$i]);
      $quote->rooms[$i]->book_price   = trim($prop_price[$i]);

      //Translate room desc if needed
      if($customer_site_lang != 'en')
      {
        if($api == 'HB')
        {
          $translation = $this->translate_model->translate_text($this->translate_model->replace_number_of_string_by_d((string)$quote->rooms[$i]->desc),"HB room type","en",$customer_site_lang);
          if(!empty($translation))
          {
            $quote->rooms[$i]->trans_desc = $this->translate_model->replace_number($number,$translation);
          }
        }
        else
        {
          $this->load->model('Hb_api_translate');
          $translation = $this->Hb_api_translate->translate_text($this->translate_model->replace_number_of_string_by_d((string)$quote->rooms[$i]->desc),"HW room type","en",$customer_site_lang);
          if(!empty($translation))
          {
            $quote->rooms[$i]->trans_desc = $this->translate_model->replace_number($number,$translation);
          }
        }

      }

    }
    $quote->property_number  = $property_number;

    if($api == 'HB')
    {
      $quote->property = $this->hostel_model->get_hostel_data($property_number);
      $quote->property->desc = $this->hostel_model->get_hb_short_desc($property_number, "en");
      if(!empty($quote->property->desc))
      {
        $quote->property->desc = $this->translate_model->translate_text($quote->property->desc,"HB short description","en",$this->site_lang);
      }
      $quote->property->images = $this->hostel_model->get_hostel_images($property_number);
      $quote->property->thumb = "";
      if(!empty($quote->property->images[0]->url))
      {
        $this->load->model('Hostelbookers_api');
        $quote->property->thumb = $this->Hostelbookers_api->build_thumb_url($quote->property->images[0]->url);
      }
    }
    else
    {
      $prop_id = $this->hostel_model->hw_hostel_id($property_number);
      $quote->property = $this->hostel_model->get_hostel_data($prop_id);
      $quote->property->desc = $this->hostel_model->get_short_desc($quote->property->hw_hostel_id, "en");

      if(!empty($quote->property->desc))
      {
        $this->load->model("Hb_api_translate");
        $quote->property->desc = $this->Hb_api_translate->translate_text($quote->property->desc,"HW short description","en",$this->site_lang);
      }
      //TODO for HW
      $quote->property->thumb = "";
    }
    $this->load->helper("text");
    $quote->property->desc = strip_tags(word_limiter($quote->property->desc, 30));
    $quote->property_url = $this->Db_links->build_property_page_link($quote->property->property_type, $quote->property->property_name, $quote->property->property_number, $this->site_lang);

    if(($this->api_used == HB_API) && ($api != 'HB'))
    {
      $quote->property_url .= '?groupbkg=B';
    }
    elseif(($this->api_used == HW_API) && ($api != 'HW'))
    {
      $quote->property_url .= '?groupbkg=A';
    }
    $quote->includes = array();
    foreach($inc as $include => $enable)
    {
      if($enable === true)
      {
        $inc_obj->desc = "";
        switch($include)
        {
          case "breakfast":
            $inc_obj->desc = _("Breakfast");
            break;
          case "lunch":
            $inc_obj->desc = _("Lunch");
            break;
          case "lunch_pack":
            $inc_obj->desc = _("Lunch Pack");
            break;
          case "dinner":
            $inc_obj->desc = _('Dinner');
            break;
          case "linen":
            $inc_obj->desc = _("Linen");
            break;
          case "towels":
            $inc_obj->desc = _("Towel");
            break;
          case "luggage_storage":
            $inc_obj->desc = _("Luggage Room");
            break;
        }
        $quote->includes[] = clone $inc_obj;
      }
    }
    $quote->col2head           = $this->_translate_col_head($col2head);
    $quote->col3head           = $this->_translate_col_head($col3head);;
    $quote->expiry_date           = $expiry_date;
    $quote->expiry_date           = $expiry_date;
    $quote->balance_payment_date  = $balance_date;
    $quote->total_book       = $total_prop;
    $quote->total_book_cur   = $total_prop_cur;
    $quote->total_custom     = $total_custom;
    $quote->total_custom_cur = $total_custom_cur;
    $quote->down_pay_book    = $down_pay_prop;
    $quote->down_pay_custom  = $down_pay_custom;

    return $quote;
  }
  public function generate_quote()
  {
//     $this->output->enable_profiler(TRUE);

    //Post data init
    $domain       = $this->input->post('domain', TRUE);
    $api          = $this->input->post('api', TRUE);

    //If quote is for another site then admin logged in switch context
    if(strcasecmp($_SERVER['HTTP_HOST'],$domain)!=0)
    {
      $this->load_lang_context($domain);
    }

    //Class init
    $this->load->model('i18n/db_translation_cache');
    if($api == 'HB')
    {
      $this->load->model('Hb_api_translate', 'translate_model');
      $this->load->model('Db_hb_hostel', 'hostel_model');
    }
    else
    {
      $this->load->model('Hw_api_translate', 'translate_model');
      $this->load->model('Db_hw_hostel', 'hostel_model');
    }

    $data['date_format'] = $this->wordpress->get_option('aj_date_format');
    $data['quote_notes']              = $this->input->post('quote_notes', TRUE);
    $data['group_request']->req_custom_ref = $this->input->post('req_custom_ref', TRUE);
    $data['group_request']->firstname = $this->input->post('req_firstname', TRUE);
    $data['group_request']->lastname  = $this->input->post('req_lastname', TRUE);
    $data['group_request']->email     = $this->input->post('req_email', TRUE);
    $data['group_request']->country   = $this->input->post('req_co_en', TRUE);
    $data['group_request']->city      = $this->input->post('req_ci_en', TRUE);

    //Translate country and city
    $data['group_request']->city = $this->Db_country->get_city($data['group_request']->country,$data['group_request']->city,$this->site_lang);
    $data['group_request']->country = $this->Db_country->get_country($data['group_request']->country,$this->site_lang);

    $arrival_my = $this->input->post('arrival_my', TRUE);
    $arrival_d  = $this->input->post('arrival_d', TRUE);
    $arrival_date = new DateTime($arrival_my."-".$arrival_d);
    $data['group_request']->arrival_date = strftime($data['date_format'],$arrival_date->format('U'));

    $data['group_request']->numnights    = $this->input->post('req_num_nights', TRUE);
    $data['group_request']->total_people = $this->input->post('req_total_people', TRUE);

    //Quotes

    $data['quote_type'] = $this->input->post('quote_type', TRUE);
    $data['quotes'] = array();


    $expiry_date_my = $this->input->post("expiry_my", TRUE);
    $expiry_date_d  = $this->input->post("expiry_d", TRUE);
    $expiry_date = new DateTime($expiry_date_my."-".$expiry_date_d);

    for($quote_id=1;$quote_id<4;$quote_id++)
    {

      $property_number  = $this->input->post("quote".$quote_id."_property_number", TRUE);

      if(!empty($property_number))
      {
        $down_pay_custom  = $this->input->post("quote".$quote_id."_custom_down_pay", TRUE);
        $total_custom     = $this->input->post("quote".$quote_id."_total_custom", TRUE);
        $total_custom_cur = $this->input->post("quote".$quote_id."_total_custom_cur", TRUE);
        $down_pay_prop    = $this->input->post("quote".$quote_id."_prop_down_pay", TRUE);
        $total_prop       = $this->input->post("quote".$quote_id."_total_prop", TRUE);
        $total_prop_cur   = $this->input->post("quote".$quote_id."_total_prop_cur", TRUE);

        $col2head         = $this->input->post("quote".$quote_id."_col2_name", TRUE);
        $col3head         = $this->input->post("quote".$quote_id."_col3_name", TRUE);

        $rooms        = $this->input->post("quote".$quote_id."_rooms", TRUE);
        $people       = $this->input->post("quote".$quote_id."_people", TRUE);
        $nights       = $this->input->post("quote".$quote_id."_nights", TRUE);
        $custom_price = $this->input->post("quote".$quote_id."_custom_price", TRUE);
        $prop_price   = $this->input->post("quote".$quote_id."_prop_price", TRUE);

        $inc['breakfast']       = $this->input->post("quote".$quote_id."_inc_breakfast", TRUE) == 'on';
        $inc['lunch']           = $this->input->post("quote".$quote_id."_inc_lunch", TRUE) == 'on';
        $inc['lunch_pack']      = $this->input->post("quote".$quote_id."_inc_lunch_pack", TRUE) == 'on';
        $inc['dinner']          = $this->input->post("quote".$quote_id."_inc_dinner", TRUE) == 'on';
        $inc['linen']           = $this->input->post("quote".$quote_id."_inc_linen", TRUE) == 'on';
        $inc['towels']          = $this->input->post("quote".$quote_id."_inc_towels", TRUE) == 'on';
        $inc['luggage_storage'] = $this->input->post("quote".$quote_id."_inc_luggage_storage", TRUE) == 'on';



        $balance_date_my = $this->input->post("quote".$quote_id."_balance_my", TRUE);
        $balance_date_d  = $this->input->post("quote".$quote_id."_balance_d", TRUE);
        $balance_date = new DateTime($balance_date_my."-".$balance_date_d);


        $data['quotes'][] = $this->_setup_quote($api,
                                                $property_number,
                                                $col2head,
                                                $col3head,
                                                $rooms,
                                                $people,
                                                $nights,
                                                $custom_price,
                                                $prop_price,
                                                $inc,
                                                $down_pay_custom,
                                                $total_custom,
                                                $total_custom_cur,
                                                $total_prop,
                                                $total_prop_cur,
                                                $down_pay_prop,
                                                strftime($data['date_format'],$expiry_date->format('U')),
                                                strftime($data['date_format'],$balance_date->format('U'))
                                                );
      }
    }

//     debug_dump($data);
    $data['current_view'] = "restricted/user_group_quote";
    $this->load->view('includes/template_book',$data);
    $html = $this->output->get_output();

    // Load library
//     $this->load->library('dompdf_gen');

    // Convert to PDF
//     $this->dompdf->load_html($html);
//     $this->dompdf->render();
//     $this->dompdf->stream("welcome.pdf");
  }

  public function gbrocks($quotes_type = 'regular')
  {

    //Post data init
    $domain       = $this->input->post('domain', TRUE);
    $api          = $this->input->post('api', TRUE);
    $rooms        = $this->input->post('quote1_rooms', TRUE);
    $people       = $this->input->post('quote1_people', TRUE);
    $nights       = $this->input->post('quote1_nights', TRUE);
    $custom_price = $this->input->post('quote1_custom_price', TRUE);
    $book_price   = $this->input->post('quote1_book_price', TRUE);

    $domain = "alberges.graphemsolutions.net";
    $api    = "HB";
    $customer_site_lang = $this->Db_links->get_lang_from_domain($domain);

    $rooms        = array("Dorm (4 beds)","Dorm (6 beds)","private room");
    $people       = array(24,6,2);
    $nights       = array(2,2,2);
    $custom_price = array(14,17,30);
    $book_price   = array(12,14,22);

    //Class init
    $this->load->model('i18n/db_translation_cache');
    if($api == 'HB')
    {
      $this->load->model('Hb_api_translate', 'translate_model');
    }
    else
    {
      $this->load->model('Hw_api_translate', 'translate_model');
    }

    $data['quote']->rooms = array();
    $data['quote']->total_people = 0;

    foreach($rooms as $i => $room)
    {
      $roomtrimmed = trim($rooms[$i]);
      if(empty($roomtrimmed)) continue;

      $data['quote']->rooms[$i]->desc = $roomtrimmed;
      $number = $this->translate_model->extract_number($data['quote']->rooms[$i]->desc);
//TODO add englsih room type
      $data['quote']->rooms[$i]->trans_desc   = "";
      $data['quote']->rooms[$i]->people       = (int) trim($people[$i]);
      $data['quote']->total_people += $data['quote']->rooms[$i]->people;
      $data['quote']->rooms[$i]->nights       = trim($nights[$i]);
      $data['quote']->rooms[$i]->custom_price = number_format(trim($custom_price[$i]), 2, '.', '');
      $data['quote']->rooms[$i]->book_price   = number_format(trim($book_price[$i]), 2, '.', '');

      //Translate room desc if needed
      if($customer_site_lang != 'en')
      {
        $translation = $this->db_translation_cache->get_translation($this->translate_model->replace_number_of_string_by_d((string)$data['quote']->rooms[$i]->desc),$customer_site_lang);
        if(!empty($translation))
        {
          $data['quote']->rooms[$i]->trans_desc = $this->translate_model->replace_number($number,$translation->translation);
        }
      }

    }

    $data['group_request']->firstname = "Bob";
    $data['group_request']->lastname  = "Marley";
    $data['group_request']->email     = "gina@funny.us";
    $data['group_request']->country   = "France";
    $data['group_request']->city      = "Nice";
    $data['group_request']->arrival_date = "21 marcho, 2013";

    $data['group_request']->numnights    = 6;
    $data['group_request']->total_people = 25;

    $data['quote']->property_number  = 18208;
    $data["quote"]->property_url = "http://alberges.graphemsolutions.net/Hotel-barato/Hotel-du-Moulin/18208";
    $inc1->desc       = "Luggage Room";
    $inc1->trans_desc = "Luggage Room translated";
    $inc2->desc       = "Breakfast";
    $inc2->trans_desc = "Breakfast translated";
    $inc3->desc       = "Linen and Towels";
    $inc3->trans_desc = "Linen and Towels translated";

    $data["quote"]->includes     = array($inc1,$inc2,$inc3);
    $data['quote']->expiry_date      = "12 Noviembre, 2012";
    $data['quote']->balance_payment_date  = "2 Februare, 2013";
    $data['quote']->total_book       = number_format(1200, 2, '.', '');
    $data['quote']->total_book_cur   = "EUR";
    $data['quote']->total_custom     = number_format(1500, 2, '.', '');
    $data['quote']->total_custom_cur = "GBP";
    $data['quote']->down_pay_book    = number_format(200, 2, '.', '');
    $data['quote']->down_pay_custom  = number_format(500, 2, '.', '');

    $data['quotes'][0] = $data['quote'];
    $data['quotes'][1] = $data['quote'];

    $data['quote_type'] = $quotes_type;
    unset($data['quote']);

    $data['current_view'] = "restricted/user_group_quote";
    $this->load->view('includes/template_book',$data);
  }
}