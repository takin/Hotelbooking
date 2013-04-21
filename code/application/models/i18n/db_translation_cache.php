<?php
/**
 *
 */
class Db_translation_cache extends CI_Model
{
  const TEXT_TABLE     = 'translations';
  const SOURCE_TABLE   = 'sources';
  const TAG_TABLE      = 'tags';
  const TAG_TRANSLATION_TABLE      = 'tags_of_translations';

  private $transDB;

  private $lang_supported = array("en","fr","es","de","pt","zh-CN","it","pl","ru","fi","cs","ko","ja","hu");

  function __construct()
  {
      parent::__construct();

      //reset translation memory cache
      $this->transDB  = $this->load->database('translation', TRUE);

      $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
      //$this->load->driver('cache', array('adapter' => 'file'));
  }

  public function add_translation($orig_text, $translation, $lang_code, $orig_lang_code, $source_id, $tag)
  {
    $orig_text = $this->transDB->escape_str($orig_text);

    $this->transDB->set('lang_code', $lang_code);
    $this->transDB->set('ref_hash', "COMPRESS('$orig_text')", FALSE);
    $this->transDB->set('translation', $translation);
    $this->transDB->set('ref_lang_code', $orig_lang_code);
    $this->transDB->set('source_id', $source_id);

    if($this->transDB->insert(self::TEXT_TABLE) !== FALSE)
    {
      $this->add_tag_to_translation($tag,$this->transDB->insert_id());
      return TRUE;
    }

    return FALSE;
  }
  public function update_translation($orig_text, $translation, $lang_code, $orig_lang_code, $source_id)
  {
    log_message('debug', 'Entering update translation');

    $orig_text = $this->transDB->escape_str($orig_text);

    $this->transDB->set('translation', $translation);
    $this->transDB->set('ref_lang_code', $orig_lang_code);
    $this->transDB->set('source_id', $source_id);

    $this->transDB->where('ref_hash', "COMPRESS('$orig_text')", FALSE);
    $this->transDB->where('lang_code', $lang_code);

    return $this->transDB->update(self::TEXT_TABLE);
  }

  public function get_translation($orig_text, $lang_code, $memcached = true)
  {
    $orig_text = $this->transDB->escape_str($orig_text);
    $lang_code = $this->transDB->escape_str($lang_code);

    if(empty($orig_text)) return $orig_text;

    if(is_numeric($orig_text)) return $orig_text;

    if (ISWINDOWS || strlen($orig_text) < 30)
    {
		$cacheKey = $lang_code.'-'.md5($orig_text);
		if ($cache = $this->cache->get($cacheKey))
		{
		  log_message('debug', 'Translation found in the Cache for:'.$orig_text);
		  return $cache;
		}
	}

    log_message('debug', 'language:'.$lang_code.' original text:'.$orig_text);

    $sql = "SELECT ".self::TEXT_TABLE.".translation_id, `translation`,`ref_lang_code`, source, key_slug,
            (
              SELECT group_concat(tag SEPARATOR '|') FROM tags_of_translations
              LEFT JOIN tags ON tags_of_translations.tag_id = tags.tag_id
               WHERE tags_of_translations.translation_id = translations.translation_id
            ) as tags
    				FROM ".self::TEXT_TABLE."
    				LEFT JOIN sources ON translations.source_id = sources.source_id
    				WHERE lang_code = '$lang_code'
              AND ref_hash = COMPRESS('$orig_text')
    				LIMIT 1";

    $query = $this->transDB->query($sql);

    if($query === false)
    {
      log_message('debug', 'Translation NOT Found for:'.$orig_text);
	}
    else if ($query->num_rows() > 0)
    {
      log_message('debug', 'Translation Found '.$query->row()->translation);

      if($memcached === true && (ISWINDOWS || strlen($orig_text) < 30))
      {
      	$this->cache->save($cacheKey, $query->row(), 60000);
        return $query->row();
      }
      else
      {
        return $query->row();
      }
    }
    else
    {
        log_message('debug', 'Translation NOT Found in the DB for:'.$orig_text);
    }
    return FALSE;
  }

  public function cache_translation($orig_text, $translation, $lang_code, $orig_lang_code, $source = "", $tag = "")
  {
    if ($lang_code === $orig_lang_code)
    {
    	return $translation;
    }

    $return = FALSE;
    $trans = $this->get_translation($orig_text, $lang_code, false);

    if($trans === FALSE)
    {
      $return = $this->add_translation($orig_text, $translation, $lang_code, $orig_lang_code, $source, $tag);
    }
    else
    {
      //TODO Update only if values are differents to save on DB performance
      //TODO Also add appropriate behavior when update DB FAIL to update now if tag fail to be added impossible to know
      $return = $this->update_translation($orig_text, $translation, $lang_code, $orig_lang_code, $source);
      $this->add_tag_to_translation($tag,$trans->translation_id);
    }
    return $return;
  }

  public function get_source($source_slug)
  {
    if(empty($source_slug)) return NULL;

    $this->transDB->where("key_slug",$source_slug);
    $query = $this->transDB->get(self::SOURCE_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row;
    }
    return NULL;
  }

  public function get_tag_id($tag, $insert_new = FALSE)
  {
    if(empty($tag)) return NULL;

    $this->transDB->select("tag_id");
    $this->transDB->where("tag",$tag);
    $query = $this->transDB->get(self::TAG_TABLE);

    if($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->tag_id;
    }
    elseif($insert_new === TRUE)
    {
      if($this->insert_tag($tag) === false)
      {
        return NULL;
      }
      else
      {
        return $this->transDB->insert_id();
      }
    }

    return NULL;
  }

  public function insert_tag($tag)
  {
    $this->transDB->set('tag', (string)$tag);
    return $this->transDB->insert(self::TAG_TABLE);
  }

//   public function insert_source($source)
//   {
//     $this->transDB->set('source', (string)$source);
//     return $this->transDB->insert(self::SOURCE_TABLE);
//   }

  public function add_tag_to_translation($tag, $translation_id)
  {
    $tag    = $this->get_tag_id($tag, TRUE);

    $this->transDB->where('tag_id', $tag);
    $this->transDB->where('translation_id', $translation_id);
    $query = $this->transDB->get(self::TAG_TRANSLATION_TABLE);

    if($query->num_rows() > 0)
    {
      return FALSE;
    }

    $this->transDB->set('tag_id', $tag);
    $this->transDB->set('translation_id', $translation_id);
    return $this->transDB->insert(self::TAG_TRANSLATION_TABLE);
  }

  public function cache_lang_array($lang_array, $orig_column, $orig_lang, $tag, $source_id = 13)
  {

    $this->transDB->save_queries = false;
    //TODO add lang array check

    //
    foreach($this->lang_supported as $lang)
    {
      if($lang == $orig_lang)
      {
        continue;
      }
      if(!empty($lang_array[$lang]) && !empty($lang_array[$orig_column]))
      {
//         debug_dump("translating ".trim($lang_array[$orig_lang]) . " to ". trim($lang_array[$lang]) ." from $orig_lang to $lang - $source_id $tag");
        $ok = $this->cache_translation(trim($lang_array[$orig_column]),
                                       trim($lang_array[$lang]),
                                       $lang,
                                       $orig_lang,
                                       $source_id,
                                       $tag );
        if($ok === FALSE)
        {
          debug_dump("erreur inserting:".$lang_array[$lang]);
        }
      }
    }

    return TRUE;
  }
}
?>
