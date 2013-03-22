<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . "/services/xml_service.php");

class Hostelbookers_feed_service extends Xml_Service {
    
    const PROPERTY_VALID = 1;
    const PROPERTY_INVALID = 0;
    
    private $existingFacilitiesCache = array();
    
    public function __construct() {
        parent::__construct();
    }
    
    public function updateAllHbProperties() {
        $startTime = microtime(true);
        $url = $this->getWebServiceUrl();
        $requestData = $this->getDataFromUrl($url);
        //$requestData = $this->getTestXml();
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
    
    private function parseXmlData($xmlData) {
        $propertiesXml = $this->getPropertiesXml($xmlData);
        
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
    
    private function getPropertiesXml($xmlData) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->strictErrorChecking = false;
        $dom->validateOnParse = false;
        $dom->recover = true;
        $dom->loadXML($xmlData);
        $propertiesXml = simplexml_import_dom($dom);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
                
        return $propertiesXml;
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
                "city_hb_id" => intval((string) $propertyXml["locationid"], 10),
                "cancellation_period" => intval((string) $propertyXml["canc"]),
                "release_unit" => intval((string) $propertyXml["release"], 10),
                "modified" => date("Y-m-d"),
                "api_sync_status" => self::PROPERTY_VALID
            ), $this->parsePropertyXmlRatings($propertyXml->ratings),
            $this->parsePropertyXmlAddress($propertyXml->add)
        );
        
        $propertyNumber = $property["property_number"];
        $prices = $this->parseXmlPrices($propertyXml->price, $propertyNumber);
        $images = $this->parseXmlImages($propertyXml->img, $propertyNumber);        
        $extras = $this->parseXmlExtras($propertyXml->opt, $propertyNumber);
        $facilities = $this->parseXmlFacilities($propertyXml->fac, $propertyNumber);
        
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
        if (!isset($pricesXml->$priceType) || !isset($pricesXml->$priceType->price)) {
            return array();
        }
        
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
        if (!isset($imagesXml->img)) return array();
        
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
    
    private function parseXmlExtras($extrasXml, $propertyNumber) {
        if (!isset($extrasXml->extra)) return array();
        
        $extras = array();
        foreach ($extrasXml->extra as $extraNode) {
            $extra = array(
                "hb_extra_id" => intval((string) $extraNode["id"], 10),
                "cost" => intval((string) $extraNode["cost"]),
                "hostel_hb_id" => intval($propertyNumber, 10),
                "api_sync_status" => self::PROPERTY_VALID
            );
            
            $extras[] = $extra;
        }
        
        return $extras;
    }
    
    /**
     * Ignores features/facilities that aren't in the hb_features table
     */
    private function parseXmlFacilities($facilitiesXml, $propertyNumber) {
        if (!isset($facilitiesXml->fac)) return array();
        
        $facilities = array();
        foreach($facilitiesXml->fac as $facilityNode) {
            $facilityId = intval($facilityNode["id"], 10);
            if (empty($facilityId)) {
                continue;
            } else if (!$this->doesFacilityExist($facilityId)) {
                continue;
            }  else {
                $this->existingFacilitiesCache[$facilityId] = $facilityId;
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
    
    private function doesFacilityExist($facilityId) {
        if (isset($this->existingFacilitiesCache[$facilityId])) return true;
        $feature = $this->ci->db_hb_hostel->get_feature_by_id($facilityId);
        if (empty($feature)) {
            return false;
        }
        else return true;
    }
    
    private function insertOrUpdatePropertiesDataInDb($propertiesData) {
        $this->updateSyncStatus(self::PROPERTY_INVALID);
        $startTime = microtime(true);
        foreach($propertiesData as $propertyData) {
            $property = $propertyData["property"];
 
            // Ignore hostels in cities that don't exist
            $cityId = $property["city_hb_id"];
            if (! $this->doesCityExist($cityId)) continue;
            
            $hostelId = $this->ci->db_hb_hostel->get_hostel_id(
                    $property["property_number"]);
            
            if (isset($hostelId) && !empty($hostelId)) {
                $property["hb_hostel_id"] = $hostelId;
                $this->updatePropertyDataInDb($propertyData);
            } else {
                $this->insertPropertyDataInDb($propertyData);
            }
        }
        $endTime = microtime(true);
        
        $this->logAudit("HB XML Service - Inserted/updated all properties/prices in DB",
                $startTime, $endTime);
    }
    
    private function doesCityExist($cityId) {
        $this->ci->load->model("db_hb_city");
        $city = $this->ci->db_hb_city->get_hb_city_from_hbid($cityId);
        if (empty($city)) {
            return false;
        }
        else return true;
    }
    
    private function updateSyncStatus($status) {
        $this->ci->db_hb_hostel->update_sync_status($status);
        $this->ci->db_hb_hostel->update_features_status($status);
        $this->ci->db_hb_hostel->update_extras_status($status);
    }
    
    private function updatePropertyDataInDb(array $propertyData) {
        $property = $propertyData["property"];
        $propertyNumber = $property["property_number"];
        unset($property["city_hb_id"]);
        
        try {
            $this->ci->db_hb_hostel->update_hostel_from_array($property);
            
            $this->ci->db_hb_hostel->delete_all_prices_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_prices($propertyData["prices"]);
            
            $this->ci->db_hb_hostel->delete_all_images_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_hb_images($propertyData["images"]);
            
            $this->ci->db_hb_hostel->delete_all_extras_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_hb_extras_to_hostel_from_array($propertyData["extras"]);
            
            $this->ci->db_hb_hostel->delete_all_facilities_for_property($propertyNumber);
            $this->ci->db_hb_hostel->insert_hb_facilities($propertyData["facilities"]);
        } catch(Exception $e) {
            $msg = sprintf("%s error: updating hostel (property_number %s) in database failed. %s \n %s", 
                    __FUNCTION__, $propertyNumber, $e->getMessage(), $e->getTraceAsString());
            log_message("error", $msg);
            $this->ci->custom_log->log($this->log_filename, $msg);
        }
    }
    
    private function insertPropertyDataInDb(array $propertyData) {
        $property = $propertyData["property"];
        $prices = $propertyData["prices"];
        $property["added"] = date("Y-m-d");
        
        if (isset($property["hb_hostel_id"])) unset($property["hb_hostel_id"]);
        
        try {
            $this->ci->db_hb_hostel->insert_hostel_from_array($property);
            $this->ci->db_hb_hostel->insert_prices($prices);
            $this->ci->db_hb_hostel->insert_hb_images($propertyData["images"]);
            $this->ci->db_hb_hostel->insert_hb_extras_to_hostel_from_array($propertyData["extras"]);
            $this->ci->db_hb_hostel->insert_hb_facilities($propertyData["facilities"]);
        } catch(Exception $e) {
            $msg = sprintf("%s error: inserting hostel (property_number %s) into database failed. %s \n %s", 
                    __FUNCTION__, $property["property_number"], $e->getMessage(),
                    $e->getTraceAsString());
            log_message("Error", $msg);
            $this->ci->custom_log->log($this->log_filename, $msg);
        }
    }
    
    private function getTestXml() {
        /*
         * Cases:
         *  1. Standard update - property number 1026
         *  2. Standard insert - property number 353535
         *  3. Location doesn't exist - property number 363636
         *  4. No facilities or extras - property number 373737
         *  5. Facility doesn't exist + no extras - property number 383838
         */
        return '<properties>
                    <property type="Hostel" id="1026" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test Clown and Bard Prague</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="1"/><fac id="6"/><fac id="7"/><fac id="8"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property>
                    <property type="Hostel" id="353535" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test Clown and Bard Prague</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">8.00</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="1"/><fac id="6"/><fac id="7"/><fac id="8"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property>
                    <property type="Hostel" id="363636" canc="2" locationid="353535" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test City Doesnt Exist</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="1"/><fac id="6"/><fac id="7"/><fac id="8"/><fac id="10"/><fac id="11"/><fac id="12"/><fac id="15"/><fac id="17"/><fac id="18"/><fac id="20"/><fac id="44"/><fac id="63"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property>
                    <property type="Hostel" id="373737" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test no extras or facilities</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac></fac><opt></opt></property>
                    <property type="Hostel" id="383838" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test no extras or facilities</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="2"/></fac><opt></opt></property>
                </properties>';
    }
}

