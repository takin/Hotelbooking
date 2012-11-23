<?php
/*
 *
 */

class DB_translator
{
  private $db = NULL;

  const LANG_TABLE           = 'translation_langs';
  const TERM_TABLE           = 'translation_terms';
  const TRANSLATION_TABLE    = 'translated_terms';

  public function DB_translator($conn)
  {
    $this->db = $conn;
  }

  /*
   * CSV import
   *
   * For now CSV column format is fixed:
   * 1 english
   * 2 chinese
   * 3 finish
   * 4 french
   * 5 german
   * 6 hungarian
   * 7 italian
   * 8 japanese
   * 9 korean
   * 10 polish
   * 11 portuguese
   * 12 russian
   * 13 spanish
   *
   */
  public function importCSV($csv_file)
  {
    $return = TRUE;

    require_once('csv.class.php');
    $csv = new CSV($csv_file);

    $rownb = 0;

    while (($row = $csv->rows()) !== FALSE)
    {
      if($rownb > 0)
      {
        $term_en = $row[0];
        $term_en_id = $this->term_exists($term_en);
        if(empty($term_en_id))
        {
          $term_en_id = $this->insert_term($term_en);
        }
        foreach($row as $i => $term)
        {

          $lang_code = "";
          switch($i)
          {
//             english
            case 0:
              $lang_code = "en";
              break;
//             chinese
            case 1:
              $lang_code = "zh-CN";
              break;
//             czech
            case 2:
              $lang_code = "cs";
              break;
//             finish
            case 3:
              $lang_code = "fi";
              break;
//             french
            case 4:
              $lang_code = "fr";
              break;
//             german
            case 5:
              $lang_code = "de";
              break;
//             hungarian
            case 6:
              $lang_code = "hu";
              break;
//             italian
            case 7:
              $lang_code = "it";
              break;
//             japanese
            case 8:
              $lang_code = "ja";
              break;
//             korean
            case 9:
              $lang_code = "ko";
              break;
//             polish
            case 10:
              $lang_code = "pl";
              break;
//             portuguese
            case 11:
              $lang_code = "pt";
              break;
//             russian
            case 12:
              $lang_code = "ru";
              break;
//             spanish
            case 13:
              $lang_code = "es";
              break;
          }
          $lang_id           = $this->get_lang_id($lang_code);
          $term_translate_id = $this->term_translation_exists($term_en_id, $lang_code);
          if(!empty($lang_id) && !empty($term_translate_id))
          {
            if(!$this->update_term_translation($term_translate_id, $term))
            {
              $return = FALSE;
            }
          }
          elseif(!empty($lang_id))
          {
            if($this->insert_term_translation($lang_id,$term_en_id, $term));
            {
              $return = FALSE;
            }
          }
        }
      }

      $rownb++;
    }
    return $return;
  }

  public function term_exists($term_en)
  {
    $term_en = $this->db->escape($term_en);
    $sql = "SELECT term_id FROM ".self::TERM_TABLE." WHERE LOWER(term_en) LIKE LOWER('$term_en')";
    return $this->db->get_var($sql);
  }

  public function insert_term($term_en)
  {
    $term_en = strtolower($this->db->escape($term_en));

    $data = array('term_en' => $term_en);
    if( $this->db->insert( self::TERM_TABLE,$data, array( '%s' ) ) !== FALSE )
    {
      return $this->db->insert_id;
    }

    return FALSE;
  }

  public function get_lang_id($lang_code)
  {
    $sql = "SELECT language_id
    				FROM ".self::LANG_TABLE."
            WHERE code_lang = '$lang_code'";
    return $this->db->get_var($sql);
  }

  public function term_translation_exists($term_id,$lang_code)
  {
    $lang_code = $this->db->escape($lang_code);
    $term_id   = $this->db->escape($term_id);

    $sql = "SELECT trans_term_id
    				FROM ".self::TRANSLATION_TABLE."
            LEFT JOIN ".self::LANG_TABLE." ON ".self::TRANSLATION_TABLE.".lang_id = ".self::LANG_TABLE.".language_id
            WHERE code_lang = '$lang_code'
              AND term_id = ".$term_id;
    return $this->db->get_var($sql);
  }

  public function insert_term_translation($lang_id, $term_en_id, $term_translation)
  {
    $data = array('term_id' => $term_en_id,
    							'lang_id' => $lang_id,
                  'term' => $term_translation);
    return $this->db->insert( self::TRANSLATION_TABLE,$data, array( '%d','%d','%s' ) );
  }
  public function update_term_translation($term_translate_id, $term_translation)
  {
    $dataids = array('trans_term_id' => $term_translate_id);
    $data    = array('term' => $term_translation);
    $this->db->update( self::TRANSLATION_TABLE, $data, $dataids, array( '%s' ), array( '%d' ) );
  }
}