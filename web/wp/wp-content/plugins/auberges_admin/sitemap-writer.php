<?php

class GoogleSitemapGeneratorXmlEntry {

  var $_xml;

  function GoogleSitemapGeneratorXmlEntry($xml)
  {
    $this->_xml = $xml;
  }

  function Render()
  {
    return $this->_xml;
  }
}

class GoogleSitemapGeneratorDebugEntry extends GoogleSitemapGeneratorXmlEntry
{
  function Render()
  {
    return "<!-- " . $this->_xml . " -->\n";
  }
}
class GoogleSitemapGeneratorPage {

  /**
   * @var string $_url Sets the URL or the relative path to the blog dir of the page
   * @access private
   */

  var $_url;

  /**
   * @var float $_priority Sets the priority of this page
   * @access private
   */

  var $_priority;

  /**
   * @var string $_changeFreq Sets the chanfe frequency of the page. I want Enums!
   * @access private
   */

  var $_changeFreq;

  /**
   * @var int $_lastMod Sets the lastMod date as a UNIX timestamp.
   * @access private
   */

  var $_lastMod;

  /**
   * Initialize a new page object
   *
   * @since 3.0
   * @access public
   * @author Arne Brachhold
   * @param bool $enabled Should this page be included in thesitemap
   * @param string $url The URL or path of the file
   * @param float $priority The Priority of the page 0.0 to 1.0
   * @param string $changeFreq The change frequency like daily, hourly, weekly
   * @param int $lastMod The last mod date as a unix timestamp
   */

  function GoogleSitemapGeneratorPage($url="",$priority=0.0,$changeFreq="never",$lastMod=0)
  {
    $this->SetUrl($url);
    $this->SetProprity($priority);
    $this->SetChangeFreq($changeFreq);
    $this->SetLastMod($lastMod);
  }

  /**
   * Returns the URL of the page
   *
   * @return string The URL
   */

  function GetUrl()
  {
    return $this->_url;
  }

  /**
   * Sets the URL of the page
   *
   * @param string $url The new URL
   */

  function SetUrl($url)
  {
    $this->_url=(string) $url;
  }

  /**
   * Returns the priority of this page
   *
   * @return float the priority, from 0.0 to 1.0
   */

  function GetPriority()
  {
    return $this->_priority;
  }

  /**
   * Sets the priority of the page
   *
   * @param float $priority The new priority from 0.1 to 1.0
   */

  function SetProprity($priority)
  {
    $this->_priority=floatval($priority);
  }

  /**
   * Returns the change frequency of the page
   *
   * @return string The change frequncy like hourly, weekly, monthly etc.
   */

  function GetChangeFreq()
  {
    return $this->_changeFreq;
  }

  /**
   * Sets the change frequency of the page
   *
   * @param string $changeFreq The new change frequency
   */

  function SetChangeFreq($changeFreq)
  {
    $this->_changeFreq=(string) $changeFreq;
  }

  /**
   * Returns the last mod of the page
   *
   * @return int The lastmod value in seconds
   */

  function GetLastMod()
  {
    return $this->_lastMod;
  }

  /**
   * Sets the last mod of the page
   *
   * @param int $lastMod The lastmod of the page
   */

  function SetLastMod($lastMod)
  {
    $this->_lastMod = intval($lastMod);
  }

  function Render()
  {
    if($this->_url == "/" || empty($this->_url)) return '';

    $r="";
    $r.= "\t<url>\n";
    $r.= "\t\t<loc>" . $this->EscapeXML($this->_url) . "</loc>\n";

    if($this->_lastMod>0) $r.= "\t\t<lastmod>" . date('Y-m-d\TH:i:s+00:00',$this->_lastMod) . "</lastmod>\n";

    if(!empty($this->_changeFreq)) $r.= "\t\t<changefreq>" . $this->_changeFreq . "</changefreq>\n";

    if($this->_priority!==false && $this->_priority!=="") $r.= "\t\t<priority>" . number_format($this->_priority,1) . "</priority>\n";

    $r.= "\t</url>\n";

    return $r;
  }
  
  function EscapeXML($string)
  {
    return str_replace ( array ( '&', '"', "'", '<', '>'), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;'), $string);
  }
  
}


class SitemapWriter
{
  
  var $_fileName       = "sitemap.xml";
  var $_filePath       = "";
  
  var $stylesheetXml   = "";
  
  var $_fileHandle    = NULL;
  var $_fileZipHandle = NULL;
  
  var $_error = FALSE;
  var $_errorMsg = "Undefined error.";
  
  var $urlCount = 0;
  
  function SitemapWriter($filePath,$fileName,$stylesheet = "")
  {
    $this->setFile($filePath,$fileName);
    $this->stylesheetXml = $stylesheet;
  }
  
  function setFile($filePath,$fileName)
  {
    $this->_fileName = $fileName;
    $this->_filePath = $filePath;
    
    if(is_writable($this->_filePath))
    {
      $this->_fileHandle = @fopen($this->_filePath.$this->_fileName,"w");
      
      if($this->_fileHandle == FALSE)
      {
        $this->_error = TRUE;
        $this->_errorMsg = "Could not write to sitemap file: ".$this->_filePath.$this->_fileName.".";
      }
    }
    else
    {
      $this->_error = TRUE;
      $this->_errorMsg = "Could not write to sitemap path: ".$this->_filePath.".";
      return false;
    }
  }
  
  function getSitemapStatus()
  {
   if($this->_error == TRUE)
   {
     return $this->_errorMsg;
   }
   else
   {
     return "Sitemap Generated OK.";
   }
  }
  
  function getSitemapError()
  {
   return $this->_error;
  }
  
  function AddElement(&$page)
  {
    if(empty($page)) return;

    $s = $page->Render();

//    if($this->_fileZipHandle && $this->IsGzipEnabled())
//    {
//      gzwrite($this->_fileZipHandle,$s);
//    }

    if($this->_fileHandle)
    {
      fwrite($this->_fileHandle,$s);
    }
  }
  
/**
   * Adds a url to the sitemap.
   *
   * @since 3.0
   * @access public
   * @param $loc string The location (url) of the page
   * @param $lastMod int The last Modification time as a UNIX timestamp
   * @param $changeFreq string The change frequenty of the page, Valid values are "always", "hourly", "daily", "weekly", "monthly", "yearly" and "never".
   * @param $priority float The priority of the page, between 0.0 and 1.0
   * @see AddElement
   * @return string The URL node
   */

  function AddUrl($loc, $lastMod = 0, $changeFreq = "monthly", $priority = 0.5)
  {
    $page = new GoogleSitemapGeneratorPage($loc, $priority, $changeFreq, $lastMod);
    $this->AddElement($page);
    $this->urlCount++;
  }
  
  function get_url_count()
  {
    return $this->urlCount;
  }
  
  function initSitemap()
  {
    $this->AddElement(new GoogleSitemapGeneratorXmlEntry('<?xml version="1.0" encoding="UTF-8"' . '?' . '>'));
    
    if(!empty($this->stylesheetXml))
    {
      $this->AddElement(new GoogleSitemapGeneratorXmlEntry('<' . '?xml-stylesheet type="text/xsl" href="' . $this->stylesheetXml . '"?' . '>'));
    }
    
    $this->AddElement(new GoogleSitemapGeneratorXmlEntry('<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"));
  }
  
  function closeSitemap()
  {
    $this->AddElement(new GoogleSitemapGeneratorXmlEntry("</urlset>"));
  }
}
?>