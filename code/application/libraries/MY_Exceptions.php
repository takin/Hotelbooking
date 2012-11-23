<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {
  
/**
	 * Exception Logger
	 *
	 * This function logs PHP generated error messages
	 * 
	 * And add URL where the error occured 
	 *
	 * @access	private
	 * @param	string	the error severity
	 * @param	string	the error string
	 * @param	string	the error filepath
	 * @param	string	the error line number
	 * @return	string
	 */
	function log_exception($severity, $message, $filepath, $line)
	{	
	  
		$severity = ( ! isset($this->levels[$severity])) ? $severity : $this->levels[$severity];
		
		log_message('error', 'Severity: '.$severity.'  --> '.$message. ' '.$filepath.' '.$line. ' @ '.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"], TRUE);
	}
}