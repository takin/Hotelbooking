<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Db_Translation_langs extends CI_Model {
    const LANG_TABLE = 'translation_langs';
    
    private $ci;
    
    public function __construct() {
        parent::__construct();
        $this->ci = &get_instance();
    }
    
    public function getSupportedLanguages() {
        $languagesQuery = $this->ci->db->get(self::LANG_TABLE);
        $languages = $languagesQuery->result();
        
        $languagesArray = array();
        foreach ($languages as $language) {
            $langCode = $language->code_lang;
            $languagesArray[$langCode] = $language->description;
        }

        return $languagesArray;
    }
}