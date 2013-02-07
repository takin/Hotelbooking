<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Output extends CI_Output {

    var $wp_dbtag = "";
    var $custom_cache_key;
    var $cache_path = "";
    var $session_data = "";
    var $cache_subdir = "";
    var $mobile_cache_subdir = "mobile";
    var $cache_override = false;
    var $currency_anticipation = "EUR";
    var $user_agent = NULL;
    var $user_agent_mobile = FALSE;
    var $user_agent_mobile_bypass = FALSE;
    var $currency_dependancy = TRUE;

    /**
     * set_user_agent_pref
     */
    function set_user_agent_pref($var_name, $cookie_time = 3600) {

        $site_pref = FALSE;
        if (!empty($_GET[$var_name])) {
            $site_pref = $_GET[$var_name];
        }

        if (empty($site_pref)) {
            if (!empty($_COOKIE[$var_name])) {
                $site_pref = $_COOKIE[$var_name];
            }

            if ($site_pref != 'full') {
                $site_pref = FALSE;
            }
        } else {
            if ($site_pref == 'full') {
                setcookie($var_name, 'full', time() + $cookie_time, "/");
            } else {
                setcookie($var_name, '', time() - 3600, "/");
            }
        }

        if ($site_pref == 'full') {
            $this->user_agent_mobile_bypass = TRUE;
        } else {
            $this->user_agent_mobile_bypass = FALSE;
        }
    }

    public function get_city_cache_key($country, $city) {
        return str_replace("/", "-", strtolower($country)) . '-' . str_replace("/", "-", strtolower($city));
    }

    public function list_cache_files() {
        if (!is_dir($this->cache_path) OR !is_really_writable($this->cache_path)) {
            return array();
        }
        $cache_files = glob($this->cache_path . '*');
        return $cache_files;
    }

    function set_mobile_properties() {
        $User_agent = & load_class('User_agent');

        $this->user_agent = $User_agent->agent_string();

        $this->user_agent_mobile = FALSE;

        if ($User_agent->is_mobile() && !$User_agent->is_tablet()) {
            $this->user_agent_mobile = TRUE;
        }

        $this->set_user_agent_pref('site');
    }

    /**
     * _get_site_currency
     *
     * No need to validate currency, because if not a valid currency the cache wont exist and the site script will proceed
     */
    function _get_site_currency($CFG, $session_data = NULL) {
        $currency = "";

        if (!empty($_GET["currency"])) {
            $currency = $_GET["currency"];
        }
        //if change from profile page
        elseif (!empty($_POST["favorite_currency"])) {
            $currency = $_POST["favorite_currency"];
        } elseif (!empty($_COOKIE["currency_selected"])) {
            $currency = $_COOKIE["currency_selected"];
        }
        //If user  is logged in get currency of user profile
        elseif ((!empty($session_data)) && isset($session_data["status"]) && ($session_data["status"] == 1) && !empty($session_data["user_id"])) {
            $currency = get_user_currency($session_data["user_id"]);
        }
        //If currency of country is available via geoIP location return it
        elseif (!empty($session_data["user_country_code"])) {
            $currency = get_currency_of_country($session_data["user_country_code"]);
        } else {
            include_once(APPPATH . "helpers/misc_tools_helper" . EXT);
            $user_country = freeGeoFromIP($_SERVER["REMOTE_ADDR"]);
            if (!empty($user_country)) {
                $user_country = $user_country->CountryCode;
                $currency = get_currency_of_country($user_country);
            }
        }

        if (!empty($currency)) {
            return $currency;
        } else {
            return get_wp_option($this->wp_dbtag, "aj_default_currency", "EUR");
        }

        return false;
    }

    function _get_custom_key_for_display($URI, $CFG) {
        include_once(APPPATH . "helpers/outside_controllers_helper" . EXT);

        $log_info = "";

        if (!empty($_COOKIE['aj_session'])) {

            $this->session_data = get_session_data();
            if (!empty($this->session_data)) {
                if (isset($this->session_data["status"]) && ($this->session_data["status"] == 1)) {
                    $log_info = "-LI";
                }
            }
        }

        $uri_string = mb_strtolower($URI->uri_string, 'UTF-8');

        if (empty($uri_string)) {
            $uri_string = "index";
        } else {
            //if last charcater of uri is a / remove it
            $uri_string = trim($uri_string, "/ ");
        }

        return str_replace("/", "-", $uri_string) . $log_info;
    }

    function _display($output = '') {
        // Note:  We use globals because we can't use $CI =& get_instance()
        // since this function is sometimes called by the caching mechanism,
        // which happens before the CI super object is available.
        global $BM, $CFG;

        // --------------------------------------------------------------------
        // Set the output data
        if ($output == '') {
            $output = & $this->final_output;
        }

        // --------------------------------------------------------------------
        // Do we need to write a cache file?
        if ($this->cache_expiration > 0) {
            $this->_write_cache($output);
        }

        // --------------------------------------------------------------------
        // Parse out the elapsed time and memory usage,
        // then swap the pseudo-variables with the data

        $elapsed = $BM->elapsed_time('total_execution_time_start', 'total_execution_time_end');
        $output = str_replace('{elapsed_time}', $elapsed, $output);

        $memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage() / 1024 / 1024, 2) . 'MB';
        $output = str_replace('{memory_usage}', $memory, $output);

        // --------------------------------------------------------------------
        // Is compression requested?
        if ($CFG->item('compress_output') === TRUE) {
            if (extension_loaded('zlib')) {
                if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE) {
                    ob_start('ob_gzhandler');
                }
            }
        }

        // --------------------------------------------------------------------
        // Are there any server headers to send?
        if (count($this->headers) > 0) {
            foreach ($this->headers as $header) {
                @header($header[0], $header[1]);
            }
        }

        // --------------------------------------------------------------------
        // Does the get_instance() function exist?
        // If not we know we are dealing with a cache file so we'll
        // simply echo out the data and exit.
        if (!function_exists('get_instance')) {
            echo $output;
            log_message('debug', "Final output sent to browser");
            log_message('debug', "Total execution time: " . $elapsed);
            return TRUE;
        }

        // --------------------------------------------------------------------
        // Grab the super object.  We'll need it in a moment...
        $CI = & get_instance();

        // Do we need to generate profile data?
        // If so, load the Profile class and run it.
        if ($this->enable_profiler == TRUE) {
            $CI->load->library('profiler');

            // If the output data contains closing </body> and </html> tags
            // we will remove them and add them back after we insert the profile data
            if (preg_match("|</body>.*?</html>|is", $output)) {
                $output = preg_replace("|</body>.*?</html>|is", '', $output);
                $output .= $CI->profiler->run();
                $output .= '</body></html>';
            } else {
                $output .= $CI->profiler->run();
            }
        }

        // --------------------------------------------------------------------
        // Does the controller contain a function named _output()?
        // If so send the output there.  Otherwise, echo it.
        if (method_exists($CI, '_output')) {
            $CI->_output($output);
        } else {
            echo $output;  // Send it to the browser!
        }

        log_message('debug', "Final output sent to browser");
        log_message('debug', "Total execution time: " . $elapsed);
    }

    // --------------------------------------------------------------------

    /**
     * Write a Cache File
     *
     * @access	public
     * @return	void
     */
    function _write_cache($output) {
        if ($this->cache_override == true) {
            return FALSE;
        }

        $CI = & get_instance();


        if (!is_dir($this->cache_path) OR !is_really_writable($this->cache_path)) {
            return;
        }

        //If diskspace is lower than 2 Gig do not cache
        $max_free_space_in_MB = get_wp_option($this->wp_dbtag, "aj_cache_space_limit", 2000);
        if (disk_free_space(FCPATH) < ($max_free_space_in_MB * 1048576)) {
            log_message('error', "Did not cache. Free space is less than $max_free_space_in_MB MB");
            //Before sending email need to check if already sent to be sure not to send big amount of mails
//		  $CI->load->library('email');
//		  $CI->email->to('someone@example.com');
//		  $CI->email->to('someone@example.com');
//		  $CI->email->subject('Cache limit reached');
//		  $CI->email->message('Cache limit reached for '.$_SERVER["HTTP_HOST"]. );
//      $CI->email->send();

            return FALSE;
        }

        $subdirpath = "";
        foreach ($this->cache_subdir as $subdir) {
            //Add cache sub direectory
            if (($subdirpath = $this->_add_cache_sub_dir($subdirpath, $subdir . "/")) == false) {
                return FALSE;
            }
        }
        //Add currency subdir if page is currency dependant
        if ($this->currency_dependancy === TRUE) {
            $site_cur = $CI->config->item('site_currency_selected');
            if (($subdirpath = $this->_add_cache_sub_dir($subdirpath, $site_cur . "/")) == false) {
                return FALSE;
            }
        }


        $cache_path = $this->cache_path . $subdirpath . $this->custom_cache_key;
        if (!$fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
            log_message('error', "Unable to write cache file: " . $cache_path);
            return;
        }

        $expire = time() + ($this->cache_expiration * 60);

        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $expire . 'TS--->' . $output);
            flock($fp, LOCK_UN);
        } else {
            log_message('error', "Unable to secure a file lock for file at: " . $cache_path);
            return;
        }
        fclose($fp);
        @chmod($cache_path, DIR_WRITE_MODE);

        log_message('debug', "Cache file written: " . $cache_path);
    }

    // --------------------------------------------------------------------
    function _add_cache_sub_dir($subdirpath, $newsubdir) {
        if (!is_dir($this->cache_path . $subdirpath . $newsubdir) OR !is_really_writable($this->cache_path . $subdirpath . $newsubdir)) {
            if (!mkdir($this->cache_path . $subdirpath . $newsubdir)) {
                return FALSE;
            }
        }
        return $subdirpath . $newsubdir;
    }

    public function validate_http_host($host) {
        $host = explode('.', strtolower($host));
        if ($host[0] !== 'www') {
            $host[0] = 'www';
        }
        $host = implode('.', $host);

        return $host;
    }

    /**
     * Update/serve a cached file
     *
     * @access	public
     * @return	void
     */
    function _display_cache(&$CFG, &$URI) {
            return FALSE;
            
        //If the hostel page is output for review
        if (!empty($_GET["comment"]) && ($_GET["comment"] == "insert")) {
            return FALSE;
        }

        if (!empty($_GET["groupbkg"])) {
            return FALSE;
        }
        $this->cache_override = !get_wp_option($this->wp_dbtag, "aj_cache_enable", true);
        if ($this->cache_override == true) {
            return FALSE;
        }

        $this->cache_path = ($CFG->item('cache_path') == '') ? BASEPATH . 'cache/' : $CFG->item('cache_path');

        if (!is_dir($this->cache_path) OR !is_really_writable($this->cache_path)) {
            return FALSE;
        }

        $http_server_host = $this->validate_http_host($_SERVER["HTTP_HOST"]);

        $this->cache_subdir = array($http_server_host, $URI->rsegments[1] . "-" . $URI->rsegments[2]);
        //set default currency dependancy
        switch ($URI->rsegments[2]) {
            case "index":
                $this->currency_dependancy = FALSE;
            case "property_page":
                $this->currency_dependancy = FALSE;
                break;
            default:
                $this->currency_dependancy = TRUE;
        }

        //Add mobile subdir if mobile site is to be displayed
        $mobile_enable = get_wp_option($this->wp_dbtag, "aj_enable_mobile", FALSE);
        if ($mobile_enable == TRUE) {
            $this->set_mobile_properties();
            if ($this->user_agent_mobile && !$this->user_agent_mobile_bypass) {
                $this->cache_subdir = array($http_server_host . "." . $this->mobile_cache_subdir, $URI->rsegments[1] . "-" . $URI->rsegments[2]);
                //	    return FALSE;
                switch ($URI->rsegments[2]) {
                    case "index":
                    case "mobile_search":
                    case "property_page":
                        $this->currency_dependancy = FALSE;
                        break;
                }
            }
        }

        $this->custom_cache_key = $this->_get_custom_key_for_display($URI, $CFG);

        $this->session_data = get_session_data();

        //If session data is false it means there is a DB connection problem
        if (!$this->session_data) {
            return FALSE;
        }

        if ($this->currency_dependancy === TRUE) {
            $this->currency_anticipation = $this->_get_site_currency($CFG, $this->session_data);

            if ($this->currency_anticipation === false) {
                return FALSE;
            }
            $filepath = $this->cache_path . implode("/", $this->cache_subdir) . "/" . $this->currency_anticipation . "/" . $this->custom_cache_key;
        } else {
            $filepath = $this->cache_path . implode("/", $this->cache_subdir) . "/" . $this->custom_cache_key;
        }

        if (!@file_exists($filepath)) {
            return FALSE;
        }

        if (!$fp = @fopen($filepath, FOPEN_READ)) {
            return FALSE;
        }

        flock($fp, LOCK_SH);

        $cache = '';
        if (filesize($filepath) > 0) {
            $cache = fread($fp, filesize($filepath));
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        // Strip out the embedded timestamp
        if (!preg_match("/(\d+TS--->)/", $cache, $match)) {
            return FALSE;
        }

        // Has the file expired? If so we'll delete it.
        if (time() >= trim(str_replace('TS--->', '', $match['1']))) {
            @unlink($filepath);
            log_message('debug', "Cache file has expired. File deleted");
            return FALSE;
        }

        // Display the cache
        $this->_display(str_replace($match['0'], '', $cache));
        log_message('debug', "Cache file is current. Sending it to browser.");
        return TRUE;
    }

}
