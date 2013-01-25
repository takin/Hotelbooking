<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Mobile
 *
 * Mobile library for code igniter
 *
 * @package   Mobile
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Mobile
{
  /*
   * Function that builds a google map link for mobile browsers
   */
  function google_map_link($title, $lat, $long, $lang = "en")
  {
    return "https://maps.google.com/maps?q=".urlencode($title)."&hl=".$lang."&ll=".$lat.",".$long."&spn=0.010334,0.033023&sll=37.0625,-95.677068&sspn=51.222969,135.263672&t=h&z=16";
  }

  function map_link($title, $lat, $long)
  {
    return site_url("map?title=".urlencode($title)."&lat=".$lat."&lng=".$long);
  }

}

?>
