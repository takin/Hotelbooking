<?php
/**
 * @author Louis-Michel
 *
 */
class Db_hw_hostel extends CI_Model
{
  const HW_CITY_TABLE           = 'hw_city';
  const HW_COUNTRY_TABLE        = 'hw_country';
  const HW_HOSTEL_TABLE         = 'hw_hostel';
  const HW_HOSTEL_DESC_TABLE    = 'hw_hostel_description';
  const HW_HOSTEL_PRICE_TABLE   = 'hw_hostel_price';

  const HW_HOSTEL_FACILITY_TABLE  = 'hw_hostel_facility';
  const HW_FACILITY_TABLE         = 'hw_facility';

  const HW_HOSTEL_DISTRICT_TABLE              = 'hw_hostel_district';
  const HW_HOSTEL_LANDMARK_TABLE              = 'hw_hostel_landmark';
  const HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE  = 'hw_hostel_landmark_attribution';

  const DISTRICTS_TABLE             = 'districts';
  const LANDMARKS_TABLE             = 'landmarks';
  const LANDMARK_TYPE_TABLE         = 'landmark_type';
  const LANDMARK_OF_TYPE_TABLE      = 'landmark_of_type';
  const LANDMARK_ATTRIBUTION_TABLE  = 'landmark_attribution';

  const PROPERTY_VALID   = 1;
  const PROPERTY_INVALID = 0;

  private $CI;

  function __construct()
  {
      parent::__construct();
      $this->CI =& get_instance();
      $this->CI->load->library('code_tracker');
      $this->db->simple_query("SET NAMES 'utf8'");
  }

  function hw_update_city_hostels($city_id, $api_country, $api_city, $date_start, $num_nights, $api_language, $xml_data_object)
  {
//    print_r($xml_data_object);
    foreach($xml_data_object as $hostel)
    {
      //If hostel is not in DB
      if(is_null($hostel_id = $this->get_hostel_id($hostel->propertyNumber)))
      {
        //Add hostel to DB
        if($this->insert_hw_hostel($hostel,$city_id))
        {
          $this->CI->code_tracker->add_trace("Added hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");

          $hostel_id = $this->db->insert_id();

          //Add short description to DB
          if($this->insert_hw_short_desc($hostel_id, $api_language, $hostel->shortDescription))
          {
            $this->CI->code_tracker->add_trace("Added $api_language short description for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
          }
          else
          {
            $this->CI->code_tracker->add_trace("Error inserting $api_language short description for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
          }

          //if is is available add price to DB
          if(!empty($hostel->BedPrices->BedPrice->price))
          {
            if($this->insert_hw_bed_price($hostel_id, $hostel->BedPrices->BedPrice->currency, $hostel->BedPrices->BedPrice->price, $date_start, $num_nights))
            {
              $this->CI->code_tracker->add_trace("Added bed price ".$hostel->BedPrices->BedPrice->price." ".$hostel->BedPrices->BedPrice->currency." for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error inserting bed price ".$hostel->BedPrices->BedPrice->price." ".$hostel->BedPrices->BedPrice->currency." for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
            }
          }
        }
        else
        {
          $this->CI->code_tracker->add_trace("Error inserting hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
        }

      }
      //If hostel is in DB
      else
      {
        //Update hostel data if needed
        if($this->compare_hostel_xml_to_db($hostel, $hostel_id, $city_id))
        {
          if($this->update_hw_hostel($hostel, $hostel_id, $city_id))
          {
            $this->CI->code_tracker->add_trace("Updated hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country");
          }
          else
          {
            $this->CI->code_tracker->add_trace("Error updating hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country");
          }
        }

        $desc = $this->get_short_desc($hostel_id,$api_language);
        $english_desc = $this->get_short_desc($hostel_id,"English");

        //if description is not in DB
        if(is_null($desc))
        {
          //if english description does not exist
          //or If english description exists AND description is not the same then the english one
          if( is_null($english_desc) ||
             (!is_null($english_desc) && (strcmp($hostel->shortDescription,$english_desc->short_description) != 0)))
          {
            //Add description to DB
            if($this->insert_hw_short_desc($hostel_id, $api_language, $hostel->shortDescription))
            {
              $this->CI->code_tracker->add_trace("Added $api_language short description for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error inserting $api_language short description for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
            }
          }
        }
        //If description is in DB AND
        //     if langage is englsih and desc not the same update
        //  or if langage not english and desc not in english and desc is not the same update
        elseif( ((strcasecmp($api_language,"English")==0) && (strcmp($hostel->shortDescription,$desc->short_description) != 0))  ||
                ((strcasecmp($api_language,"English")!=0) && (strcmp($hostel->shortDescription,$desc->short_description) != 0)
                                                          && (strcmp($hostel->shortDescription,$english_desc->short_description) != 0)) )
        {
          //update it
          if($this->update_hw_short_desc($desc->hw_hostel_description_id, $hostel_id, $api_language, $hostel->shortDescription))
          {
            $this->CI->code_tracker->add_trace("Updated $api_language short description for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
          }
          else
          {
            $this->CI->code_tracker->add_trace("Error updating $api_language short description for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
          }
        }

        //if price is available
          if(!empty($hostel->BedPrices->BedPrice->price))
          {
            //if price is not in DB
//             if(is_null($db_price = $this->get_hostel_price($hostel_id,$hostel->BedPrices->BedPrice->currency)))
            if(is_null($db_price = $this->get_hostel_price($hostel_id,(string)$hostel->BedPrices->BedPrice->currency)))
            {
              //add price
              if($this->insert_hw_bed_price($hostel_id, (string)$hostel->BedPrices->BedPrice->currency, $hostel->BedPrices->BedPrice->price, $date_start, $num_nights))
              {
                $this->CI->code_tracker->add_trace("Added bed price ".$hostel->BedPrices->BedPrice->price." ".$hostel->BedPrices->BedPrice->currency." for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
              }
              else
              {
                $this->CI->code_tracker->add_trace("Error inserting bed price ".$hostel->BedPrices->BedPrice->price." ".$hostel->BedPrices->BedPrice->currency." for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
              }
            }
            //if price is in db
            else
            {
              if($this->update_hw_bed_price($db_price->hw_hostel_price_id, $hostel_id, (string)$hostel->BedPrices->BedPrice->currency, $hostel->BedPrices->BedPrice->price, $date_start, $num_nights))
              {
                $this->CI->code_tracker->add_trace("Updated bed price from ".$db_price->bed_price." to ".$hostel->BedPrices->BedPrice->price." ".$hostel->BedPrices->BedPrice->currency." for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
              }
              else
              {
                $this->CI->code_tracker->add_trace("Error updating bed price ".$hostel->BedPrices->BedPrice->price." ".$hostel->BedPrices->BedPrice->currency." for hostel ".$hostel->propertyName." (".$hostel->propertyNumber.") in city $api_city, $api_country to DB");
              }
            }
          }

      }

      //Update avail
//       if(!is_null($dateStart) && !is_null($numNights))
//       {
//         update last avail

//       }
    }

    //If xml data is an HW api return of a property location search of all hostels in city (without availability restrictions)
    if(is_null($date_start) && is_null($num_nights))
    {
      $city_hostels = $this->get_all_hostel_of_city($city_id);
      if(!is_null($city_hostels))
      {
        //For each hostel in db for this city if it is not in the xml list delete it
        foreach($city_hostels as $db_hostel)
        {
          //If hostel is not in xml
          if(!$this->hostel_in_xml($xml_data_object,$db_hostel->property_number))
          {
            //delete hostel from DB and all is descriptions and prices
            $this->db->where('hw_hostel_id', $db_hostel->hw_hostel_id);
            if($this->db->delete(self::HW_HOSTEL_DESC_TABLE))
            {
              $this->CI->code_tracker->add_trace("Deleted all short descriptions of hostel ".$db_hostel->property_name." (".$db_hostel->property_number.") in city $api_city, $api_country from DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error deleting all short descriptions of hostel ".$db_hostel->property_name." (".$db_hostel->property_number.") in city $api_city, $api_country from DB");
            }

            $this->db->where('hw_hostel_id', $db_hostel->hw_hostel_id);
            if($this->db->delete(self::HW_HOSTEL_PRICE_TABLE))
            {
              $this->CI->code_tracker->add_trace("Deleted all prices of hostel ".$db_hostel->property_name." (".$db_hostel->property_number.") in city $api_city, $api_country from DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error deleting all prices of hostel ".$db_hostel->property_name." (".$db_hostel->property_number.") in city $api_city, $api_country from DB");
            }

            $this->db->where('hw_hostel_id', $db_hostel->hw_hostel_id);
            if($this->db->delete(self::HW_HOSTEL_TABLE))
            {
              $this->CI->code_tracker->add_trace("Deleted hostel ".$db_hostel->property_name." (".$db_hostel->property_number.") in city $api_city, $api_country from DB");
            }
            else
            {
              $this->CI->code_tracker->add_trace("Error deleting hostel ".$db_hostel->property_name." (".$db_hostel->property_number.") in city $api_city, $api_country from DB");
            }
          }
        }
      }
    }
  }

  public function validate_property_type($type)
  {
    switch(strtolower($type))
    {
      case 'hotel';
        $type = 'Hotel';
        break;
      case 'hostel';
        $type = 'Hostel';
        break;
      case 'guesthouse';
        $type = 'Guesthouse';
        break;
      case 'apartment';
        $type = 'Apartment';
        break;
      default:
        $type = NULL;
        break;
    }

    return $type;
  }
  function propertyLocationSearch_DB($hw_city, $hw_country, $currency, $langage, $limit = 150, $filters = array())
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

      $district_join = "  LEFT JOIN hw_hostel_district ON hw_hostel.property_number = hw_hostel_district.property_number
												LEFT JOIN districts ON hw_hostel_district.district_id = districts.district_id";
      $district_where = " AND districts.slug LIKE '".$filters["district"]."'";
    }

    $landmark_join = "";
    $landmark_where = "";

    if(!empty($filters["landmark"]))
    {
      $this->CI->load->model('Db_landmarks');
      $filters["landmark"] = $this->db->escape_str($filters["landmark"]);

      $landmark_join = "  LEFT JOIN hw_hostel_landmark ON hw_hostel.property_number = hw_hostel_landmark.property_number
												LEFT JOIN landmarks ON hw_hostel_landmark.landmark_id = landmarks.landmark_id";
      $landmark_where = " AND landmarks.slug LIKE '".$filters["landmark"]."'";
    }

    $langage    = $this->db->escape_str($langage);
    $hw_country = $this->db->escape_str($hw_country);
    $hw_city    = $this->db->escape_str($hw_city);
    $currency   = $this->db->escape_str($currency);
    $limit      = $this->db->escape($limit);

    $sql = "SELECT *, hw_hostel.geo_longitude as hostel_geo_long, hw_hostel.geo_latitude as hostel_geo_lat";
    $sql.= " FROM hw_hostel";
    $sql.= " LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id";
    $sql.= " LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id";
    $sql.= " LEFT JOIN hw_hostel_price ON hw_hostel.hw_hostel_id = hw_hostel_price.hw_hostel_id";
    $sql.= " LEFT JOIN hw_hostel_description ON hw_hostel.hw_hostel_id = hw_hostel_description.hw_hostel_id";
    $sql.= $landmark_join;
    $sql.= $district_join;
    $sql.= " WHERE hw_hostel_price.bed_price > 0";
    $sql.= " AND hw_hostel.minNights <= 2";
    $sql.= " AND hw_hostel_price.date_start > curdate()";
    $sql.= " AND hw_country = '$hw_country'";
    $sql.= " AND hw_city = '$hw_city'";
    $sql.= " AND hw_hostel_description.langage = '$langage'";
    $sql.= " AND hw_hostel_price.currency_price = '$currency'";
    $sql.= $property_type_where;
    $sql.= $landmark_where;
    $sql.= $district_where;
    $sql.= " ORDER BY bed_price ASC";

    if(!empty($limit) && ($limit > 0))
    {
      $sql.= " LIMIT $limit";
    }
    $query = $this->db->query($sql);

    //If no results try without the price date validation constraint
    if ($query->num_rows() == 0)
    {
      $sql = str_replace("AND hw_hostel_price.date_start > curdate()","",$sql);
      $query = $this->db->query($sql);
    }

    $response = Array();

    $this->load->helper('xml');

    foreach($query->result() as $hostel)
    {
      $hostel_object = new SimpleXMLElement("<?xml version='1.0' ?>\n<Property></Property>");
      $hostel_object->addAttribute("size", 11);
      $hostel_object->addChild("propertyNumber", $hostel->property_number);
      $hostel_object->addChild("propertyName", xml_convert($hostel->property_name));
      $hostel_object->addChild("address1", xml_convert($hostel->address1));
      if(!empty($hostel->address2))
      {
        $hostel_object->addChild("address2", xml_convert($hostel->address2));
      }

      $hostel_object->addChild("propertyType", xml_convert($hostel->property_type));

      $bedPrices = new SimpleXMLElement("<BedPrices></BedPrices>");
      $bedPrices = $hostel_object->addChild("BedPrices", $bedPrices);

      $bedPrice = new SimpleXMLElement("<BedPrice></BedPrice>");
      $bedPrice = $bedPrices->addChild("BedPrice", $bedPrices);
      $bedPrice->addChild("price", $hostel->bed_price);
      $bedPrice->addChild("currency", xml_convert($hostel->currency_price));

      if(!empty($hostel->translated_desc))
      {
        $hostel_object->addChild("shortDescription", xml_convert($hostel->translated_desc));
      }
      else
      {
        $hostel_object->addChild("shortDescription", xml_convert($hostel->short_description));
      }

      $PropertyImages = new SimpleXMLElement("<PropertyImages></PropertyImages>");
      $PropertyImages->addAttribute("size", 1);
      $PropertyImages = $hostel_object->addChild("PropertyImages", $PropertyImages);

      $PropertyImage = new SimpleXMLElement("<PropertyImage></PropertyImage>");
      $PropertyImage = $PropertyImages->addChild("PropertyImage", $bedPrices);
      $PropertyImage->addChild("imageFormat", $hostel->imageFormat);
      $PropertyImage->addChild("imageType", xml_convert($hostel->imageType));
      $PropertyImage->addChild("imageWidth", $hostel->imageWidth);
      $PropertyImage->addChild("imageHeight", $hostel->imageHeight);

//      $imageURL = str_replace("images.webresint.com", "193.95.151.171", $hostel->imageURL);
      $PropertyImage->addChild("imageURL", xml_convert($hostel->imageURL));

      $Geo = new SimpleXMLElement("<Geo></Geo>");
      $Geo->addAttribute("size", 2);
      $Geo = $hostel_object->addChild("Geo", $Geo);
      $Geo->addChild("Longitude", $hostel->hostel_geo_long);
      $Geo->addChild("Latitude", $hostel->hostel_geo_lat);

      array_push($response,$hostel_object);
    }

    return $response;

  }

  function delete_hw_hostels_of_city($city_id,$cityname,$country)
  {
    $this->db->where('hw_city_id', $city_id);
    if($this->db->delete(self::HW_HOSTEL_TABLE))
    {
      $this->CI->code_tracker->add_trace("Deleted all hostels of $cityname, $country ($city_id) from DB");
    }
    else
    {
      $this->CI->code_tracker->add_trace("Error deleting all hostels of $cityname, $country ($city_id) from DB");
    }
    //hostels description, hostel prices are delete via ON DELETE CASCADE DB command
  }

  function insert_hw_hostel($xml_hostel, $city_id)
  {
  	$this->db->set('hw_city_id', $city_id);
    $this->db->set('property_number', $xml_hostel->propertyNumber);
    $this->db->set('property_name', (string)$xml_hostel->propertyName);
    $this->db->set('property_type', (string)$xml_hostel->propertyType);
    $this->db->set('geo_longitude', $xml_hostel->Geo->Longitude);
    $this->db->set('geo_latitude', $xml_hostel->Geo->Latitude);
    $this->db->set('address1', (string)$xml_hostel->address1);
    if(!empty($xml_hostel->address2))
    {
      $this->db->set('address2', (string)$xml_hostel->address2);
    }
    if(!empty($xml_hostel->minNights))
    {
      $this->db->set('minNights', (int)$xml_hostel->minNights);
    }
    //TODO add empty check for imagee
    $this->db->set('imageFormat', (string)$xml_hostel->PropertyImages->PropertyImage->imageFormat);
    $this->db->set('imageType', (string)$xml_hostel->PropertyImages->PropertyImage->imageType);
    $this->db->set('imageWidth', (string)$xml_hostel->PropertyImages->PropertyImage->imageWidth);
    $this->db->set('imageHeight', (string)$xml_hostel->PropertyImages->PropertyImage->imageHeight);
    $this->db->set('imageURL', (string)$xml_hostel->PropertyImages->PropertyImage->imageURL);

    return $this->db->insert(self::HW_HOSTEL_TABLE);
  }

  function insert_hw_short_desc($hostel_id, $langage, $short_description)
  {
    $this->db->set('hw_hostel_id', $hostel_id);
    $this->db->set('langage', (string)$langage);
    $this->db->set('short_description', (string)$short_description);

    return $this->db->insert(self::HW_HOSTEL_DESC_TABLE);

  }

  function insert_hw_bed_price($hostel_id, $currency, $price, $date_start, $num_nights)
  {
    $this->db->set('hw_hostel_id', $hostel_id);
    $this->db->set('currency_price', (string)$currency);
    $this->db->set('bed_price', $price);
    $this->db->set('date_start', $date_start);
    $this->db->set('num_nights', $num_nights);

    return $this->db->insert(self::HW_HOSTEL_PRICE_TABLE);
  }

  function insert_hw_facilities($facility)
  {
    $this->CI->db->set('description', (string)$facility);
    $this->CI->db->set('api_sync_status', 1);

    return $this->CI->db->insert(self::HW_FACILITY_TABLE);
  }

  public function set_attribution_of_prop_number($property_number,$attribution_id)
  {
    $prop_attribution_link_id = NULL;
    if(is_null($prop_attribution_link_id = $this->get_property_attribution_id($property_number,$attribution_id)))
    {
      $this->CI->db->set('property_number', (int)$property_number);
      $this->CI->db->set('landmark_attribution_id', (int)$attribution_id);
      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
      if($this->CI->db->insert(self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE) === false)
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
      $this->CI->db->where('hw_hostel_landmark_attribution_id', $prop_attribution_link_id);
      if($this->CI->db->update(self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking landmark attribution $prop_attribution_link_id to property $property_number -> : ".$this->CI->db->last_query());
      }
    }

    return $prop_attribution_link_id;
  }

  function update_hostel_facilities_sync_status($property_number, $sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("property_number",$property_number );
    $this->CI->db->update(self::HW_HOSTEL_FACILITY_TABLE);
  }

  function delete_hostel_facilities_sync_status($property_number, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("property_number",$property_number );
    $this->CI->db->delete(self::HW_HOSTEL_FACILITY_TABLE);
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

  function update_hw_hostel($xml_hostel, $db_hostel_id, $city_id)
  {
    $this->db->set('hw_city_id', $city_id);
    $this->db->set('property_number', $xml_hostel->propertyNumber);
    $this->db->set('property_name', (string)$xml_hostel->propertyName);
    $this->db->set('property_type', (string)$xml_hostel->propertyType);
    $this->db->set('geo_longitude', $xml_hostel->Geo->Longitude);
    $this->db->set('geo_latitude', $xml_hostel->Geo->Latitude);
    $this->db->set('address1', (string)$xml_hostel->address1);
    $address2 = NULL;
    if(!empty($xml_hostel->address2))
    {
      $address2 = (string)$xml_hostel->address2;
    }
    if(!empty($xml_hostel->minNights))
    {
      $this->db->set('minNights', (int)$xml_hostel->minNights);
    }
    $this->db->set('address2', $address2);
    //TODO add empty check for imagee
    $this->db->set('imageFormat', (string)$xml_hostel->PropertyImages->PropertyImage->imageFormat);
    $this->db->set('imageType', (string)$xml_hostel->PropertyImages->PropertyImage->imageType);
    $this->db->set('imageWidth', (string)$xml_hostel->PropertyImages->PropertyImage->imageWidth);
    $this->db->set('imageHeight', (string)$xml_hostel->PropertyImages->PropertyImage->imageHeight);
    $this->db->set('imageURL', (string)$xml_hostel->PropertyImages->PropertyImage->imageURL);

    $this->db->where('hw_hostel_id', $db_hostel_id);
    return $this->db->update(self::HW_HOSTEL_TABLE);
  }

  function update_hw_short_desc($short_desc_id, $hostel_id, $langage, $short_description)
  {
    $this->db->set('hw_hostel_id', $hostel_id);
    $this->db->set('langage', (string)$langage);
    $this->db->set('short_description', (string)$short_description);

    $this->db->where('hw_hostel_description_id', $short_desc_id);
    return $this->db->update(self::HW_HOSTEL_DESC_TABLE);

  }

  function update_hw_bed_price($hostel_price_id, $hostel_id, $currency, $price, $date_start, $num_nights)
  {
    $this->db->set('hw_hostel_id', $hostel_id);
    $this->db->set('currency_price', (string)$currency);
    $this->db->set('bed_price', $price);
    $this->db->set('date_start', $date_start);
    $this->db->set('num_nights', $num_nights);

    $this->db->where('hw_hostel_price_id', $hostel_price_id);
    return $this->db->update(self::HW_HOSTEL_PRICE_TABLE);
  }

  public function update_hw_hostel_facilities($property_number, $facilities)
  {
    $return = true;

    //Invalidate all current facilities of property
    $this->update_hostel_facilities_sync_status($property_number,self::PROPERTY_INVALID);

    if((!empty($facilities) && (is_array($facilities))))
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating facilities of property $property_number to ". print_r($facilities,true));

      foreach($facilities as $facility)
      {
        //prevent empty string to become a facility in DB
        if(empty($facility)) continue;

        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating facility $facility of property $property_number ");

        //IF facility does not exist
        //  add it in DB and then get created ID
        //else
        //  get facility id and validate with sync field
        // Create feed trace on error
        $facility_id = NULL;
        if(is_null($facility_id = $this->get_facility_id($facility)))
        {
          if($this->insert_hw_facilities($facility) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error inserting facility -> $facility: ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added facility $facility in DB");
            $facility_id = $this->CI->db->insert_id();
          }
        }

        //IF facility ID is valid
        //  IF link with this facility ID is NOT already in DB with this property
        //    insert link to this facility for this property
        //  ELSE
        //     Validate it using sync status field
        if(!empty($facility_id) && is_null($hostel_facility_id = $this->get_hostel_facility_id($property_number, $facility_id)))
        {
          $this->CI->db->set('property_number', $property_number);
          $this->CI->db->set('hw_facility_id', $facility_id);
          $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

          if($this->CI->db->insert(self::HW_HOSTEL_FACILITY_TABLE) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error linking facility $facility_id with hostel $property_number -> : ".$this->CI->db->last_query());
            $return = false;
          }
          else
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added facility $facility ($facility_id) to in property $property_number");
          }
        }
        elseif(!empty($facility_id))
        {
          $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
          $this->CI->db->where('hw_hostel_facility_id', $hostel_facility_id);
          if($this->CI->db->update(self::HW_HOSTEL_FACILITY_TABLE) === false)
          {
            $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking facility $hostel_facility_id -> : ".$this->CI->db->last_query());
            $return = false;
          }
        }
        else
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error with facility ".$facility. " for hostel number $property_number");
        }

      }
    }

    $this->delete_hostel_facilities_sync_status($property_number);

    //Update timestamp
    if($this->update_hostel_facilities_timestamp($property_number) === FALSE)
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating last updated facility timestamp for property number $property_number");
    }

    return $return;
  }

  public function update_hostel_facilities_timestamp($property_number)
  {
    $this->CI->db->set('last_facility_update', 'NOW()', FALSE);

    $this->CI->db->where('property_number', $property_number);
    return $this->CI->db->update(self::HW_HOSTEL_TABLE);
  }

  public function update_hostel_x_timestamp($x, $property_number)
  {
    $this->CI->db->set($x, 'NOW()', FALSE);

    $this->CI->db->where('property_number', $property_number);
    return $this->CI->db->update(self::HW_HOSTEL_TABLE);
  }


  public function update_landmark_attribution_of_prop_sync_status($property_number, $sync_status = 0)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("property_number", $property_number);
    return $this->CI->db->update(self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE);
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
    $this->CI->db->delete(self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE);
  }

  /**
   * @return true if xml hostel is different then db hostel
   */
  function compare_hostel_xml_to_db($xml_hostel, $db_hostel_id, $city_id)
  {
    $db_hostel = $this->get_hostel_data($db_hostel_id);

    if(empty($xml_hostel->address2))
    {
      $address2 = NULL;
    }
    else
    {
      $address2 = $xml_hostel->address2;
    }
    //TODO add empty check for imagee
    if(($db_hostel->hw_city_id != $city_id) ||
       ($db_hostel->property_number != $xml_hostel->propertyNumber) ||
       ($db_hostel->minNights != $xml_hostel->minNights) ||
       ($db_hostel->geo_longitude != $xml_hostel->Geo->Longitude) ||
       ($db_hostel->geo_latitude  != $xml_hostel->Geo->Latitude) ||
       (strcmp($db_hostel->property_name, $xml_hostel->propertyName) != 0) ||
       (strcmp($db_hostel->property_type, $xml_hostel->propertyType) != 0) ||
       (strcmp($db_hostel->address1, $xml_hostel->address1) != 0) ||
       (strcmp($db_hostel->address2, $address2) != 0) ||
       (strcmp($db_hostel->imageFormat, $xml_hostel->PropertyImages->PropertyImage->imageFormat) != 0) ||
       (strcmp($db_hostel->imageType, $xml_hostel->PropertyImages->PropertyImage->imageType) != 0) ||
       (strcmp($db_hostel->imageWidth, $xml_hostel->PropertyImages->PropertyImage->imageWidth) != 0) ||
       (strcmp($db_hostel->imageHeight, $xml_hostel->PropertyImages->PropertyImage->imageHeight) != 0) ||
       (strcmp($db_hostel->imageURL, $xml_hostel->PropertyImages->PropertyImage->imageURL) != 0) )
    {
      return TRUE;
    }

    return FALSE;
  }

  function hostel_in_xml($xml_hostels, $property_number)
  {
    foreach($xml_hostels as $xml_hostel)
    {
      if($xml_hostel->propertyNumber == $property_number)
      {
        return TRUE;
      }
    }

    return FALSE;
  }

  function get_all_properties()
  {
//     $this->db->select('property_number');
//     $this->db->select('geo_longitude');
//     $this->db->select('geo_latitude');

    $query = $this->db->get(self::HW_HOSTEL_TABLE);

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

    $query = $this->db->get(self::HW_HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return array();
  }

  function get_all_hostel_of_city($city_id)
  {
    $this->db->where("hw_city_id",$city_id);
    $query = $this->db->get(self::HW_HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function get_hostel_id($property_number)
  {
  	$this->db->where("property_number",$property_number);
    $query = $this->db->get(self::HW_HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_hostel_id;
    }
    return NULL;
  }

  function get_hostel_data($hostel_id)
  {
    $this->db->where("hw_hostel_id",$hostel_id);
    $query = $this->db->get(self::HW_HOSTEL_TABLE);

    if($query->num_rows() == 1)
    {
      return $query->row();
    }
    return NULL;
  }

  function get_hostel_main_services($property_number)
  {
    $property_number = $this->db->escape($property_number);
    $query = "(
                SELECT hw_hostel_facility.hw_facility_id AS service_id,
                       'facility' AS service_type,
                       description
                FROM hw_hostel_facility
                LEFT JOIN hw_facility ON hw_hostel_facility.hw_facility_id = hw_facility.hw_facility_id
                WHERE hw_hostel_facility.property_number = $property_number
                  AND hw_facility.desc_order IS NOT NULL
                ORDER BY -(desc_order) DESC
              )UNION(
                SELECT hw_hostel_landmark.landmark_id AS service_id,
                       'landmark' AS service_type,
                       landmark_name AS description
                FROM hw_hostel_landmark
                LEFT JOIN landmarks ON hw_hostel_landmark.landmark_id = landmarks.landmark_id
                WHERE hw_hostel_landmark.property_number = $property_number
                  AND landmark_name LIKE 'City Center'
              )";

    $query = $this->db->query($query);

    $services = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $i => $row)
      {
        $services[$i]=new stdClass();
        $services[$i]->service_id    = (int)$row->service_id;
        $services[$i]->description   = (string)$row->description;
        $services[$i]->service_type  = (string)$row->service_type;
      }
    }
    return $services;
  }

  function get_hostel_facilities($property_number)
  {
    $this->CI->db->select(self::HW_FACILITY_TABLE.'.hw_facility_id as facility_id');
    $this->CI->db->select(self::HW_FACILITY_TABLE.'.description');
    $this->CI->db->select("IF(desc_order IS NULL,0,1) AS to_display",false);
    $this->CI->db->join(self::HW_FACILITY_TABLE, self::HW_HOSTEL_FACILITY_TABLE.'.hw_facility_id = '.self::HW_FACILITY_TABLE.'.hw_facility_id', "left");
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->order_by("-(desc_order) DESC");

    $query = $this->CI->db->get(self::HW_HOSTEL_FACILITY_TABLE);

    $facilities = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $i => $row)
      {
      	$facilities[$i] = new stdClass();
        $facilities[$i]->facility_id = (int)$row->facility_id;
        $facilities[$i]->description = $row->description;
        $facilities[$i]->to_display  = (int)$row->to_display;
      }
    }
    return $facilities;
  }
  function get_hostel_facilities_for_filter($property_number)
  {
    $this->CI->db->select(self::HW_FACILITY_TABLE.'.hw_facility_id');
    $this->CI->db->join(self::HW_FACILITY_TABLE, self::HW_HOSTEL_FACILITY_TABLE.'.hw_facility_id = '.self::HW_FACILITY_TABLE.'.hw_facility_id', "left");
    $this->CI->db->where("property_number", $property_number);

    $query = $this->CI->db->get(self::HW_HOSTEL_FACILITY_TABLE);

    $facilities = array();
    if($query->num_rows() > 0)
    {
      foreach($query->result() as $row)
      {
        $facilities[] = $row->hw_facility_id;
      }
    }
    return $facilities;
  }

  function get_property_type($property_number)
  {
    $this->db->select("property_type");
    $this->db->where("property_number",$property_number);
    $query = $this->db->get(self::HW_HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->property_type;
    }
    //TONOTICE if hostel not in DB default type is property
    return "property";
  }
  function get_short_desc($hostel_id, $langage)
  {
    $langage = $this->db->escape_str($langage);

    $this->db->where("hw_hostel_id",$hostel_id);
    $this->db->where("LOWER(langage) LIKE LOWER('$langage')");
    $query = $this->db->get(self::HW_HOSTEL_DESC_TABLE);

    if($query->num_rows() == 1)
    {
      return $query->row();
    }
    return NULL;
  }

  function get_hostel_price($hostel_id, $currency)
  {
    $currency = $this->db->escape_str($currency);

    $this->db->where("hw_hostel_id",$hostel_id);
    $this->db->where("LOWER(currency_price) LIKE LOWER('$currency')");
    $query = $this->db->get(self::HW_HOSTEL_PRICE_TABLE);

    if($query->num_rows() == 1)
    {
      return $query->row();
    }
    return NULL;
  }

  function get_hostel_facility_id($property_number, $facility_id)
  {
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("hw_facility_id", $facility_id);

    $query = $this->CI->db->get(self::HW_HOSTEL_FACILITY_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_hostel_facility_id;
    }
    return NULL;
  }

  function get_hostel_district_id($property_number, $district_id)
  {
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("district_id", $district_id);

    $query = $this->CI->db->get(self::HW_HOSTEL_DISTRICT_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_hostel_district_id;
    }
    return NULL;
  }

  public function get_hostel_landmark_id($property_number, $landmark_id)
  {
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("landmark_id", $landmark_id);

    $query = $this->CI->db->get(self::HW_HOSTEL_LANDMARK_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_hostel_landmark_id;
    }
    return NULL;
  }

  public function get_property_attribution_id($property_number, $attribution_id)
  {
    $this->CI->db->select("hw_hostel_landmark_attribution_id");
    $this->CI->db->where("property_number", $property_number);
    $this->CI->db->where("landmark_attribution_id", $attribution_id);

    $query = $this->CI->db->get(self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE);
    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_hostel_landmark_attribution_id;
    }
    return NULL;
  }

  function get_hostel_count()
  {
    return $this->db->count_all(self::HW_HOSTEL_TABLE);
  }

  function get_hostel_data_from_number($property_number, $currency_code = "EUR")
  {
    $currency_code = $this->validate_price_currency($currency_code);

    $query = "SELECT *";
    $query.= " FROM ".self::HW_HOSTEL_TABLE;
    $query.= " LEFT JOIN ".self::HW_HOSTEL_PRICE_TABLE." ON ".self::HW_HOSTEL_TABLE.".hw_hostel_id = ".self::HW_HOSTEL_PRICE_TABLE.".hw_hostel_id";
    $query.= " WHERE (currency_price LIKE'$currency_code' OR currency_price IS NULL)";
    $query.= " AND property_number = $property_number";

    $query = $this->db->query($query);

    if($query->num_rows() == 1)
    {
      return $query->row();
    }
    return NULL;
  }

  function get_last_facilites_updated_properties($limit = 100)
  {
    $this->db->select('property_number');
    $this->db->order_by('last_facility_update',"asc");
    $this->db->limit($limit);

    $query = $this->db->get(self::HW_HOSTEL_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return array();
  }

  function get_hostel_for_cache_data($limit = NULL)
  {
    $sql = "SELECT * FROM ".self::HW_HOSTEL_TABLE;
    $sql.= " ORDER BY last_fake_booking ASC";

    if(!is_null($limit))
    {
      $sql.= " LIMIT ".$limit;
    }

    $query = $this->db->query($sql);

    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function update_hostel_booking_info($property_number,
                                      $email = NULL,
                                      $post_code = NULL,
                                      $state = NULL,
                                      $phone = NULL,
                                      $fax = NULL,
                                      $currency_code = NULL,
                                      $deposit_percent = NULL)
  {
    $this->db->set('post_code', $post_code);
    $this->db->set('state', $state);
    $this->db->set('phone', $phone);
    $this->db->set('fax', $fax);
    $this->db->set('email', $email);
    $this->db->set('currency_code', $currency_code);
    $this->db->set('deposit_percent', $deposit_percent);
    $this->db->set('last_fake_booking', date(DATE_ATOM,time()));

    $this->db->where('property_number', $property_number);
    return $this->db->update(self::HW_HOSTEL_TABLE);
  }

  function get_facility_id($facility)
  {
    $facility = $this->CI->db->escape_str($facility);
    $this->CI->db->where("LOWER(`description`) LIKE LOWER('$facility')");

    $query = $this->CI->db->get(self::HW_FACILITY_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->hw_facility_id;
    }
    return NULL;
  }

  //TODO MOVE in facility model!
  public function get_amenities_city_for_filter()
  {
    $this->db->select('hw_facility_id as facility_id');
    $this->db->select('description as facility_name');
    $this->db->where('filter_order IS NOT NULL');
    $this->db->order_by('filter_order ASC');

    $query = $this->db->get(self::HW_FACILITY_TABLE);

    $popularFacilitiesById = $this->config->item("hwMostPopularFacilitiesById");
    $mostPopularAmenities = array();
    $amenities = array();

    if($query->num_rows() > 0)
    {
      foreach($query->result() as $row)
      {
      	$amenity = new stdClass();
        $amenity->facility_id = $row->facility_id;
        $amenity->amenity_id = $amenity->facility_id;
        $amenity->facility_name = (string)$row->facility_name;

        $amenity->id_to_display = $this->getFacilityIdToDisplay($amenity);

        $popularAmenityKey = array_search($amenity->facility_id, $popularFacilitiesById);
        if ($popularAmenityKey !== FALSE) {
            $mostPopularAmenities[$popularAmenityKey] = $amenity;
        } else {
            $amenities[] = $amenity;
        }
      }
    }

    $amenityGroups = array(
        "mostPopularAmenities" => $mostPopularAmenities,
        "amenities" => $amenities
    );

    return $amenityGroups;
  }

  private function getFacilityIdToDisplay($amenity) {
      if ($amenity->facility_name == 'Breakfast Included' || $amenity->facility_name == 'Breakfast') {
        $idToDisplay = 'free-breakfast';
    } else {
        $idToDisplay = $amenity->facility_id;
    }

    return $idToDisplay;
  }

  public function get_amenities_by_city_id($city_id)
  {
    $city_id = $this->db->escape_str($city_id);
    $sql = "SELECT `".self::HW_FACILITY_TABLE."`.`hw_facility_id` as facility_id, `".self::HW_FACILITY_TABLE."`.`description` as facility_name, COUNT(`".self::HW_HOSTEL_TABLE."`.`property_number`) as `amenities_count`
            FROM (`".self::HW_HOSTEL_TABLE."`)
            RIGHT JOIN `".self::HW_HOSTEL_FACILITY_TABLE."` ON `".self::HW_HOSTEL_TABLE."`.`property_number` = `".self::HW_HOSTEL_FACILITY_TABLE."`.`property_number`
            RIGHT JOIN `".self::HW_FACILITY_TABLE."` ON `".self::HW_FACILITY_TABLE."`.`hw_facility_id` = `".self::HW_HOSTEL_FACILITY_TABLE."`.`hw_facility_id`
            WHERE `".self::HW_HOSTEL_TABLE."`.`hw_city_id` = '".$city_id."'
            GROUP BY `".self::HW_FACILITY_TABLE."`.`hw_facility_id`
            ;";
    $query = $this->db->query($sql);

    $return = array();
    if($query->num_rows() > 0)
    {
      return $query->result();
    }
    return $return;
  }

  public function get_districts_by_city_id($city_id)
  {
    $city_id = $this->db->escape_str($city_id);
    $sql = "SELECT `".self::DISTRICTS_TABLE."`.`district_id`, `".self::DISTRICTS_TABLE."`.`um_id`, `".self::DISTRICTS_TABLE."`.`district_name`, COUNT(`".self::HW_HOSTEL_TABLE."`.`property_number`) as `district_count`
            FROM (`".self::HW_HOSTEL_TABLE."`)
            RIGHT JOIN `".self::HW_HOSTEL_DISTRICT_TABLE."` ON `".self::HW_HOSTEL_TABLE."`.`property_number` = `".self::HW_HOSTEL_DISTRICT_TABLE."`.`property_number`
            RIGHT JOIN `".self::DISTRICTS_TABLE."` ON `".self::DISTRICTS_TABLE."`.`district_id` = `".self::HW_HOSTEL_DISTRICT_TABLE."`.`district_id`
            WHERE `".self::HW_HOSTEL_TABLE."`.`hw_city_id` = '".$city_id."'
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
            self::HW_HOSTEL_DISTRICT_TABLE.".district_id ,"
            .self::DISTRICTS_TABLE.".district_name ,"
            .self::DISTRICTS_TABLE.".um_id "
            );
    $this->db->join(self::DISTRICTS_TABLE, self::DISTRICTS_TABLE.'.district_id = '.self::HW_HOSTEL_DISTRICT_TABLE.'.district_id', "left");
    $this->db->where(self::HW_HOSTEL_DISTRICT_TABLE.".property_number",$property_number);

    $query = $this->db->get(self::HW_HOSTEL_DISTRICT_TABLE);

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
    $this->db->join(self::DISTRICTS_TABLE, self::DISTRICTS_TABLE.'.district_id = '.self::HW_HOSTEL_DISTRICT_TABLE.'.district_id', "left");
    $this->db->where(self::HW_HOSTEL_DISTRICT_TABLE.".property_number",$property_number);
    $query = $this->db->get(self::HW_HOSTEL_DISTRICT_TABLE);

    $return = array();
    if($query->num_rows() > 0)
    {
      $return = $query->result();
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
            FROM ".self::HW_HOSTEL_TABLE."
            RIGHT JOIN `".self::HW_HOSTEL_LANDMARK_TABLE."` ON `".self::HW_HOSTEL_LANDMARK_TABLE."`.`property_number` = `".self::HW_HOSTEL_TABLE."`.`property_number`
            LEFT JOIN `".self::LANDMARKS_TABLE."` ON `".self::LANDMARKS_TABLE."`.`landmark_id` = `".self::HW_HOSTEL_LANDMARK_TABLE."`.`landmark_id`
            WHERE `".self::HW_HOSTEL_TABLE."`.`hw_city_id` = $city_id
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

  public function get_property_landmarks_for_filter($property_number, $range_km = 5, $landmark_source_id = 2)
  {
    $range_km = $this->db->escape_str($range_km);

    $this->db->select(self::HW_HOSTEL_LANDMARK_TABLE.".landmark_id");
    $this->db->select(self::LANDMARKS_TABLE.".slug");
    $this->db->select(self::LANDMARKS_TABLE.".landmark_name");
    $this->db->select(self::LANDMARKS_TABLE.".geo_latitude");
    $this->db->select(self::LANDMARKS_TABLE.".geo_longitude");
    $this->db->join(self::LANDMARKS_TABLE, self::HW_HOSTEL_LANDMARK_TABLE.'.landmark_id = '.self::LANDMARKS_TABLE.'.landmark_id');
    $this->db->where("property_number",$property_number);
    $this->db->where("source",$landmark_source_id);
    $this->db->where("distance <= $range_km");

    $query = $this->db->get(self::HW_HOSTEL_LANDMARK_TABLE);

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
                      RIGHT JOIN `".self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE."` ON `".self::LANDMARK_ATTRIBUTION_TABLE."`.`landmark_attribution_id` = `".self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE."`.`landmark_attribution_id`
                      WHERE `".self::HW_HOSTEL_LANDMARK_ATTRIBUTION_TABLE."`.`property_number` = `".self::HW_HOSTEL_LANDMARK_TABLE."`.`property_number`
                    )
                    as attributions
            FROM (
              SELECT `property_number`, `landmark_id`
              FROM (`".self::HW_HOSTEL_LANDMARK_TABLE."`)
              WHERE `property_number` = '".$property_number."' AND `distance` <= '".$range_km."'
            ) AS hw_hostel_landmark
            LEFT JOIN `".self::LANDMARKS_TABLE."` ON `".self::LANDMARKS_TABLE."`.`landmark_id` = `".self::HW_HOSTEL_LANDMARK_TABLE."`.`landmark_id`;";
    $query = $this->db->query($sql);

    $return = array();
    if($query->num_rows() > 0)
    {
      $return = $query->result();
    }
    return $return;
  }

  function validate_price_currency($cur)
  {
    switch(strtolower($cur))
    {
      case strtolower("eur"):
        return "EUR";
      case strtolower("usd"):
        return "USD";
      case strtolower("gbp"):
        return "GBP";
      default:
        return "EUR";
    }

    return "EUR";
  }

  function set_logfile($filename)
  {
    $this->CI->code_tracker->set_logfile($filename);
  }

  function add_trace_to_report($text)
  {
    $this->CI->code_tracker->add_trace($text);
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
    $this->update_hostel_sync_status($property_number,self::PROPERTY_INVALID,self::HW_HOSTEL_DISTRICT_TABLE);

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

          if($this->CI->db->insert(self::HW_HOSTEL_DISTRICT_TABLE) === false)
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
          $this->CI->db->where('hw_hostel_district_id', $hostel_district_id);
          if($this->CI->db->update(self::HW_HOSTEL_DISTRICT_TABLE) === false)
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

    $this->delete_hostel_sync_status($property_number, self::PROPERTY_INVALID, self::HW_HOSTEL_DISTRICT_TABLE);

    //Update timestamp
    if($this->update_hostel_x_timestamp('last_district_update', $property_number) === FALSE)
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating last updated district timestamp for property number $property_number");
    }

    return $return;
  }


  //THIS NEEDS TO BE TESTED AGAIN, untested changes has been insert
  public function update_property_landmark($property_number, $prop_geo_lat, $prop_geo_lng ,$landmarks)
  {
    $this->CI->load->model('Db_landmarks');

    $return = true;

    //Invalidate all current landmarks of property
    $this->update_hostel_sync_status($property_number,self::PROPERTY_INVALID,self::HW_HOSTEL_LANDMARK_TABLE);

    if(!empty($landmarks))
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating landmark of property $property_number");

      foreach($landmarks as $landmark)
      {
        //prevent empty string to become a landmark in DB
        if(empty($landmark)) continue;

        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updating landmark ".$landmark->name." of property $property_number ");

        //insert or update landmark
        $landmark_id = $this->CI->Db_landmarks->update_landmark($landmark);

        if(isset($landmark->geometry->location->lat)||isset($landmark->geometry->location->lng))
        {
          $this->CI->load->library("Geos");
          $landmark->distance_between_property = $this->geos->get_distance($prop_geo_lat, $prop_geo_lng, $landmark->geometry->location->lat, $landmark->geometry->location->lng);
        }

        //link property and landmark
        $this->CI->Db_landmarks->link_hw_property_landmark($landmark_id,$property_number, $landmark->distance_between_property);

      }
    }

    $this->delete_hostel_sync_status($property_number, self::PROPERTY_INVALID, self::HW_HOSTEL_LANDMARK_TABLE);

    //Update timestamp
    if($this->update_hostel_x_timestamp('last_landmark_update', $property_number) === FALSE)
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating last updated landmark timestamp for property number $property_number");
    }

    return $return;
  }

  function get_properties_with_last_x_update($x, $days_too_old = NULL, $limit = 1000)
  {
    $query = "SELECT property_number,geo_longitude,geo_latitude";
    $query.= " FROM ".self::HW_HOSTEL_TABLE;
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
  public function compare_cookie_property_hw($property_number)
  {
    $this->CI->db->select("hw_hostel.property_name,hw_hostel.property_type,hw_hostel.hw_hostel_id,hw_hostel_price.currency_price,hw_hostel_price.bed_price,hw_rating.rating");
	$this->CI->db->from("hw_hostel");
	$this->db->join('hw_hostel_price','hw_hostel_price.hw_hostel_id=hw_hostel.hw_hostel_id','left');
	$this->db->join('hw_rating','hw_rating.property_number=hw_hostel.property_number','left');
  	$this->CI->db->where("hw_hostel.property_number",$property_number);
	$this->CI->db->limit(1);
	$query=$this->CI->db->get();
	return $query->row_array();
  }
  public function compare_property($property_number)
  {
    $this->CI->db->select("hw_hostel.*,hw_hostel_price.currency_price,hw_hostel_price.bed_price,hw_rating.rating,currencies.symbol");
	$this->CI->db->from("hw_hostel");
	$this->db->join('hw_hostel_price','hw_hostel_price.hw_hostel_id=hw_hostel.hw_hostel_id','left');
	$this->db->join('hw_rating','hw_rating.property_number=hw_hostel.property_number','left');
	$this->db->join('currencies','currencies.currency_code=hw_hostel.currency_code','left');
  	$this->CI->db->where("hw_hostel.property_number",$property_number);
	$this->CI->db->limit(1);
	$query=$this->CI->db->get();
	return $query->row_array();
  }
  public function compare_property_facelity($property_number)
  {
    $this->CI->db->select("hw_fact.hw_facility_id,hw_facility.description");
	$this->CI->db->from("hw_hostel_facility as hw_fact");
	$this->db->join('hw_facility','hw_facility.hw_facility_id=hw_fact.hw_facility_id','left');
  	$this->CI->db->where("hw_fact.property_number",$property_number);
	$query=$this->CI->db->get();
	return $query->result();
  }
   public function property_facelity()
  {
    $this->CI->db->select("hw_facility_id,description");
	$this->CI->db->from("hw_facility");
	$query=$this->CI->db->get();
	return $query->result();
  }
  public function compare_property_info($property_number)
  {
    $this->CI->db->select("property_name,property_type,geo_longitude,geo_latitude");
	$this->CI->db->from("hw_hostel");
  	$this->CI->db->where("property_number",$property_number);
	$query=$this->CI->db->get();
	return $query->row_array();
  }
  public function property_detail_review($property_number)
  {
  	$this->wpblog_hw = $this->load->database('wpblog_hw', TRUE);
	$this->wpblog_hw->from("wp_ext_hw_reviews");
  	$this->wpblog_hw->where("property_number",$property_number);
	$query=$this->wpblog_hw->get();
	echo $this->wpblog_hw->last_query();
	return $query->result();
  }
  //compare property function end
    
    /**
     * get property geo by property number
     * @param int $hostel_number
     * @return stdClass if exists or false if not exists
     */
    function get_hostel_geos($hostel_number) {
        $this->db->select('geo_longitude');
        $this->db->select('geo_latitude');
        $this->db->where("property_number", $hostel_number);
        $query = $this->db->get(self::HW_HOSTEL_TABLE);

        $result = $query->row();

        if (empty($result)) {
            $result = false;
        }
        return $result;
    }
    
}
