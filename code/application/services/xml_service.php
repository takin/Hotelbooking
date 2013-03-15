<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class Xml_Service {
    
    protected $ci;
    
    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->library("custom_log");
    }

    protected function getDataFromUrl($url) {
        ini_set('memory_limit', "512M");
        set_time_limit(2000);
        
        $curl = curl_init();
        
        try {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER, $this->getHttpHeader(),
            ));       
            $this->logAudit("HB XML Service - requesting data from $url", 
                    0, 0, 0);
            
            $requestTime = microtime(true);
            $memoryMeasurements = array("requestMem" => memory_get_usage());
            $result = curl_exec($curl);
            $responseTime = microtime(true);
            
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpStatus != 200) {
                throw new Exception(
                        "Error: Request to $url received http status code of $httpStatus");
            }
            
            $memoryMeasurements["responseMem"] = memory_get_usage();
            $this->logAudit("HB XML Service - XML retrieval from $url", 
                    $requestTime, $responseTime, $memoryMeasurements);
        } catch (Exception $e) {
            log_message("error", 
                    sprintf("%s error: retrieving data from %s failed. %s", 
                        __FUNCTION__, $url, $e->message));
            $result = null;
            curl_close($curl);
        }
        
        return $result;
    }
    
    private function getHttpHeader() {
        return array(
                "ACCEPT: text/xml,application/xml",
                "ACCEPT-CHARSET: utf-8;",
                "CACHE-CONTROL: max-age=0");
    }
    
    protected function logAudit($message, $requestTime, $responseTime, $memoryMeasurements = array()) {
        if (!empty($memoryMeasurements)) {
            $memoryUsed = $memoryMeasurements["responseMem"] - $memoryMeasurements["requestMem"];
            $memMsg = sprintf("- memory used is %s bytes", $memoryUsed);
        } else {
            $memMsg = "";
        }
        
        $totalTime = floor(($responseTime - $requestTime) * 1000);
        $this->ci->custom_log->log("audit", sprintf(
                "%s - %s ms %s", $message, $totalTime, $memMsg));
    }
    
    protected function trimCDataTag($propertyXml) {
        return $this->ci->db->escape((string) trim($propertyXml));
    }    
}