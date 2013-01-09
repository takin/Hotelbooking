<?php
/**
 * @author Louis-Michel Raynauld
 *
 * @license commercial
 *
 * References: none
 *
 * Beware function will be call from wordpress environment
 */

/**
 * var_check
 *
 * Check if variable is set. If not, return a default value instead.
 *
 * @param $var - variable to check
 * @param $default_value - value to return if variable is not set
 *
 * @return variable - $var or default if $var is not set
 */
function var_check($var, $default_value)
{
	if (isset($var))
	{
	 return $var;
  }
  else
  {
	 return $default_value;
  }
}

/**
 * login_check
 *
 * Check if user is logged in. If not, return a default value instead.
 *
 * @param $logged - variable to check if user is logged in
 * @param $logged_display - variable to display if user is logged in
 * @param $default_display - variable to display otherwise
 *
 * @return variable - $var or default if $var is not set
 */
function login_check($logged = 0, $logged_display, $default_display)
{
  if ($logged > 0)
  {
   return $logged_display;
  }
  else
  {
   return $default_display;
  }
}

/**
 * date_conv
 *
 * Format a date YYYY-MM-DD using locale settings to format like 3 february 2002
 *
 * @param $date_YYYY_MM_DD - string date to format
 *
 * @return date in format like 25 March 2010
 */
function date_conv($date_YYYY_MM_DD, $custom_format = NULL)
{
  $date = mktime(0, 0, 0, substr($date_YYYY_MM_DD,5,2) , substr($date_YYYY_MM_DD,8,2) ,substr($date_YYYY_MM_DD,0,4) );
  if((empty($custom_format))) {
    $custom_format = "%e %B %Y";
  }
  if (ISWINDOWS) {
    log_message('debug', "date_conv for date $date_YYYY_MM_DD with format $custom_format");
    $custom_format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $custom_format);
    log_message('debug', "date_conv replaced for date $date_YYYY_MM_DD with format $custom_format");
  }
  $return = strftime($custom_format,$date);
  return $return;
}

/**
 * slash_date_conv
 *
 * Format a date DD/MM/YYYY using locale settings to format like 3 february 2002
 *
 * @param $date_DD_MM_YYYY - string date to format
 *
 * @return date in format like 25 March 2010
 */
function slash_date_conv($date_DD_MM_YYYY, $custom_format = NULL)
{
  $date = mktime(0, 0, 0, substr($date_DD_MM_YYYY,3,2) , substr($date_DD_MM_YYYY,0,2) ,substr($date_DD_MM_YYYY,6,4) );
	if((empty($custom_format))) {
		return strftime("%e %B %Y",$date);
	} else {
		return strftime($custom_format,$date);
	}

}
/**
 * get default date
 *
 */
function get_date_default($day_offset = 10)
{
  return date("Y-m-d",mktime(0, 0, 0, date("m") , date("d")+$day_offset, date("Y")));

}
/**
 * get_error_key
 *
 * get error key to translate error message from api
 *
 * @param error_msg: API error message
 *
 * @return string error key of message
 */
//function get_error_key($error_msg)
//{
//  switch(strtolower($error_msg)){
//    case strtolower('3D Secure Request'):
//      $error_key = '3D_Secure_Request';
//      break;
//    case strtolower('Invalid PropertyNumber'):
//      $error_key = 'invalid_propertyNumber';
//      break;
//    case strtolower('No Properties Found'):
//      $error_key = 'No_Properties_Found';
//      break;
//    case strtolower('No PropertyNumber Supplied'):
//      $error_key = 'No_PropertyNumber_Supplied';
//      break;
//    case strtolower('No Room Preference'):
//      $error_key = 'No_Room_Preference';
//      break;
//    default:
//      $error_key = 'unknown_error';
//  }
//  return $error_key;
//}

/**
 * nl2p
 *
 * @param $string:
 * @param $line_breaks:
 * @param $xml:
 *
 * @return string
 */
function nl2p($string, $line_breaks = true, $xml = true)
{
  // Remove existing HTML formatting to avoid double-wrapping things
  $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

  // It is conceivable that people might still want single line-breaks
  // without breaking into a new paragraph.
  if ($line_breaks == true)
    return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br'.($xml == true ? ' /' : '').'>'), trim($string)).'</p>';
  else
    return '<p>'.preg_replace("/([\n]{1,})/i", "</p>\n<p>", trim($string)).'</p>';
}

/*
 *
 */
function br2nl($string){
  $return=mb_eregi_replace('<br[[:space:]]*/?'.'[[:space:]]*>',chr(13).chr(10),$string);
  return $return;
}
/**
 * select_month
 *
 * @param $month_id:      ID of the select box for the month
 * @param $month_name:    Name of the select box for the month
 * @param $month_class:   Class of the select box for the month
 * @param $value:         Value to set the date select
 *
 * @return generate two select box to select a month
 */

function select_month($month_id,$month_name,$month_class,$value = NULL)
{
  if($value != NULL)
  {
//    $day_selected = substr($value, 8, 2);
    $month_selected = substr($value, 0, 2);
  }
  else
  {
    $default_date = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
//    $day_selected = date("d",$default_date);
    $month_selected = date("m",$default_date);
  }
  ?>
  <select class="<?php echo $month_class; ?>" id="<?php echo $month_id; ?>" name="<?php echo $month_name; ?>">
  <?php
  for($date_qty=0;$date_qty<12;$date_qty++)
  {
    $date  = mktime(0, 0, 0, 1+$date_qty  , 1, date("Y"));
    ?>
    <option <?php if(strcmp($month_selected,date("m",$date))==0) echo "selected=\"selected\""; ?> value="<?php echo date("m",$date); ?>"><?php echo strftime("%B",$date); ?></option>
    <?php
  }
  ?>
   </select>
  <?php
}
/**
 * select_year
 *
 * @param $year_id:      ID of the select box for the year
 * @param $year_name:    Name of the select box for the year
 * @param $year_class:   Class of the select box for the year
 * @param $min:
 * @param $max:
 * @param $value:        Value to set the year select
 *
 * @return generate select box to select a year
 */

function select_year($year_id,$year_name,$year_class,$min,$max,$value = NULL)
{
  if($value != NULL)
  {
//    $day_selected = substr($value, 8, 2);
//    $month_selected = substr($value, 6, 2);
    $year_selected = substr($value, 0, 4);
  }
  else
  {
    $default_date = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
//    $day_selected = date("d",$default_date);
//    $month_selected = date("m",$default_date);
    $year_selected = date("Y",$default_date);
  }
  ?>

   <select class="<?php echo $year_class; ?> id="<?php echo $year_id; ?>" name="<?php echo $year_name; ?>">
  <?php
  if($max>$min)
  {
    for($date_qty=$min;$date_qty<$max;$date_qty++)
    {
      $date  = mktime(0, 0, 0, date("m"), date("d"), date("Y")+$date_qty);
      ?>
      <option <?php if(strcmp($year_selected,date("Y",$date))==0) echo "selected=\"selected\""; ?> value="<?php echo date("Y",$date); ?>"><?php echo strftime("%Y",$date); ?></option>
      <?php
    }
  }
  else
  {
    for($date_qty=$min;$date_qty>$max;$date_qty--)
    {
      $date  = mktime(0, 0, 0, date("m"), date("d"), date("Y")+$date_qty);
      ?>
      <option <?php if(strcmp($year_selected,date("Y",$date))==0) echo "selected=\"selected\""; ?> value="<?php echo date("Y",$date); ?>"><?php echo strftime("%Y",$date); ?></option>
      <?php
    }
  }
  ?>
   </select>
  <?php
}
/**
 * select_month_year
 *
 * @param $select_id:      ID of the select box for the year
 * @param $select_name:    Name of the select box for the year
 * @param $select_class:   Class of the select box for the year
 * @param $min:
 * @param $max:
 * @param $value:        Value to set the month year select
 *
 * @return generate select box to select a year
 */

function select_month_year($select_id,$select_name,$select_class,$min,$max,$value = NULL)
{
  if(!empty($value))
  {
//    $day_selected = substr($value, 8, 2);
//    $month_selected = substr($value, 6, 2);
    $month_year_selected = substr($value, 0, 7);
  }
  else
  {
    $default_date = mktime(0, 0, 0, date("m") , date("d"), date("Y"));
//    $day_selected = date("d",$default_date);
//    $month_selected = date("m",$default_date);
    $month_year_selected = date("Y",$default_date)."-".date("m",$default_date);
  }
  ?>

   <select class="<?php echo $select_class; ?>" id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>">
  <?php
  if($max>$min)
  {
    for($date_qty=$min;$date_qty<$max;$date_qty++)
    {
      $month_inc = date("m")+$date_qty;
      $year_inc  = date("Y");
      $date  = mktime(0, 0, 0, $month_inc, 0, $year_inc);
      $month_year_value = date("Y",$date)."-".date("m",$date);
      ?>
      <option <?php if(strcmp($month_year_selected,$month_year_value)==0) echo "selected=\"selected\""; ?> value="<?php echo $month_year_value; ?>"><?php echo strftime("%B %Y",$date); ?></option>
      <?php
    }
  }
  else
  {
    for($date_qty=$min;$date_qty>$max;$date_qty--)
    {
      $date  = mktime(0, 0, 0, date("m")+$date_qty, 0, date("Y"));
      ?>
      <option <?php if(strcmp($month_year_selected,$month_year_value)==0) echo "selected=\"selected\""; ?> value="<?php echo $month_year_value; ?>"><?php echo strftime("%B %Y",$date); ?></option>
      <?php
    }
  }
  ?>
   </select>
  <?php
}
/**
 * select_date
 *
 * @param $day_id:      ID of the select box for the day
 * @param $day_name:    Name of the select box for the day
 * @param $month_id:    ID of the select box for the month and year
 * @param $month_name:  Name of the select box for the month and year
 * @param $value:       Value to set the date select
 *
 * @return generate two select box to select a date
 */

function select_date($label_title,$day_id,$day_name,$month_id,$month_name,$value = NULL)
{
//print_r(setlocale(LC_ALL,0));
//  setlocale(LC_ALL,setlocale(LC_ALL,0));
  if($value != NULL)
  {
    $day_selected = substr($value, 8, 2);
    $monthyear_selected = substr($value, 0, 7);
  }
  else
  {
    $default_date = mktime(0, 0, 0, date("m") , date("d")+10, date("Y"));
    $day_selected = date("d",$default_date);
    $monthyear_selected = date("Y-m",$default_date);
  }
  ?>
    <label for="<?php echo $day_id; ?>"><?php echo $label_title; ?></label>
      <select class="margright"  id="<?php echo $day_id; ?>" name="<?php echo $day_name; ?>">
          <option <?php if(strcmp($day_selected,"01")==0) echo "selected=\"selected\""; ?> value="01">1</option>
          <option <?php if(strcmp($day_selected,"02")==0) echo "selected=\"selected\""; ?> value="02">2</option>
          <option <?php if(strcmp($day_selected,"03")==0) echo "selected=\"selected\""; ?> value="03">3</option>
          <option <?php if(strcmp($day_selected,"04")==0) echo "selected=\"selected\""; ?> value="04">4</option>
          <option <?php if(strcmp($day_selected,"05")==0) echo "selected=\"selected\""; ?> value="05">5</option>
          <option <?php if(strcmp($day_selected,"06")==0) echo "selected=\"selected\""; ?> value="06">6</option>
          <option <?php if(strcmp($day_selected,"07")==0) echo "selected=\"selected\""; ?> value="07">7</option>
          <option <?php if(strcmp($day_selected,"08")==0) echo "selected=\"selected\""; ?> value="08">8</option>
          <option <?php if(strcmp($day_selected,"09")==0) echo "selected=\"selected\""; ?> value="09">9</option>
          <option <?php if(strcmp($day_selected,"10")==0) echo "selected=\"selected\""; ?> value="10">10</option>
          <option <?php if(strcmp($day_selected,"11")==0) echo "selected=\"selected\""; ?> value="11">11</option>
          <option <?php if(strcmp($day_selected,"12")==0) echo "selected=\"selected\""; ?> value="12">12</option>
          <option <?php if(strcmp($day_selected,"13")==0) echo "selected=\"selected\""; ?> value="13">13</option>
          <option <?php if(strcmp($day_selected,"14")==0) echo "selected=\"selected\""; ?> value="14">14</option>
          <option <?php if(strcmp($day_selected,"15")==0) echo "selected=\"selected\""; ?> value="15">15</option>
          <option <?php if(strcmp($day_selected,"16")==0) echo "selected=\"selected\""; ?> value="16">16</option>
          <option <?php if(strcmp($day_selected,"17")==0) echo "selected=\"selected\""; ?> value="17">17</option>
          <option <?php if(strcmp($day_selected,"18")==0) echo "selected=\"selected\""; ?> value="18">18</option>
          <option <?php if(strcmp($day_selected,"19")==0) echo "selected=\"selected\""; ?> value="19">19</option>
          <option <?php if(strcmp($day_selected,"20")==0) echo "selected=\"selected\""; ?> value="20">20</option>
          <option <?php if(strcmp($day_selected,"21")==0) echo "selected=\"selected\""; ?> value="21">21</option>
          <option <?php if(strcmp($day_selected,"22")==0) echo "selected=\"selected\""; ?> value="22">22</option>
          <option <?php if(strcmp($day_selected,"23")==0) echo "selected=\"selected\""; ?> value="23">23</option>
          <option <?php if(strcmp($day_selected,"24")==0) echo "selected=\"selected\""; ?> value="24">24</option>
          <option <?php if(strcmp($day_selected,"25")==0) echo "selected=\"selected\""; ?> value="25">25</option>
          <option <?php if(strcmp($day_selected,"26")==0) echo "selected=\"selected\""; ?> value="26">26</option>
          <option <?php if(strcmp($day_selected,"27")==0) echo "selected=\"selected\""; ?> value="27">27</option>
          <option <?php if(strcmp($day_selected,"28")==0) echo "selected=\"selected\""; ?> value="28">28</option>
          <option <?php if(strcmp($day_selected,"29")==0) echo "selected=\"selected\""; ?> value="29">29</option>
          <option <?php if(strcmp($day_selected,"30")==0) echo "selected=\"selected\""; ?> value="30">30</option>
          <option <?php if(strcmp($day_selected,"31")==0) echo "selected=\"selected\""; ?> value="31">31</option>
</select>
  <select id="<?php echo $month_id; ?>" name="<?php echo $month_name; ?>">

  <?php
  for($date_qty=0;$date_qty<23;$date_qty++)
  {
    $date  = mktime(0, 0, 0, date("m")+$date_qty  , 1, date("Y"));
    ?>
    <option <?php if(strcmp($monthyear_selected,date("Y-m",$date))==0) echo "selected=\"selected\""; ?> value="<?php echo date("Y-m",$date); ?>"><?php echo strftime("%b %Y",$date); ?></option>
    <?php
  }
  ?>

   </select>
  <?php
}

function select_day($day_id,$day_name,$day_selected = '01')
{
  ?>
  <select class="margright"  id="<?php echo $day_id; ?>" name="<?php echo $day_name; ?>">
    <option<?php if(strcmp($day_selected,"01")==0) echo " selected=\"selected\""; ?> value="01">1</option>
    <option<?php if(strcmp($day_selected,"02")==0) echo " selected=\"selected\""; ?> value="02">2</option>
    <option<?php if(strcmp($day_selected,"03")==0) echo " selected=\"selected\""; ?> value="03">3</option>
    <option<?php if(strcmp($day_selected,"04")==0) echo " selected=\"selected\""; ?> value="04">4</option>
    <option<?php if(strcmp($day_selected,"05")==0) echo " selected=\"selected\""; ?> value="05">5</option>
    <option<?php if(strcmp($day_selected,"06")==0) echo " selected=\"selected\""; ?> value="06">6</option>
    <option<?php if(strcmp($day_selected,"07")==0) echo " selected=\"selected\""; ?> value="07">7</option>
    <option<?php if(strcmp($day_selected,"08")==0) echo " selected=\"selected\""; ?> value="08">8</option>
    <option<?php if(strcmp($day_selected,"09")==0) echo " selected=\"selected\""; ?> value="09">9</option>
    <option<?php if(strcmp($day_selected,"10")==0) echo " selected=\"selected\""; ?> value="10">10</option>
    <option<?php if(strcmp($day_selected,"11")==0) echo " selected=\"selected\""; ?> value="11">11</option>
    <option<?php if(strcmp($day_selected,"12")==0) echo " selected=\"selected\""; ?> value="12">12</option>
    <option<?php if(strcmp($day_selected,"13")==0) echo " selected=\"selected\""; ?> value="13">13</option>
    <option<?php if(strcmp($day_selected,"14")==0) echo " selected=\"selected\""; ?> value="14">14</option>
    <option<?php if(strcmp($day_selected,"15")==0) echo " selected=\"selected\""; ?> value="15">15</option>
    <option<?php if(strcmp($day_selected,"16")==0) echo " selected=\"selected\""; ?> value="16">16</option>
    <option<?php if(strcmp($day_selected,"17")==0) echo " selected=\"selected\""; ?> value="17">17</option>
    <option<?php if(strcmp($day_selected,"18")==0) echo " selected=\"selected\""; ?> value="18">18</option>
    <option<?php if(strcmp($day_selected,"19")==0) echo " selected=\"selected\""; ?> value="19">19</option>
    <option<?php if(strcmp($day_selected,"20")==0) echo " selected=\"selected\""; ?> value="20">20</option>
    <option<?php if(strcmp($day_selected,"21")==0) echo " selected=\"selected\""; ?> value="21">21</option>
    <option<?php if(strcmp($day_selected,"22")==0) echo " selected=\"selected\""; ?> value="22">22</option>
    <option<?php if(strcmp($day_selected,"23")==0) echo " selected=\"selected\""; ?> value="23">23</option>
    <option<?php if(strcmp($day_selected,"24")==0) echo " selected=\"selected\""; ?> value="24">24</option>
    <option<?php if(strcmp($day_selected,"25")==0) echo " selected=\"selected\""; ?> value="25">25</option>
    <option<?php if(strcmp($day_selected,"26")==0) echo " selected=\"selected\""; ?> value="26">26</option>
    <option<?php if(strcmp($day_selected,"27")==0) echo " selected=\"selected\""; ?> value="27">27</option>
    <option<?php if(strcmp($day_selected,"28")==0) echo " selected=\"selected\""; ?> value="28">28</option>
    <option<?php if(strcmp($day_selected,"29")==0) echo " selected=\"selected\""; ?> value="29">29</option>
    <option<?php if(strcmp($day_selected,"30")==0) echo " selected=\"selected\""; ?> value="30">30</option>
    <option<?php if(strcmp($day_selected,"31")==0) echo " selected=\"selected\""; ?> value="31">31</option>
  </select>
  <?php
}
/**
 * select_nights
 *
 * @param $id:      ID of the select box for the number of nights
 * @param $name:    Name of the select box for the number of nights
 * @param $value:   Value to set the select box
 *
 * @return generate two select box to select a date
 */

function select_nights($label_title,$id,$name,$value)
{
  ?>
  <label for="<?php echo $id; ?>"><?php echo $label_title; ?></label>
  <select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
  <?php
  if(!isset($numnights_selected))
  {
    $numnights_selected = 4;
  }

  for($nights_qty=1;$nights_qty<=18;$nights_qty++)
  {
    ?>
    <option value="<?php echo $nights_qty; ?>"<?php if($value==$nights_qty) echo " selected=\"selected\" ";?>><?php echo $nights_qty; ?></option>
    <?php
  }
  ?>

  </select>
  <?php
}
/** settle_currency_filter
 *
 * @param $currency_iso: currency iso letter code (in caps)
 * @param $default_currency: currency iso letter code (in caps) USD, EUR or GBP only
 *
 * @return $currency_iso_filterered  return default settle currency
 */
  function settle_currency_filter($currency_iso,$default_currency)
  {
    $cur = $currency_iso;

    switch ($default_currency)
    {
      case 'EUR':
        $default_currency = 'EUR';
        break;
      case 'USD':
        $default_currency = 'USD';
        break;
      case 'GBP':
        $default_currency = 'GBP';
        break;
      default:
        $default_currency = 'USD';
    }


    switch ($currency_iso)
    {
      case 'EUR':
        $cur = 'EUR';
        break;
      case 'USD':
        $cur = 'USD';
        break;
      case 'GBP':
        $cur = 'GBP';
        break;
      default:
        $cur = $default_currency;
    }

    return $cur;
  }

/** currency_symbol
 * @param $currency_iso: currency iso letter code (in caps)
 *
 * @return $currency symbol
 */

function currency_symbol($currency_iso)
{
    $cur = $currency_iso;
    switch ($currency_iso){
      case 'EUR':
        $cur = '&euro;';
        break;
      case 'USD':
        $cur = '$';
        break;
//      case 'CAD':
//        $cur = '$';
//        break;
      case 'GBP':
        $cur = '&pound;';
        break;
			case 'BRL':
        $cur = 'R$';
        break;
    }

    return $cur;
}

/* dateDiff
 *
 */
function dateDiff($startDate, $endDate)
{
  // Parse dates for conversion
  $startArry = date_parse($startDate);
  $endArry = date_parse($endDate);

  // Convert dates to Julian Days
  $start_date = gregoriantojd($startArry["month"], $startArry["day"], $startArry["year"]);
  $end_date = gregoriantojd($endArry["month"], $endArry["day"], $endArry["year"]);

  // Return difference
  return round(($end_date - $start_date), 0);
}

/*
 * my_mb_ucfirst
 */

function my_mb_ucfirst($str, $e='UTF-8') {
    $fc = mb_strtoupper(mb_substr($str, 0, 1, $e), $e);
    return $fc.mb_substr($str, 1, mb_strlen($str, $e), $e);
}
/*
 * mb_ucwords
 */
function mb_ucwords($str, $e='UTF-8')
{
      return mb_convert_case($str, MB_CASE_TITLE, $e);
}
/* stripAccents
 *
 */
function stripAccents ($string){
    $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $string = utf8_decode($string);
    $string = strtr($string, utf8_decode($a), $b);
//    $string = strtolower($string);
    return utf8_encode($string);
}

function customurldecode($url)
{
  $url = urldecode($url);

  $encoded   = array("-2F-", "-27-", "-5C-");
  $special   = array("/"   , "'"   , "\\"  );

  return str_replace($encoded, $special, $url);

}

function customurlencode($url)
{
  $url = urlencode($url);

  $encoded   = array("-2F-", "-27-", "-5C-");
  $special   = array("/"   , "'"   , "\\");

  return str_replace($special, $encoded, $url);

}

/**
 * Deprecated. Not I18n friendly. Use a database driven function instead.
 *
 */

function select_country_old($select_id,$select_name,$country_selected = "",$ipAddr = NULL, $otherAttributes = "")
{
  trigger_error("Deprecated function called.", E_USER_NOTICE);

  $selected = "";
  if($country_selected!="")
  {
    $selected = $country_selected;
  }
  elseif($ipAddr!=NULL)
  {
    $IPDetail = countryCityFromIP($ipAddr);
    if($IPDetail!==false)
    {
      $selected = $IPDetail['country'];
    }
  }

  ?>
  <select <?php echo $otherAttributes; ?> name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>">
    <option <?php if(strcasecmp($selected,"Afghanistan")==0) echo "selected=\"selected\""; ?> value="Afghanistan">Afghanistan</option>
    <option <?php if(strcasecmp($selected,"Albania")==0) echo "selected=\"selected\""; ?> value="Albania">Albania</option>
    <option <?php if(strcasecmp($selected,"Algeria")==0) echo "selected=\"selected\""; ?> value="Algeria">Algeria</option>
    <option <?php if(strcasecmp($selected,"American Samoa")==0) echo "selected=\"selected\""; ?> value="American Samoa">American Samoa</option>
    <option <?php if(strcasecmp($selected,"Andorra")==0) echo "selected=\"selected\""; ?> value="Andorra">Andorra</option>
    <option <?php if(strcasecmp($selected,"Angola")==0) echo "selected=\"selected\""; ?> value="Angola">Angola</option>
    <option <?php if(strcasecmp($selected,"Anguilla")==0) echo "selected=\"selected\""; ?> value="Anguilla">Anguilla</option>
    <option <?php if(strcasecmp($selected,"Antarctica")==0) echo "selected=\"selected\""; ?> value="Antarctica">Antarctica</option>
    <option <?php if(strcasecmp($selected,"Antigua And Barbuda")==0) echo "selected=\"selected\""; ?> value="Antigua And Barbuda">Antigua And Barbuda</option>
    <option <?php if(strcasecmp($selected,"Argentina")==0) echo "selected=\"selected\""; ?> value="Argentina">Argentina</option>
    <option <?php if(strcasecmp($selected,"Armenia")==0) echo "selected=\"selected\""; ?> value="Armenia">Armenia</option>
    <option <?php if(strcasecmp($selected,"Aruba")==0) echo "selected=\"selected\""; ?> value="Aruba">Aruba</option>
    <option <?php if(strcasecmp($selected,"Australia")==0) echo "selected=\"selected\""; ?> value="Australia">Australia</option>
    <option <?php if(strcasecmp($selected,"Austria")==0) echo "selected=\"selected\""; ?> value="Austria">Austria</option>
    <option <?php if(strcasecmp($selected,"Azerbaijan")==0) echo "selected=\"selected\""; ?> value="Azerbaijan">Azerbaijan</option>
    <option <?php if(strcasecmp($selected,"Bahamas")==0) echo "selected=\"selected\""; ?> value="Bahamas">Bahamas</option>
    <option <?php if(strcasecmp($selected,"Bahrain")==0) echo "selected=\"selected\""; ?> value="Bahrain">Bahrain</option>
    <option <?php if(strcasecmp($selected,"Bangladesh")==0) echo "selected=\"selected\""; ?> value="Bangladesh">Bangladesh</option>
    <option <?php if(strcasecmp($selected,"Barbados")==0) echo "selected=\"selected\""; ?> value="Barbados">Barbados</option>
    <option <?php if(strcasecmp($selected,"Belarus")==0) echo "selected=\"selected\""; ?> value="Belarus">Belarus</option>
    <option <?php if(strcasecmp($selected,"Belgium")==0) echo "selected=\"selected\""; ?> value="Belgium">Belgium</option>
    <option <?php if(strcasecmp($selected,"Belize")==0) echo "selected=\"selected\""; ?> value="Belize">Belize</option>
    <option <?php if(strcasecmp($selected,"Benin")==0) echo "selected=\"selected\""; ?> value="Benin">Benin</option>
    <option <?php if(strcasecmp($selected,"Bermuda")==0) echo "selected=\"selected\""; ?> value="Bermuda">Bermuda</option>
    <option <?php if(strcasecmp($selected,"Bhutan")==0) echo "selected=\"selected\""; ?> value="Bhutan">Bhutan</option>
    <option <?php if(strcasecmp($selected,"Bolivia")==0) echo "selected=\"selected\""; ?> value="Bolivia">Bolivia</option>
    <option <?php if(strcasecmp($selected,"Bosnia And Herzegovina")==0) echo "selected=\"selected\""; ?> value="Bosnia And Herzegovina">Bosnia And Herzegovina</option>
    <option <?php if(strcasecmp($selected,"Botswana")==0) echo "selected=\"selected\""; ?> value="Botswana">Botswana</option>
    <option <?php if(strcasecmp($selected,"Bouvet Island")==0) echo "selected=\"selected\""; ?> value="Bouvet Island">Bouvet Island</option>
    <option <?php if(strcasecmp($selected,"Brazil")==0) echo "selected=\"selected\""; ?> value="Brazil">Brazil</option>
    <option <?php if(strcasecmp($selected,"British Indian Ocean Territory")==0) echo "selected=\"selected\""; ?> value="British Indian Ocean Territory">British Indian Ocean Territory</option>
    <option <?php if(strcasecmp($selected,"British Virgin Islands")==0) echo "selected=\"selected\""; ?> value="British Virgin Islands">British Virgin Islands</option>
    <option <?php if(strcasecmp($selected,"Brunei")==0) echo "selected=\"selected\""; ?> value="Brunei">Brunei</option>
    <option <?php if(strcasecmp($selected,"Bulgaria")==0) echo "selected=\"selected\""; ?> value="Bulgaria">Bulgaria</option>
    <option <?php if(strcasecmp($selected,"Burkina Faso")==0) echo "selected=\"selected\""; ?> value="Burkina Faso">Burkina Faso</option>
    <option <?php if(strcasecmp($selected,"Burundi")==0) echo "selected=\"selected\""; ?> value="Burundi">Burundi</option>
    <option <?php if(strcasecmp($selected,"Cambodia")==0) echo "selected=\"selected\""; ?> value="Cambodia">Cambodia</option>
    <option <?php if(strcasecmp($selected,"Cameroon")==0) echo "selected=\"selected\""; ?> value="Cameroon">Cameroon</option>
    <option <?php if(strcasecmp($selected,"Canada")==0) echo "selected=\"selected\""; ?> value="Canada">Canada</option>
    <option <?php if(strcasecmp($selected,"Cape Verde")==0) echo "selected=\"selected\""; ?> value="Cape Verde">Cape Verde</option>
    <option <?php if(strcasecmp($selected,"Cayman Islands")==0) echo "selected=\"selected\""; ?> value="Cayman Islands">Cayman Islands</option>
    <option <?php if(strcasecmp($selected,"Central African Republic")==0) echo "selected=\"selected\""; ?> value="Central African Republic">Central African Republic</option>
    <option <?php if(strcasecmp($selected,"Chad")==0) echo "selected=\"selected\""; ?> value="Chad">Chad</option>
    <option <?php if(strcasecmp($selected,"Chile")==0) echo "selected=\"selected\""; ?> value="Chile">Chile</option>
    <option <?php if(strcasecmp($selected,"China")==0) echo "selected=\"selected\""; ?> value="China">China</option>
    <option <?php if(strcasecmp($selected,"Christmas Island")==0) echo "selected=\"selected\""; ?> value="Christmas Island">Christmas Island</option>
    <option <?php if(strcasecmp($selected,"Cocos (keeling) Islands")==0) echo "selected=\"selected\""; ?> value="Cocos (keeling) Islands">Cocos (keeling) Islands</option>
    <option <?php if(strcasecmp($selected,"Colombia")==0) echo "selected=\"selected\""; ?> value="Colombia">Colombia</option>
    <option <?php if(strcasecmp($selected,"Comoros")==0) echo "selected=\"selected\""; ?> value="Comoros">Comoros</option>
    <option <?php if(strcasecmp($selected,"Congo")==0) echo "selected=\"selected\""; ?> value="Congo">Congo</option>
    <option <?php if(strcasecmp($selected,"Cook Islands")==0) echo "selected=\"selected\""; ?> value="Cook Islands">Cook Islands</option>
    <option <?php if(strcasecmp($selected,"Costa Rica")==0) echo "selected=\"selected\""; ?> value="Costa Rica">Costa Rica</option>
    <option <?php if(strcasecmp($selected,"Cote d'Ivoire")==0) echo "selected=\"selected\""; ?> value="Cote d'Ivoire">Cote d'Ivoire</option>
    <option <?php if(strcasecmp($selected,"Croatia")==0) echo "selected=\"selected\""; ?> value="Croatia">Croatia</option>
    <option <?php if(strcasecmp($selected,"Cyprus")==0) echo "selected=\"selected\""; ?> value="Cyprus">Cyprus</option>
    <option <?php if(strcasecmp($selected,"Czech Republic")==0) echo "selected=\"selected\""; ?> value="Czech Republic">Czech Republic</option>
    <option <?php if(strcasecmp($selected,"Democratic Rep. of Congo")==0) echo "selected=\"selected\""; ?> value="Democratic Rep. of Congo">Democratic Rep. of Congo</option>
    <option <?php if(strcasecmp($selected,"Denmark")==0) echo "selected=\"selected\""; ?> value="Denmark">Denmark</option>
    <option <?php if(strcasecmp($selected,"Djibouti")==0) echo "selected=\"selected\""; ?> value="Djibouti">Djibouti</option>
    <option <?php if(strcasecmp($selected,"Dominica")==0) echo "selected=\"selected\""; ?> value="Dominica">Dominica</option>
    <option <?php if(strcasecmp($selected,"Dominican Republic")==0) echo "selected=\"selected\""; ?> value="Dominican Republic">Dominican Republic</option>
    <option <?php if(strcasecmp($selected,"East Timor")==0) echo "selected=\"selected\""; ?> value="East Timor">East Timor</option>
    <option <?php if(strcasecmp($selected,"Ecuador")==0) echo "selected=\"selected\""; ?> value="Ecuador">Ecuador</option>
    <option <?php if(strcasecmp($selected,"Egypt")==0) echo "selected=\"selected\""; ?> value="Egypt">Egypt</option>
    <option <?php if(strcasecmp($selected,"El Salvador")==0) echo "selected=\"selected\""; ?> value="El Salvador">El Salvador</option>
    <option <?php if(strcasecmp($selected,"England")==0) echo "selected=\"selected\""; ?> value="England">England</option>
    <option <?php if(strcasecmp($selected,"Equatorial Guinea")==0) echo "selected=\"selected\""; ?> value="Equatorial Guinea">Equatorial Guinea</option>
    <option <?php if(strcasecmp($selected,"Eritrea")==0) echo "selected=\"selected\""; ?> value="Eritrea">Eritrea</option>
    <option <?php if(strcasecmp($selected,"Estonia")==0) echo "selected=\"selected\""; ?> value="Estonia">Estonia</option>
    <option <?php if(strcasecmp($selected,"Ethiopia")==0) echo "selected=\"selected\""; ?> value="Ethiopia">Ethiopia</option>
    <option <?php if(strcasecmp($selected,"Falkland Islands (malvinas)")==0) echo "selected=\"selected\""; ?> value="Falkland Islands (malvinas)">Falkland Islands (malvinas)</option>
    <option <?php if(strcasecmp($selected,"Faroe Islands")==0) echo "selected=\"selected\""; ?> value="Faroe Islands">Faroe Islands</option>
    <option <?php if(strcasecmp($selected,"Fiji")==0) echo "selected=\"selected\""; ?> value="Fiji">Fiji</option>
    <option <?php if(strcasecmp($selected,"Finland")==0) echo "selected=\"selected\""; ?> value="Finland">Finland</option>
    <option <?php if(strcasecmp($selected,"France")==0) echo "selected=\"selected\""; ?> value="France">France</option>
    <option <?php if(strcasecmp($selected,"French Guiana")==0) echo "selected=\"selected\""; ?> value="French Guiana">French Guiana</option>
    <option <?php if(strcasecmp($selected,"French Polynesia")==0) echo "selected=\"selected\""; ?> value="French Polynesia">French Polynesia</option>
    <option <?php if(strcasecmp($selected,"French Southern Territories")==0) echo "selected=\"selected\""; ?> value="French Southern Territories">French Southern Territories</option>
    <option <?php if(strcasecmp($selected,"Gabon")==0) echo "selected=\"selected\""; ?> value="Gabon">Gabon</option>
    <option <?php if(strcasecmp($selected,"Gambia")==0) echo "selected=\"selected\""; ?> value="Gambia">Gambia</option>
    <option <?php if(strcasecmp($selected,"Georgia")==0) echo "selected=\"selected\""; ?> value="Georgia">Georgia</option>
    <option <?php if(strcasecmp($selected,"Germany")==0) echo "selected=\"selected\""; ?> value="Germany">Germany</option>
    <option <?php if(strcasecmp($selected,"Ghana")==0) echo "selected=\"selected\""; ?> value="Ghana">Ghana</option>
    <option <?php if(strcasecmp($selected,"Gibraltar")==0) echo "selected=\"selected\""; ?> value="Gibraltar">Gibraltar</option>
    <option <?php if(strcasecmp($selected,"Greece")==0) echo "selected=\"selected\""; ?> value="Greece">Greece</option>
    <option <?php if(strcasecmp($selected,"Greenland")==0) echo "selected=\"selected\""; ?> value="Greenland">Greenland</option>
    <option <?php if(strcasecmp($selected,"Grenada")==0) echo "selected=\"selected\""; ?> value="Grenada">Grenada</option>
    <option <?php if(strcasecmp($selected,"Guadeloupe")==0) echo "selected=\"selected\""; ?> value="Guadeloupe">Guadeloupe</option>
    <option <?php if(strcasecmp($selected,"Guam")==0) echo "selected=\"selected\""; ?> value="Guam">Guam</option>
    <option <?php if(strcasecmp($selected,"Guatemala")==0) echo "selected=\"selected\""; ?> value="Guatemala">Guatemala</option>
    <option <?php if(strcasecmp($selected,"Guinea")==0) echo "selected=\"selected\""; ?> value="Guinea">Guinea</option>
    <option <?php if(strcasecmp($selected,"Guinea-bissau")==0) echo "selected=\"selected\""; ?> value="Guinea-bissau">Guinea-bissau</option>
    <option <?php if(strcasecmp($selected,"Guyana")==0) echo "selected=\"selected\""; ?> value="Guyana">Guyana</option>
    <option <?php if(strcasecmp($selected,"Haiti")==0) echo "selected=\"selected\""; ?> value="Haiti">Haiti</option>
    <option <?php if(strcasecmp($selected,"Heard Island And Mcdonald Islands")==0) echo "selected=\"selected\""; ?> value="Heard Island And Mcdonald Islands">Heard Island And Mcdonald Isla</option>
    <option <?php if(strcasecmp($selected,"Holy See (vatican City State)")==0) echo "selected=\"selected\""; ?> value="Holy See (vatican City State)">Holy See (vatican City State)</option>
    <option <?php if(strcasecmp($selected,"Honduras")==0) echo "selected=\"selected\""; ?> value="Honduras">Honduras</option>
    <option <?php if(strcasecmp($selected,"Hong Kong")==0) echo "selected=\"selected\""; ?> value="Hong Kong">Hong Kong</option>
    <option <?php if(strcasecmp($selected,"Hungary")==0) echo "selected=\"selected\""; ?> value="Hungary">Hungary</option>
    <option <?php if(strcasecmp($selected,"Iceland")==0) echo "selected=\"selected\""; ?> value="Iceland">Iceland</option>
    <option <?php if(strcasecmp($selected,"India")==0) echo "selected=\"selected\""; ?> value="India">India</option>
    <option <?php if(strcasecmp($selected,"Indonesia")==0) echo "selected=\"selected\""; ?> value="Indonesia">Indonesia</option>
    <option <?php if(strcasecmp($selected,"Iraq")==0) echo "selected=\"selected\""; ?> value="Iraq">Iraq</option>
    <option <?php if(strcasecmp($selected,"Ireland")==0) echo "selected=\"selected\""; ?> value="Ireland">Ireland</option>
    <option <?php if(strcasecmp($selected,"Israel")==0) echo "selected=\"selected\""; ?> value="Israel">Israel</option>
    <option <?php if(strcasecmp($selected,"Italy")==0) echo "selected=\"selected\""; ?> value="Italy">Italy</option>
    <option <?php if(strcasecmp($selected,"Jamaica")==0) echo "selected=\"selected\""; ?> value="Jamaica">Jamaica</option>
    <option <?php if(strcasecmp($selected,"Japan")==0) echo "selected=\"selected\""; ?> value="Japan">Japan</option>
    <option <?php if(strcasecmp($selected,"Jordan")==0) echo "selected=\"selected\""; ?> value="Jordan">Jordan</option>
    <option <?php if(strcasecmp($selected,"Kazakhstan")==0) echo "selected=\"selected\""; ?> value="Kazakhstan">Kazakhstan</option>
    <option <?php if(strcasecmp($selected,"Kazakstan")==0) echo "selected=\"selected\""; ?> value="Kazakstan">Kazakstan</option>
    <option <?php if(strcasecmp($selected,"Kenya")==0) echo "selected=\"selected\""; ?> value="Kenya">Kenya</option>
    <option <?php if(strcasecmp($selected,"Kiribati")==0) echo "selected=\"selected\""; ?> value="Kiribati">Kiribati</option>
    <option <?php if(strcasecmp($selected,"Kosovo")==0) echo "selected=\"selected\""; ?> value="Kosovo">Kosovo</option>
    <option <?php if(strcasecmp($selected,"Kuwait")==0) echo "selected=\"selected\""; ?> value="Kuwait">Kuwait</option>
    <option <?php if(strcasecmp($selected,"Kyrgyzstan")==0) echo "selected=\"selected\""; ?> value="Kyrgyzstan">Kyrgyzstan</option>
    <option <?php if(strcasecmp($selected,"Laos")==0) echo "selected=\"selected\""; ?> value="Laos">Laos</option>
    <option <?php if(strcasecmp($selected,"Latvia")==0) echo "selected=\"selected\""; ?> value="Latvia">Latvia</option>
    <option <?php if(strcasecmp($selected,"Lebanon")==0) echo "selected=\"selected\""; ?> value="Lebanon">Lebanon</option>
    <option <?php if(strcasecmp($selected,"Lesotho")==0) echo "selected=\"selected\""; ?> value="Lesotho">Lesotho</option>
    <option <?php if(strcasecmp($selected,"Liberia")==0) echo "selected=\"selected\""; ?> value="Liberia">Liberia</option>
    <option <?php if(strcasecmp($selected,"Libyan Arab Jamahiriya")==0) echo "selected=\"selected\""; ?> value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
    <option <?php if(strcasecmp($selected,"Liechtenstein")==0) echo "selected=\"selected\""; ?> value="Liechtenstein">Liechtenstein</option>
    <option <?php if(strcasecmp($selected,"Lithuania")==0) echo "selected=\"selected\""; ?> value="Lithuania">Lithuania</option>
    <option <?php if(strcasecmp($selected,"Luxembourg")==0) echo "selected=\"selected\""; ?> value="Luxembourg">Luxembourg</option>
    <option <?php if(strcasecmp($selected,"Macau")==0) echo "selected=\"selected\""; ?> value="Macau">Macau</option>
    <option <?php if(strcasecmp($selected,"Macedonia")==0) echo "selected=\"selected\""; ?> value="Macedonia">Macedonia</option>
    <option <?php if(strcasecmp($selected,"Madagascar")==0) echo "selected=\"selected\""; ?> value="Madagascar">Madagascar</option>
    <option <?php if(strcasecmp($selected,"Malawi")==0) echo "selected=\"selected\""; ?> value="Malawi">Malawi</option>
    <option <?php if(strcasecmp($selected,"Malaysia")==0) echo "selected=\"selected\""; ?> value="Malaysia">Malaysia</option>
    <option <?php if(strcasecmp($selected,"Maldives")==0) echo "selected=\"selected\""; ?> value="Maldives">Maldives</option>
    <option <?php if(strcasecmp($selected,"Mali")==0) echo "selected=\"selected\""; ?> value="Mali">Mali</option>
    <option <?php if(strcasecmp($selected,"Malta")==0) echo "selected=\"selected\""; ?> value="Malta">Malta</option>
    <option <?php if(strcasecmp($selected,"Marshall Islands")==0) echo "selected=\"selected\""; ?> value="Marshall Islands">Marshall Islands</option>
    <option <?php if(strcasecmp($selected,"Martinique")==0) echo "selected=\"selected\""; ?> value="Martinique">Martinique</option>
    <option <?php if(strcasecmp($selected,"Mauritania")==0) echo "selected=\"selected\""; ?> value="Mauritania">Mauritania</option>
    <option <?php if(strcasecmp($selected,"Mauritius")==0) echo "selected=\"selected\""; ?> value="Mauritius">Mauritius</option>
    <option <?php if(strcasecmp($selected,"Mayotte")==0) echo "selected=\"selected\""; ?> value="Mayotte">Mayotte</option>
    <option <?php if(strcasecmp($selected,"Mexico")==0) echo "selected=\"selected\""; ?> value="Mexico">Mexico</option>
    <option <?php if(strcasecmp($selected,"Micronesia")==0) echo "selected=\"selected\""; ?> value="Micronesia">Micronesia</option>
    <option <?php if(strcasecmp($selected,"Moldova")==0) echo "selected=\"selected\""; ?> value="Moldova">Moldova</option>
    <option <?php if(strcasecmp($selected,"Monaco")==0) echo "selected=\"selected\""; ?> value="Monaco">Monaco</option>
    <option <?php if(strcasecmp($selected,"Mongolia")==0) echo "selected=\"selected\""; ?> value="Mongolia">Mongolia</option>
    <option <?php if(strcasecmp($selected,"Montenegro")==0) echo "selected=\"selected\""; ?> value="Montenegro">Montenegro</option>
    <option <?php if(strcasecmp($selected,"Montserrat")==0) echo "selected=\"selected\""; ?> value="Montserrat">Montserrat</option>
    <option <?php if(strcasecmp($selected,"Morocco")==0) echo "selected=\"selected\""; ?> value="Morocco">Morocco</option>
    <option <?php if(strcasecmp($selected,"Mozambique")==0) echo "selected=\"selected\""; ?> value="Mozambique">Mozambique</option>
    <option <?php if(strcasecmp($selected,"Namibia")==0) echo "selected=\"selected\""; ?> value="Namibia">Namibia</option>
    <option <?php if(strcasecmp($selected,"Nauru")==0) echo "selected=\"selected\""; ?> value="Nauru">Nauru</option>
    <option <?php if(strcasecmp($selected,"Nepal")==0) echo "selected=\"selected\""; ?> value="Nepal">Nepal</option>
    <option <?php if(strcasecmp($selected,"Netherlands")==0) echo "selected=\"selected\""; ?> value="Netherlands">Netherlands</option>
    <option <?php if(strcasecmp($selected,"Netherlands Antilles")==0) echo "selected=\"selected\""; ?> value="Netherlands Antilles">Netherlands Antilles</option>
    <option <?php if(strcasecmp($selected,"New Caledonia")==0) echo "selected=\"selected\""; ?> value="New Caledonia">New Caledonia</option>
    <option <?php if(strcasecmp($selected,"New Zealand")==0) echo "selected=\"selected\""; ?> value="New Zealand">New Zealand</option>
    <option <?php if(strcasecmp($selected,"Nicaragua")==0) echo "selected=\"selected\""; ?> value="Nicaragua">Nicaragua</option>
    <option <?php if(strcasecmp($selected,"Niger")==0) echo "selected=\"selected\""; ?> value="Niger">Niger</option>
    <option <?php if(strcasecmp($selected,"Nigeria")==0) echo "selected=\"selected\""; ?> value="Nigeria">Nigeria</option>
    <option <?php if(strcasecmp($selected,"Niue")==0) echo "selected=\"selected\""; ?> value="Niue">Niue</option>
    <option <?php if(strcasecmp($selected,"Norfolk Island")==0) echo "selected=\"selected\""; ?> value="Norfolk Island">Norfolk Island</option>
    <option <?php if(strcasecmp($selected,"Northern Ireland")==0) echo "selected=\"selected\""; ?> value="Northern Ireland">Northern Ireland</option>
    <option <?php if(strcasecmp($selected,"Northern Mariana Islands")==0) echo "selected=\"selected\""; ?> value="Northern Mariana Islands">Northern Mariana Islands</option>
    <option <?php if(strcasecmp($selected,"Norway")==0) echo "selected=\"selected\""; ?> value="Norway">Norway</option>
    <option <?php if(strcasecmp($selected,"Oman")==0) echo "selected=\"selected\""; ?> value="Oman">Oman</option>
    <option <?php if(strcasecmp($selected,"Pakistan")==0) echo "selected=\"selected\""; ?> value="Pakistan">Pakistan</option>
    <option <?php if(strcasecmp($selected,"Palau")==0) echo "selected=\"selected\""; ?> value="Palau">Palau</option>
    <option <?php if(strcasecmp($selected,"Palestine")==0) echo "selected=\"selected\""; ?> value="Palestine">Palestine</option>
    <option <?php if(strcasecmp($selected,"Panama")==0) echo "selected=\"selected\""; ?> value="Panama">Panama</option>
    <option <?php if(strcasecmp($selected,"Papua New Guinea")==0) echo "selected=\"selected\""; ?> value="Papua New Guinea">Papua New Guinea</option>
    <option <?php if(strcasecmp($selected,"Paraguay")==0) echo "selected=\"selected\""; ?> value="Paraguay">Paraguay</option>
    <option <?php if(strcasecmp($selected,"Peru")==0) echo "selected=\"selected\""; ?> value="Peru">Peru</option>
    <option <?php if(strcasecmp($selected,"Philippines")==0) echo "selected=\"selected\""; ?> value="Philippines">Philippines</option>
    <option <?php if(strcasecmp($selected,"Pitcairn")==0) echo "selected=\"selected\""; ?> value="Pitcairn">Pitcairn</option>
    <option <?php if(strcasecmp($selected,"Poland")==0) echo "selected=\"selected\""; ?> value="Poland">Poland</option>
    <option <?php if(strcasecmp($selected,"Portugal")==0) echo "selected=\"selected\""; ?> value="Portugal">Portugal</option>
    <option <?php if(strcasecmp($selected,"Puerto Rico")==0) echo "selected=\"selected\""; ?> value="Puerto Rico">Puerto Rico</option>
    <option <?php if(strcasecmp($selected,"Qatar")==0) echo "selected=\"selected\""; ?> value="Qatar">Qatar</option>
    <option <?php if(strcasecmp($selected,"Reunion")==0) echo "selected=\"selected\""; ?> value="Reunion">Reunion</option>
    <option <?php if(strcasecmp($selected,"Romania")==0) echo "selected=\"selected\""; ?> value="Romania">Romania</option>
    <option <?php if(strcasecmp($selected,"Russia")==0) echo "selected=\"selected\""; ?> value="Russia">Russia</option>
    <option <?php if(strcasecmp($selected,"Rwanda")==0) echo "selected=\"selected\""; ?> value="Rwanda">Rwanda</option>
    <option <?php if(strcasecmp($selected,"Saint Helena")==0) echo "selected=\"selected\""; ?> value="Saint Helena">Saint Helena</option>
    <option <?php if(strcasecmp($selected,"Saint Kitts And Nevis")==0) echo "selected=\"selected\""; ?> value="Saint Kitts And Nevis">Saint Kitts And Nevis</option>
    <option <?php if(strcasecmp($selected,"Saint Lucia")==0) echo "selected=\"selected\""; ?> value="Saint Lucia">Saint Lucia</option>
    <option <?php if(strcasecmp($selected,"Saint Pierre And Miquelon")==0) echo "selected=\"selected\""; ?> value="Saint Pierre And Miquelon">Saint Pierre And Miquelon</option>
    <option <?php if(strcasecmp($selected,"Saint Vincent And The Grenadines")==0) echo "selected=\"selected\""; ?> value="Saint Vincent And The Grenadines">Saint Vincent And The Grenadin</option>
    <option <?php if(strcasecmp($selected,"Samoa")==0) echo "selected=\"selected\""; ?> value="Samoa">Samoa</option>
    <option <?php if(strcasecmp($selected,"San Marino")==0) echo "selected=\"selected\""; ?> value="San Marino">San Marino</option>
    <option <?php if(strcasecmp($selected,"Sao Tome And Principe")==0) echo "selected=\"selected\""; ?> value="Sao Tome And Principe">Sao Tome And Principe</option>
    <option <?php if(strcasecmp($selected,"Saudi Arabia")==0) echo "selected=\"selected\""; ?> value="Saudi Arabia">Saudi Arabia</option>
    <option <?php if(strcasecmp($selected,"Scotland")==0) echo "selected=\"selected\""; ?> value="Scotland">Scotland</option>
    <option <?php if(strcasecmp($selected,"Senegal")==0) echo "selected=\"selected\""; ?> value="Senegal">Senegal</option>
    <option <?php if(strcasecmp($selected,"Serbia")==0) echo "selected=\"selected\""; ?> value="Serbia">Serbia</option>
    <option <?php if(strcasecmp($selected,"Seychelles")==0) echo "selected=\"selected\""; ?> value="Seychelles">Seychelles</option>
    <option <?php if(strcasecmp($selected,"Sierra Leone")==0) echo "selected=\"selected\""; ?> value="Sierra Leone">Sierra Leone</option>
    <option <?php if(strcasecmp($selected,"Singapore")==0) echo "selected=\"selected\""; ?> value="Singapore">Singapore</option>
    <option <?php if(strcasecmp($selected,"Slovakia")==0) echo "selected=\"selected\""; ?> value="Slovakia">Slovakia</option>
    <option <?php if(strcasecmp($selected,"Slovenia")==0) echo "selected=\"selected\""; ?> value="Slovenia">Slovenia</option>
    <option <?php if(strcasecmp($selected,"Solomon Islands")==0) echo "selected=\"selected\""; ?> value="Solomon Islands">Solomon Islands</option>
    <option <?php if(strcasecmp($selected,"Somalia")==0) echo "selected=\"selected\""; ?> value="Somalia">Somalia</option>
    <option <?php if(strcasecmp($selected,"South Africa")==0) echo "selected=\"selected\""; ?> value="South Africa">South Africa</option>
    <option <?php if(strcasecmp($selected,"South Korea")==0) echo "selected=\"selected\""; ?> value="South Korea">South Korea</option>
    <option <?php if(strcasecmp($selected,"Spain")==0) echo "selected=\"selected\""; ?> value="Spain">Spain</option>
    <option <?php if(strcasecmp($selected,"Sri Lanka")==0) echo "selected=\"selected\""; ?> value="Sri Lanka">Sri Lanka</option>
    <option <?php if(strcasecmp($selected,"Suriname")==0) echo "selected=\"selected\""; ?> value="Suriname">Suriname</option>
    <option <?php if(strcasecmp($selected,"Svalbard And Jan Mayen")==0) echo "selected=\"selected\""; ?> value="Svalbard And Jan Mayen">Svalbard And Jan Mayen</option>
    <option <?php if(strcasecmp($selected,"Swaziland")==0) echo "selected=\"selected\""; ?> value="Swaziland">Swaziland</option>
    <option <?php if(strcasecmp($selected,"Sweden")==0) echo "selected=\"selected\""; ?> value="Sweden">Sweden</option>
    <option <?php if(strcasecmp($selected,"Switzerland")==0) echo "selected=\"selected\""; ?> value="Switzerland">Switzerland</option>
    <option <?php if(strcasecmp($selected,"Taiwan")==0) echo "selected=\"selected\""; ?> value="Taiwan">Taiwan</option>
    <option <?php if(strcasecmp($selected,"Tajikistan")==0) echo "selected=\"selected\""; ?> value="Tajikistan">Tajikistan</option>
    <option <?php if(strcasecmp($selected,"Tanzania")==0) echo "selected=\"selected\""; ?> value="Tanzania">Tanzania</option>
    <option <?php if(strcasecmp($selected,"Thailand")==0) echo "selected=\"selected\""; ?> value="Thailand">Thailand</option>
    <option <?php if(strcasecmp($selected,"Togo")==0) echo "selected=\"selected\""; ?> value="Togo">Togo</option>
    <option <?php if(strcasecmp($selected,"Tokelau")==0) echo "selected=\"selected\""; ?> value="Tokelau">Tokelau</option>
    <option <?php if(strcasecmp($selected,"Tonga")==0) echo "selected=\"selected\""; ?> value="Tonga">Tonga</option>
    <option <?php if(strcasecmp($selected,"Trinidad and Tobago")==0) echo "selected=\"selected\""; ?> value="Trinidad and Tobago">Trinidad and Tobago</option>
    <option <?php if(strcasecmp($selected,"Tunisia")==0) echo "selected=\"selected\""; ?> value="Tunisia">Tunisia</option>
    <option <?php if(strcasecmp($selected,"Turkey")==0) echo "selected=\"selected\""; ?> value="Turkey">Turkey</option>
    <option <?php if(strcasecmp($selected,"Turkmenistan")==0) echo "selected=\"selected\""; ?> value="Turkmenistan">Turkmenistan</option>
    <option <?php if(strcasecmp($selected,"Turks And Caicos Islands")==0) echo "selected=\"selected\""; ?> value="Turks And Caicos Islands">Turks And Caicos Islands</option>
    <option <?php if(strcasecmp($selected,"Tuvalu")==0) echo "selected=\"selected\""; ?> value="Tuvalu">Tuvalu</option>
    <option <?php if(strcasecmp($selected,"Uganda")==0) echo "selected=\"selected\""; ?> value="Uganda">Uganda</option>
    <option <?php if(strcasecmp($selected,"Ukraine")==0) echo "selected=\"selected\""; ?> value="Ukraine">Ukraine</option>
    <option <?php if(strcasecmp($selected,"United Arab Emirates")==0) echo "selected=\"selected\""; ?> value="United Arab Emirates">United Arab Emirates</option>
    <option <?php if(strcasecmp($selected,"Uruguay")==0) echo "selected=\"selected\""; ?> value="Uruguay">Uruguay</option>
    <option <?php if(strcasecmp($selected,"US Virgin Islands")==0) echo "selected=\"selected\""; ?> value="US Virgin Islands">US Virgin Islands</option>
    <option <?php if(strcasecmp($selected,"USA")==0) echo "selected=\"selected\""; ?> value="USA">USA</option>
    <option <?php if(strcasecmp($selected,"Uzbekistan")==0) echo "selected=\"selected\""; ?> value="Uzbekistan">Uzbekistan</option>
    <option <?php if(strcasecmp($selected,"Vanuatu")==0) echo "selected=\"selected\""; ?> value="Vanuatu">Vanuatu</option>
    <option <?php if(strcasecmp($selected,"Venezuela")==0) echo "selected=\"selected\""; ?> value="Venezuela">Venezuela</option>
    <option <?php if(strcasecmp($selected,"Vietnam")==0) echo "selected=\"selected\""; ?> value="Vietnam">Vietnam</option>
    <option <?php if(strcasecmp($selected,"Wales")==0) echo "selected=\"selected\""; ?> value="Wales">Wales</option>
    <option <?php if(strcasecmp($selected,"Wallis And Futuna")==0) echo "selected=\"selected\""; ?> value="Wallis And Futuna">Wallis And Futuna</option>
    <option <?php if(strcasecmp($selected,"Western Sahara")==0) echo "selected=\"selected\""; ?> value="Western Sahara">Western Sahara</option>
    <option <?php if(strcasecmp($selected,"Yemen")==0) echo "selected=\"selected\""; ?> value="Yemen">Yemen</option>
    <option <?php if(strcasecmp($selected,"Zambia")==0) echo "selected=\"selected\""; ?> value="Zambia">Zambia</option>
    <option <?php if(strcasecmp($selected,"Zimbabwe")==0) echo "selected=\"selected\""; ?> value="Zimbabwe">Zimbabwe</option>
  </select>
  <?php
}
function countryCityFromIP($ipAddr)
{
  //function to find country and city from IP address

  $ipDetail = array();

  //get the XML result from hostip.info
  $xml = false;
  try
  {
    //verify the IP address for the
    ip2long($ipAddr)== -1 || ip2long($ipAddr) === false ? trigger_error("Invalid IP", E_USER_ERROR) : "";

    $xml = file_get_contents("http://api.hostip.info/?ip=".$ipAddr);

    if ($xml  === false)
    {
      throw new Exception("Problem reading http://api.hostip.info/?ip=".$ipAddr);
    }
  }
  catch(Exception $e)
  {
    log_message('error', 'countryCityFromIP(): '.$e->getMessage());
  }

  if ($xml  === false)
  {
    return false;
  }
  else
  {
    //get the city name inside the node <gml:name> and </gml:name>
    preg_match("@<Hostip>(\s)*<gml:name>(.*?)</gml:name>@si",$xml,$match);

    //assing the city name to the array
    //$ipDetail['city']=$match[2];

    //get the country name inside the node <countryName> and </countryName>
    preg_match("@<countryName>(.*?)</countryName>@si",$xml,$matches);

    //assign the country name to the $ipDetail array
    $ipDetail['country']=$matches[1];

    //get the country name inside the node <countryName> and </countryName>
    preg_match("@<countryAbbrev>(.*?)</countryAbbrev>@si",$xml,$cc_match);
    $ipDetail['country_code']=$cc_match[1]; //assing the country code to array

    //return the array containing city, country and country code
    return $ipDetail;
  }

  return false;
}

function freeGeoFromIP($ipAddr)
{
  //Bypass for now as freegeoip site seems down
  return FALSE;
  //function to find country and city from IP address

  $ipDetail = array();

  //get the XML result from hostip.info
  $xml = false;
  try
  {
    //verify the IP address for the
    ip2long($ipAddr)== -1 || ip2long($ipAddr) === false ? trigger_error("Invalid IP", E_USER_ERROR) : "";

    // Create the stream context
    $context = stream_context_create(array(
        'http' => array(
            'timeout' => 1      // Timeout in seconds
        )
    ));

    $xml = @file_get_contents("http://freegeoip.net/xml/".$ipAddr,0,$context);

    if ($xml  === false)
    {
      throw new Exception("Problem reading http://freegeoip.net/xml/".$ipAddr);
    }
  }
  catch(Exception $e)
  {
    if(function_exists('log_message'))
    {
      log_message('error', 'freeGeoFromIP: '.$e->getMessage());
    }
    else
    {
      error_log('freeGeoFromIP: '.$e->getMessage());
    }
  }

  if ($xml  === false)
  {
    return false;
  }
  else
  {
    try
    {
      $xml = simplexml_load_string($xml);
      return $xml;
    }
    catch(Exception $e)
    {

      if(function_exists('log_message'))
      {
        log_message('error', 'freeGeoFromIP: simplexml_load_string: '.$e->getMessage());
      }
      else
      {
        error_log('freeGeoFromIP: simplexml_load_string: '.$e->getMessage());
      }
    }
  }

  return false;
}


/*
 * xmlobj2arr
 */
function xmlobj2arr($Data) {
       $ret = NULL;
       if (is_object($Data)) {
               foreach (get_object_vars($Data) as $key => $val) {
                       $ret[$key] = xmlobj2arr($val);
               }
               return $ret;
       } elseif (is_array($Data)) {
               foreach ($Data as $key => $val) {
                       $ret[$key] = xmlobj2arr($val);
               }
               return $ret;
       } else {
               return $Data;
       }
}

function list_system_locales(){
    ob_start();
    system('locale -a');
    $str = ob_get_contents();
    ob_end_clean();
    return split("\\n", trim($str));
}

function debug_dump($Str, $ip = NULL)
{
  if(empty($ip) || ($_SERVER["REMOTE_ADDR"] == $ip))
  {
    print "<pre>";
    print_r( $Str );
    print "</pre>";
  }
}

/*
 * getColorRating
 *
 * This function is specific for the HB hostel view
 *
 * Will probably be better to find a better for this function in the future.
 */
function getColorRating($value)
{
	switch ($value){
		case $value >= 60:
			$color =  ' green';
			break;
		case $value > 40 && $value < 60:
			$color = ' yellow';
			break;
		case $value <= 40:
			$color = ' red';
			break;
		default:
			$color = '';
	}
	return $color;
}
?>
