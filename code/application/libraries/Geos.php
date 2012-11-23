<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Geos
 *
 * Geos library for code igniter
 *
 * @package   Geos
 * @author    Louis-Michel Raynauld
 * @version   0.1
 * @license   Commercial
 */
class Geos
{
  //Because the Earth is not perfectly spherical, no single value serves as its natural radius.
  //Distances from points on the surface to the center range from 6,353 km to 6,384 km
  //Several different ways of modeling the Earth as a sphere each yield a convenient mean radius of 6,371 km (≈3,959 mi).
  private $earth_radius = 6371;

  /**
   * Constructor
   *
   * @access  public
   */
  public function __construct()
  {
    log_message('debug', "Geos Class Initialized");
  }

  //Based on the Haversine Formula
  //http://en.wikipedia.org/wiki/Haversine_formula
  //http://www.codecodex.com/wiki/Calculate_Distance_Between_Two_Points_on_a_Globe
  //+/- 1 KM
  public function get_distance($latitude1, $longitude1, $latitude2, $longitude2) {

    $dLat = deg2rad($latitude2 - $latitude1);
    $dLon = deg2rad($longitude2 - $longitude1);

    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
    $c = 2 * asin(sqrt($a));
    $d = $this->earth_radius * $c;

    return $d;
  }
}

?>