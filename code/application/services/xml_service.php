<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Xml_Service {
    
    public $errors = array();
    public $successCount = 0;
    public $failureCount = 0;
    
    private $auditor;
    
    public function __construct() {
        require_once(APPPATH . "/services/audit_service.php");
        $this->auditor = new Audit_Service();
    }

    public function getDataFromUrl($url) {        
        $curl = curl_init();
        
        try {
            curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER, $this->getHttpHeader(),
            ));       
            $this->auditor->log("HB XML Service - requesting data from $url", 
                    0, 0, 0);
            
            $requestTime = microtime(true);
            $result = curl_exec($curl);
            $responseTime = microtime(true);
            
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpStatus != 200) {
                $msg = "Error: Request to $url received http status code of $httpStatus";
                throw new Exception($msg);
            }
            
            $this->successCount++;
            
            $this->auditor->log("HB XML Service - XML retrieval from $url", 
                    $requestTime, $responseTime, true);
        } catch (Exception $e) {
            $msg = sprintf("%s error: retrieving data from %s failed. %s \n %s", 
                        __FUNCTION__, $url, $e->getMessage(), $e->getTraceAsString());
            log_message("error", $msg);
            $this->errors[] = $msg;
            $this->failureCount++;
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
    
    public function getXmlObject($xmlString) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument("1.0", "UTF-8");
        $dom->strictErrorChecking = false;
        $dom->validateOnParse = false;
        $dom->recover = true;
        $dom->loadXML($xmlString, LIBXML_NOCDATA);
        $xmlObject = simplexml_import_dom($dom);
        libxml_clear_errors();
        libxml_use_internal_errors(false);
                
        return $xmlObject;
    }
}
