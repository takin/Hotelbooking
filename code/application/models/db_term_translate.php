<?php
/**
 * @author Louis-Michel
 *
 */
class Db_term_translate extends Model
{
  const LANG_TABLE           = 'translation_langs';
  const TERM_TABLE           = 'translation_terms';
  const TRANSLATION_TABLE    = 'translated_terms';

  private $cached_translation = array();

  function Db_term_translate()
  {
      parent::Model();

      $this->cached_translation = array();
  }

  function get_term_translation($term, $lang_code)
  {
    $term_en = strtolower($term);

    if(!empty($this->cached_translation[$lang_code][$term_en]))
    {
      return $this->cached_translation[$lang_code][$term_en];
    }

    $this->db->select('term');
    $this->db->from(self::TRANSLATION_TABLE);
    $this->db->join(self::LANG_TABLE, self::TRANSLATION_TABLE.".lang_id = ".self::LANG_TABLE.".language_id", 'left');
    $this->db->join(self::TERM_TABLE, self::TRANSLATION_TABLE.".term_id = ".self::TERM_TABLE.".term_id", 'left');
    $this->db->where('code_lang', $lang_code);
    $this->db->where('term_en',$term_en);

    $query = $this->db->get();

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      //Add to mem cache
      $this->cached_translation[$lang_code][$term_en] = $row->term;
      return $row->term;
    }
    log_message('debug',"No translation for $term in $lang_code");
    return $term;
  }
}
?>