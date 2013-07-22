<?php
class Db_currencies
{
  const CURRENCY_TABLE = 'currencies';
  const CURRENCY_COUNTRY_TABLE = 'currency_country';
  
  var $db = "";
  var $default_currency = "EUR";
  var $default_lang     = "en";
  
  var $description_fields = array();
  
  function Db_currencies($conn)
  {
    $this->db = $conn;
    
    $this->initialize();
  }
  
  function initialize()
  {
      $this->db->query("SET NAMES 'utf8'");
      
      //Initialize all langages continent fields
      $sql = "SHOW COLUMNS FROM ".self::CURRENCY_TABLE." WHERE Field LIKE'description%'";
      $results = $this->db->get_results($sql);

      foreach ($results as $row)
      {
         array_push($this->description_fields, $row->Field);
      }
  }
  
  function getCurrencyCodesArray()
  {
    $query = "SELECT currency_code,description_en FROM ".self::CURRENCY_TABLE. " ORDER BY `order` DESC, description_en ASC";
    $db_currencies = $this->db->get_results($query);

    $currencies = array();
    foreach($db_currencies as $currency)
    {
      array_push($currencies, array($currency->description_en,$currency->currency_code));
    }
    return $currencies;
  }
  
  function validate_currency($currency_code)
  {
    $query = "SELECT currency_code FROM ".self::CURRENCY_TABLE." WHERE LOWER(currency_code) LIKE LOWER('".$currency_code."')";
    $currency = $this->db->get_var($query);
    
    if (!empty($currency))
    {
      return $currency;
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
  * sasya8080
  * 
  */
  function select_currency_list( $select_id, 
                            $select_name, 
                            $currency_selected = "", 
                            $otherAttributes = "",
                            $text_lang = "en", 
                            $no_selection_text = NULL){
      $text_lang  = $this->validate_currency_lang($text_lang);

        $selected = "";
        if(!empty($currency_selected))
        {
          $selected = $currency_selected;
        }
        
        $query = "SELECT * FROM ".self::CURRENCY_TABLE;
        $query.= " ORDER BY `order` DESC, `description_$text_lang` ASC";
        $currencies = $this->db->get_results($query);
        ?>
        <div  <?php echo $otherAttributes; ?> id="<?php echo $select_id; ?>" class="dropdown dropdown-tip" >
            <ul class="dropdown-menu">
            <?php
            if(!empty($no_selection_text))
            {
              ?>
              <li <?php if(empty($selected)) echo "active=1 "; ?> value="">----- <?php echo $no_selection_text; ?> -----</li>
              <?php
            }

            foreach ($currencies as $row)
            {
              $desc_field = "description_".$text_lang;
              $currency_symbol = empty($row->symbol) ? $row->currency_code : $row->symbol;
              ?>
              <li <?php if(strcasecmp($selected,$row->currency_code)==0) echo "active=1"; ?> data-symbol="<?php echo $currency_symbol; ?>" data-code="<?php echo $row->currency_code; ?>">
              <a>
                <span class="currency-code"><?php echo $currency_symbol;?> : </span>
                <?php echo $row->$desc_field;?>
              </a></li>
              <?php
            }
            ?>
            </ul>
        </div>
      <?php        
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
    ?>
    <select <?php echo $otherAttributes; ?> name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>">
    <?php 
    if(!empty($no_selection_text))
    {
      ?>
      <option <?php if(empty($selected)) echo "selected=\"selected\" "; ?>value="">----- <?php echo $no_selection_text; ?> -----</option>
      <?php
    }
    $query = "SELECT * FROM ".self::CURRENCY_TABLE;
    $query.= " ORDER BY `order` DESC, `description_$text_lang` ASC";
    $currencies = $this->db->get_results($query);

    foreach ($currencies as $row)
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
    $country_iso_code_2 = $this->db->escape($country_iso_code_2);
    
    $query = "SELECT currency_code 
              FROM ".self::CURRENCY_COUNTRY_TABLE." 
							WHERE LOWER(country_iso_code_2) LIKE LOWER('$country_iso_code_2')";
    
    $query = $this->db->get_var($query);
    
    return $query;
  }
}
?>