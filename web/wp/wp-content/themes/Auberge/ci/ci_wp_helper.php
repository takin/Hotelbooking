<?php
if ( ! function_exists('url_title'))
{
  function url_title($str, $separator = 'dash', $lowercase = FALSE)
  {
    if ($separator == 'dash')
    {
      $search   = '_';
      $replace  = '-';
    }
    else
    {
      $search   = '-';
      $replace  = '_';
    }
  
    $trans = array(
            '&\#\d+?;'        => '',
            '&\S+?;'        => '',
            '\s+'         => $replace,
            '[^a-z0-9\-\._]'    => '',
            $replace.'+'      => $replace,
            $replace.'$'      => $replace,
            '^'.$replace      => $replace,
            '\.+$'          => ''
            );
  
    $str = strip_tags($str);
  
    foreach ($trans as $key => $val)
    {
      $str = preg_replace("#".$key."#i", $val, $str);
    }
  
    if ($lowercase === TRUE)
    {
      $str = strtolower($str);
    }
    
    return trim(stripslashes($str));
  }
}
?>