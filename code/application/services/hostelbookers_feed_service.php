<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Hostelbookers_feed_service {
    
    public $errors = array();
    
    private $auditor;
    private $ci;
    private $xmlService;
    private $existingFacilitiesCache = array();
    private $imageUrlPrefix = "http://assets.hb-assets.com";
    
    public function __construct() {
        $this->ci = &get_instance();

        require_once(APPPATH . "/services/audit_service.php");
        $this->auditor = new Audit_Service();
        
        require_once(APPPATH . "/services/xml_service.php");
        $this->xmlService = new Xml_Service();
        
        $this->ci->load->model("db_hb_hostel");
        $this->ci->load->library("custom_log");
        $this->log_filename = "hb_cache_staticfeeds-" . date("Y-m");
    }
    
    public function updateAllHbProperties() {
        $startTime = microtime(true);
        $url = $this->getWebServiceUrl();
        $requestData = $this->xmlService->getDataFromUrl($url);
        $propertiesData = $this->parseXmlData($requestData);
        $this->insertOrUpdatePropertiesDataInDb($propertiesData);
        $endTime = microtime(true);
        
        $this->auditor->log("HB XML Service - updated all property details", 
                $startTime, $endTime);
    }
    
    private function getWebServiceUrl() {
        $url = sprintf("%s-[%s]-[%s].xml",
                "http://feeds.hostelbookers.com/generic/Property",
                date("Y"), date("m"));
        
        return $url;
    }
    
    private function parseXmlData($xmlData) {
        $propertiesXml = $this->xmlService->getXmlObject($xmlData);
        
        $properties = array();
        
        $startTime = microtime(true);
        foreach($propertiesXml->property as $propertyXml) {
            $property = $this->parsePropertyXml($propertyXml);            
            $properties[] = $property;
        }
        $endTime = microtime(true);
        $this->auditor->log("HB XML Service - Parsed monthly property details feed",
                $startTime, $endTime);
                
        return $properties;
    }
    
    private function parsePropertyXml($propertyXml) {        
        $property = array_merge(
            array(
                "property_name" => (string) $propertyXml->name,
                "rating_overall" => floatval((string) $propertyXml->total),
                "geo_latitude" => floatval((string) $propertyXml->lat),
                "geo_longitude" => floatval((string) $propertyXml->lon),
                "map_url" => (string) $propertyXml->map,
                "property_number" => intval((string) $propertyXml["id"], 10),
                "property_type" => (string) $propertyXml["type"],
                "city_hb_id" => intval((string) $propertyXml["locationid"], 10),
                "cancellation_period" => intval((string) $propertyXml["canc"]),
                "release_unit" => intval((string) $propertyXml["release"], 10),
                "modified" => date("Y-m-d"),
                "api_sync_status" => 1
            ), $this->parsePropertyXmlRatings($propertyXml->rating),
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
            "rating_atmosphere" => floatval((string) $ratingsXml->atmos),
            "rating_staff" => floatval((string) $ratingsXml->staff),
            "rating_location" => floatval((string) $ratingsXml->loc),
            "rating_cleanliness" => floatval((string) $ratingsXml->clean),
            "rating_facilities" => floatval((string) $ratingsXml->facil),
            "rating_safety" => floatval((string) $ratingsXml->safety),
            "rating_value" => floatval((string) $ratingsXml->value),
        );
        
        return $ratings;
    }
    
    private function parsePropertyXmlAddress($addressXml) {
        $address = array(
            "address1" => (string) $addressXml->add1,
            "address2" => (string) $addressXml->add2,
            "address3" => (string) $addressXml->add3,
            "zip" => (string) $addressXml->zip,
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
                "currency_code" => (string) $priceNode["c"],
                "bed_price" => floatval((string) $priceNode),
                "type" => $priceType,
                "hostel_hb_id" => intval((string) $propertyNumber, 10)
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
                "url" => $this->imageUrlPrefix . (string) $imgNode,
                "api_sync_status" => 1
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
                "hostel_hb_id" => intval((string) $propertyNumber, 10),
                "api_sync_status" => 1
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
            $facilityId = intval((string) $facilityNode["id"], 10);
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
        $startTime = microtime(true);
        $this->ci->db_hb_hostel->update_hb_hostel_sync_status(Db_hb_hostel::PROPERTY_INVALID);
        
        foreach ($propertiesData as $propertyData) {
            try {
                $this->ci->db_hb_hostel->insert_or_update_hb_hostel_data($propertyData);
            } catch(Exception $e) {
                $property = $propertyData["property"];
                $msg = sprintf("%s error: inserting or updating hostel (property_number %s) 
                        into database failed. %s \n %s", __FUNCTION__, 
                        $property["property_number"], $e->getMessage(), $e->getTraceAsString());
                log_message("Error", $msg);
                $this->ci->custom_log->log($this->log_filename, $msg);
                $this->errors[] = $msg;
            }
        }
        
        $endTime = microtime(true);
        
        $this->auditor->log("HB XML Service - Inserted/updated all properties/prices in DB",
                $startTime, $endTime);
    }
    
    public function getErrors() {
        return array_merge($this->errors, $this->xmlService->errors);
    }
    
    private function getTestXml() {
        /*
         * Cases:
         *  1. Update - property number 1026
         *  2. Insert - property number 353535 - hostel just created
         *  3. Location doesn't exist - property number 363636
         *  4. No facilities or extras - property number 373737
         *  5. Facility doesn't exist + no extras - property number 383838
         */
        return '<properties>
                    <property type="Hostel" id="1026" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test Update</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="1"/><fac id="6"/><fac id="7"/><fac id="8"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property>
                    <property type="Hostel" id="353535" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test Insert</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">8.00</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="1"/><fac id="6"/><fac id="7"/><fac id="8"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property>
                    <property type="Hostel" id="363636" canc="2" locationid="353535" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test City Doesnt Exist</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="1"/><fac id="6"/><fac id="7"/><fac id="8"/><fac id="10"/><fac id="11"/><fac id="12"/><fac id="15"/><fac id="17"/><fac id="18"/><fac id="20"/><fac id="44"/><fac id="63"/></fac><opt><extra id="1" cost="-1.0000"/><extra id="2" cost="0.0000"/><extra id="3" cost="50.0000"/><extra id="4" cost="0.0000"/><extra id="5" cost="0.0000"/></opt></property>
                    <property type="Hostel" id="373737" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test no extras or facilities</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac></fac><opt></opt></property>
                    <property type="Hostel" id="383838" canc="2" locationid="1014" countryid="13" checkin="11:00" checkout="11:00" release="24"><name>Test no extras or facilities</name><price><shared><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></shared><private><price c="AUD">6.22453200</price><price c="EUR">4.86985200</price><price c="GBP">4.19442000</price><price c="USD">6.36000000</price></private></price><lat>5.008267380000000e+001</lat><lon>1.444668730000000e+001</lon><rating><totalrating>302</totalrating><total>73.25714</total><atmos>76.00</atmos><staff>84.00</staff><loc>59.20</loc><clean>72.00</clean><facil>64.80</facil><safety>75.20</safety><value>81.60</value></rating><add><add1>Borivojova 102</add1><add2>Prague 3, Zizkov</add2><add3/><zip>13000</zip></add><img><img>/p/1000/1026-20120903040914.jpg</img><img>/p/1000/1026-20120903030934.JPG</img></img><fac><fac id="2"/></fac><opt></opt></property>
                </properties>';
    }
}