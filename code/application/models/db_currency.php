<?php
/**
 * @author Louis-Michel
 *
 */
class Db_currency extends CI_Model
{
  const CURRENCY_TABLE      = 'currencies';
  const CURRENCY_COUNTRY_TABLE = 'currency_country';

  var $CI;
  var $default_currency = "EUR";
  var $default_lang     = "en";

  var $description_fields = array();
  function Db_currency()
  {
    parent::__construct();

    $this->CI =& get_instance();
    $this->default_currency = $this->validate_currency($this->CI->config->item('site_currency_default'));

    $this->initialize();

  }

  function initialize()
  {
      $this->db->simple_query("SET NAMES 'utf8'");

      //Initialize all langages continent fields
      $sql = "SHOW COLUMNS FROM ".self::CURRENCY_TABLE." WHERE Field LIKE'description%'";
      //$this->db->model_cache_single(__CLASS__ , __FUNCTION__);
      $query = $this->db->query($sql);

      foreach ($query->result() as $row)
      {
         array_push($this->description_fields, $row->Field);
      }
  }

  function validate_currency($currency_code)
  {
    $query = "SELECT currency_code FROM ".self::CURRENCY_TABLE." WHERE LOWER(currency_code) LIKE LOWER('".$currency_code."')";
    $query = $this->db->query($query);

    if ($query->num_rows() > 0)
    {
      $currency = $query->row();
      return $currency->currency_code;
    }

    return $this->default_currency;

  }

  function validate_currency_lang($lang)
  {
    foreach($this->description_fields AS $desc_field)
    {
      if(strcmp($desc_field,"description_".$lang)==0)
      {
        return $lang;
      }
    }
    return $this->default_lang;
  }
  /**
   *
   *
   */
  function get_currency_id($currency_code)
  {
    settype($currency_code, "string");
    $currency_id = NULL;
    if($currency_code != NULL)
    {
      $query = "SELECT currency_id FROM ".self::CURRENCY_TABLE." WHERE currency_code = '".$currency_code."' COLLATE utf8_general_ci";
      $query = $this->db->query($query);

      if ($query->num_rows() > 0)
      {
        $currency_id = $query->row();
        $currency_id = $currency_id->currency_id;
      }
//      else
//      {
//        $data = array('currency_code' => $currency_code);
//
//        $this->db->insert(self::CURRENCY_TABLE, $data);
//        $currency_id = $this->db->insert_id();
//      }
    }
    return $currency_id;

  }


  function get_currency_code($currency_id)
  {
    if(!is_null($currency_id))
    {
      $query = "SELECT currency_code FROM ".self::CURRENCY_TABLE." WHERE currency_id = '".$currency_id."' COLLATE utf8_general_ci";
      $query = $this->db->query($query);
      $cur = $query->row();
      return $cur->currency_code;
    }
    return NULL;
  }

  function get_all_currencies($text_lang = 'en')
  {
    $text_lang  = $this->validate_currency_lang($text_lang);

    $this->db->order_by("`order`", "DESC");
    $this->db->order_by("`description_".$text_lang."`", "ASC");
    $query = $this->db->get(self::CURRENCY_TABLE);

    if ($query->num_rows() > 0)
    {
      return $query->result();
    }
    return NULL;
  }

  function update_hb_equivalent($currency_code, $price)
  {
    $this->db->set('hb_equivalent', $price);
    $this->db->set('last_equivalent_update', date(DATE_ATOM));
    $this->db->where('currency_code', $currency_code);
    return $this->db->update(self::CURRENCY_TABLE);
  }

  function convert_from_hw_rates($from_cur_iso, $to_cur_iso, $amount)
  {
    $query = "SELECT count(DISTINCT currency_price) as currency_count, hw_hostel_id ";
    $query.= " FROM hw_hostel_price";
    $query.= " GROUP BY hw_hostel_id";
    $query.= " ORDER BY currency_count DESC";
    $query.= " LIMIT 1";

    $query = $this->db->query($query);
    $hostel_for_rates = $query->row();

    //TONOTICE hardcoded for faster conversion rate but might need to count the currencies of the table
    if ($hostel_for_rates->currency_count >= 95)
    {
      $query = "SELECT currency_price,bed_price FROM hw_hostel_price ";
      $query.= " WHERE (hw_hostel_id = ".$hostel_for_rates->hw_hostel_id." AND currency_price LIKE'".$from_cur_iso."') OR";
      $query.= "       (hw_hostel_id = ".$hostel_for_rates->hw_hostel_id." AND currency_price LIKE'".$to_cur_iso."')";
      $query = $this->db->query($query);

      if ($query->num_rows() == 2)
      {
        $from_cur_amount = 0;
        $to_cur_amount   = 0;

        foreach($query->result() as $row)
        {
          if(strcasecmp($row->currency_price,$from_cur_iso)==0)
          {
            $from_cur_amount = $row->bed_price;
          }
          elseif(strcasecmp($row->currency_price,$to_cur_iso)==0)
          {
            $to_cur_amount = $row->bed_price;
          }
        }

        if(!empty($from_cur_amount)&&!empty($to_cur_amount))
        {
          return round($amount*$to_cur_amount/$from_cur_amount,2);
        }
      }
      return NULL;
    }
    return NULL;
  }

  function convert_from_hb_rates($from_cur_iso, $to_cur_iso, $amount)
  {
    $query = "SELECT (SELECT hb_equivalent FROM currencies WHERE currency_code = '$to_cur_iso')/(SELECT hb_equivalent FROM currencies WHERE currency_code = '$from_cur_iso') as convert_rate";

    $query = $this->db->query($query);

    if($query->num_rows() == 1)
    {
      $row = $query->row();
      return number_format($row->convert_rate*$amount,2,".","");
    }

    return NULL;
  }

  /**
   * get_currency_select
   *
   * @param $select_id
   * @param $select_name
   * @param $currency_selected
   * @param $otherAttributes
   * @param $text_lang
   * @param $no_selection_text
   * @return unknown_type
   */
  function select_currency( $select_id,
                            $select_name,
                            $currency_selected = "",
                            $otherAttributes = "",
                            $text_lang = "en",
                            $no_selection_text = NULL)
  {
    $text_lang  = $this->validate_currency_lang($text_lang);

    $selected = "";
    if(!empty($currency_selected))
    {
      $selected = $currency_selected;
    }

    $this->db->order_by("`order`", "DESC");
    $this->db->order_by("`description_".$text_lang."`", "ASC");
    //$this->db->model_cache_single(__CLASS__ , __FUNCTION__);
    $query = $this->db->get(self::CURRENCY_TABLE);

    ?>
    <select <?php echo $otherAttributes; ?> name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>">
    <?php
    if(!empty($no_selection_text))
    {
      ?>
      <option <?php if(empty($selected)) echo "selected=\"selected\" "; ?>value="">----- <?php echo $no_selection_text; ?> -----</option>
      <?php
    }

    foreach ($query->result() as $row)
    {
      $desc_field = "description_".$text_lang;
      ?>
      <option <?php if(strcasecmp($selected,$row->currency_code)==0) echo "selected=\"selected\" "; ?>value="<?php echo $row->currency_code; ?>"><?php echo $row->$desc_field;?></option>
      <?php
    }
    ?>
    </select>
    <?php
  }

  /*
   *
   */
  function get_currency_of_country($country_iso_code_2)
  {
    $country_iso_code_2 = $this->db->escape_like_str($country_iso_code_2);
    $this->db->select("`currency_code`");
    $this->db->where("LOWER(country_iso_code_2) LIKE LOWER('$country_iso_code_2')");
    $query = $this->db->get(self::CURRENCY_COUNTRY_TABLE);

    if ($query->num_rows() > 0)
    {
      $query = $query->row();
      return $query->currency_code;
    }
    return "";
  }
}