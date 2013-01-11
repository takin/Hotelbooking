<?php
/**
 * @author Louis-Michel
 *
 */
class Db_links extends CI_Model
{
  const LINKS_TABLE = 'site_links';
  const DOMAINS_TABLE = 'site_domains';
  const TRANSLATION_LINK_TABLE = 'translation_links';

  public $cached_property_type_links = array();

  function Db_links()
  {
      parent::__construct();
      $this->db->simple_query("SET NAMES 'utf8'");
      $this->cached_property_type_links = array();
  }

  function get_link($keyword)
  {

    $query = "SELECT link FROM site_links";
    $query.= " JOIN site_domains ON site_links.site_domain_id = site_domains.site_domain_id";
    $query.= " JOIN links ON site_links.link_id = links.link_id";
    $query.= " WHERE ( LOWER(site_domain) LIKE LOWER('%".$this->db->escape_like_str($_SERVER['HTTP_HOST'])."')";
    $query.= "      OR LOWER(secure_site_domain) LIKE LOWER('%".$this->db->escape_like_str($_SERVER['HTTP_HOST'])."') )";
    $query.= " AND keyword LIKE '".$this->db->escape_like_str($keyword)."'";

    $query = $this->db->query($query);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      $link = $row->link;
      if(strcmp(mb_substr($link, -5, 5),"/:any")==0) $link = mb_substr($link, 0, -5);
      return $link;
    }
    log_message('error', 'Link keyword unknown: '.$keyword. ' for host '.$_SERVER['HTTP_HOST']);

    return "";
  }

  function build_property_page_link($property_type, $property_name, $property_number, $lang = "en")
  {
    $property_type = $this->get_property_type_link($property_type, $lang);

    return site_url($property_type.'/'.url_title(iconv('UTF-8', 'ASCII//TRANSLIT', $property_name)).'/'.$property_number);
  }

  function get_property_type_link($property_type, $lang = "en")
  {
    $lang = $this->lang_code_convert($lang);
    $property_type = $this->db->escape_str($property_type);

    if(!empty($this->cached_property_type_links[$lang][$property_type]))
    {
      return $this->cached_property_type_links[$lang][$property_type];
    }
    $field = "term_$lang";
    $this->db->select("`$field`");
    $this->db->select('term_en');
    $this->db->where("LOWER(term) LIKE LOWER('$property_type')");
    $query = $this->db->get(self::TRANSLATION_LINK_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      if(!empty($row->$field))
      {
        $this->cached_property_type_links[$lang][$property_type] = $row->$field;
        return $row->$field;
      }
      return $row->term_en;
    }

    return NULL;

  }

  function get_property_type_term($property_type, $lang = "en")
  {
    $lang = $this->lang_code_convert($lang);
    $property_type = $this->db->escape_str($property_type);

    $field = "term_$lang";
    $this->db->select($field);
    $this->db->select('term');
    $this->db->where("LOWER(`$field`) LIKE LOWER('$property_type')");
    $query = $this->db->get(self::TRANSLATION_LINK_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->term;
    }
    return NULL;
  }

  public function update_term_translation_link($term, $term_lang, $lang)
  {
    $db_term = $this->get_translation_link_term($term);
    if(empty($db_term))
    {
      $this->insert_term_translation_link($term);
    }

    $this->udpate_translation_link($term, $term_lang, $lang);
  }

  public function insert_term_translation_link($term)
  {
    $this->db->set('term', (string)$term);
    $this->db->set('term_en', (string)$term);
    return $this->db->insert(self::TRANSLATION_LINK_TABLE);
  }

  public function udpate_translation_link($term, $term_lang, $lang)
  {
    $lang = $this->lang_code_convert($lang);

    $this->db->set('term_'.$lang, (string)$term_lang);
    $this->db->where('term', (string)$term);
    return $this->db->update(self::TRANSLATION_LINK_TABLE);
  }
//obsolete get_translation_link_term not used anymore not accurate for translation that can have multiple terms like
//'Centro-de-la-ciudad' => 'City-Center'
//'Centro-de-la-ciudad' => 'Midtown'
//'Centro-de-la-ciudad' => 'Centretown'
  public function get_translation_link_term($term)
  {
    if(empty($term))
    {
      return null;
    }

    $this->db->where('term', (string)$term);
    $this->db->or_where('term_en', (string)$term);
    $this->db->or_where('term_fr', (string)$term);
    $this->db->or_where('term_es', (string)$term);
    $this->db->or_where('term_de', (string)$term);
    $this->db->or_where('term_pt', (string)$term);
    $this->db->or_where('term_zh-CN', (string)$term);
    $this->db->or_where('term_it', (string)$term);
    $this->db->or_where('term_pl', (string)$term);
    $this->db->or_where('term_ru', (string)$term);
    $this->db->or_where('term_no', (string)$term);
    $this->db->or_where('term_fi', (string)$term);
    $this->db->or_where('term_cs', (string)$term);
    $this->db->or_where('term_ko', (string)$term);
    $this->db->or_where('term_ja', (string)$term);
    $this->db->or_where('term_hu', (string)$term);

    $query = $this->db->get(self::TRANSLATION_LINK_TABLE);

    if ($query->num_rows() > 0)
    {
      $row = $query->row();
      return $row->term;
    }

    return NULL;
  }

  function get_lang_from_domain($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);

    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->lang;
    }

    return NULL;
  }

  /**
   * Get a locale from a domain name
   *
   * @access  public
   * @param domain_name
   * @return  locale
   */
  function get_locale_from_domain($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);
    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->locale;
    }

    return NULL;
  }

  /**
   * Get CI langage from domain name
   *
   * @access  public
   * @param domain_name
   * @return  CI langage
   */
  function get_ci_lang_from_domain($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);

    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->CI_lang;
    }

    return NULL;
  }

  /**
   * Get Configuration filename from domain name
   *
   * @access  public
   * @param domain_name
   * @return  configuration filename
   */
  function get_conf_file_from_domain($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);
    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->conf_filename;
    }

    return NULL;
  }

  /**
   * Get HTML langage code from domain name
   *
   * @access  public
   * @param domain_name
   * @return  HTML langage code
   */
  function get_html_lang_from_domain($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);
    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->HTML_lang_code;
    }

    return NULL;
  }

  /**
   * Get domain ID
   *
   * @access  public
   * @param domain_name
   * @return  domain ID
   */
  function get_domain_ID($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);
    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->site_domain_id;
    }

    return 0;
  }

  /**
   * Get domain object data
   *
   * @access  public
   * @param domain_name
   * @return  domain object
   */
  function get_domain($domain_name)
  {
    $domain_name = $this->db->escape_str($domain_name);

    $this->db->where("LOWER(site_domain) LIKE LOWER('%$domain_name')");
    $this->db->or_where("LOWER(secure_site_domain) LIKE LOWER('%$domain_name')");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      return $query->row();
    }

    return NULL;
  }

  /**
   * Get all domain result data
   *
   * @access  public
   * @param
   * @return  domain result set
   */
  function get_all_domains()
  {
    $this->db->order_by("site_domain_id");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() > 0)
    {
      return $query->result();
    }

    return NULL;
  }

  function select_domains($label_title,$id,$name,$value)
  {
    $domains = $this->get_all_domains();
    ?>
    <label for="<?php echo $id; ?>"><?php echo $label_title; ?></label>
    <select id="<?php echo $id; ?>" name="<?php echo $name; ?>">
    <?php

    foreach($domains as $domain)
    {
      ?>
      <option value="<?php echo $domain->site_domain; ?>"<?php if($value==$domain->site_domain) echo " selected=\"selected\" ";?>><?php echo $domain->site_domain; ?></option>
      <?php
    }
    ?>

    </select>
    <?php
  }
  /**
   * Get all domain langresult data
   *
   * @access  public
   * @param
   * @return  domain result set
   */
  function get_all_domains_distinct_lang()
  {
    $this->db->select('lang');
    $this->db->select('site_domain');
    $this->db->select('locale');
    $this->db->group_by("lang");
    $this->db->order_by("site_domain_id");
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() > 0)
    {
      return $query->result();
    }

    return NULL;
  }
  /**
   * Get domain name from domain ID
   *
   * @access  public
   * @param domain_id
   * @return  domain name
   */
  function get_domain_name($domain_id)
  {
    $this->db->where("site_domain_id",$domain_id);
    $query = $this->db->get(self::DOMAINS_TABLE);

    if ($query->num_rows() == 1)
    {
      $row = $query->row();
      return $row->site_domain;
    }

    return NULL;
  }

  /**
   * Build a dropdown menu selection of languages
   *
   * @access  public
   * @param select_id
   * @param $select_name
   * @param $lang_selected
   * @param $otherAttributes
   */
  function select_lang($select_id,$select_name,$lang_selected_id = 0, $otherAttributes = "")
  {
    $this->db->group_by("lang_description");
    $this->db->order_by("lang_description", "ASC");
    $query = $this->db->get(self::DOMAINS_TABLE);

    ?>
    <select <?php echo $otherAttributes; ?> name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>">
    <?php
    foreach ($query->result() as $row)
    {
      ?>
      <option <?php if($lang_selected_id == $row->site_domain_id) echo "selected=\"selected\" "; ?>value="<?php echo $row->site_domain_id; ?>"><?php echo $row->lang_description;?></option>
      <?php
    }
    ?>
    </select>
    <?php
  }

  /**
   *  langage support
   */
  function lang_code_convert($lang_code)
  {
    switch(strtolower($lang_code))
    {
      case strtolower("fr"):
        return "fr";
      case strtolower("en"):
        return "en";
      case strtolower("es"):
        return "es";
      case strtolower("de"):
        return "de";
      case strtolower("pt"):
        return "pt";
      case strtolower("it"):
        return "it";
      case strtolower("zh-CN"):
        return "zh-CN";
      case strtolower("pl"):
        return "pl";
      case strtolower("ru"):
        return "ru";
      case strtolower("no"):
        return "no";
      case strtolower("fi"):
        return "fi";
      case strtolower("cs"):
        return "cs";
      case strtolower("ko"):
        return "ko";
      case strtolower("ja"):
        return "ja";
      case strtolower("hu"):
        return "hu";
      default:
        return "en";
    }

    return "en";
  }
}
?>