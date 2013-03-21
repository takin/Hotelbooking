<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

abstract class Xml_Service {
    
    protected $ci;
    protected $log_filename;
    
    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->model("db_hb_hostel");
        $this->ci->load->library("custom_log");
        $this->log_filename = "hb_cache_staticfeeds-" . date("Y-m");
    }

    protected function getDataFromUrl($url) {        
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
            $result = curl_exec($curl);
            $responseTime = microtime(true);
            
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpStatus != 200) {
                throw new Exception(
                        "Error: Request to $url received http status code of $httpStatus");
            }
            
            $this->logAudit("HB XML Service - XML retrieval from $url", 
                    $requestTime, $responseTime, true);
        } catch (Exception $e) {
            log_message("error", 
                    sprintf("%s error: retrieving data from %s failed. %s \n %s", 
                        __FUNCTION__, $url, $e->getMessage(), $e->getTraceAsString()));
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
    
    protected function logAudit($message, $requestTime, $responseTime, $displayMemUsage=false) {
        if ($displayMemUsage) {
            $this->ci->load->helper("memory_helper");
            $memoryUsed = memory_usage_in_mb();
            $memMsg = sprintf("- memory used is %s mb", $memoryUsed);
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