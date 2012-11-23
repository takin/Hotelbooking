<?php
  if( ! function_exists('array_change_all_key_case') )
  { 
    /*
     * Taken from http://ca3.php.net/manual/en/function.array-change-key-case.php
     */
    function array_change_all_key_case(&$array, $case = CASE_LOWER, $flag_rec = false) 
    {
      $array = array_change_key_case($array, $case);
      if ( $flag_rec )
      {
        foreach ($array as $key => $value) {
            if ( is_array($value) ) {
                array_change_all_key_case($array[$key], $case, true);
            }
        }
      }
    }
  }

?>