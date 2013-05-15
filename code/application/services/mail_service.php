<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mail_Service {
    
    public $emailAddress;
    
    private $ci;
    private $isSent = false;
    
    public function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->library("email");
        $this->emailAddress = "korir.mordecai@gmail.com";//technical@mcwebmanagement.com
    }
    
    public function mailReport(array $reportInfo) {
        $emailcontent = $this->createErrorEmailContent($reportInfo);

        //Send report to appropriate admin
        $this->ci->email->from($this->ci->config->item('admin_booking_email'),"HB API cron job");
        $this->ci->email->to($this->emailAddress);
        $this->ci->email->subject($reportInfo["subject"]);
        $this->ci->email->message($emailcontent);
        
        if ($this->ci->email->send()) {
          $this->isSent = true;
        }        
    }
    
    private function createErrorEmailContent(array $reportInfo) {
        $contentBody = $this->getErrorEmailContentBody($reportInfo["errors"]);
        $cronUrls = $reportInfo["urls"];
        $successCount = $reportInfo["successCount"];
        $failureCount = $reportInfo["failureCount"];
        $urlSuccessCount = $reportInfo["urlSuccessCount"];
        $urlFailureCount = $reportInfo["urlFailureCount"];
        
        $content = "
            <html>
            <body>
                <h2 align='center'>Cron Report</h2>
                <ul>
                    <li>Urls: $cronUrls</li>
                    <ul>
                        <li>Count of successfully retrieved urls: $urlSuccessCount</li>
                        <li>Count of url retrieval failures: $urlFailureCount</li>
                    </ul>
                    <li>Hostel success count: $successCount</li>
                    <li>Hostel failure count: $failureCount</li>
                </ul>
                <table border='1' cellpadding='7'>
                    <thead>
                        <tr>
                            <th colspan='2' align='center'>Error Report</th>
                        </tr>
                    </thead>
                    <tbody>
                        $contentBody
                    </tbody>
                </table>
            </body>";
        
        return $content;
    }
    
    private function getErrorEmailContentBody(array $errors) {
        $body = "";
        
        if (empty($errors)) {
            $body = "
                <tr>
                    <td>No Errors</td>
                </tr>";
        } else {
            foreach ($errors as $count => $error) {
                $body .= "
                    <tr>
                        <td>$count</td>
                        <td>$error</td>
                    </tr>";
            }
        }
        
        return $body;
        
    }
    
    public function isMailSent() {
        return $this->isSent;
    }
}