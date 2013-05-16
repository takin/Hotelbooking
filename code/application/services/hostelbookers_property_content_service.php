<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . "/services/xml_service.php");

class Hostelbookers_Property_Content_Service {
    
    public $errors = array();
    public $successCount = 0;
    public $failureCount = 0;
    
    private $urlsByLangCode = array();
    private $ci;
    private $auditor;
    private $xmlService;
    
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
    
    public function updateShortDescriptions($languageCodes) {
        $this->urlsByLangCode = $this->getUrlsFromLanguageCodes($languageCodes);
        
        foreach ($this->urlsByLangCode as $langCode => $url) {
            $startTime = microtime(true);
            
            $requestData = $this->xmlService->getDataFromUrl($url);
            $propertiesData = $this->parseXmlData($requestData, $langCode);
            $this->insertOrUpdatePropertiesInDb($propertiesData);

            $endTime = microtime(true);

            $this->auditor->log("HB XML Service - updated all property descriptions 
                    for $langCode language", $startTime, $endTime);
        }
    }
    
    private function getUrlsFromLanguageCodes($languageCodes) {
        $urlsByLangCodes = array();
        foreach ($languageCodes as $langCode) {
            $url = sprintf("%s-[%s]-[%s]-[%s].xml",
                            "http://feeds.hostelbookers.com/generic/PropertyContent",
                            $langCode, date("Y"), date("m"));
            
            $urlsByLangCodes[strtolower($langCode)] = $url;
        }
        
        return $urlsByLangCodes;
    }
    
    private function parseXmlData($xmlData, $langCode) {
        if (empty($xmlData)) return array();
        
        $startTime = microtime(true);
        $propertiesXml = $this->xmlService->getXmlObject($xmlData);
        
        $properties = array();
        foreach($propertiesXml->property as $propertyXml) {
            $property = $this->parsePropertyXml($propertyXml, $langCode);            
            $properties[] = $property;
        }
        $endTime = microtime(true);
        $this->auditor->log("HB Property Content XML Service - Parsed monthly property content feed",
                $startTime, $endTime);
                
        return $properties;
    }
    
    private function parsePropertyXml($propertyXml, $langCode) {
        $property = array(
            "hostel_hb_id" => intval((string) $propertyXml["ID"], 10),
            "short_description" => (string) $propertyXml->overview,
            "language" => $langCode,
        );
        
        return $property;
    }
    
    private function insertOrUpdatePropertiesInDb($propertiesData) {
        $startTime = microtime(true);
        foreach($propertiesData as $propertyContent) {
            $this->insertOrUpdateDescriptionInDb($propertyContent);
        }
        $endTime = microtime(true);
        
        $this->auditor->log("HB XML Service - Inserted/updated all property descriptions in DB",
                $startTime, $endTime);
    }
    
    private function insertOrUpdateDescriptionInDb($propertyContent) {
        $propertyNumber = $propertyContent["hostel_hb_id"];
        $langCode = $propertyContent["language"];
        $description = $propertyContent["short_description"];
        
        try {
            $this->ci->db_hb_hostel->update_hb_translations($propertyNumber, $propertyContent);
            $this->successCount++;
        } catch(Exception $e) {
            $msg = sprintf("%s error: inserting/updating hostel description (property_number %s) in database. %s \n %s", 
                    __FUNCTION__, $propertyNumber, $e->getMessage(), $e->getTraceAsString());
            log_message("error", $msg);
            $this->ci->custom_log->log($this->log_filename, $msg);
            $this->errors[] = $msg;
            $this->failureCount++;
        }
    }
    
    public function getErrors() {
        return array_merge($this->errors, $this->xmlService->errors);
    }
    
    public function getUpdatedUrlsString() {
        return implode(", ", $this->urlsByLangCode);
    }
    
    public function getUrlSuccessCount() {
        return $this->xmlService->successCount;
    }
    
    public function getUrlFailureCount() {
        return $this->xmlService->failureCount;
    }
    
    private function getTestXml() {
        /*
         * Cases:
         *   Update - property number 1026
         *   Hostel Doesn't exist - property number 41536
         *   Insert - property number 353535
         */
        $this->xmlService->successCount++;
        return '<properties>
                    <property ID="1026"><overview><![CDATA[Updating Hostel\'s Description: --- Basel St. Alban enjoys a location near the centre of Basel and is the ideal place to stay to explore this historic city!]]></overview><info><![CDATA[The newly renovated, modern youth hostel provides 234 beds in a variety of room types (double room to six-bed rooms). It is glorious to stay here in this quiet, green, sleepy oasis of Basel, not far from the river Rhine. We have a reception and front desk open 24/7 where our warm and friendly staff is on hand to assist you throughout your stay. Facilities include a common area with a bar, a conference room, games room, kiosk, washing facilities and internet corner. **PLEASE NOTE: There is an extra charge if you are not a member of any Youth Hostel Association of CHF 6.00 per night, payable on arrival**]]></info><desc><![CDATA[Check-in at any time, rooms are ready from 3 pm heck-out until 10 am 21 double rooms with shower/WC 3 rooms with 4 beds (family rooms) and shower/WC 36 rooms with 4 beds and washbasin 6 rooms with 6 beds and washbasin A total of 234 beds. Showers/WC on the floor. Catering Breakfast buffet (incl.) Lunch and evening meal (3-course menu)  Vegetarian meals Special events and meals for groups on request Bar service snacks 24 hours a day Infrastructure Large lobby  Snack bar 1 conference room (22 seats) Extensive seminar equipment (video projector, TV/DVD, etc.) Kiosk with souvenirs (Swatch, Victorinox, Wenger, SYH merchandising, etc.) Bicycle cellar Internet corner WiFi Suitable for wheelchairs]]></desc><location><![CDATA[The youth hostel is situated in the venerable St Alban quarter, the original home of the Basler "Daig", as the old established Basler families are called. Behind the building flows the St Alban Canal, which earlier linked the various factories in the quarter to the Rhine. From the hostel it’s only a 10 minutes walk to the effervescence of Basel’s city life. The optimal starting point from which you can set out to discover Basel and its unique way of life: from the Old Town to the famous museums and the Carnival.]]></location><directions><![CDATA[On foot from the main railway station is a 15 minutes walk via Aeschenplatz. Alternatively you can take tram No. 2 from the station as far as the Kunstmuseum stop, thereafter 5 minutes on foot through the Alban quarter and down the Mühleberg, passing St. Albans Church to the youth hostel (sign). <br><br>Parking possibilities in the vicinity.]]></directions></property>
                    <property ID="41536"><overview><![CDATA[Test Hostel Doesn\'t exist. St. Alban enjoys a location near the centre of Basel and is the ideal place to stay to explore this historic city!]]></overview><info><![CDATA[The newly renovated, modern youth hostel provides 234 beds in a variety of room types (double room to six-bed rooms). It is glorious to stay here in this quiet, green, sleepy oasis of Basel, not far from the river Rhine. We have a reception and front desk open 24/7 where our warm and friendly staff is on hand to assist you throughout your stay. Facilities include a common area with a bar, a conference room, games room, kiosk, washing facilities and internet corner. **PLEASE NOTE: There is an extra charge if you are not a member of any Youth Hostel Association of CHF 6.00 per night, payable on arrival**]]></info><desc><![CDATA[Check-in at any time, rooms are ready from 3 pm heck-out until 10 am 21 double rooms with shower/WC 3 rooms with 4 beds (family rooms) and shower/WC 36 rooms with 4 beds and washbasin 6 rooms with 6 beds and washbasin A total of 234 beds. Showers/WC on the floor. Catering Breakfast buffet (incl.) Lunch and evening meal (3-course menu)  Vegetarian meals Special events and meals for groups on request Bar service snacks 24 hours a day Infrastructure Large lobby  Snack bar 1 conference room (22 seats) Extensive seminar equipment (video projector, TV/DVD, etc.) Kiosk with souvenirs (Swatch, Victorinox, Wenger, SYH merchandising, etc.) Bicycle cellar Internet corner WiFi Suitable for wheelchairs]]></desc><location><![CDATA[The youth hostel is situated in the venerable St Alban quarter, the original home of the Basler "Daig", as the old established Basler families are called. Behind the building flows the St Alban Canal, which earlier linked the various factories in the quarter to the Rhine. From the hostel it’s only a 10 minutes walk to the effervescence of Basel’s city life. The optimal starting point from which you can set out to discover Basel and its unique way of life: from the Old Town to the famous museums and the Carnival.]]></location><directions><![CDATA[On foot from the main railway station is a 15 minutes walk via Aeschenplatz. Alternatively you can take tram No. 2 from the station as far as the Kunstmuseum stop, thereafter 5 minutes on foot through the Alban quarter and down the Mühleberg, passing St. Albans Church to the youth hostel (sign). <br><br>Parking possibilities in the vicinity.]]></directions></property>
                    <property ID="353535"><overview><![CDATA[Test Hostel insert Basel St. Alban enjoys a location near the centre of Basel and is the ideal place to stay to explore this historic city!]]></overview><info><![CDATA[The newly renovated, modern youth hostel provides 234 beds in a variety of room types (double room to six-bed rooms). It is glorious to stay here in this quiet, green, sleepy oasis of Basel, not far from the river Rhine. We have a reception and front desk open 24/7 where our warm and friendly staff is on hand to assist you throughout your stay. Facilities include a common area with a bar, a conference room, games room, kiosk, washing facilities and internet corner. **PLEASE NOTE: There is an extra charge if you are not a member of any Youth Hostel Association of CHF 6.00 per night, payable on arrival**]]></info><desc><![CDATA[Check-in at any time, rooms are ready from 3 pm heck-out until 10 am 21 double rooms with shower/WC 3 rooms with 4 beds (family rooms) and shower/WC 36 rooms with 4 beds and washbasin 6 rooms with 6 beds and washbasin A total of 234 beds. Showers/WC on the floor. Catering Breakfast buffet (incl.) Lunch and evening meal (3-course menu)  Vegetarian meals Special events and meals for groups on request Bar service snacks 24 hours a day Infrastructure Large lobby  Snack bar 1 conference room (22 seats) Extensive seminar equipment (video projector, TV/DVD, etc.) Kiosk with souvenirs (Swatch, Victorinox, Wenger, SYH merchandising, etc.) Bicycle cellar Internet corner WiFi Suitable for wheelchairs]]></desc><location><![CDATA[The youth hostel is situated in the venerable St Alban quarter, the original home of the Basler "Daig", as the old established Basler families are called. Behind the building flows the St Alban Canal, which earlier linked the various factories in the quarter to the Rhine. From the hostel it’s only a 10 minutes walk to the effervescence of Basel’s city life. The optimal starting point from which you can set out to discover Basel and its unique way of life: from the Old Town to the famous museums and the Carnival.]]></location><directions><![CDATA[On foot from the main railway station is a 15 minutes walk via Aeschenplatz. Alternatively you can take tram No. 2 from the station as far as the Kunstmuseum stop, thereafter 5 minutes on foot through the Alban quarter and down the Mühleberg, passing St. Albans Church to the youth hostel (sign). <br><br>Parking possibilities in the vicinity.]]></directions></property>
                </properties>';
    }
}