<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Db_Translation_langs extends CI_Model {
    const LANG_TABLE = 'translation_langs';
    
    private $ci;
    
    public function __construct() {
        parent::__construct();
        $this->ci = &get_instance();
    }
    
    public function getSupportedLangCodes() {
        $languagesQuery = $this->ci->db->get(self::LANG_TABLE);
        $languages = $languagesQuery->result();
        
        $langCodes = array();
        foreach ($languages as $language) {
            $langCodes[] = $language->code_lang;
        }

        return $langCodes;
    }
}