<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . "/services/xml_service.php");

class Hostelbookers_feed_service extends Xml_Service {
    
    const PROPERTY_VALID = 1;
    const PROPERTY_INVALID = 0;
    
    private $existingFacilities = array();
    
    public function __construct() {
        parent::__construct();
    }
    
    public function updateAllHbProperties() {
        $startTime = microtime(true);
        $this->ci->load->model("db_hb_hostel");
        $url = $this->getWebServiceUrl();
        //$requestData = $this->getDataFromUrl($url);
        $requestData = $this->getTestUpdateXml();
        $propertiesData = $this->parseXmlData($requestData);
        $this->insertOrUpdatePropertiesDataInDb($propertiesData);
        $endTime = microtime(true);
        
        $this->logAudit("HB XML Service - updated all property details", 
                $startTime, $endTime);
    }
    
    private function getWebServiceUrl() {
        $url = sprintf("%s-[%s]-[%s].xml",
                "http://feeds.hostelbookers.com/generic/Property",
                date("Y"), date("m"));
        
        return $url;
    }
    
    private function parseXmlData(&$xmlData) {
        $propertiesXml = simplexml_load_string($xmlData);
        
        $properties = array();
        
        $startTime = microtime(true);
        foreach($propertiesXml->property as $propertyXml) {
            $property = $this->parsePropertyXml($propertyXml);            
            $properties[] = $property;
        }
        $endTime = microtime(true);
        $this->logAudit("HB XML Service - Parsed monthly property details feed",
                $startTime, $endTime);
                
        return $properties;
    }
    
    private function parsePropertyXml($propertyXml) {        
        $property = array_merge(
            array(
                "property_name" => $this->trimCDataTag($propertyXml->name),
                "rating_overall" => floatval($this->trimCDataTag($propertyXml->total)),
                "geo_latitude" => floatval($this->trimCDataTag($propertyXml->lat)),
                "geo_longitude" => floatval($this->trimCDataTag($propertyXml->lon)),
                "map_url" => $this->trimCDataTag($propertyXml->map),
                "property_number" => intval((string) $propertyXml["id"], 10),
                "property_type" => (string) $propertyXml["type"],
         //       "checkout" => (string) $propertyXml["checkout"],
         //       "checkin" => (string) $propertyXml["checkin"],
                "city_hb_id" => (string) $propertyXml["locationid"],
                "cancellation_period" => (string) $propertyXml["canc"],
                "release_unit" => (string) $propertyXml["release"],
                "modified" => date("Y-m-d"),
                "api_sync_status" => self::PROPERTY_VALID
            ), $this->parsePropertyXmlRatings($propertyXml->ratings),
            $this->parsePropertyXmlAddress($propertyXml->add)
        );
        
        $propertyNumber = $property["property_number"];
        $prices = $this->parseXmlPrices($propertyXml->price, $propertyNumber);
        $images = $this->parseXmlImages($propertyXml->img, $propertyNumber);        
        $extras = $this->parseXmlExtras($propertyXml->facilities, $propertyNumber);
        $facilities = $this->parseXmlFacilities($propertyXml->facilities, $propertyNumber);
        
        return array(
            "property" => $property,
            "prices" => $prices,
            "images" => $images,
            "extras" => $extras,
            "facilities" => $facilities,
        );
    }
    
    private function parsePropertyXmlRatings($ratingsXml) {
        // ratings_overall is parsed in the calling method
        $ratings = array(
            "rating_atmosphere" => floatval($this->trimCDataTag($ratingsXml->atmos)),
            "rating_staff" => floatval($this->trimCDataTag($ratingsXml->staff)),
            "rating_location" => floatval($this->trimCDataTag($ratingsXml->loc)),
            "rating_cleanliness" => floatval($this->trimCDataTag($ratingsXml->clean)),
            "rating_facilities" => floatval($this->trimCDataTag($ratingsXml->facil)),
            "rating_safety" => floatval($this->trimCDataTag($ratingsXml->safety)),
            "rating_value" => floatval($this->trimCDataTag($ratingsXml->value)),
        );
        
        return $ratings;
    }
    
    private function parsePropertyXmlAddress($addressXml) {
        $address = array(
            "address1" => $this->trimCDataTag($addressXml->add1),
            "address2" => $this->trimCDataTag($addressXml->add2),
            "address3" => $this->trimCDataTag($addressXml->add3),
            "zip" => $this->trimCDataTag($addressXml->zip),
        );
        
        return $address;
    }
    
    private function parseXmlPrices($pricesXml, $propertyNumber) {
        $shared = $this->parseXmlPriceType($pricesXml, 'shared', $propertyNumber);
        $private = $this->parseXmlPriceType($pricesXml, 'private', $propertyNumber);
        
        return array_merge($shared, $private);
    }
    
    private function parseXmlPriceType($pricesXml, $priceType, $propertyNumber) {
        /* TODO: Do an empty check? */
        $prices = array();
        
        foreach($pricesXml->$priceType->price as $priceNode) {
            $price = array(
                "currency_code" => $this->ci->db->escape((string) $priceNode["c"]),
                "bed_price" => floatval((string) $priceNode),
                "type" => $this->ci->db->escape((string) $priceType),
                "hostel_hb_id" => intval($propertyNumber, 10)
            );
            
            $prices[] = $price;
        }
            
        return $prices;
    }
    
    private function parseXmlImages($imagesXml, $propertyNumber) {
        $images = array();
        
        foreach ($imagesXml->img as $imgNode) {
            $img = array(
                "hostel_hb_id" => $propertyNumber,
                "url" => (string) $imgNode,
                "api_sync_status" => self::PROPERTY_VALID
            );
            
            $images[] = $img;
        }
        
        return $images;
    }
    
    private function parseXmlExtras($facilitiesXml, $propertyNumber) {
        $extrasXml = $facilitiesXml->xpath("//extra");
        
        $extras = array();
        foreach ($extrasXml as $extraNode) {
            $extra = array(
                "hb_extra_id" => intval((string) $extraNode["id"], 10),
                "cost" => intval((string) $extraNode["cost"]),
                "hostel_hb_id" => $propertyNumber,
                "api_sync_status" => self::PROPERTY_VALID
            );
            
            $extras[] = $extra;
        }
        
        return $extras;
    }
    
    /**
     * Ignores facilities that aren't in the hb_features table
     */
    private function parseXmlFacilities($facilitiesXml, $propertyNumber) {
        $facilitiesXml = $facilitiesXml->xpath("//fac");
        
        $facilities = array();
        foreach($facilitiesXml as $facilityNode) {
            $facilityId = intval($facilityNode["id"], 10);
            if (empty($facilityId)) {
                continue;
            }
            
            if (!isset($this->existingFacilities[$facilityId]) &&
                    !$this->ci->db_hb_hostel->get_feature_by_id($facilityId)) {
                continue;
            }  else {
                $this->existingFacilities[$facilityId] = $facilityId;
            }
            
            $facility = array(
                "hostel_hb_id" => $propertyNumber,
                "hb_feature_id" => $facilityId,
                "api_sync_status" => 1
            );
            
            $facilities[] = $facility;
        }
        
        return $facilities;
    }
    
    private function insertOrUpdatePropertiesDataInDb($propertiesData) {
        $this->updateSyncStatus(self::PROPERTY_INVALID);
        $startTime = microtime(true);
        foreach($propertiesData as $propertyData) {
            $property = $propertyData["property"];
            $hostelId = $this->ci->db_hb_hostel->get_hostel_id(
                    $property["property_number"]);
        
            if (isset($hostelId)) $property["hb_hostel_id"] = $hostelId;
            
            if (!empty($hostelId)) {
                $this->updatePropertyDataInDb($propertyData);
            } else {
                $this->insertPropertyDataInDb($propertyData);
            }
        }
        $endTime = microtime(true);
        
        $this->logAudit("HB XML Service - Inserted/updated all properties/prices in DB",
                $startTime, $endTime);
    }
    
    private function updateSyncStatus($status) {
        $this->ci->db_hb_hostel->update_sync_status($status);
        $this->ci->db_hb_hostel->update_features_status($status);
        $this->ci->db_hb_hostel->update_extras_status($status);
    }
    
    private function updatePropertyDataInDb(array $propertyData) {
        $property = $propertyData["property"];
        $propertyNumber = $property["property_number"];
            
        try {
            $this->ci->db_hb_hostel->update_hostel($property);
            
            // Deleting because this is a comprehensive feed and I don't think the pks are ever referenced
            $this->ci->db_hb_hostel->delete_all_prices_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_or_update_hb_prices(
                    $propertyNumber, $propertyData["prices"]);
            
            $this->ci->db_hb_hostel->delete_all_images_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_hb_images($propertyData["images"]);
            
            $this->ci->db_hb_hostel->delete_all_extras_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_hb_extras_to_hostel($propertyData["extras"]);
            
            $this->ci->db_hb_hostel->delete_all_facilities_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_hb_facilities($propertyData["facilities"]);
        } catch(Exception $e) {
            log_message("error", 
                sprintf("%s error: updating hostel (property_number %s) in database failed. %s", 
                    __FUNCTION__, $propertyNumber, $e->message));
        }
    }
    
    private function insertPropertyDataInDb(array $propertyData) {
        $property = $propertyData["property"];
        $prices = $propertyData["prices"];
        $propertyNumber = $property["property_number"];
        
        if (isset($property["hb_hostel_id"])) unset($property["hb_hostel_id"]);
        
        try {
            $this->ci->db_hb_hostel->insert_hostel($property);
            $this->ci->db_hb_hostel->insert_or_update_hb_prices($propertyNumber, $prices);
            $this->ci->db_hb_hostel->insert_or_update_hb_prices(
                    $propertyNumber, $propertyData["prices"]);
            $this->ci->db_hb_hostel->insert_hb_images($propertyData["images"]);
            $this->ci->db_hb_hostel->insert_hb_extras_to_hostel($propertyData["extras"]);
            $this->ci->db_hb_hostel->insert_or_update_hb_facilities(
                    $propertyNumber, $propertyData["facilities"]);
            $this->ci->db_hb_hostel->insert_hb_facilities($propertyData["facilities"]);
        } catch(Exception $e) {
            $this->logAudit(sprintf(
                    "HB XML Service - error inserting hostel: %s", $e->getMessage()),
                    0, 0);
            log_message("Error", 
                sprintf("%s error: inserting hostel (property_number %s) into database failed. %s", 
                    __FUNCTION__, $property["property_number"], $e->message));
        }
    }
    
    private function getTestUpdateXml() {
        return '<properties><property type="Hostel" id="1026" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Kyle Clown and Bard Prague</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img><img>/p/1000/1026-20120903030956.JPG</img><img>/p/1000/1026-20120903020928.JPG</img><img>/p/1000/1026-20120903060907.jpg</img><img>/p/1000/1026-20120903050948.jpg</img><img>/p/1000/1026-20120903050952.jpg</img><img>/p/1000/1026-20120903050958.JPG</img><img>/p/1000/1026-20120903050943.JPG</img><img>/p/1000/1026-20120903030909.JPG</img><img>/p/1000/1026-20120903050954.JPG</img></img><fac><fac id="2"/><fac id="6"/><fac id="7"/><fac id="8"/><fac id="10"/><fac id="11"/><fac id="12"/><fac id="15"/><fac id="17"/><fac id="18"/><fac id="20"/><fac id="44"/><fac id="63"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property></properties>';        
    }
    
    private function getTestInsertXml() {
        return '<properties><property type="Hostel" id="343434" canc="4" locationid="1833" countryid="9" release="24"><name>Kyle Sofia Hostel</name><price><shared><price c="AUD">14.06391900</price><price c="EUR">11.00310900</price><price c="GBP">9.47701500</price><price c="USD">14.37000000</price></shared><private><price c="AUD">0.00000000</price><price c="EUR">0.00000000</price><price c="GBP">0.00000000</price><price c="USD">0.00000000</price></private></price><lat>4.269623800000000e+001</lat><lon>2.331893000000000e+001</lon><rating><totalrating>7</totalrating><total>72.24489</total><atmos>74.29</atmos><staff>85.71</staff><loc>82.86</loc><clean>65.71</clean><facil>65.71</facil><safety>60.00</safety><value>71.43</value></rating><add><add1>16 Pozitano</add1><add2>Sofia 1000</add2><add3/><zip>1000</zip></add><map>http://www.multimap.com/map/browse.cgi?GridE=&amp;GridN=&amp;client=public&amp;lon=23.3319&amp;lat=42.7073&amp;scale=1000000&amp;place=Sofia,+,+Bulgaria&amp;db=w3&amp;local=&amp;type=&amp;start=&amp;coordsys=&amp;limit=&amp;overviewmap=</map><img><img>/p/1000/1004-20120424092015.jpg</img><img>/p/1000/1004-20120518022957.jpg</img></img><fac><fac id="1"/><fac id="5"/><fac id="6"/><fac id="11"/><fac id="12"/><fac id="15"/><fac id="18"/><fac id="21"/></fac><opt><extra id="2" cost="0.0000"/><extra id="3" cost="0.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property></properties>';
    }
}

