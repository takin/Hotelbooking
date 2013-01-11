<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MY_Router extends CI_Router {

    const CITY_TABLE = 'cities2';

    var $api_shortname = "hw";
    var $wp_dbtag = "";

    function MY_Router() {
        //Load outside controllers function helpers
        include(APPPATH . "helpers/outside_controllers_helper" . EXT);

        //get api site to get all the properties data
        //TONOTICE need to be run before parent constructor for default controllers to be set correctly
        $this->api_shortname = get_wp_option($this->wp_dbtag, "aj_api_site_data", $this->api_shortname);

        parent::__construct();
    }

    /**
     * Set the route mapping
     *
     * This function determines what should be served based on the URI request,
     * as well as any "routes" that have been set in the routing config file.
     *
     * Modifcation made to dynamically change the default controller depending on wich booking API to use
     *
     * @access	private
     * @return	void
     */
    function _set_routing() {
        // Are query strings enabled in the config file?
        // If so, we're done since segment based URIs are not used with query strings.
        if ($this->config->item('enable_query_strings') === TRUE AND isset($_GET[$this->config->item('controller_trigger')])) {
            $this->set_class(trim($this->uri->_filter_uri($_GET[$this->config->item('controller_trigger')])));

            if (isset($_GET[$this->config->item('function_trigger')])) {
                $this->set_method(trim($this->uri->_filter_uri($_GET[$this->config->item('function_trigger')])));
            }

            return;
        }

        // Load the routes.php file.
        @include(APPPATH . 'config/routes' . EXT);
        $this->routes = (!isset($route) OR !is_array($route)) ? array() : $route;
        unset($route);

        // Set the default controller so we can display it in the event
        // the URI doesn't correlated to a valid controller.
        $this->default_controller = (!isset($this->routes['default_controller']) OR $this->routes['default_controller'] == '') ? FALSE : strtolower($this->routes['default_controller']);

        /*         * ***********************
         * Modification
         */
        if (strcasecmp($this->api_shortname, "hb") == 0) {
            $this->default_controller = "chostelbk";
        }

        /*
         * End of modification
         * ********************** */

        // Fetch the complete URI string
        $this->uri->_fetch_uri_string();

        // Is there a URI string? If not, the default controller specified in the "routes" file will be shown.
        if ($this->uri->uri_string == '') {
            if ($this->default_controller === FALSE) {
                show_error("Unable to determine what should be displayed. A default route has not been specified in the routing file.");
            }

            if (strpos($this->default_controller, '/') !== FALSE) {
                $x = explode('/', $this->default_controller);

                $this->set_class(end($x));
                $this->set_method('index');
                $this->_set_request($x);
            } else {
                $this->set_class($this->default_controller);
                $this->set_method('index');
                $this->_set_request(array($this->default_controller, 'index'));
            }

            // re-index the routed segments array so it starts with 1 rather than 0
            $this->uri->_reindex_segments();

            log_message('debug', "No URI present. Default controller set.");
            return;
        }
        unset($this->routes['default_controller']);

        // Do we need to remove the URL suffix?
        $this->uri->_remove_url_suffix();

        // Compile the segments into an array
        $this->uri->_explode_segments();

        // Parse any custom routing that may exist
        $this->_parse_routes();

        // Re-index the segment array so that it starts with 1 rather than 0
        $this->uri->_reindex_segments();
    }

    // --------------------------------------------------------------------

    function _validate_request($segments) {
        $dbroutes = $this->_db_routing($segments);
        if ($dbroutes !== FALSE) {
            return $dbroutes;
        }

        // Does the requested controller exist in the root folder?
        if (file_exists(APPPATH . "controllers/" . $segments[0] . EXT)) {
            return $segments;
        }

        // Is the controller in a sub-folder?
        if (is_dir(APPPATH . "controllers/" . $segments[0])) {
            // Set the directory and remove it from the segment array
            $this->set_directory($segments[0]);
            $segments = array_slice($segments, 1);

            if (count($segments) > 0) {
                // Does the requested controller exist in the sub-folder?
                if (!file_exists(APPPATH . "controllers/" . $this->fetch_directory() . $segments[0] . EXT)) {
                    show_404($this->fetch_directory() . $segments[0]);
                }
            } else {
                $this->set_class($this->default_controller);
                $this->set_method("index");

                // Does the default controller exist in the sub-folder?
                if (!file_exists(APPPATH . "controllers/" . $this->fetch_directory() . $this->default_controller . EXT)) {
                    $this->directory = "";
                    return array();
                }
            }
            return $segments;
        }

        if (!empty($this->routes['error_controller'])) {
            $error_controller = explode('/', $this->routes['error_controller']);

            log_message('debug', '404 Page Not Found --> ' . implode("/", $segments));
            header("HTTP/1.0 404 Not Found");

            return $error_controller;
        }
        show_404($segments[0]);
    }

    function _db_routing($segments) {
        //TONOTICE Order here has an impact on performance.
        //Page that use last function will have to go through all other DB search before being routed.
        //Heavier page are place first here to minimize the impact of this logic on them for now.

        if ($this->api_shortname == "hb") {
            $link = $this->_dynamic_hb_country_links($segments);
            if ($link !== FALSE) {
                return $link;
            }
        } else {
            $link = $this->_dynamic_hw_country_links($segments);
            if ($link !== FALSE) {
                return $link;
            }
        }

        $link = $this->_dynamic_continent_links($segments);
        if ($link !== FALSE) {
            return $link;
        }

        //Hostel pages driven by property type as first URL segment
        $link = $this->_dynamic_property_types_links($segments);
        if ($link !== FALSE) {
            return $link;
        }


        $link = $this->_dynamic_site_links($segments);
        if ($link !== FALSE) {
            return $link;
        }

        return FALSE;
    }

    function _dynamic_site_links($segments) {
        $conn = db_connect();
        if (!$conn)
            return false;

        $uri = implode('/', $this->uri->segments);
        $site_link = mysql_real_escape_string($uri);

        $routefieldname = "ci_route_" . $this->api_shortname;

        // Is there a literal match?  If so we're done
        $sql = "SELECT $routefieldname FROM site_links";
        $sql.= " JOIN site_domains ON site_links.site_domain_id = site_domains.site_domain_id";
        $sql.= " JOIN links ON site_links.link_id = links.link_id";
        $sql.= " WHERE ( LOWER(site_domain) LIKE LOWER('%" . $_SERVER['HTTP_HOST'] . "')";
        $sql.= "      OR LOWER(secure_site_domain) LIKE LOWER('%" . $_SERVER['HTTP_HOST'] . "') )";
        $sql.= " AND LOWER(link) LIKE LOWER ('$site_link')";

        $query = mysql_query($sql);
        $row = mysql_fetch_row($query);
//		print_r($row);
        if (mysql_num_rows($query) > 0) {
            db_close($conn);
            return explode("/", $row[0]);
        }

        // Loop through the routes in DB looking for wild-cards
        $sql = "SELECT * FROM site_links";
        $sql.= " JOIN site_domains ON site_links.site_domain_id = site_domains.site_domain_id";
        $sql.= " JOIN links ON site_links.link_id = links.link_id";
        $sql.= " WHERE ( LOWER(site_domain) LIKE LOWER('%" . $_SERVER['HTTP_HOST'] . "')";
        $sql.= "      OR LOWER(secure_site_domain) LIKE LOWER('%" . $_SERVER['HTTP_HOST'] . "') )";
        $query = mysql_query($sql);

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            $key = $row["link"];
            //get route associated to the right booking API
            $val = $row[$routefieldname];

            // Convert wild-cards to RegEx
            $key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

            // Does the RegEx match?
            if (preg_match('#^' . $key . '$#', $uri)) {
                // Do we have a back-reference?
                if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) {
                    $val = preg_replace('#^' . $key . '$#', $val, $uri);
                }
                db_close($conn);
                return explode('/', $val . $uri);
            }
        }

        db_close($conn);
        return FALSE;
    }

    function _dynamic_hw_country_links($segments) {
        //TODO added translated country not only english
        $conn = db_connect();
        if (!$conn)
            return false;

        //Get column link of all language
        $sql = "SHOW COLUMNS FROM " . self::CITY_TABLE . " WHERE Type LIKE'varchar(255)' AND Field LIKE'country%'";
        $query = mysql_query($sql);

        include(APPPATH . "helpers/misc_tools_helper" . EXT);
        $country = mysql_real_escape_string(urldecode(customurldecode($segments[0])));
        $where = "";

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            $where.= "LOWER(`" . $row["Field"] . "`) LIKE LOWER('" . $country . "') OR ";
        }
        $where = substr($where, 0, -3);

        $sql = "SELECT hw_country, country_en, hw_country.country_iso_code_2, cities2.country_iso_code_2, city_en
              FROM hw_country
              LEFT JOIN cities2 ON hw_country.hw_country = cities2.country_en
              WHERE " . $where . "
              GROUP BY hw_country.hw_country_id";
//    print $sql;
        $query = mysql_query($sql);
        db_close($conn);
        if (mysql_num_rows($query) > 0) {
            array_unshift($segments, "cmain", "property_search");
            return $segments;
        }
        return FALSE;
    }

    function _dynamic_hb_country_links($segments) {
        //TODO added translated country not only english
        $conn = db_connect();
        if (!$conn)
            return false;

        //Get column link of all language
        $sql = "SHOW COLUMNS FROM " . self::CITY_TABLE . " WHERE Type LIKE'varchar(255)' AND Field LIKE'country%'";
        $query = mysql_query($sql);

        include(APPPATH . "helpers/misc_tools_helper" . EXT);
        $country = mysql_real_escape_string(urldecode(customurldecode($segments[0])));
        $where = "";

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            $where.= "LOWER(`" . $row["Field"] . "`) LIKE LOWER('" . $country . "') OR ";
        }
        $where = substr($where, 0, -3);

        $sql = "SELECT lname_en, country_en, hb_country.country_iso_code_2, cities2.country_iso_code_2
              FROM hb_country
              LEFT JOIN cities2 ON hb_country.country_iso_code_2 = cities2.country_iso_code_2
              WHERE " . $where . "
              OR LOWER(`lname_en`) LIKE LOWER('" . $country . "')
              GROUP BY hb_country.hb_country_id";

//    print $sql;
        $query = mysql_query($sql);
        db_close($conn);
        if (mysql_num_rows($query) > 0) {
            array_unshift($segments, "cmain", "property_search");
            return $segments;
        }
        return FALSE;
    }

    function _dynamic_continent_links($segments) {
        $conn = db_connect();
        if (!$conn)
            return false;

        //Get column link of all language
        $sql = "SHOW COLUMNS FROM continents WHERE Type LIKE'varchar(255)' AND Field LIKE'continent%'";
        $query = mysql_query($sql);

        $continent = mysql_real_escape_string(urldecode($segments[0]));
        $where = "";

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            $where.= "LOWER(`" . $row["Field"] . "`) LIKE LOWER('" . $continent . "') OR ";
        }
        $where = substr($where, 0, -3);

        $sql = "SELECT * FROM continents WHERE " . $where;
        $query = mysql_query($sql);
        db_close($conn);
        if (mysql_num_rows($query) > 0) {
            array_unshift($segments, "cmain", "continent_country_page");
            return $segments;
        }

        return FALSE;
    }

    function _dynamic_property_types_links($segments) {
        $conn = db_connect();
        if (!$conn)
            return false;

        //Get column link of all language
        $sql = "SHOW COLUMNS FROM translation_links WHERE Type LIKE'varchar(255)' AND Field LIKE'term%'";
        $query = mysql_query($sql);

        $property_type = mysql_real_escape_string(urldecode($segments[0]));
        $where = "";

        while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
            $where.= "LOWER(`" . $row["Field"] . "`) LIKE LOWER('" . $property_type . "') OR ";
        }
        $where = substr($where, 0, -3);

        $sql = "SELECT * FROM translation_links WHERE " . $where;

        $query = mysql_query($sql);
        db_close($conn);

        if (mysql_num_rows($query) > 0) {
            array_unshift($segments, "cmain", "property_page");
            return $segments;
        }

        return FALSE;
    }

}

?>
