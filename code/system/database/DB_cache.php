<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Database Cache Class
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
class CI_DB_Cache {

	var $CI;
	var $db;	// allows passing of db object so that multiple database connections and returned db objects can be supported

	/**
	 * Constructor
	 *
	 * Grabs the CI super object instance so we can access it.
	 *
	 */	
	function CI_DB_Cache(&$db)
	{
		// Assign the main CI object to $this->CI
		// and load the file helper since we use it a lot
		$this->CI =& get_instance();
		$this->db =& $db;
		$this->CI->load->helper('file');	
	}

	// --------------------------------------------------------------------

	/**
	 * Set Cache Directory Path
	 *
	 * @access	public
	 * @param	string	the path to the cache directory
	 * @return	bool
	 */		
	function check_path($path = '')
	{
		if ($path == '')
		{
			if ($this->db->cachedir == '')
			{
				return $this->db->cache_off();
			}
		
			$path = $this->db->cachedir;
		}
	
		// Add a trailing slash to the path if needed
		$path = preg_replace("/(.+?)\/*$/", "\\1/",  $path);

		if ( ! is_dir($path) OR ! is_really_writable($path))
		{
			// If the path is wrong we'll turn off caching
			return $this->db->cache_off();
		}
		
		$this->db->cachedir = $path;
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Retrieve a cached query
	 *
	 * The URI being requested will become the name of the cache sub-folder.
	 * An MD5 hash of the SQL statement will become the cache file name
	 *
	 * @access	public
	 * @return	string
	 */
	function read($sql)
	{
		$cache_mode = $this->db->cache_mode;
	    $cache_id   = $this->db->cache_ids->$cache_mode;
	    	    
	    if ($cache_mode == 'ar_table')
	    {
	    	$this->_get_tables_from_ar();
	    }
	    
	    $cache_mode = ($cache_mode == 'ar_table') ? 'table' : $cache_mode;
	    
	    $dir_path  = $this->db->cachedir.'/'.$cache_mode.'/';
		$file_name = $cache_id.md5($sql);
		
		if ( ! @is_dir($dir_path))
		{
			return FALSE;
		}
		
		if (FALSE === ($cachedata = read_file($dir_path.$file_name)))
		{	
			return FALSE;
		}
		
		return unserialize($cachedata);			
	}	

	// --------------------------------------------------------------------

	/**
	 * Write a query to a cache file
	 *
	 * @access	public
	 * @return	bool
	 */
	function write($sql, $object)
	{
		$cache_mode = $this->db->cache_mode;
	    $cache_id   = $this->db->cache_ids->$cache_mode;
	    	    	    
	    if ($cache_mode == 'ar_table')
	    {
	    	$this->_get_tables_from_ar();
	    }
	    
	    $cache_mode = ($cache_mode == 'ar_table') ? 'table' : $cache_mode;

	    $dir_path  = $this->db->cachedir.'/'.$cache_mode.'/';
		$file_name = $cache_id.md5($sql);
		
		if ( ! @is_dir($dir_path))
		{
			if ( ! @mkdir($dir_path, DIR_WRITE_MODE))
			{
				return FALSE;
			}
			
			@chmod($dir_path, DIR_WRITE_MODE);			
		}
		
		if (write_file($dir_path.$file_name, serialize($object)) === FALSE)
		{
			return FALSE;
		}
		
		@chmod($dir_path.$file_name, DIR_WRITE_MODE);
		return TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Detect Tables from Active Record
	 *
	 * @access	public
	 * @return	bool
	 */
	function _get_tables_from_ar()
	{
        if ($this->db->cache_mode == 'table' && !empty($this->CI->db->ar_from))
        {
            $ar_tables = array();
		
		    foreach ($this->db->ar_from as $table)
		    {
                $ar_tables[] = substr($table,1,strlen($table)-2);
    		}
            $this->db->cache_ids->table = '+'.implode('+',$ar_tables).'+';
        }
        return TRUE;
    }
	
    // --------------------------------------------------------------------

    /**
    * Find cache files matching the criteria within a particular directory
    *
    * @access	public
    * @param	array	the names that were used for caching (required)
    * @param	string	the directory to be scanned (required)
    * @param	array	array of already found cache files (optional)
    * @return	bool
    */
	function delete_cache_files($mode = NULL, $criteria = NULL, $cache_dir = NULL, &$cache_files = array())
	{
    	
    	if ( ! @is_dir($cache_dir))
		{
			return FALSE;
		}
        if ($handle = opendir($cache_dir)) {
            while (false !== ($file = readdir($handle)))
            {
                //Ignore items starting with '.'
                if (strncasecmp($file,'.',1))
                {
                    //If it's a file and matches the criteria, then add it to the array
                    if (is_file($cache_dir.$file))
                    {                
                        //Tables do not need to be found at offset 0
                        if ($mode == 'table')
                        {
                        	if (strpos($file,$criteria) !== FALSE)
                        	{
	                        	$cache_files[] = $cache_dir.$file;
                        	}
                        } else //Everything else does need to be found at offset 0
                        {
                            if (strpos($file,$criteria) === 0)
                            {
                                $cache_files[] = $cache_dir.$file;
                            }
                        }
                    } else if (is_dir($cache_dir.$file))
                    {
                        //Scan subdir for cache files
                        $this->delete_cache_files($mode, $criteria, $cache_dir.$file.'/', $cache_files);
                    }
                }
            }
            closedir($handle);
        }
        
        foreach ($cache_files as $cache_file)
        {
        	if (FALSE === unlink($cache_file))
        	{
        		return FALSE;
        	}
        }
        
        return $cache_files;
    }
	
	// --------------------------------------------------------------------

	/**
	 * Delete cache files within a particular directory
	 *
	 * @access	public
	 * @return	bool
	 */
	function delete($segment_one = '', $segment_two = '')
	{	
		$this->db->delete_controller_cache($segment_one = '', $segment_two = '');
	}

	// --------------------------------------------------------------------

	/**
	 * Delete all existing cache files
	 *
	 * @access	public
	 * @return	bool
	 */
	function delete_all($mode = '')
	{
		$mode = (empty($mode)) ? '' : $mode.'/';
		delete_files($this->db->cachedir.$mode, TRUE);
	}

}


/* End of file DB_cache.php */
/* Location: ./system/database/DB_cache.php */
