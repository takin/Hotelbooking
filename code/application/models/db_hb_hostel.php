<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hb_hostel extends CI_Model
{
  const CURRENCY_TABLE  = 'currencies';

  const CITY_TABLE      = 'hb_city';

  const HOSTEL_TABLE           = 'hb_hostel';
  const HOSTEL_DESC_TABLE      = 'hb_hostel_description';
  const HOSTEL_FEATURE_TABLE   = 'hb_hostel_feature';
  const HOSTEL_EXTRA_TABLE     = 'hb_hostel_extra';
  const HOSTEL_IMAGE_TABLE     = 'hb_hostel_image';
  const HOSTEL_PRICE_TABLE     = 'hb_hostel_price';

  const HB_HOSTEL_DISTRICT_TABLE              = 'hb_hostel_district';
  const HB_HOSTEL_LANDMARK_TABLE              = 'hb_hostel_landmark';
  const HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE  = 'hb_hostel_landmark_attribution';

  const DISTRICTS_TABLE             = 'districts';
  const LANDMARKS_TABLE             = 'landmarks';
  const LANDMARK_TYPE_TABLE         = 'landmark_type';
  const LANDMARK_OF_TYPE_TABLE      = 'landmark_of_type';
  const LANDMARK_ATTRIBUTION_TABLE  = 'landmark_attribution';

  const FEATURE_TABLE   = 'hb_feature';
  const EXTRA_TABLE     = 'hb_extra';

  const PROPERTY_VALID   = 1;
  const PROPERTY_INVALID = 0;

  const FEED_INFO = 0;
  const FEED_DEBUG = 1;
  const FEED_ERROR = 2;
  const FEED_ALWAYS = 10;

  private $CI;

  private $hb_currencies = NULL;

  private $feed_error_level_treshold = 2;
  private $feed_counts = NULL;

  public function __construct()
  {
      parent::__construct();

      $this->CI =& get_instance();
      $this->CI->load->library('code_tracker');

      $this->CI->db->simple_query("SET NAMES 'utf8'");
  }

  //TODO function should have been in code_tracker library with contants FEED_*
  //      So, now update to use code_Tracker library
  public function feed_trace($level, $text)
  {
    if($level >= $this->feed_error_level_treshold)
    {
      $this->CI->code_tracker->add_trace($text);
    }
  }


  function get_location_properties($country_system_name, $city_system_name, $language_code = "en", $strCurrencyCode = "GBP", $limit = 0, $filters = array())
  {
    $property_type_where = "";
    if(!empty($filters["type"]))
    {
      if(!empty($filters["type"]))
      {
        $filters["type"] = $this->db->escape_str($filters["type"]);
        $property_type_where = " AND property_type LIKE '".$filters["type"]."'";
      }
    }

    $district_join = "";
    $district_where = "";

    if(!empty($filters["district"]))
    {
      $this->CI->load->model('Db_districts');
      $filters["district"] = $this->CI->Db_districts->create_slug($filters["district"]);
      $filters["district"] = $this->db->escape_str($filters["district"]);

      $district_join = "  LEFT JOIN hb_hostel_district ON hb_hostel.property_number = hb_hostel_district.property_number
    												LEFT JOIN districts ON hb_hostel_district.district_id = districts.district_id";
      $district_where = " AND districts.slug LIKE '".$filters["district"]."'";
    }

    $landmark_join = "";
    $landmark_where = "";

    if(!empty($filters["landmark"]))
    {
      $this->CI->load->model('Db_landmarks');
      $filters["landmark"] = $this->db->escape_str($filters["landmark"]);

      $landmark_join = "  LEFT JOIN hb_hostel_landmark ON hb_hostel.property_number = hb_hostel_landmark.property_number
    												LEFT JOIN landmarks ON hb_hostel_landmark.landmark_id = landmarks.landmark_id";
      $landmark_where = " AND landmarks.slug LIKE '".$filters["landmark"]."'";
    }

    $api_db_cur_code = 'EUR';

    $country_system_name    = $this->db->escape_str($country_system_name);
    $city_system_name       = $this->db->escape_str($city_system_name);
    $language_code          = $this->db->escape_str($language_code);
    $api_db_cur_code        = $this->db->escape_str($api_db_cur_code);
    $limit                  = $this->db->escape($limit);

    $sql = "SELECT hb_hostel.property_number, property_name, property_type, round(rating_overall) as rating,hb_hostel.geo_latitude, hb_hostel.geo_longitude,
                   currency_code, bed_price as min_price, type as price_type,
                   hb_hostel_image.url as image_url, hb_hostel_description.short_description,
                   hostel_translated.requested_lang, hostel_translated.translated_desc
            FROM hb_hostel
            LEFT JOIN hb_city ON hb_city.hb_id = hb_hostel.city_hb_id
            LEFT JOIN hb_country ON hb_country.hb_country_id = hb_city.hb_country_id
            LEFT JOIN hb_hostel_image ON hb_hostel_image.hostel_hb_id = hb_hostel.property_number
            LEFT JOIN hb_hostel_description ON hb_hostel_description.hostel_hb_id = hb_hostel.property_number
            LEFT JOIN hb_hostel_price ON hb_hostel_price.hostel_hb_id = hb_hostel.property_number
            LEFT JOIN
            ( SELECT hb_hostel.property_number, language AS requested_lang, hb_hostel_description.short_description as translated_desc FROM hb_hostel
                   LEFT JOIN hb_city    ON hb_city.hb_id  = hb_hostel.city_hb_id
                   LEFT JOIN hb_country ON hb_country.hb_country_id = hb_city.hb_country_id
                   LEFT JOIN hb_hostel_description ON hb_hostel.property_number = hb_hostel_description.hostel_hb_id
                   WHERE hb_city.system_name LIKE('$city_system_name') AND hb_country.system_name LIKE('$country_system_name') AND hb_hostel_description.language LIKE'$language_code'
                   ) AS hostel_translated
                   ON hb_hostel.property_number = hostel_translated.property_number
            $landmark_join
            $district_join
            WHERE hb_hostel_price.bed_price > 0
            AND hb_city.system_name LIKE('$city_system_name')
            AND hb_country.system_name LIKE('$country_system_name')
            AND hb_hostel_description.language ='en'
            AND currency_code = '$api_db_cur_code'
            $property_type_where
            $landmark_where
            $district_where
            GROUP BY hb_hostel.property_number
    				ORDER BY min_price ASC";

    if(!empty($limit) && ($limit > 0))
    {
      $sql.= " LIMIT $limit";
    }

    $query = $this->CI->db->query($sql);

    $response["response"]["properties"] = Array();

    if($query->num_rows() > 0)
    {
      $index = 0;
      foreach($query->result() as $hostel)
      {
        $response["response"]["properties"][$index]["name"] = $hostel->property_name;
        $response["response"]["properties"][$index]["id"] = $hostel->property_number;

        $response["response"]["properties"][$index]["intro"] = $hostel->short_description;
        if(!empty($hostel->translated_desc) )
        {
          $response["response"]["properties"][$index]["intro"] = $hostel->translated_desc;
        }
        $response["response"]["properties"][$index]["image"] = $hostel->image_url;
        $response["response"]["properties"][$index]["rating"] = $hostel->rating;
        $response["response"]["properties"][$index]["type"] = $hostel->property_type;
        $response["response"]["properties"][$index]["geo_latitude"] = $hostel->geo_latitude;
        $response["response"]["properties"][$index]["geo_longitude"] = $hostel->geo_longitude;


        if($api_db_cur_code == 'GBP')
        {
          $response["response"]["properties"][$index]["prices"]["payment"]["currency"] = "GBP";
          $response["response"]["properties"][$index]["prices"]["payment"]["minprice"] = $hostel->min_price;
        }
        else
        {
          $this->CI->load->model('Db_currency');
          $response["response"]["properties"][$index]["prices"]["payment"]["currency"] = "GBP";
          $response["response"]["properties"][$index]["prices"]["payment"]["minprice"] = $this->CI->Db_currency->convert_from_hb_rates($api_db_cur_code,"GBP",$hostel->min_price);
        }

        if($strCurrencyCode == $api_db_cur_code)
        {
          $response["response"]["properties"][$index]["prices"]["customer"]["currency"] = $api_db_cur_code;
          $response["response"]["properties"][$index]["prices"]["customer"]["minprice"] = $hostel->min_price;
        }
        else
        {
          $this->CI->load->model('Db_currency');
          $response["response"]["properties"][$index]["prices"]["customer"]["currency"] = $strCurrencyCode;
          $response["response"]["properties"][$index]["prices"]["customer"]["minprice"] = $this->CI->Db_currency->convert_from_hb_rates($api_db_cur_code,$strCurrencyCode,$hostel->min_price);
        }
        $index++;
      }

      return $response;
    }
    return NULL;
  }

  function parse_static_feed($static_file_content, $verbose = 2)
  {
    $this->feed_error_level_treshold = $verbose;

    $this->feed_counts->property_total    = 0;
    $this->feed_counts->property_inserted = 0;
    $this->feed_counts->property_updated  = 0;
    $this->feed_counts->property_deleted  = 0;

    $this->CI->load->model("Db_hb_city");
    $this->CI->load->helper('memory');

    //disable query history because with multiple query it will cause memory problems
    $this->CI->db->save_queries = false;

    $z = new XMLReader;
    $z->xml($static_file_content);

    unset($static_file_content);
    $doc = new DOMDocument;

    //put all hostel api status to 0
    $this->update_sync_status(self::PROPERTY_INVALID);
    $this->update_features_status(self::PROPERTY_INVALID);
    $this->update_extras_status(self::PROPERTY_INVALID);

    while ($z->read() && $z->name !== 'country');

    while ($z->name === 'country')
    {
        $country_node = simplexml_import_dom($doc->importNode($z->expand(), true));

        foreach($country_node->location as $city)
        {
          if(!is_null($city_db = $this->CI->Db_hb_city->get_hb_city_from_hbid((int)$city['id'])))
          {
            foreach($city->property as $property)
            {
              set_time_limit(30);
              $this->hb_update_from_hb_xml((int)$city['id'], $city->name, $property);
              $this->feed_counts->property_total++;
            }
          }
          else
          {
            $city_prop_count = 0;
            foreach($city->property as $property)
            {
              $city_prop_count++;
              $this->feed_counts->property_total++;
            }
            $this->feed_trace(self::FEED_ERROR,"Location NOT in DB ->  ".$country_node->name . " ".$city->name. " HBID# ".(int)$city['id'] ." - $city_prop_count properties ignored");
          }

        }

        $z->next('country');
    }

    $this->feed_trace(self::FEED_ALWAYS,"DONE Inserting and updating properties");
    //Update currencies exchange rates
    $this->feed_trace(self::FEED_DEBUG,"Updating HB currencies exchange");
    $this->update_hb_currencies_exchange();

    //Delete hostel that were not updated by static feed file
    $this->feed_trace(self::FEED_DEBUG,"Deleting properties not in recent static feed");

    //Keep count of properties that will be deleted
    $this->db->where("api_sync_status",0);
    $this->feed_counts->property_deleted = $this->db->count_all_results("hb_hostel");

    $this->delete_sync_status();
    $this->feed_trace(self::FEED_DEBUG,"Done deleting properties not in recent static feed");
    $this->feed_trace(self::FEED_DEBUG,"Deleting features not in recent static feed");
    $this->delete_features_sync_status();
    $this->feed_trace(self::FEED_DEBUG,"Done deleting features not in recent static feed");
    $this->feed_trace(self::FEED_DEBUG,"Deleting extras not in recent static feed");
    $this->delete_extras_sync_status();
    $this->feed_trace(self::FEED_DEBUG,"Done deleting extras not in recent static feed");

    $hb_prop_db_count = $this->db->count_all(self::HOSTEL_TABLE);

    $this->feed_trace(self::FEED_ALWAYS,"Done parsing static feed of ".$this->feed_counts->property_total." properties in HB feed");
    $this->feed_trace(self::FEED_ALWAYS,"$hb_prop_db_count properties counted in DB");
    $this->feed_trace(self::FEED_ALWAYS,$this->feed_counts->property_inserted . " new properties inserted");
    $this->feed_trace(self::FEED_ALWAYS,$this->feed_counts->property_updated . " properties kept as is or updated");
    $this->feed_trace(self::FEED_ALWAYS,$this->feed_counts->property_deleted . " properties deleted");

  }

  function hb_update_from_hb_xml($location_hb_id, $location_name, $property_xml)
  {
    $property_id = (int)$property_xml["id"];

    if(is_null($hostel_id = $this->get_hostel_id($property_id)))
    {
      //Add hostel to DB
      if($this->insert_hostel($property_xml, $location_hb_id))
      {
        $this->feed_trace(self::FEED_DEBUG,"Added hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        $this->feed_counts->property_inserted++;
        //Add short description to DB
        if($this->insert_hb_short_desc($property_id, "en", $property_xml->shortdescription))
        {
          $this->feed_trace(self::FEED_DEBUG,"Added en short description for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error inserting en short description for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }

        //Add features to DB
        if($this->insert_hb_features_to_hostel($property_id, $property_xml->features->feature))
        {
          $this->feed_trace(self::FEED_DEBUG,"Added features for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error inserting features for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }

        //Add extras to DB
        if($this->insert_hb_extras_to_hostel($property_id, $property_xml->optionalextras->optionalextra))
        {
          $this->feed_trace(self::FEED_DEBUG,"Added extras for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error inserting extras for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }

        //Add images to DB
        if($this->insert_hb_images_to_hostel($property_id, $property_xml->images->image))
        {
          $this->feed_trace(self::FEED_DEBUG,"Added images for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error inserting images for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }

        //Add prices to DB
        if($this->insert_hb_prices($property_id, $property_xml))
        {
          $this->feed_trace(self::FEED_DEBUG,"Added prices for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error inserting prices for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
        }
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error inserting hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

    }
    //If hostel is in DB
    else
    {
      //Update hostel data
      if($this->update_hostel($property_xml, $location_hb_id))
      {
       $this->feed_trace(self::FEED_DEBUG,"Updated hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
       $this->feed_counts->property_updated++;
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error updating hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

      //Update descriptions
      if($this->update_hb_short_desc($property_id, "en", $property_xml->shortdescription ))
      {
       $this->feed_trace(self::FEED_DEBUG,"Updated en short description for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error updating en short description for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

      //Update features
      if($this->update_hb_hostel_features($property_id, $property_xml->features->feature ))
      {
       $this->feed_trace(self::FEED_DEBUG,"Updated features for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error updating features for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

      //Update extras
      if($this->update_hb_hostel_extras($property_id, $property_xml->optionalextras->optionalextra ))
      {
       $this->feed_trace(self::FEED_DEBUG,"Updated extras for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error updating extras for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

      //Update images
      if($this->update_hb_images($property_id, $property_xml->images->image))
      {
       $this->feed_trace(self::FEED_DEBUG,"Updated images for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error updating images for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

      //Update prices to DB
      if($this->update_hb_prices($property_id, $property_xml))
      {
       $this->feed_trace(self::FEED_DEBUG,"Updated prices for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }
      else
      {
        $this->feed_trace(self::FEED_ERROR,"Error updating prices for hostel ".$property_xml->name." (".$property_id.") in city $location_name hb id $location_hb_id to DB");
      }

    }
    $this->feed_trace(self::FEED_DEBUG,"property $property_id done - Mem:".memory_usage_in_mb());
  }



  function update_sync_status($sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->update(self::HOSTEL_TABLE);
  }

  function update_features_status($sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->update(self::FEATURE_TABLE);
  }

  function update_extras_status($sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->update(self::EXTRA_TABLE);
  }

  function update_hostel_features_sync_status($hostel_hb_id, $sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->update(self::HOSTEL_FEATURE_TABLE);
  }

  function update_hostel_extras_sync_status($hostel_hb_id, $sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->update(self::HOSTEL_EXTRA_TABLE);
  }

  function update_hostel_image_sync_status($hostel_hb_id, $sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->update(self::HOSTEL_IMAGE_TABLE);
  }

  function update_hostel_price_sync_status($hostel_hb_id, $sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->update(self::HOSTEL_PRICE_TABLE);
  }

    public function update_hostel_from_array(array $hostel) {
        $propertyNumber = $hostel["property_number"];
        unset($hostel["property_number"]);
        
        if (empty($propertyNumber)) {
            throw new Exception(sprintf(
                    "Error updating hostel named %s: no property number",
                    $hostel["property_name"]));
        }
        
        $this->CI->db->where("property_number", $propertyNumber);
        $isUpdated = $this->CI->db->update(self::HOSTEL_TABLE, $hostel);
                
        if (!$isUpdated) {
            throw new Exception(sprintf(
                "Hostel with property number %s unable to be updated.",
                $propertyNumber));
        }
    }
  
  function update_hostel($xml_hostel, $city_id)
  {
    $this->feed_trace(self::FEED_INFO,print_r($xml_hostel,true));
  	$this->CI->db->set('city_hb_id', $city_id);

    $this->CI->db->set('property_name', (string)$xml_hostel->name);
    $this->CI->db->set('property_type', (string)$xml_hostel["type"]);
    $this->CI->db->set('currency', (string)$xml_hostel["currency"]);
    $this->CI->db->set('geo_longitude', $xml_hostel->longitude);
    $this->CI->db->set('geo_latitude', $xml_hostel->latitude);

    $this->CI->db->set('address1', (string)$xml_hostel->address->address1);
    if(!empty($xml_hostel->address->address2))
    {
      $this->CI->db->set('address2', (string)$xml_hostel->address->address2);
    }
    if(!empty($xml_hostel->address->address3))
    {
      $this->CI->db->set('address3', (string)$xml_hostel->address->address3);
    }
    if(!empty($xml_hostel->address->state))
    {
      $this->CI->db->set('state', (string)$xml_hostel->address->state);
    }

    $this->CI->db->set('zip', (string)$xml_hostel->address->zipcode);

    $this->CI->db->set('cancellation_period', (int)$xml_hostel["cancellationperiod"]);
    $this->CI->db->set('page_url', (string)$xml_hostel->pageurl);

    if(!empty($xml_hostel->mapurl))
    {
      $this->CI->db->set('map_url', (string)$xml_hostel->mapurl);
    }

    if(!empty($xml_hostel->importantinfo))
    {
      $this->CI->db->set('important_info', (string)$xml_hostel->importantinfo);
    }
    else
    {
      $this->CI->db->set('important_info', null);
    }

    $this->CI->db->set('release_unit', (int)$xml_hostel->releaseunit);

    $rating = NULL;
    if((float)$xml_hostel->rating->overall > 0)
    {
      $rating = (string)$xml_hostel->rating->overall;
    }
    $this->CI->db->set('rating_overall', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->atmosphere > 0)
    {
      $rating = (string)$xml_hostel->rating->atmosphere;
    }
    $this->CI->db->set('rating_atmosphere', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->staff > 0)
    {
      $rating = (string)$xml_hostel->rating->staff;
    }
    $this->CI->db->set('rating_staff', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->location > 0)
    {
      $rating = (string)$xml_hostel->rating->location;
    }
    $this->CI->db->set('rating_location', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->cleanliness > 0)
    {
      $rating = (string)$xml_hostel->rating->cleanliness;
    }
    $this->CI->db->set('rating_cleanliness', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->facilities > 0)
    {
      $rating = (string)$xml_hostel->rating->facilities;
    }
    $this->CI->db->set('rating_facilities', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->safety > 0)
    {
      $rating = (string)$xml_hostel->rating->safety;
    }
    $this->CI->db->set('rating_safety', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->fun > 0)
    {
      $rating = (string)$xml_hostel->rating->fun;
    }
    $this->CI->db->set('rating_fun', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->value > 0)
    {
      $rating = (string)$xml_hostel->rating->value;
    }
    $this->CI->db->set('rating_value', $rating);


    if(!empty($xml_hostel["added"]))
    {
      $dbdate = new DateTime((string)$xml_hostel["added"]);
      $this->CI->db->set('added', $dbdate->format("Y-m-d"));
    }

    if(!empty($xml_hostel["modified"]))
    {
      $dbdate = new DateTime((string)$xml_hostel["modified"]);
      $this->CI->db->set('modified', $dbdate->format("Y-m-d"));
    }
    $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

    $this->CI->db->where('property_number', (int)$xml_hostel["id"]);
    $this->feed_trace(self::FEED_DEBUG,"before update hostel");
    return $this->CI->db->update(self::HOSTEL_TABLE);
  }

  function update_hb_short_desc($hostel_id, $language, $short_description)
  {
    if(is_null($this->get_hb_short_desc($hostel_id, $language)))
    {
      return $this->insert_hb_short_desc($hostel_id, $language, $short_description);
    }
    $this->CI->db->set('short_description', (string)$short_description);

    $this->CI->db->where('language', $language);
    $this->CI->db->where('hostel_hb_id', $hostel_id);
    return $this->CI->db->update(self::HOSTEL_DESC_TABLE);

  }
  
  /**
   * 
   * Update HB translations
   * 
   * @param type $hostel_id
   * @param array $translations
   * @return type
   */
  function update_hb_translations($hostel_id, $translations)
  {
    if(is_null($this->get_hb_translations($hostel_id, $translations['language'])))
    {
      return $this->insert_hb_translations($hostel_id, $translations);
    }
    $this->CI->db->set('short_description', (string)$translations['short_description']);
    $this->CI->db->set('long_description', (string)$translations['long_description']);
    $this->CI->db->set('hostel_location', (string)$translations['hostel_location']);
    $this->CI->db->set('hostel_directions', (string)$translations['hostel_directions']);

    $this->CI->db->where('language', $translations['language']);
    $this->CI->db->where('hostel_hb_id', $hostel_id);
    return $this->CI->db->update(self::HOSTEL_DESC_TABLE);

  }

  function update_hb_hostel_features($hostel_id, $features)
  {
    $return = true;

    $this->update_hostel_features_sync_status($hostel_id,self::PROPERTY_INVALID);

    if(!empty($features))
    {
      $this->feed_trace(self::FEED_DEBUG,"Updating features of property $hostel_id to ". print_r($features,true));
      foreach($features as $feature)
      {
        $this->feed_trace(self::FEED_DEBUG,"Updating feature $feature of property $hostel_id ");
        $feature_id = 0;
        if(is_null($feature_id = $this->get_feature_id($feature)))
        {
          if($this->insert_hb_feature($feature) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting feature -> $feature: ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $feature_id = $this->CI->db->insert_id();
          }
        }
        else
        {
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->where('hb_feature_id', $feature_id);
          if($this->CI->db->update(self::FEATURE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error updating feature -> $feature_id: ".$this->CI->db->last_query());
            $return = false;
          }
        }

        if(!empty($feature_id) && is_null($hostel_feature_id = $this->get_hostel_feature_id($hostel_id, $feature_id)))
        {
          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('hb_feature_id', $feature_id);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_FEATURE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error linking feature $feature_id with hostel $hostel_id -> : ".$this->CI->db->last_query());
            $return = false;
          }
        }
        elseif(!empty($feature_id))
        {
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->where('hb_hostel_feature_id', $hostel_feature_id);
          if($this->CI->db->update(self::HOSTEL_FEATURE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error updating linking feature $hostel_feature_id -> : ".$this->CI->db->last_query());
            $return = false;
          }
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error with feature ".$feature. " for hostel hb id $hostel_id");
        }

      }
    }

    $this->delete_hostel_features_sync_status($hostel_id);

    return $return;
  }

  function update_hb_hostel_extras($hostel_id, $extras)
  {
    $return = true;

    $this->update_hostel_extras_sync_status($hostel_id,self::PROPERTY_INVALID);

    if(!empty($extras))
    {
      foreach($extras as $extra)
      {
        $extra_id = 0;
        if(is_null($extra_id = $this->get_extra_id($extra)))
        {

          if($this->insert_hb_extra($extra) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting extra -> $extra: ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $extra_id = $this->CI->db->insert_id();
          }
        }
        else
        {
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->where('hb_extra_id', $extra_id);
          if($this->CI->db->update(self::EXTRA_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error updating extra -> $extra_id: ".$this->CI->db->last_query());
            $return = false;
          }
        }

        //If extra ID is valid and not alreay link to property
        // link it and save cost too!
        if(!empty($extra_id) && is_null($hostel_extra_id = $this->get_hostel_extra_id($hostel_id, $extra_id)))
        {
          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('hb_extra_id', $extra_id);
          $this->CI->db->set('cost', $extra["cost"]);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_EXTRA_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting link extra $extra_id to hostel $hostel_id: ".$this->CI->db->last_query());
            $return = false;
          }
        }
        //IF extra ID is valid and already link to property
        // Update cost and status
        elseif(!empty($extra_id))
        {
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->set('cost', $extra["cost"]);
          $this->CI->db->where('hb_hostel_extra_id', $hostel_extra_id);
          if($this->CI->db->update(self::HOSTEL_EXTRA_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error updating link extra -> $hostel_extra_id: ".$this->CI->db->last_query());
            $return = false;
          }
        }
        else
        {
          $this->feed_trace(self::FEED_ERROR,"Error with extra ".$extra. " for hostel hb id $hostel_id");
        }

      }
    }

    $this->delete_hostel_extras_sync_status($hostel_id);

    return $return;
  }

  function update_hb_images($hostel_id, $images)
  {
    $return = true;

    $this->update_hostel_image_sync_status($hostel_id,self::PROPERTY_INVALID);
    if(!empty($images))
    {
      foreach($images as $image)
      {
        $hostel_image_id = 0;
        if(is_null($hostel_image_id = $this->get_hostel_image_id($hostel_id, $image)))
        {

          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('url', (string)$image);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_IMAGE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting image -> (string)$image: ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $hostel_image_id = $this->CI->db->insert_id();
          }
        }
        else
        {
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->where('hb_hostel_image_id', $hostel_image_id);
          if($this->CI->db->update(self::HOSTEL_IMAGE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error updating image id -> $hostel_image_id: ".$this->CI->db->last_query());
            $return = false;
          }
        }
      }
    }

    $this->delete_hostel_images_sync_status($hostel_id);

    return $return;
  }
    
    public function delete_all_images_for_property($propertyNumber) {
        $isDeleted = $this->CI->db->delete(
                self::HOSTEL_IMAGE_TABLE, 
                array('hostel_hb_id' => $propertyNumber));
        
        if (!$isDeleted) {
            throw new Exception(sprintf(
                "Error occurred while deleting images for hb hostel with property number %s",
                $propertyNumber));
        }
    }
    
    public function delete_all_extras_for_property($propertyNumber) {
        $isDeleted = $this->CI->db->delete(
                self::HOSTEL_EXTRA_TABLE, 
                array('hostel_hb_id' => $propertyNumber));
        
        if (!$isDeleted) {
            throw new Exception(sprintf(
                "Error occurred while deleting extras for hb hostel with property number %s",
                $propertyNumber));
        }
    }
    
    public function delete_all_facilities_for_property($propertyNumber) {
        $isDeleted = $this->CI->db->delete(
                self::HOSTEL_FEATURE_TABLE, 
                array('hostel_hb_id' => $propertyNumber));
        
        if (!$isDeleted) {
            throw new Exception(sprintf(
                "Error occurred while deleting facilities/features for hb hostel with property number %s",
                $propertyNumber));
        }
    }
    
    public function delete_all_prices_for_property($propertyNumber) {
        $isDeleted = $this->CI->db->delete(
                self::HOSTEL_PRICE_TABLE, 
                array('hostel_hb_id' => $propertyNumber));
        
        if (!$isDeleted) {
            throw new Exception(sprintf(
                "Error occurred while deleting prices for hb hostel with property number %s",
                $propertyNumber));
        }
    }
  
    public function insert_prices(array $prices) {
        if (empty($prices)) return true;
      
        $isInserted = $this->CI->db->insert_batch(self::HOSTEL_PRICE_TABLE, $prices);
        if (!$isInserted) {
            throw new Exception("Error inserting prices into database for hb properties");
        }
    }
  
  function update_hb_prices($hostel_id, $property_xml)
  {
    $return = true;

    $this->update_hostel_price_sync_status($hostel_id,self::PROPERTY_INVALID);

    if(!empty($property_xml->privateprice))
    {
      for($i = 0;$i < count($property_xml->privateprice);$i++)
      {
        if(isset($property_xml->privateprice[$i]) && ($property_xml->privateprice[$i] > 0))
        {
          if(is_null($hostel_price_id = $this->get_hostel_price_id($hostel_id, (string)$property_xml->privateprice[$i]["currency"])))
          {

            $this->CI->db->set('hostel_hb_id', $hostel_id);
            $this->CI->db->set('currency_code', (string)$property_xml->privateprice[$i]["currency"]);
            $this->CI->db->set('bed_price', $property_xml->privateprice[$i]);
            $this->CI->db->set('type', (string)$property_xml->privateprice[$i]["type"]);
            $this->CI->db->set('api_sync_status', 1);

            if($this->CI->db->insert(self::HOSTEL_PRICE_TABLE) === false)
            {
              $this->feed_trace(self::FEED_ERROR,"Error inserting private prices -> ".$this->CI->db->last_query());
              $return = false;
            }
            else
            {
              $hostel_price_id = $this->CI->db->insert_id();
            }
          }
          else
          {
            $this->CI->db->set('api_sync_status', 1);
            $this->CI->db->set('bed_price', $property_xml->privateprice[$i]);
            $this->CI->db->set('type', (string)$property_xml->privateprice[$i]["type"]);
            $this->CI->db->where('hb_hostel_price_id', $hostel_price_id);
            if($this->CI->db->update(self::HOSTEL_PRICE_TABLE) === false)
            {
              $this->feed_trace(self::FEED_ERROR,"Error updating private proces-> ".$this->CI->db->last_query());
              $return = false;
            }
          }
        }

        $this->add_hb_currency_exchange((string)$property_xml->privateprice[$i]["currency"], (float)$property_xml->privateprice[$i]["exchange"], $hostel_id);
      }
    }

    if(!empty($property_xml->dormprice))
    {
      for($i = 0;$i < count($property_xml->dormprice);$i++)
      {
        if(isset($property_xml->dormprice[$i]) && ($property_xml->dormprice[$i] > 0))
        {
          if(is_null($hostel_price_id = $this->get_hostel_price_id($hostel_id, (string)$property_xml->dormprice[$i]["currency"])))
          {

            $this->CI->db->set('hostel_hb_id', $hostel_id);
            $this->CI->db->set('currency_code', (string)$property_xml->dormprice[$i]["currency"]);
            $this->CI->db->set('bed_price', $property_xml->dormprice[$i]);
            $this->CI->db->set('type', (string)$property_xml->dormprice[$i]["type"]);
            $this->CI->db->set('api_sync_status', 1);

            if($this->CI->db->insert(self::HOSTEL_PRICE_TABLE) === false)
            {
              $this->feed_trace(self::FEED_ERROR,"Error inserting dorm prices-> ".$this->CI->db->last_query());
              $return = false;
            }
            else
            {
              $hostel_price_id = $this->CI->db->insert_id();
            }
          }
          else
          {
            $this->CI->db->set('api_sync_status', 1);
            $this->CI->db->set('bed_price', $property_xml->dormprice[$i]);
            $this->CI->db->set('type', (string)$property_xml->dormprice[$i]["type"]);
            $this->CI->db->where('hb_hostel_price_id', $hostel_price_id);
            if($this->CI->db->update(self::HOSTEL_PRICE_TABLE) === false)
            {
              $this->feed_trace(self::FEED_ERROR,"Error updating dorm prices-> ".$this->CI->db->last_query());
              $return = false;
            }
          }
        }

        $this->add_hb_currency_exchange((string)$property_xml->dormprice[$i]["currency"], (float)$property_xml->dormprice[$i]["exchange"], $hostel_id);
      }
    }

    $this->delete_hostel_price_sync_status($hostel_id);

    return $return;
  }

  function update_hb_currencies_exchange()
  {
    $return = true;
    $this->feed_trace(self::FEED_DEBUG,"Updating HB currencies rates");

    foreach($this->hb_currencies as $currency => $rate)
    {
      $this->CI->db->set('hb_exchange', number_format($rate,2,".",""));
      $this->CI->db->where('currency_code', $currency);

      if($this->CI->db->update(self::CURRENCY_TABLE) === false)
      {
        $return = false;
        $this->feed_trace(self::FEED_ERROR,"Error Updating HB currencies rate - $currency -> ".number_format($rate,2,".",""));
      }
    }
    $this->feed_trace(self::FEED_DEBUG,"Done Updating HB currencies rates");
    return $return;
  }

  function update_hostel_booking_info($property_number,
                                      $email = NULL,
                                      $post_code = NULL,
                                      $phone = NULL,
                                      $fax = NULL,
                                      $currency_code = NULL)
  {
    $this->CI->db->set('zip', $post_code);
    $this->CI->db->set('phone', $phone);
    $this->CI->db->set('fax', $fax);
    $this->CI->db->set('email', $email);
    $this->CI->db->set('currency', $currency_code);
//    $this->CI->db->set('last_fake_booking', date(DATE_ATOM,time()));

    $this->CI->db->where('property_number', $property_number);
    return $this->CI->db->update(self::HOSTEL_TABLE);
  }


  public function update_hostel_x_timestamp($x, $property_number)
  {
    $this->CI->db->set($x, 'NOW()', FALSE);

    $this->CI->db->where('property_number', $property_number);
    return $this->CI->db->update(self::HOSTEL_TABLE);
  }

  function update_hostel_sync_status($property_number, $sync_status, $table)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("property_number",$property_number );
    $this->CI->db->update($table);
  }

  function delete_hostel_sync_status($property_number, $sync_status = 0, $table)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("property_number",$property_number );
    $this->CI->db->delete($table);
  }

  public function insert_or_update_hb_hostel_data(array $hostelData) {
        $hostel = $hostelData["property"];

        // Ignore hostels in cities that don't exist
        $cityId = $hostel["city_hb_id"];
        if (! $this->does_city_exist($cityId)) return NULL;

        $hostelId = $this->get_hostel_id($hostel["property_number"]);

        if (isset($hostelId) && !empty($hostelId)) {
            $hostel["hb_hostel_id"] = $hostelId;
            $this->update_hostel_data($hostelData);
        } else {
            $this->insert_hb_hostel_data($hostelData);
        }

        return true;
  }
  
  public function update_hb_hostel_sync_status($status) {
        $this->update_sync_status($status);
        $this->update_features_status($status);
        $this->update_extras_status($status);
  }
  
  private function does_city_exist($cityId) {
        $this->CI->load->model("db_hb_city");
        $city = $this->CI->db_hb_city->get_hb_city_from_hbid($cityId);
        if (empty($city)) return false;
        else return true;
  }
  
  private function update_hostel_data(array $hostelData) {
        $property = $hostelData["property"];
        $propertyNumber = $property["property_number"];
        unset($property["city_hb_id"]);
        
        $this->update_hostel_from_array($property);

        $this->delete_all_prices_for_property($propertyNumber);
        $this->insert_prices($hostelData["prices"]);

        $this->delete_all_images_for_property($propertyNumber);
        $this->insert_hb_images($hostelData["images"]);

        $this->delete_all_extras_for_property($propertyNumber);
        $this->insert_hb_extras_to_hostel_from_array($hostelData["extras"]);

        $this->delete_all_facilities_for_property($propertyNumber);
        $this->insert_hb_facilities($hostelData["facilities"]);
  }
  
  private function insert_hb_hostel_data(array $hostelData) {
        $hostel = $hostelData["property"];
        $prices = $hostelData["prices"];
        $hostel["added"] = date("Y-m-d");
        
        if (isset($hostel["hb_hostel_id"])) unset($hostel["hb_hostel_id"]);
        
        $this->insert_hostel_from_array($hostel);
        $this->insert_prices($prices);
        $this->insert_hb_images($hostelData["images"]);
        $this->insert_hb_extras_to_hostel_from_array($hostelData["extras"]);
        $this->insert_hb_facilities($hostelData["facilities"]);
  }
  
  function insert_hostel_from_array(array $hostel) {
    if (empty($hostel)) return true;
      
    $isInserted = $this->CI->db->insert(self::HOSTEL_TABLE, $hostel);

    if (!$isInserted) {
        die("Error inserting hostel: " . print_r($hostel));

        throw new Exception(sprintf(
              "Error inserting hostel (property number %s in db", 
                $hostel["property_number"]));
    }

    return $this->CI->db->insert_id();
      
  }
  
  function insert_hostel($xml_hostel, $city_id)
  {

  	$this->CI->db->set('city_hb_id', $city_id);
    $this->CI->db->set('property_number', (int)$xml_hostel["id"]);
    $this->CI->db->set('property_name', (string)$xml_hostel->name);
    $this->CI->db->set('property_type', (string)$xml_hostel["type"]);
    $this->CI->db->set('currency', (string)$xml_hostel["currency"]);
    $this->CI->db->set('geo_longitude', $xml_hostel->longitude);
    $this->CI->db->set('geo_latitude', $xml_hostel->latitude);

    $this->CI->db->set('address1', (string)$xml_hostel->address->address1);
    if(!empty($xml_hostel->address2))
    {
      $this->CI->db->set('address2', (string)$xml_hostel->address->address2);
    }
    if(!empty($xml_hostel->address3))
    {
      $this->CI->db->set('address3', (string)$xml_hostel->address->address3);
    }
    if(!empty($xml_hostel->address->state))
    {
      $this->CI->db->set('state', (string)$xml_hostel->address->state);
    }

    $this->CI->db->set('zip', (string)$xml_hostel->address->zipcode);

    $this->CI->db->set('cancellation_period', (int)$xml_hostel["cancellationperiod"]);
    $this->CI->db->set('page_url', (string)$xml_hostel->pageurl);

    if(!empty($xml_hostel->mapurl))
    {
      $this->CI->db->set('map_url', (string)$xml_hostel->mapurl);
    }
    if(!empty($xml_hostel->importantinfo))
    {
      $this->CI->db->set('important_info', (string)$xml_hostel->importantinfo);
    }

    $this->CI->db->set('release_unit', (int)$xml_hostel->releaseunit);

    $rating = NULL;
    if((float)$xml_hostel->rating->overall > 0)
    {
      $rating = (string)$xml_hostel->rating->overall;
    }
    $this->CI->db->set('rating_overall', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->atmosphere > 0)
    {
      $rating = (string)$xml_hostel->rating->atmosphere;
    }
    $this->CI->db->set('rating_atmosphere', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->staff > 0)
    {
      $rating = (string)$xml_hostel->rating->staff;
    }
    $this->CI->db->set('rating_staff', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->location > 0)
    {
      $rating = (string)$xml_hostel->rating->location;
    }
    $this->CI->db->set('rating_location', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->cleanliness > 0)
    {
      $rating = (string)$xml_hostel->rating->cleanliness;
    }
    $this->CI->db->set('rating_cleanliness', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->facilities > 0)
    {
      $rating = (string)$xml_hostel->rating->facilities;
    }
    $this->CI->db->set('rating_facilities', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->safety > 0)
    {
      $rating = (string)$xml_hostel->rating->safety;
    }
    $this->CI->db->set('rating_safety', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->fun > 0)
    {
      $rating = (string)$xml_hostel->rating->fun;
    }
    $this->CI->db->set('rating_fun', $rating);

    $rating = NULL;
    if((float)$xml_hostel->rating->value > 0)
    {
      $rating = (string)$xml_hostel->rating->value;
    }
    $this->CI->db->set('rating_value', $rating);


    if(!empty($xml_hostel["added"]))
    {
      $dbdate = new DateTime((string)$xml_hostel["added"]);
      $this->CI->db->set('added', $dbdate->format("Y-m-d"));
    }

    if(!empty($xml_hostel["modified"]))
    {
      $dbdate = new DateTime((string)$xml_hostel["modified"]);
      $this->CI->db->set('modified', $dbdate->format("Y-m-d"));
    }
    $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

    return $this->CI->db->insert(self::HOSTEL_TABLE);
  }
  
  /**
   * Insert HB translations
   * 
   * @param int $hostel_id
   * @param array $translations
   * @return boolean
   * @throws Exception
   */
  function insert_hb_translations($hostel_id, $translations)
  {
    $hostelId = $this->get_hostel_id($hostel_id);
            
    if (!isset($hostelId) || empty($hostelId)) {
        throw new Exception("Error inserting short description: hostel with property number $hostel_id doesn't exist");
        return true;
    }
      
    $this->CI->db->set('hostel_hb_id', $hostel_id);
    $this->CI->db->set('language', (string)$translations['language']);
    $this->CI->db->set('short_description', (string)$translations['short_description']);
    $this->CI->db->set('long_description', (string)$translations['long_description']);
    $this->CI->db->set('hostel_location', (string)$translations['hostel_location']);
    $this->CI->db->set('hostel_directions', (string)$translations['hostel_directions']);

    return $this->CI->db->insert(self::HOSTEL_DESC_TABLE);

  }

  function insert_hb_short_desc($hostel_id, $langage, $short_description)
  {
    $hostelId = $this->get_hostel_id($hostel_id);
            
    if (!isset($hostelId) || empty($hostelId)) {
        throw new Exception("Error inserting short description: hostel with property number $hostel_id doesn't exist");
        return true;
    }
      
    $this->CI->db->set('hostel_hb_id', $hostel_id);
    $this->CI->db->set('language', (string)$langage);
    $this->CI->db->set('short_description', (string)$short_description);

    return $this->CI->db->insert(self::HOSTEL_DESC_TABLE);

  }

  function insert_hb_feature($feature)
  {
    $this->CI->db->set('description', (string)$feature);
    $this->CI->db->set('api_sync_status', 1);

    return $this->CI->db->insert(self::FEATURE_TABLE);
  }

  function insert_hb_extra($extra)
  {
    $this->CI->db->set('description', (string)$extra);
    $this->CI->db->set('api_sync_status', 1);

    return $this->CI->db->insert(self::EXTRA_TABLE);
  }

  function insert_hb_features_to_hostel($hostel_id, $features)
  {
    $return = true;

    if(!empty($features))
    {
      foreach ($features as $feature)
      {
        $feature_id = 0;
        if(is_null($feature_id = $this->get_feature_id($feature)))
        {
          if($this->insert_hb_feature($feature) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting feature -> $feature: ".$this->CI->db->last_query());
            $return = false;
          }
          $feature_id = $this->CI->db->insert_id();
        }
        else
        {
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->where('hb_feature_id', $feature_id);
          if($this->CI->db->update(self::FEATURE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error updating feature -> $feature: ".$this->CI->db->last_query());
            $return = false;
          }
        }

        if(!empty($feature_id))
        {
          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('hb_feature_id', $feature_id);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_FEATURE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting link between feature $feature_id and property $hostel_id -> ".$this->CI->db->last_query());
            $return = false;
          }
        }
      }
    }

    return $return;
  }
  
  public function insert_hb_extras_to_hostel_from_array(array $extras) {
      if (empty($extras)) return true;
      
      $isInserted = $this->CI->db->insert_batch(self::HOSTEL_EXTRA_TABLE, $extras);
      
      if (!$isInserted) {
          throw new Exception("Error inserting hb extras into database");
      }
  }
  
  function insert_hb_extras_to_hostel($hostel_id, $extras)
  {
    $return = true;
    if(!empty($extras))
    {
      foreach ($extras as $extra)
      {
        $extra_id = 0;
        //If extra is not in DB insert it
        if(is_null($extra_id = $this->get_extra_id($extra)))
        {
          if($this->insert_hb_extra($extra) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting extra $extra -> ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $extra_id = $this->CI->db->insert_id();
          }
        }
        else
        {
          //Validate status
          $this->CI->db->set('api_sync_status', 1);
          $this->CI->db->where('hb_extra_id', $extra_id);
          if($this->CI->db->update(self::EXTRA_TABLE) === false)
          {
            $return = false;
          }
        }

        //If extra is in extra DB and ID is valid
        //  Associate this extra to the current property
        if(!empty($extra_id))
        {
          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('hb_extra_id', $extra_id);
          $this->CI->db->set('cost', $extra["cost"]);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_EXTRA_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting link between extra $extra_id and property $hostel_id -> ".$this->CI->db->last_query());
            $return = false;
          }
        }
      }
    }

    return $return;
  }

  public function insert_hb_images($images) {
      if (empty($images)) return true;
      
      $isInserted = $this->CI->db->insert_batch(self::HOSTEL_IMAGE_TABLE, $images);
      if (!$isInserted) {
          throw new Exception("Error inserting images into database for hb properties");
      }
  }
  
  public function insert_hb_facilities($facilities) {
      if (empty($facilities)) return true;
      
      $isInserted = $this->CI->db->insert_batch(self::HOSTEL_FEATURE_TABLE, $facilities);
      if (!$isInserted) {
          throw new Exception("Error inserting facilities/features into database for hb properties");
      }
  }
    
  function insert_hb_images_to_hostel($hostel_id, $images)
  {
    $return = true;
    if(!empty($images))
    {
      foreach ($images as $image)
      {
        $this->CI->db->set('hostel_hb_id', $hostel_id);
        $this->CI->db->set('url', (string)$image);
        $this->CI->db->set('api_sync_status', 1);

        if($this->CI->db->insert(self::HOSTEL_IMAGE_TABLE) === false)
        {
          $this->feed_trace(self::FEED_ERROR,"Error inserting image ".(string)$image." -> ".$this->CI->db->last_query());
          $return = false;
        }
      }
    }
    return $return;
  }
  
  function insert_hb_prices($hostel_id, $property_xml)
  {
    $return = true;

    if(!empty($property_xml->privateprice))
    {
      for($i = 0;$i < count($property_xml->privateprice);$i++)
      {
        if(isset($property_xml->privateprice[$i]) && ($property_xml->privateprice[$i] > 0))
        {
          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('currency_code', (string)$property_xml->privateprice[$i]["currency"]);
          $this->CI->db->set('bed_price', $property_xml->privateprice[$i]);
          $this->CI->db->set('type', (string)$property_xml->privateprice[$i]["type"]);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_PRICE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting private prices -> ".$this->CI->db->last_query());
            $return = false;
          }
        }

        $this->add_hb_currency_exchange((string)$property_xml->privateprice[$i]["currency"], (float)$property_xml->privateprice[$i]["exchange"], $hostel_id);
      }
    }

    if(!empty($property_xml->dormprice))
    {
      for($i = 0;$i < count($property_xml->dormprice);$i++)
      {
        if(isset($property_xml->dormprice[$i]) && ($property_xml->dormprice[$i] > 0))
        {
          $this->CI->db->set('hostel_hb_id', $hostel_id);
          $this->CI->db->set('currency_code', (string)$property_xml->dormprice[$i]["currency"]);
          $this->CI->db->set('bed_price', $property_xml->dormprice[$i]);
          $this->CI->db->set('type', (string)$property_xml->dormprice[$i]["type"]);
          $this->CI->db->set('api_sync_status', 1);

          if($this->CI->db->insert(self::HOSTEL_PRICE_TABLE) === false)
          {
            $this->feed_trace(self::FEED_ERROR,"Error inserting dorm prices -> ".$this->CI->db->last_query());
            $return = false;
          }
        }

        $this->add_hb_currency_exchange((string)$property_xml->dormprice[$i]["currency"], (float)$property_xml->dormprice[$i]["exchange"], $hostel_id);
      }
    }
    return $return;
  }

  function delete_sync_status($sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->delete(self::HOSTEL_TABLE);
  }

  function delete_features_sync_status($sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->delete(self::FEATURE_TABLE);
  }

  function delete_extras_sync_status($sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->delete(self::EXTRA_TABLE);
  }

  function delete_hostel_features_sync_status($hostel_hb_id, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->delete(self::HOSTEL_FEATURE_TABLE);
  }

  function delete_hostel_extras_sync_status($hostel_hb_id, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->delete(self::HOSTEL_EXTRA_TABLE);
  }

  function delete_hostel_images_sync_status($hostel_hb_id, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->delete(self::HOSTEL_IMAGE_TABLE);
  }

  function delete_hostel_price_sync_status($hostel_hb_id, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("hostel_hb_id",$hostel_hb_id );
    $this->CI->db->delete(self::HOSTEL_PRICE_TABLE);
  }

  function add_hb_currency_exchange($currency, $rate, $hostel_id)
  {
    if(empty($this->hb_currencies) || !isset($this->hb_currencies[$currency]))
    {
      $this->hb_currencies[$currency] = $rate;
    }
    elseif(($rate > 0) && ($this->hb_currencies[$currency] != $rate))
    {
      $this->feed_trace(self::FEED_DEBUG,"Price exchange $currency rate of hostel $hostel_id is different than a previous one. Previous -> ".$this->hb_currencies[$currency]." actual -> $rate");
    }
  }

  function get_all_properties()
  {
//     $this->db->select('property_number');
//     $this->db->select('geo_longitude');
//     $this->db->select('geo_latitude');

    $query = $this->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return array();
  }

  function get_all_properties_geos()
  {
    $this->db->select('property_number');
    $this->db->select('geo_longitude');
    $this->db->select('geo_latitude');

    $query = $this->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return array();
  }

  function get_hostel_id($property_number)
  {
    $this->CI->db->select("hb_hostel_id");
  	$this->CI->db->where("property_number",$property_number);
    $query = $this->CI->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_id;
    }
    return NULL;
  }

  function get_hostel_data($property_number)
  {
    $this->CI->db->select("*");
  	$this->CI->db->where("property_number",$property_number);
    $query = $this->CI->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }

  function get_hostel_important_info($property_number)
  {
    $this->CI->db->select("important_info");
  	$this->CI->db->where("property_number",$property_number);
    $query = $this->CI->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->important_info;
    }
    return NULL;
  }

  function get_hostel_main_services($property_number)
  {
    $property_number = $this->db->escape($property_number);

    $query = "(
                SELECT
                    *
                FROM
                    ((SELECT
                        hb_hostel_feature.hb_feature_id as service_id,
                            'feature' as service_type,
                            description,
                            desc_order,
                            0 as cost
                    FROM
                        hb_hostel_feature
                    LEFT JOIN hb_feature ON hb_hostel_feature.hb_feature_id = hb_feature.hb_feature_id
                    WHERE
                        hb_hostel_feature.hostel_hb_id = $property_number) UNION (SELECT
                        hb_hostel_extra.hb_extra_id as service_id,
                            'extra' as service_type,
                            description,
                            desc_order,
                            cost
                    FROM
                        hb_hostel_extra
                    LEFT JOIN hb_extra ON hb_hostel_extra.hb_extra_id = hb_extra.hb_extra_id
                    WHERE
                        hb_hostel_extra.hostel_hb_id = $property_number)) as amenities
                WHERE
                    cost = 0
                AND desc_order IS NOT NULL
                ORDER BY -(desc_order) DESC
                )UNION(
                  SELECT hb_hostel_landmark.landmark_id AS service_id,
                         'landmark' AS service_type,
                         landmark_name AS description,
                         NULL as desc_order,
                         0 as cost
                  FROM hb_hostel_landmark
                  LEFT JOIN landmarks ON hb_hostel_landmark.landmark_id = landmarks.landmark_id
                  WHERE hb_hostel_landmark.property_number = $property_number
                    AND landmark_name LIKE 'City Center'
                )UNION(
                  SELECT hb_hostel_id as service_id,
                         'security_rating' AS service_type,
                          rating_safety as description,
                          NULL as desc_order,
                          0 as cost
                  FROM hb_hostel
                  WHERE property_number = $property_number
                )";
    $query = $this->db->query($query);

    $services = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $i => $row)
      {
        $services[$i] = new stdClass();
        $services[$i]->service_id    = (int)$row->service_id;
        $services[$i]->description   = (string)$row->description;
        $services[$i]->service_type  = (string)$row->service_type;
        switch((string)$row->description)
        {
          case 'Internet / Wi-Fi':
            $services[$i]->service_type = 'internet';
            break;
          case 'Breakfast':
            $services[$i]->service_type = 'breakfast';
            break;
          case 'City Center':
            $services[$i]->service_type = 'downtown';
            break;
        }
      }
    }
    return $services;
  }

  function get_hostel_facilities($property_number)
  {
    $property_number = $this->CI->db->escape($property_number);
    $query = "SELECT * FROM
    					(
      					(
                  SELECT
                    hb_hostel_feature.hb_feature_id as ID,
                    'feature' as type,
                    description,
                    desc_order,
                    0 as cost
                  FROM hb_hostel_feature
                  LEFT JOIN hb_feature ON hb_hostel_feature.hb_feature_id = hb_feature.hb_feature_id
                  WHERE hb_hostel_feature.hostel_hb_id = $property_number
                )
                UNION
                (
                  SELECT
                    hb_hostel_extra.hb_extra_id as ID,
                    'extra' as type,
                    description,
                    desc_order,
                    cost
                  FROM hb_hostel_extra
                  LEFT JOIN hb_extra ON hb_hostel_extra.hb_extra_id = hb_extra.hb_extra_id
                  WHERE hb_hostel_extra.hostel_hb_id = $property_number
                )
    					) as amenities
							WHERE cost = 0
    					--	AND desc_order IS NOT NULL
    					ORDER BY -(desc_order) DESC";
    

	$query = $this->CI->db->query($query);

    $amenities = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $i => $row)
      {
      	$amenities[$i] = new stdClass();
        $amenities[$i]->facility_id = $row->type.$row->ID;
        $amenities[$i]->description = (string)$row->description;
		$amenities[$i]->type = (string)$row->type;
        $amenities[$i]->to_display  = 0;
        if(!empty($row->desc_order))
        {
          $amenities[$i]->to_display  = 1;
        }
      }
    }
    return $amenities;
  }

  function get_hostel_facilities_for_filter($property_number)
  {
    $property_number = $this->CI->db->escape($property_number);
    $query = "SELECT * FROM
    					(
      					(
                  SELECT
                    hb_hostel_feature.hb_feature_id as amenity_id,
                    'feature' as type,
        						0 as cost
                  FROM hb_hostel_feature
                  WHERE hb_hostel_feature.hostel_hb_id = $property_number
                )
                UNION
                (
                  SELECT
                    hb_hostel_extra.hb_extra_id as amenity_id,
                    'extra' as type,
                    cost
                  FROM hb_hostel_extra
                  WHERE hb_hostel_extra.hostel_hb_id = $property_number
                )
    					) as amenities
							WHERE cost = 0";

    $query = $this->CI->db->query($query);

    $amenities = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $i => $row)
      {
        $amenities[] = $row->type.$row->amenity_id;
      }
    }
    return $amenities;
  }

  //TODO MOVE in facility model!
  public function get_amenities_city_for_filter()
  {
    $query = "SELECT * FROM
              (
                (
                SELECT
                  hb_feature_id as amenity_id,
                  'feature' as type,
                  description as facility_name,
                  filter_order,
                  desc_order
                FROM hb_feature
                )
                UNION
                (
                SELECT
                  hb_extra_id as amenity_id,
                  'extra' as type,
                  description as facility_name,
                  filter_order,
                  desc_order
                FROM hb_extra
                )
              ) as amenities
              WHERE filter_order IS NOT NULL
              ORDER BY filter_order ASC";

    $query = $this->CI->db->query($query);

    $popularFacilitiesById = $this->config->item("hbMostPopularFacilitiesById");
    $mostPopularAmenities = array();
    $amenities = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $row)
      {
      	$amenity = new stdClass();
        $amenity->amenity_id = (int)$row->amenity_id;
        $amenity->facility_id = $row->type.$row->amenity_id;
        $amenity->type        = (string)$row->type;
        $amenity->facility_name = (string)$row->facility_name;
        $amenity->filter_order  = (int)$row->filter_order;
        
        $amenity->id_to_display = $this->getFacilityIdToDisplay($amenity);
        
        $popularAmenityKey = array_search($amenity->amenity_id, $popularFacilitiesById);
        if ($popularAmenityKey !== FALSE) {
            $mostPopularAmenities[$popularAmenityKey] = $amenity;
        } else {
            $amenities[] = $amenity;
        }
      }
    }
    
    return array(
        "mostPopularAmenities" => $mostPopularAmenities,
        "amenities" => $amenities
    );
  }
  
  private function getFacilityIdToDisplay($amenity) {
      if ($amenity->facility_name == 'Breakfast Included' || $amenity->facility_name == 'Breakfast') {
        $idToDisplay = 'free-breakfast';
    } else {
        $idToDisplay = $amenity->facility_id;
    }
    
    return $idToDisplay;
  }
          
  function get_property_type($property_number)
  {
  	$this->CI->db->select("property_type");
  	$this->CI->db->where("property_number",$property_number);
    $query = $this->CI->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->property_type;
    }
    //TONOTICE if hostel not in DB default type is property
    return "property";
  }

  function get_hb_long_desc($property_number, $lang = "en")
  {
    $this->CI->db->where('language', $lang);
    $this->CI->db->where('hostel_hb_id', $property_number);

    $query = $this->CI->db->get(self::HOSTEL_DESC_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->long_description;
    }
    return NULL;

  }
  
  function get_hb_short_desc($property_number, $lang = "en")
  {
    $this->CI->db->where('language', $lang);
    $this->CI->db->where('hostel_hb_id', $property_number);

    $query = $this->CI->db->get(self::HOSTEL_DESC_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->short_description;
    }
    return NULL;

  }
  
  function get_hb_translations($property_number, $lang = "en")
  {
    $this->CI->db->where('language', $lang);
    $this->CI->db->where('hostel_hb_id', $property_number);

    $query = $this->CI->db->get(self::HOSTEL_DESC_TABLE);
    if($query->num_rows() > 0)
    {
      return $query->row();
    }
    return NULL;

  }

  public function get_feature_by_id($featureId) {
      $this->CI->db->where("hb_feature_id", $featureId);
      $query = $this->CI->db->get(self::FEATURE_TABLE, 1);
      
      return $query->result();
  }
  
  function get_feature_id($feature)
  {
    $feature = $this->CI->db->escape_str($feature);
    $this->feed_trace(self::FEED_DEBUG,"Adding feature $feature -> LOWER(`description`) LIKE LOWER('$feature')");
    $this->CI->db->where("LOWER(`description`) LIKE LOWER('$feature')");

    $query = $this->CI->db->get(self::FEATURE_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_feature_id;
    }
    return NULL;
  }

  function get_extra_id($extra)
  {
    $extra = $this->CI->db->escape_str($extra);
    $this->CI->db->where("LOWER(`description`) LIKE LOWER('$extra')");

    $query = $this->CI->db->get(self::EXTRA_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_extra_id;
    }
    return NULL;
  }

  function get_hostel_feature_id($hostel_hb_id, $feature_id)
  {
    $this->CI->db->where("hostel_hb_id", $hostel_hb_id);
    $this->CI->db->where("hb_feature_id", $feature_id);

    $query = $this->CI->db->get(self::HOSTEL_FEATURE_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_feature_id;
    }
    return NULL;
  }

	// Need to work to get all the features not just one row
	function get_hostel_features($hostel_hb_id)
  {
    $this->CI->db->select(self::FEATURE_TABLE.'.description,'.self::FEATURE_TABLE.'.hb_feature_id');
		$this->CI->db->join(self::HOSTEL_FEATURE_TABLE, self::HOSTEL_FEATURE_TABLE.'.hb_feature_id = '.self::FEATURE_TABLE.'.hb_feature_id', "left");
		$this->CI->db->where("hostel_hb_id", $hostel_hb_id);

    $query = $this->CI->db->get(self::FEATURE_TABLE);
		if($query->num_rows() > 0)
		{
			return $query->result_array();
		}
    return NULL;
  }

  function get_hostel_extra_id($hostel_hb_id, $extra_id)
  {
    $this->CI->db->where("hostel_hb_id", $hostel_hb_id);
    $this->CI->db->where("hb_extra_id", $extra_id);

    $query = $this->CI->db->get(self::HOSTEL_EXTRA_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_extra_id;
    }
    return NULL;
  }

  /**
  * Function to get property address
  * param property number
  */
  function get_property_address($property_number = 0)
  {

	if($property_number == 0) // ID specified?
	{
		return false;
	}
	$this->CI->db->where("property_number", $property_number);
	$query = $this->CI->db->get(self::HOSTEL_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
	  $address2 = (isset($row->address2) AND $row->address2!=NULL) ? ', '.$row->address2 : ''; // address2 has value or null
      return $row->address1.$address2;
    }
    return false;
  }
  
	// Need to work to get all the extras not just one row
	function get_hostel_extras($hostel_hb_id)
  {
    $this->CI->db->select(self::EXTRA_TABLE.'.description,'.self::EXTRA_TABLE.'.hb_extra_id');
		$this->CI->db->join(self::HOSTEL_EXTRA_TABLE, self::HOSTEL_EXTRA_TABLE.'.hb_extra_id = '.self::EXTRA_TABLE.'.hb_extra_id', "left");
		$this->CI->db->where("hostel_hb_id", $hostel_hb_id);

    $query = $this->CI->db->get(self::EXTRA_TABLE);
		//var_dump($query);
		if($query->num_rows() > 0)
		{
			//$row = $query->row();
			return $query->result_array();
		}
    return NULL;
  }

  function get_hostel_images($property_number)
  {

    $this->CI->db->select("url");
    $this->CI->db->where("hostel_hb_id", $property_number);

    $query = $this->CI->db->get(self::HOSTEL_IMAGE_TABLE);
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function get_hostel_image_id($hostel_hb_id, $image_url)
  {

    $this->CI->db->where("hostel_hb_id", $hostel_hb_id);
    $this->CI->db->where("url", (string)$image_url);

    $query = $this->CI->db->get(self::HOSTEL_IMAGE_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_image_id;
    }
    return NULL;
  }

  function get_hostel_price_id($hostel_hb_id, $currency_code)
  {

    $this->CI->db->where("hostel_hb_id", $hostel_hb_id);
    $this->CI->db->where("currency_code", (string)$currency_code);

    $query = $this->CI->db->get(self::HOSTEL_PRICE_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_price_id;
    }
    return NULL;
  }

  function get_hostel($property_number)
  {
    $this->CI->db->where("property_number",$property_number);
    $query = $this->CI->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }

  function get_hostel_prices($property_number , $currency_code)
  {
    $this->CI->db->join(self::HOSTEL_PRICE_TABLE, self::HOSTEL_PRICE_TABLE.'.hostel_hb_id = '.self::HOSTEL_TABLE.'.property_number', "left");
    $this->CI->db->where("property_number",$property_number);
    $this->CI->db->where("currency_code",$currency_code);
    $query = $this->CI->db->get(self::HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }

  function get_hostel_district_id($property_number, $district_id)
  {
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("district_id", $district_id);

    $query = $this->CI->db->get(self::HB_HOSTEL_DISTRICT_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_district_id;
    }
    return NULL;
  }

  public function get_hostel_landmark_id($property_number, $landmark_id)
  {
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("landmark_id", $landmark_id);

    $query = $this->CI->db->get(self::HB_HOSTEL_LANDMARK_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_landmark_id;
    }
    return NULL;
  }

  public function get_districts_by_city_id($city_id)
  {
    $city_id = $this->db->escape_str($city_id);
        $sql = "SELECT `".self::DISTRICTS_TABLE."`.`district_id`, `".self::DISTRICTS_TABLE."`.`um_id`, `".self::DISTRICTS_TABLE."`.`district_name`, COUNT(`".self::HOSTEL_TABLE."`.`property_number`) as `district_count`
            FROM (`".self::HOSTEL_TABLE."`)
            RIGHT JOIN `".self::HB_HOSTEL_DISTRICT_TABLE."` ON `".self::HOSTEL_TABLE."`.`property_number` = `".self::HB_HOSTEL_DISTRICT_TABLE."`.`property_number`
            RIGHT JOIN `".self::DISTRICTS_TABLE."` ON `".self::DISTRICTS_TABLE."`.`district_id` = `".self::HB_HOSTEL_DISTRICT_TABLE."`.`district_id`
            WHERE `".self::HOSTEL_TABLE."`.`city_hb_id` = '".$city_id."'
            GROUP BY `".self::DISTRICTS_TABLE."`.`district_id`
            ;";
    $query = $this->db->query($sql);

    $return = array();
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return $return;
  }

  public function get_property_districts_for_filter($property_number)
  {
     $this->db->select(
            self::HB_HOSTEL_DISTRICT_TABLE.".district_id ,"
            .self::DISTRICTS_TABLE.".district_name ,"
            .self::DISTRICTS_TABLE.".um_id "
            );
    $this->db->join(self::DISTRICTS_TABLE, self::DISTRICTS_TABLE.'.district_id = '.self::HB_HOSTEL_DISTRICT_TABLE.'.district_id', "left");
    $this->db->where(self::HB_HOSTEL_DISTRICT_TABLE.".property_number",$property_number);

    $query = $this->db->get(self::HB_HOSTEL_DISTRICT_TABLE);

    $return = array();
    if($query->num_rows() > 0)
    {
      $return = $query->result();
    }
    return $return;
  }

  public function get_property_districts($property_number)
  {
    $this->db->select(self::DISTRICTS_TABLE.".district_name,".self::DISTRICTS_TABLE.".um_id");
    $this->db->join(self::DISTRICTS_TABLE, self::DISTRICTS_TABLE.'.district_id = '.self::HB_HOSTEL_DISTRICT_TABLE.'.district_id', "left");
    $this->db->where(self::HB_HOSTEL_DISTRICT_TABLE.".property_number",$property_number);
    $query = $this->db->get(self::HB_HOSTEL_DISTRICT_TABLE);

    $return = array();
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return $return;
  }

  public function get_landmarks_by_city_id($city_id, $range_km = 5, $landmark_source = 'manual')
  {
    $this->CI->load->model("Db_landmarks");
    $landmark_source_id = $this->CI->Db_landmarks->get_landmark_source_id($landmark_source);

    $city_id  = $this->db->escape_str($city_id);
    $range_km = $this->db->escape($range_km);
    $landmark_source_id = $this->db->escape($landmark_source_id);

    $sql = "SELECT `".self::LANDMARKS_TABLE."`.`landmark_id`,
                   `".self::LANDMARKS_TABLE."`.`landmark_name`,
                   `".self::LANDMARKS_TABLE."`.`geo_latitude`,
                   `".self::LANDMARKS_TABLE."`.`geo_longitude`,
                     SUM(if( distance <= $range_km,1,0)) as landmark_count
              FROM ".self::HOSTEL_TABLE."
              RIGHT JOIN `".self::HB_HOSTEL_LANDMARK_TABLE."` ON `".self::HB_HOSTEL_LANDMARK_TABLE."`.`property_number` = `".self::HOSTEL_TABLE."`.`property_number`
              LEFT JOIN `".self::LANDMARKS_TABLE."` ON `".self::LANDMARKS_TABLE."`.`landmark_id` = `".self::HB_HOSTEL_LANDMARK_TABLE."`.`landmark_id`
              WHERE `".self::HOSTEL_TABLE."`.`city_hb_id` = $city_id
              	AND `".self::LANDMARKS_TABLE."`.source = $landmark_source_id
              GROUP BY `".self::LANDMARKS_TABLE."`.`landmark_id`
              ORDER BY ".self::LANDMARKS_TABLE.".landmark_name ASC";
    $query = $this->db->query($sql);

    $return = array();
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return $return;
  }

  public function get_property_landmarks_for_filter($property_number,$range_km = 5, $landmark_source_id = 2)
  {
    $range_km = $this->db->escape_str($range_km);

    $this->db->select(self::HB_HOSTEL_LANDMARK_TABLE.".landmark_id");
    $this->db->select(self::LANDMARKS_TABLE.".slug");
    $this->db->select(self::LANDMARKS_TABLE.".landmark_name");
    $this->db->select(self::LANDMARKS_TABLE.".geo_latitude");
    $this->db->select(self::LANDMARKS_TABLE.".geo_longitude");
    $this->db->join(self::LANDMARKS_TABLE, self::HB_HOSTEL_LANDMARK_TABLE.'.landmark_id = '.self::LANDMARKS_TABLE.'.landmark_id');
    $this->db->where("property_number",$property_number);
    $this->db->where("source",$landmark_source_id);
    $this->db->where("distance <= $range_km");

    $query = $this->db->get(self::HB_HOSTEL_LANDMARK_TABLE);

    $return = array();
    if($query->num_rows() > 0)
    {
      $return = $query->result();
    }
    return $return;
  }

  public function get_property_landmarks($property_number, $range_km = 5)
  {
    $property_number = $this->db->escape_str($property_number);
    $range_km = $this->db->escape_str($range_km);
    $sql = "SELECT `".self::LANDMARKS_TABLE."`.*,
                    (
                      SELECT GROUP_CONCAT(type SEPARATOR '|')
                      FROM `".self::LANDMARK_TYPE_TABLE."`
                      RIGHT JOIN `".self::LANDMARK_OF_TYPE_TABLE."` ON `".self::LANDMARK_TYPE_TABLE."`.`landmark_type_id` = `".self::LANDMARK_OF_TYPE_TABLE."`.`landmark_type_id`
                      WHERE `".self::LANDMARK_OF_TYPE_TABLE."`.`landmark_id` = `".self::LANDMARKS_TABLE."`.`landmark_id`
                    )
                    as types,
                    (
                      SELECT GROUP_CONCAT(html_attribution SEPARATOR '|')
                      FROM `".self::LANDMARK_ATTRIBUTION_TABLE."`
                      RIGHT JOIN `".self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE."` ON `".self::LANDMARK_ATTRIBUTION_TABLE."`.`landmark_attribution_id` = `".self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE."`.`landmark_attribution_id`
                      WHERE `".self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE."`.`property_number` = `".self::HB_HOSTEL_LANDMARK_TABLE."`.`property_number`
                    )
                    as attributions
            FROM (
              SELECT `property_number`, `landmark_id`
              FROM (`".self::HB_HOSTEL_LANDMARK_TABLE."`)
              WHERE `property_number` = '".$property_number."' AND `distance` <= '".$range_km."'
            ) AS hw_hostel_landmark
            LEFT JOIN `".self::LANDMARKS_TABLE."` ON `".self::LANDMARKS_TABLE."`.`landmark_id` = `".self::HB_HOSTEL_LANDMARK_TABLE."`.`landmark_id`
            ;";
    $query = $this->db->query($sql);

    $return = array();
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return $return;
  }

    public function appendAdditionalPropertyData(&$hostelList) {
        foreach($hostelList as $propertyId => &$property)
        {
            $db_property = $this->get_hostel($property["id"]);
            if(!is_null($db_property)) {
                $property["geo_latitude"] = $db_property->geo_latitude;
                $property["geo_longitude"] = $db_property->geo_longitude;
                $property["ratings"] = $this->getRatingsFromDbProperty($db_property);
            }
        }
        
        return $hostelList;
    }
  
    private function getRatingsFromDbProperty($property) {
        $ratings = array(
            "atmosphere" => round($property->rating_atmosphere),
            "staff" => round($property->rating_staff),
            "location" => round($property->rating_location),
            "cleanliness" => round($property->rating_cleanliness),
            "facilities" => round($property->rating_facilities),
            "safety" => round($property->rating_safety),
            "value" => round($property->rating_value),
        );
        
        return $ratings;
    }
    
  function append_geo_location_data(&$hbhostellist)
  {
    foreach($hbhostellist as $key => $property)
    {
      if(!is_null($db_property = $this->get_hostel($property["id"])))
      {
        $hbhostellist[$key]["geo_latitude"] = $db_property->geo_latitude;
        $hbhostellist[$key]["geo_longitude"] = $db_property->geo_longitude;
      }
    }

    return $hbhostellist;
  }

  function set_logfile($filename)
  {
    $this->CI->code_tracker->set_logfile($filename);
  }

  function modification_html_report()
  {
    return $this->CI->code_tracker->html_report();
  }















  public function update_property_district($property_number, $districts)
  {
    $this->CI->load->model('Db_districts');

    $return = true;

    //Invalidate all current districts of property
    $this->update_hostel_sync_status($property_number,self::PROPERTY_INVALID,self::HB_HOSTEL_DISTRICT_TABLE);

    if(!empty($districts))
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating district of property $property_number to ". print_r($districts,true));

      foreach($districts as $district)
      {
        //prevent empty string to become a district in DB
        if(empty($district)) continue;

        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating district ".$district->properties->name." of property $property_number ");

        //IF district does not exist
        //  add it in DB and then get created ID
        //else
        //  get district id and validate with sync field
        // Create feed trace on error
        $district_id = NULL;
        if(is_null($district_id = $this->CI->Db_districts->get_district_id($district->id)))
        {
          if($this->CI->Db_districts->insert_district($district) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error inserting district -> ".$district->properties->name.": ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added district ".$district->properties->name." in DB");
            $district_id = $this->CI->db->insert_id();
          }
        }
        else
        {
          //Else update district data
          if($this->CI->Db_districts->update_district($district) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating district -> ".$district->properties->name.": ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updated district ".$district->properties->name." in DB");
          }
        }

        //IF district ID is valid
        //  IF link with this district ID is NOT already in DB with this property
        //    insert link to this district for this property
        //  ELSE
        //     Validate it using sync status field
        if(!empty($district_id) && is_null($hostel_district_id = $this->get_hostel_district_id($property_number, $district_id)))
        {
          $this->CI->db->set('property_number', (int)$property_number);
          $this->CI->db->set('district_id', (int)$district_id);
          $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

          if($this->CI->db->insert(self::HB_HOSTEL_DISTRICT_TABLE) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error linking district $district_id with hostel $property_number -> : ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added district ".$district->properties->name." ($district_id) into property $property_number");
          }
        }
        elseif(!empty($district_id))
        {
          $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
          $this->CI->db->where('hb_hostel_district_id', $hostel_district_id);
          if($this->CI->db->update(self::HB_HOSTEL_DISTRICT_TABLE) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking district $hostel_district_id -> : ".$this->CI->db->last_query());
            $return = false;
          }
        }
        else
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error with district ".$district->properties->name." for property number $property_number");
        }

      }
    }

    $this->delete_hostel_sync_status($property_number, self::PROPERTY_INVALID, self::HB_HOSTEL_DISTRICT_TABLE);

    //Update timestamp
    if($this->update_hostel_x_timestamp('last_district_update', $property_number) === FALSE)
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating last updated district timestamp for property number $property_number");
    }

    return $return;
  }


  public function update_property_landmark($property_number, $prop_geo_lat, $prop_geo_lng ,$landmarks)
  {
    $this->CI->load->model('Db_landmarks');
    $return = true;

    //Invalidate all current landmarks of property
    $this->update_hostel_sync_status($property_number,self::PROPERTY_INVALID,self::HB_HOSTEL_LANDMARK_TABLE);

    if(!empty($landmarks))
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating landmark of property $property_number");

      foreach($landmarks as $landmark)
      {
        //prevent empty string to become a landmark in DB
        if(empty($landmark)) continue;

        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating landmark ".$landmark->name." of property $property_number ");

        //IF landmark does not exist
        //  add it in DB and then get created ID
        //else
        //  get landmark id and validate with sync field
        // Create feed trace on error
        $landmark_id = NULL;
        if(is_null($landmark_id = $this->CI->Db_landmarks->get_landmark_id($landmark->id)))
        {
          if($this->CI->Db_landmarks->insert_landmark($landmark) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error inserting landmark -> ".$landmark->name.": ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added landmark ".$landmark->name." in DB");
            $landmark_id = $this->CI->db->insert_id();
          }
        }
        else
        {
          //Else update landmark data
          if($this->CI->Db_landmarks->update_landmark($landmark) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating landmark -> ".$landmark->name.": ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updated landmark ".$landmark->name." in DB");
          }
        }

        //Update types of landmark
        if(!empty($landmark_id))
        {
          if($this->CI->Db_landmarks->update_types_of_landmark($landmark_id , $landmark->types) === false)
          {
            $return = false;
          }
        }

        if(isset($landmark->geometry->location->lat)||isset($landmark->geometry->location->lng))
        {
          $this->CI->load->library("Geos");
          $landmark->distance_between_property = $this->geos->get_distance($prop_geo_lat, $prop_geo_lng, $landmark->geometry->location->lat, $landmark->geometry->location->lng);
        }

        //IF landmark ID is valid
        //  IF link with this landmark ID is NOT already in DB with this property
        //    insert link to this landmark for this property
        //  ELSE
        //     Validate it using sync status field
        if(!empty($landmark_id) && is_null($hostel_landmark_id = $this->get_hostel_landmark_id($property_number, $landmark_id)))
        {
          $this->CI->db->set('property_number', (int)$property_number);
          $this->CI->db->set('landmark_id', (int)$landmark_id);

          if(!empty($landmark->distance_between_property))
          {
            $this->CI->db->set('distance', (float)$landmark->distance_between_property);
          }

          $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

          if($this->CI->db->insert(self::HB_HOSTEL_LANDMARK_TABLE) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error linking landmark $landmark_id with hostel $property_number -> : ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added landmark ".$landmark->name." ($landmark_id) into property $property_number");
          }
        }
        elseif(!empty($landmark_id))
        {
          $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
          if(!empty($landmark->distance_between_property))
          {
            $this->CI->db->set('distance', (float)$landmark->distance_between_property);
          }
          else
          {
            $this->CI->db->set('distance', NULL);
          }
          $this->CI->db->where('hb_hostel_landmark_id', $hostel_landmark_id);
          if($this->CI->db->update(self::HB_HOSTEL_LANDMARK_TABLE) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking landmark $hostel_landmark_id -> : ".$this->CI->db->last_query());
            $return = false;
          }
        }
        else
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error with landmark ".$landmark->name." for hostel number $property_number");
        }
      }
    }

    $this->delete_hostel_sync_status($property_number, self::PROPERTY_INVALID, self::HB_HOSTEL_LANDMARK_TABLE);

    //Update timestamp
    if($this->update_hostel_x_timestamp('last_landmark_update', $property_number) === FALSE)
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating last updated landmark timestamp for property number $property_number");
    }

    return $return;
  }

  public function set_attribution_of_prop_number($property_number,$attribution_id)
  {
    $prop_attribution_link_id = NULL;
    if(is_null($prop_attribution_link_id = $this->get_property_attribution_id($property_number,$attribution_id)))
    {
      $this->CI->db->set('property_number', (int)$property_number);
      $this->CI->db->set('landmark_attribution_id', (int)$attribution_id);
      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
      if($this->CI->db->insert(self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error inserting landmark attribution ".$html_attribution." to property $property_number in DB -> ".$this->CI->db->last_query());
      }
      else
      {
        $prop_attribution_link_id = $this->CI->db->insert_id();
      }
    }
    else
    {
      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
      $this->CI->db->where('hb_hostel_landmark_attribution_id', $prop_attribution_link_id);
      if($this->CI->db->update(self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking landmark attribution $prop_attribution_link_id to property $property_number -> : ".$this->CI->db->last_query());
      }
    }

    return $prop_attribution_link_id;
  }

  function update_landmark_attribution_of_prop_sync_status($property_number, $sync_status = 0)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("property_number", $property_number);
    return $this->CI->db->update(self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE);
  }

  public function update_property_landmark_attribution($property_number, $html_attributions)
  {
    $this->CI->load->model('Db_landmarks');
    //invalidate all html landmarks attributions of property
    $this->update_landmark_attribution_of_prop_sync_status($property_number,self::PROPERTY_INVALID);

    foreach($html_attributions as $html_attribution)
    {
      $attribution_id = $this->CI->Db_landmarks->update_landmark_attribution($html_attribution);
      if(!empty($attribution_id))
      {
        $this->set_attribution_of_prop_number($property_number,$attribution_id);
      }
    }
    //Delete all no more valid landmarks attribution of property
    $this->delete_landmark_attribution_of_prop_sync_status($property_number);
  }

  public function delete_landmark_attribution_of_prop_sync_status($property_number, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->delete(self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE);
  }

  public function get_property_attribution_id($property_number, $attribution_id)
  {
    $this->CI->db->select("hb_hostel_landmark_attribution_id");
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("landmark_attribution_id", $attribution_id);

    $query = $this->CI->db->get(self::HB_HOSTEL_LANDMARK_ATTRIBUTION_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hb_hostel_landmark_attribution_id;
    }
    return NULL;
  }


  function get_properties_with_last_x_update($x, $days_too_old = NULL, $limit = 1000)
  {
    $query = "SELECT property_number,geo_longitude,geo_latitude";
    $query.= " FROM ".self::HOSTEL_TABLE;
    $query.= " WHERE `".$x."` IS NULL";
    $query.= " AND (`geo_latitude` != 0 OR `geo_longitude` != 0)";

    if (!is_null($days_too_old))
    {
      settype($days_too_old,'integer');
      $query.= " OR DATEDIFF(current_date(),`".$x."`) >= ".$this->CI->db->escape($days_too_old);
    }

    $query.= " ORDER BY `".$x."` ASC";

    if (!is_null($limit))
    {
      settype($limit,'integer');
      $query.= " LIMIT ".$this->CI->db->escape($limit);
    }

    $query = $this->CI->db->query($query);

    $return = array();

    if($query->num_rows() > 0)
    {
      $return = $query->result();
    }
    return $return;
  }
  
  //compare property function
  public function compare_property($property_number)
  {
    $this->CI->db->select("hb_hostel.*,hb_hostel_price.currency_code,hb_hostel_price.bed_price,hb_hostel_price.type,currencies.symbol");
	$this->CI->db->from("hb_hostel");
	$this->db->join('hb_hostel_price','hb_hostel_price.hostel_hb_id=hb_hostel.property_number','left');
	$this->db->join('currencies','currencies.currency_code=hb_hostel.currency','left');
  	$this->CI->db->where("hb_hostel.property_number",$property_number);
	$query=$this->CI->db->get();
	return $query->result();
  } 
  public function compare_property_extra($property_number)
  {
    $this->CI->db->select("hb_ext.hb_extra_id,hb_extra.description");
	$this->CI->db->from("hb_hostel_extra as hb_ext");
	$this->db->join('hb_extra','hb_extra.hb_extra_id=hb_ext.hb_extra_id','left');
  	$this->CI->db->where("hb_ext.hostel_hb_id",$property_number);
	$query=$this->CI->db->get();
	return $query->result();
  }
  public function compare_property_feature($property_number)
  {
    $this->CI->db->select("hb_feat.hb_feature_id,hb_feature.description");
	$this->CI->db->from("hb_hostel_feature as hb_feat");
	$this->db->join('hb_feature','hb_feature.hb_feature_id=hb_feat.hb_feature_id','left');
  	$this->CI->db->where("hb_feat.hostel_hb_id",$property_number);
	$query=$this->CI->db->get();
	return $query->result();
  }
  public function compare_property_image($property_number)
  {
    $this->CI->db->select("url");
	$this->CI->db->from("hb_hostel_image");
  	$this->CI->db->where("hostel_hb_id",$property_number);
	$this->CI->db->order_by("url","desc");
	$this->CI->db->limit(1);
	$query=$this->CI->db->get();
	return $query->row_array();
  }
  public function property_extra()
  {
    $this->CI->db->select("hb_extra_id,description");
	$this->CI->db->from("hb_extra");
	$query=$this->CI->db->get();
	return $query->result();
  }
  public function property_feature()
  {
    $this->CI->db->select("hb_feature_id,description");
	$this->CI->db->from("hb_feature");
	$query=$this->CI->db->get();
	return $query->result();
  }
  public function compare_cookie_property($property_number)
  {
    $this->CI->db->select("hb_hostel.property_name,hb_hostel.property_type,hb_hostel.rating_overall,hb_hostel_price.currency_code,hb_hostel_price.bed_price");
	$this->CI->db->from("hb_hostel");
	$this->db->join('hb_hostel_price','hb_hostel_price.hostel_hb_id=hb_hostel.property_number','left');
  	$this->CI->db->where("hb_hostel.property_number",$property_number);
	$query=$this->CI->db->get();
	return $query->row_array();
  }
  public function compare_property_info($property_number)
  {
    $this->CI->db->select("property_name,property_type,geo_latitude,geo_longitude");
	$this->CI->db->from("hb_hostel");
  	$this->CI->db->where("property_number",$property_number);
	$query=$this->CI->db->get();
	return $query->row_array();
  }
  //compare property function end
}
