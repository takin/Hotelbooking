<?php 
class MY_Loader extends CI_Loader
{
    public function MY_Loader()
    {
        parent::CI_Loader();                
    }
    
    public function database($params = '', $return = FALSE, $active_record = FALSE)
    {
        // Grab the super object
        $CI =& get_instance();
        
        // Do we even need to load the database class?
        if (class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE AND isset($CI->db) AND is_object($CI->db))
        {
            return FALSE;
        }    
    
        require_once(BASEPATH.'database/DB'.EXT);     
        
        /**
        * these lines are modified a litle bit 
        * to make sure that the database object 
        * is assigned to a CI Super Object's property
        * even if the second parameter is set to TRUE
        *
        */
        if ($return === TRUE)
        {
            $db = 'db_' . $params;
            if (isset($CI->$db)){
                return $CI->$db;
            }
            $CI->$db = DB($params, $active_record); 
            return $CI->$db;
        }
        // Initialize the db variable.  Needed to prevent   
        // reference errors with some configurations
        $CI->db = '';
        
        // Load the DB class
        $CI->db =& DB($params, $active_record);    
        
        // Assign the DB object to any existing models
        $this->_ci_assign_to_models();
    }
} 
?>