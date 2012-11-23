<?php
class Db_links
{
  const DOMAINS_TABLE = 'site_domains';
  const TRANSLATION_LINK_TABLE = 'translation_links';

  var $_dbconn = NULL;
  var $db = "";
  var $_host = "";

  function Db_links($conn,$host)
  {
    $this->db = $conn;
    $this->_host   = $this->db->escape($host);
  }

  function get_link($keyword)
  {
    $keyword = $this->db->escape($keyword);

    $query = "SELECT * FROM site_links";
    $query.= " JOIN site_domains ON site_links.site_domain_id = site_domains.site_domain_id";
    $query.= " JOIN links ON site_links.link_id = links.link_id";
    $query.= " WHERE site_domain LIKE '%".$this->_host."'";
    $query.= " AND keyword LIKE '".$keyword."'";

    $query = $this->db->get_row($query);

    if (!empty($query))
    {
      $link = $query->link;
      if(strcmp(substr($link, -5, 5),"/:any")==0) $link = substr($link, 0, -5);
      return $link;
    }

    return "";
  }

  function get_lang_from_domain($domain_name)
  {
    $domain_name = $this->db->escape($domain_name);

    $query = "SELECT lang FROM ".self::DOMAINS_TABLE;
    $query.= " WHERE LOWER(site_domain) LIKE LOWER('%$domain_name')";

    $lang = $this->db->get_var($query);
    return $lang;
  }

  public function get_domain($domain_name)
  {
    $domain_name = $this->db->escape($domain_name);

    $query = "SELECT * FROM ".self::DOMAINS_TABLE;
    $query.= " WHERE LOWER(site_domain) LIKE LOWER('%$domain_name')
                  OR LOWER(secure_site_domain) LIKE LOWER('%$domain_name')";

    $domain = $this->db->get_row($query);
    return $domain;
  }

  function get_property_type_link($property_type, $lang = "en")
  {
    $property_type = $this->db->escape($property_type);

    $query = "SELECT * FROM ".self::TRANSLATION_LINK_TABLE;
    $query.= " WHERE LOWER(term) LIKE LOWER('%$property_type')";

    $row = $this->db->get_row($query);
    if (!empty($query))
    {
      $field = "term_$lang";
      if(!empty($row->$field)) return $row->$field;
      return $row->term_en;
    }

    return NULL;
  }
}
?>