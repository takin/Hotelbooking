<?php

class Db_landmarks extends CI_Model
{
  const PROPERTY_VALID   = 1;
  const PROPERTY_INVALID = 0;

  const LANDMARKS_TABLE               = 'landmarks';
  const LANDMARK_TYPE_TABLE           = 'landmark_type';
  const LANDMARK_OF_TYPE_TABLE        = 'landmark_of_type';
  const LANDMARK_ATTRIBUTION_TABLE    = 'landmark_attribution';
  const LANDMARK_SOURCES              = 'landmark_sources';

  private $CI;

  private $hash_base = 'd;!n3';

  function __construct()
  {
      parent::__construct();
      $this->CI =& get_instance();
  }

  public function create_landmark_hash($landmark, $source_id)
  {
    $hash = NULL;
    $key = $this->hash_base.$source_id.$landmark->geometry->location->lat.$landmark->geometry->location->lng.$landmark->name;

    return sha1($key);
  }

  public function create_landmark_slug($landmark_name)
  {
    return mb_cleanURL(mb_stripAccent($landmark_name));
  }

  public function insert_landmark($landmark)
  {
    $this->CI->db->set('geo_latitude',  (float)$landmark->geometry->location->lat);
    $this->CI->db->set('geo_longitude', (float)$landmark->geometry->location->lng);
    $this->CI->db->set('hash_id',     (string)$landmark->id);
    $this->CI->db->set('landmark_name', (string)$landmark->name);

    if(!empty($landmark->slug))
    {
      $this->CI->db->set('slug',    (string)$landmark->slug);
    }
    if(!empty($landmark->reference))
    {
      $this->CI->db->set('reference',    (string)$landmark->reference);
    }
    if(!empty($landmark->rating))
    {
      $this->CI->db->set('rating',      (float)$landmark->rating);
    }
    if(!empty($landmark->icon))
    {
      $this->CI->db->set('icon_url',      (string)$landmark->icon);
    }
    if(!empty($landmark->vicinity))
    {
      $this->CI->db->set('vicinity',      (string)$landmark->vicinity);
    }
    if(!empty($landmark->source_id))
    {
      $this->CI->db->set('source',      (int)$landmark->source_id);
    }

    return $this->CI->db->insert(self::LANDMARKS_TABLE);
  }

  public function db_update_landmark($landmark)
  {
    $this->CI->db->set('geo_latitude',  (float)$landmark->geometry->location->lat);
    $this->CI->db->set('geo_longitude', (float)$landmark->geometry->location->lng);
    $this->CI->db->set('landmark_name', (string)$landmark->name);

    if(!empty($landmark->slug))
    {
      $this->CI->db->set('slug',    (string)$landmark->slug);
    }
    else
    {
      $this->CI->db->set('slug',      NULL);
    }

    if(!empty($landmark->reference))
    {
      $this->CI->db->set('reference',    (string)$landmark->reference);
    }
    else
    {
      $this->CI->db->set('reference',      NULL);
    }

    if(!empty($landmark->rating))
    {
      $this->CI->db->set('rating',      (float)$landmark->rating);
    }
    else
    {
      $this->CI->db->set('rating',      NULL);
    }
    if(!empty($landmark->icon))
    {
      $this->CI->db->set('icon_url',      (string)$landmark->icon);
    }
    else
    {
      $this->CI->db->set('icon_url',      NULL);
    }

    if(!empty($landmark->vicinity))
    {
      $this->CI->db->set('vicinity',      (string)$landmark->vicinity);
    }
    else
    {
      $this->CI->db->set('vicinity',      NULL);
    }

    if(!empty($landmark->source))
    {
      $this->CI->db->set('source',      (int)$landmark->source);
    }

    $this->CI->db->where('hash_id', (string)$landmark->id);
    return $this->CI->db->update(self::LANDMARKS_TABLE);
  }

  public function update_landmark($landmark, $source = 'google')
  {
    $this->CI->load->library('code_tracker');

    $landmark->source_id = $this->get_landmark_source_id($source);
    if(empty($landmark->source_id))
    {
      $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Unknown landmark source -> $source");
      return NULL;
    }

    //IF landmark does not exist
    //  add it in DB and then get created ID
    //else
    //  get landmark id
    // Create feed trace on error
    $landmark_id = NULL;
    if(is_null($landmark_id = $this->get_landmark_id($landmark->id,$landmark->source_id)))
    {
      if($this->insert_landmark($landmark) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error inserting landmark -> ".$landmark->name.": ".$this->CI->db->last_query());
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
      if($this->db_update_landmark($landmark) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating landmark -> ".$landmark->name.": ".$this->CI->db->last_query());
      }
      else
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Updated landmark ".$landmark->name." in DB");
      }
    }

    //Update types of landmark
    if(!empty($landmark_id))
    {
      if($this->update_types_of_landmark($landmark_id, $landmark->source_id, $landmark->types) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating landmark types -> ".$landmark->name.": ".$this->CI->db->last_query());
      }
    }

    return $landmark_id;
  }

  public function update_types_of_landmark($landmark_id, $source_id, $types)
  {

    $return = true;
    //Invalidate all current landmarks types for this landmark id of this source
    $this->update_landmark_of_type_sync_status($landmark_id,self::PROPERTY_INVALID);

    //  FOREACH $type
    //    IF landmark type is NOT already in DB
    //      insert type of this landmark
    //  THEN link type with landmark ($landmark_of_type_id)
    foreach($types as $type)
    {
      if(empty($type))
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Trying to insert empty landmark type");
        continue;
      }

      $landmark_type_id = NULL;
      if(is_null($landmark_type_id = $this->get_landmark_type_id($type, $source_id)))
      {
        $this->CI->db->set('type', (string)$type);
        $this->CI->db->set('source', $source_id);
        $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

        if($this->CI->db->insert(self::LANDMARK_TYPE_TABLE) === false)
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error creating landmark type $type -> : ".$this->CI->db->last_query());
          $return = false;
        }
        else
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added landmark type $type ($landmark_id) -> : ".$this->CI->db->last_query());
          $landmark_type_id = $this->db->insert_id();
        }
      }

      $landmark_of_type_id = NULL;
      // link type with landmark ($landmark_of_type_id)
      if(!empty($landmark_type_id) && is_null($landmark_of_type_id = $this->get_landmark_of_type_id($landmark_type_id, $landmark_id)))
      {
        $this->CI->db->set('landmark_id', (int)$landmark_id);
        $this->CI->db->set('landmark_type_id', (int)$landmark_type_id);
        $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

        if($this->CI->db->insert(self::LANDMARK_OF_TYPE_TABLE) === false)
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error linking landmark $landmark_id with type $landmark_type_id -> : ".$this->CI->db->last_query());
          $return = false;
        }
        else
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added linked landmark ($landmark_id) into type $landmark_type_id -> : ".$this->CI->db->last_query());
          $landmark_of_type_id = $this->db->insert_id();
        }
      }
      elseif(!empty($landmark_type_id))
      {
        $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
        $this->CI->db->where('landmark_of_type_id', $landmark_of_type_id);
        if($this->CI->db->update(self::LANDMARK_OF_TYPE_TABLE) === false)
        {
          $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking landmark of type $landmark_of_type_id -> : ".$this->CI->db->last_query());
          $return = false;
        }
      }
      else
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error with landmark ".$landmark->name." for type $landmark_type_id");
      }
    }

    //Delete invalid landmark types for this landmark
    $this->delete_landmark_of_type_sync_status($landmark_id, self::PROPERTY_INVALID);

    return $return;
  }

  public function update_landmark_attribution($html_attribution)
  {
    $html_attribution_id = NULL;
    if(is_null($html_attribution_id = $this->get_attribution_id($html_attribution)))
    {
      //insert attribution
      $this->CI->db->set('html_attribution', (string)$html_attribution);

      if($this->CI->db->insert(self::LANDMARK_ATTRIBUTION_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error inserting landmark attribution ".$html_attribution." in DB -> ".$this->CI->db->last_query());
      }
      else
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added landmark attribution".$html_attribution." in DB");
        $html_attribution_id = $this->CI->db->insert_id();
      }
    }
    else
    {
      //No update because if attribution change it will be detected as new one
    }

    return $html_attribution_id;
  }

  public function get_landmark_id($hash_id, $source_id)
  {
    $this->CI->db->where("hash_id", $hash_id);
    $this->CI->db->where("source", $source_id);

    $query = $this->CI->db->get(self::LANDMARKS_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->landmark_id;
    }
    return NULL;
  }
  public function get_city_landmark_from_slug($city_id, $slug)
  {
    $this->CI->db->select(self::LANDMARKS_TABLE.".landmark_id");
    $this->CI->db->select(self::LANDMARKS_TABLE.".geo_latitude");
    $this->CI->db->select(self::LANDMARKS_TABLE.".geo_longitude");
    $this->CI->db->select(self::LANDMARKS_TABLE.".hash_id");
    $this->CI->db->select(self::LANDMARKS_TABLE.".landmark_name");
    $this->CI->db->select(self::LANDMARKS_TABLE.".slug");

    if($this->CI->api_used == HB_API)
    {
      $this->CI->db->select("city_hb_id AS city_id");
      $this->CI->db->join('hb_hostel_landmark', 'landmarks.landmark_id = hb_hostel_landmark.landmark_id', 'left');
      $this->CI->db->join('hb_hostel', 'hb_hostel_landmark.property_number = hb_hostel.property_number', 'left');
      $this->CI->db->where("slug",$slug);
      $this->CI->db->where("city_hb_id",$city_id);
      $this->CI->db->group_by("city_hb_id");
    }
    else
    {
      $this->CI->db->select("hw_city_id AS city_id");
      $this->CI->db->join('hw_hostel_landmark', 'landmarks.landmark_id = hw_hostel_landmark.landmark_id', 'left');
      $this->CI->db->join('hw_hostel', 'hw_hostel_landmark.property_number = hw_hostel.property_number', 'left');
      $this->CI->db->where("slug",$slug);
      $this->CI->db->where("hw_city_id",$city_id);
      $this->CI->db->group_by("hw_city_id");
    }

    $query = $this->CI->db->get(self::LANDMARKS_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }
  public function get_landmark($landmark_id)
  {
    $this->CI->db->where("landmark_id", $landmark_id);

    $query = $this->CI->db->get(self::LANDMARKS_TABLE);

    if($query->num_rows() > 0)
    {
      return $query->row();
    }
    return NULL;
  }

  public function get_landmark_source_id($source)
  {
    $this->CI->db->where("source", $source);

    $query = $this->CI->db->get(self::LANDMARK_SOURCES);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->id;
    }
    return NULL;
  }

  //TONOTICE distance should be in KM distance calculation is +/- 1 km, so below 5 km could give strange results
  public function update_landmark_close_properties($landmark_id, $distance_in_km = 5)
  {
    $this->update_landmark_hw_properties($landmark_id, $distance_in_km);
    $this->update_landmark_hb_properties($landmark_id, $distance_in_km);

  }

  public function update_landmark_hw_properties($landmark_id, $distance_in_km)
  {
    $this->CI->load->model('Db_hw_hostel');
    $this->CI->load->library("Geos");

    $landmark = $this->get_landmark($landmark_id);

    foreach($this->CI->Db_hw_hostel->get_all_properties_geos() as $property)
    {
      $prop_distance = 5000;
      if(!isset($property->geo_longitude)||!isset($property->geo_latitude))
      {
        continue;
      }

      $prop_distance = $this->geos->get_distance($property->geo_latitude, $property->geo_longitude, $landmark->geo_latitude, $landmark->geo_longitude);
      if($prop_distance > $distance_in_km)
      {
        continue;
      }
      $this->link_hw_property_landmark($landmark_id,$property->property_number,$prop_distance);
    }
  }
  public function update_landmark_hb_properties($landmark_id, $distance_in_km)
  {
    $this->CI->load->model('Db_hb_hostel');
    $this->CI->load->library("Geos");

    $landmark = $this->get_landmark($landmark_id);

    foreach($this->CI->Db_hb_hostel->get_all_properties_geos() as $property)
    {
      $prop_distance = 5000;
      if(!isset($property->geo_longitude)||!isset($property->geo_latitude))
      {
        continue;
      }

      $prop_distance = $this->geos->get_distance($property->geo_latitude, $property->geo_longitude, $landmark->geo_latitude, $landmark->geo_longitude);
      if($prop_distance > $distance_in_km)
      {
        continue;
      }

      $this->link_hb_property_landmark($landmark_id,$property->property_number,$prop_distance);
    }
  }

  public function link_hw_property_landmark($landmark_id,$property_number, $distance_in_km = NULL)
  {
    $this->CI->load->model('Db_hw_hostel');
    //  IF link with this landmark ID is NOT already in DB with this property
    //    insert link to this landmark for this property
    //  ELSE
    //     Validate it using sync status field
    $hostel_landmark_id = NULL;
    if(is_null($hostel_landmark_id = $this->CI->Db_hw_hostel->get_hostel_landmark_id($property_number, $landmark_id)))
    {
      $this->CI->db->set('property_number', (int)$property_number);
      $this->CI->db->set('landmark_id', (int)$landmark_id);

      if(!is_null($distance_in_km))
      {
        $this->CI->db->set('distance', (float)$distance_in_km);
      }

      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

      if($this->CI->db->insert(Db_hw_hostel::HW_HOSTEL_LANDMARK_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error linking landmark $landmark_id with hostel $property_number -> : ".$this->CI->db->last_query());
        $return = false;
      }
      else
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added landmark ".$landmark_id." into property $property_number");
      }
    }
    else
    {
      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
      if(!is_null($distance_in_km))
      {
        $this->CI->db->set('distance', (float)$distance_in_km);
      }
      else
      {
        $this->CI->db->set('distance', NULL);
      }
      $this->CI->db->where('hw_hostel_landmark_id', $hostel_landmark_id);
      if($this->CI->db->update(Db_hw_hostel::HW_HOSTEL_LANDMARK_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking landmark $hostel_landmark_id -> : ".$this->CI->db->last_query());
        $return = false;
      }
    }

    return $hostel_landmark_id;
  }
  public function link_hb_property_landmark($landmark_id,$property_number, $distance_in_km = NULL)
  {
    $this->CI->load->model('Db_hb_hostel');
    //  IF link with this landmark ID is NOT already in DB with this property
    //    insert link to this landmark for this property
    //  ELSE
    //     Validate it using sync status field
    $hostel_landmark_id = NULL;
    if(is_null($hostel_landmark_id = $this->Db_hb_hostel->get_hostel_landmark_id($property_number, $landmark_id)))
    {
      $this->CI->db->set('property_number', (int)$property_number);
      $this->CI->db->set('landmark_id', (int)$landmark_id);

      if(!empty($distance_in_km))
      {
        $this->CI->db->set('distance', (float)$distance_in_km);
      }

      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);

      if($this->CI->db->insert(Db_hb_hostel::HB_HOSTEL_LANDMARK_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error linking landmark $landmark_id with hostel $property_number -> : ".$this->CI->db->last_query());
        $return = false;
      }
      else
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_DEBUG,"Added landmark $landmark_id into property $property_number");
      }
    }
    else
    {
      $this->CI->db->set('api_sync_status', self::PROPERTY_VALID);
      if(!empty($distance_in_km))
      {
        $this->CI->db->set('distance', (float)$distance_in_km);
      }
      else
      {
        $this->CI->db->set('distance', NULL);
      }
      $this->CI->db->where('hb_hostel_landmark_id', $hostel_landmark_id);
      if($this->CI->db->update(Db_hb_hostel::HB_HOSTEL_LANDMARK_TABLE) === false)
      {
        $this->CI->code_tracker->feed_trace(code_tracker::FEED_ERROR,"Error updating linking landmark $hostel_landmark_id -> : ".$this->CI->db->last_query());
        $return = false;
      }
    }
    return $hostel_landmark_id;
  }

  private function get_landmark_type_id($type, $source_id)
  {
    $type = $this->db->escape_str($type);

    $this->db->where("LOWER(type) LIKE LOWER('$type')");
    $this->db->where("source", $source_id);
    $query = $this->db->get(self::LANDMARK_TYPE_TABLE);

    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->landmark_type_id;
    }
    return NULL;
  }

  private function get_landmark_of_type_id($landmark_type_id, $landmark_id)
  {
    $landmark_type_id = $this->db->escape_str($landmark_type_id);
    $landmark_id = $this->db->escape_str($landmark_id);

    $this->CI->db->where("landmark_type_id", $landmark_type_id);
    $this->CI->db->where("landmark_id", $landmark_id);
    $query = $this->db->get(self::LANDMARK_OF_TYPE_TABLE);

    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->landmark_of_type_id;
    }
    return NULL;
  }

  private function get_attribution_id($attribution)
  {
    $attribution = $this->CI->db->escape($attribution);

    $this->CI->db->select("landmark_attribution_id");
    $this->CI->db->where("LOWER(`html_attribution`) LIKE LOWER($attribution)");

    $query = $this->CI->db->get(self::LANDMARK_ATTRIBUTION_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->landmark_attribution_id;
    }
    return NULL;
  }

  private function update_landmark_of_type_sync_status($landmark_id, $sync_status)
  {
    $this->CI->db->set('api_sync_status', $sync_status);
    $this->CI->db->where("landmark_id", $landmark_id);
    $this->CI->db->update(self::LANDMARK_OF_TYPE_TABLE);
  }

  private function delete_landmark_of_type_sync_status($landmark_id, $sync_status = 0)
  {
    $this->CI->db->where('api_sync_status', $sync_status);
    $this->CI->db->where("landmark_id", $landmark_id);
    $this->CI->db->delete(self::LANDMARK_OF_TYPE_TABLE);
  }

}