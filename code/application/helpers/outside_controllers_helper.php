<?php

	function db_connect()
	{
	  //TONOTICE maybe this can work too but might decrease performance:
    // require_once BASEPATH.'database/DB'.EXT;
    //$db = DB();
    
	  include(APPPATH."config/database".EXT);
 
    $conn = mysql_connect($db["default"]["hostname"], $db["default"]["username"], $db["default"]["password"]);
    if(!$conn) return false;
    mysql_select_db($db["default"]["database"],$conn);
    mysql_query("SET NAMES 'utf8'");
    
    return $conn;
	}
	
	function db_close(&$conn)
	{
	  mysql_close($conn);
	}
	
	
	function get_domain_data($domain_name)
	{
	  $conn = db_connect();
	  if(!$conn) return false;
	  
	  $domain_name = mysql_real_escape_string($domain_name);
	  
    $sql = "SELECT * FROM site_domains";
    $sql.= " WHERE LOWER(site_domain) LIKE LOWER('%".$domain_name."')";
    $sql.= "      OR LOWER(secure_site_domain) LIKE LOWER('%".$domain_name."')";
    
    $query = mysql_query($sql);
    if(!$query)
    {
      log_message('error', 'SQL query error for query ->'.$sql);
      return false;
    }
		$row = mysql_fetch_array($query, MYSQL_ASSOC);
		
		 db_close($conn);
		 
		return $row;
	}
	function db_wp_connect(&$wp_dbtag = NULL)
	{
	  if(empty($wp_dbtag))
	  {
	    //load site config file
	    $domain = get_domain_data($_SERVER['HTTP_HOST']);
	    if(!$domain) return false;
      $site_conf_file = $domain["conf_filename"];
      include(APPPATH."config/$site_conf_file".EXT);
      
      //get site config wp DB tag
      $wp_dbtag = $config['wp_db_tag'];
	  }
    
    include(APPPATH."config/database".EXT);
    $conn = mysql_connect($db[$wp_dbtag]["hostname"], $db[$wp_dbtag]["username"], $db[$wp_dbtag]["password"]);
    if(!$conn) return false;
    
    mysql_select_db($db[$wp_dbtag]["database"],$conn);
    mysql_query("SET NAMES 'utf8'");
    
    return $conn;
	}
 
	function get_wp_option($wp_dbtag, $option_name, $default = "")
	{
	  $wpconn = db_wp_connect($wp_dbtag);
	  if(!$wpconn) return $default;
	  
	  if(is_null($wpconn))
    {
      log_message('error','options DB not found: '.$wp_dbtag);
    }

    $option_name = mysql_real_escape_string($option_name);
    
	  $sql = "SELECT option_value FROM wp_options";
    $sql.= " WHERE option_name LIKE '%".$option_name."'";
	  
    $query = mysql_query($sql);
		$row = mysql_fetch_array($query, MYSQL_ASSOC);
		
	  db_close($wpconn);
	  
	  if(empty($row))
	  {
	    return $default;
	  }
	  else
	  {
	    return $row["option_value"];
	  }
	  
	  return $default;
	}
	
	function get_user_currency($user_id)
	{
	  $conn = db_connect();
	  if(!$conn) return false;
	  
	  $user_id = mysql_real_escape_string($user_id);
	  
	  $sql = "SELECT currencies.currency_code AS favorite_currency_code";
	  $sql.= " FROM user_profiles";
	  $sql.= " LEFT JOIN currencies ON currencies.currency_id = user_profiles.favorite_currency";
	  $sql.= " WHERE user_id = ".$user_id;

	  $query = mysql_query($sql);
		$row = mysql_fetch_array($query, MYSQL_ASSOC);
		
		 db_close($conn);
		 
		 if(!empty($row["favorite_currency_code"]))
		 {
		   return $row["favorite_currency_code"];
		 }
		 
		 return FALSE;
	}
	
	function get_currency_of_country($country_code)
	{
	  $conn = db_connect();
	  if(!$conn) return false;
	  
	  $country_code = mysql_real_escape_string($country_code);
	  
	  $sql = "SELECT currency_code";
	  $sql.= " FROM currency_country";
	  $sql.= " WHERE LOWER(country_iso_code_2) LIKE LOWER('$country_code')";

	  $query = mysql_query($sql);
		$row = mysql_fetch_array($query, MYSQL_ASSOC);
		
		 db_close($conn);
		 
		 if(!empty($row["currency_code"]))
		 {
		   return $row["currency_code"];
		 }
		 
		 return FALSE;
	}
	
	function get_session_data()
	{
	  $session_data = "";
	  $conn = db_connect();
    if(!$conn) return false;
    
	  if(!empty($_COOKIE['aj_session']))
	  {
      $cisess_cookie = $_COOKIE['aj_session'];  
      $cisess_cookie = stripslashes($cisess_cookie);  
      $cisess_cookie = unserialize($cisess_cookie);
      
      $cisess_cookie['session_id'] = mysql_real_escape_string($cisess_cookie['session_id']);
  	  
      $sql = "SELECT user_data FROM ci_sessions";
      $sql.= " WHERE session_id = '".$cisess_cookie['session_id']."'";
  
      $query = mysql_query($sql);
  		$session_data = mysql_fetch_array($query, MYSQL_ASSOC);
  		db_close($conn);
  		
  		if(!empty($session_data))
  		{
  		  $session_data = unserialize($session_data["user_data"]);
  		}
	  }
		return $session_data;
	}
?>