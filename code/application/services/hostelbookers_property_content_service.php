<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . "/services/xml_service.php");

class Hostelbookers_Property_Content_Service extends Xml_Service {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function updateMonthlyPropertyContent() {
        
    }
    
    private function getWebServiceUrls() {
        $this->ci->load->model("db_translation_langs");
        $supportedLanguages = $this->ci->db_translation_langs->getSupportedLanguages();
        
        $urls = array();
        
        foreach ($supportedLanguages as $langCode => $language) {
            $urls[] = sprintf("%s-[%s]-[%s]-[%s].xml",
                "http://feeds.hostelbookers.com/generic/PropertyContent",
                $langCode, date("Y"), date("m"));
        }
        
        return $urls;
    }
    
    protected function parseXmlData(&$xmlData) {
        $propertiesXml = simplexml_load_string($xmlData);
        
        $properties = array();
        
        $startTime = microtime(true);
        foreach($propertiesXml->property as $propertyXml) {
            $property = $this->parsePropertyXml($propertyXml);            
            $properties[] = $property;
        }
        $endTime = microtime(true);
        $this->logAudit("HB Property Content XML Service - Parsed monthly property content feed",
                $startTime, $endTime);
                
        return $properties;
    }
}