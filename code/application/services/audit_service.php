<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Audit_Service {
    
    private $ci;
    
    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->library("custom_log");
    }
    
    public function log($message, $requestTime, $responseTime, $displayMemUsage=false) {
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
}

