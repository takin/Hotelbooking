<?php
class Db_translate
{
  var $db = NULL;

  function Db_translate($conn)
  {
    $this->db = $conn;
  }

  function translate_top_hostels($api, $top_hostels, $lang = "en")
  {
    require_once(get_template_directory()."/translator_tools/translator_tools.php");

    $translations = new TranslatorTool();
    $translations->start_batch_translate($lang);

    if(!empty($top_hostels))
    {
      foreach($top_hostels as $top_hostel)
      {
        if(empty($top_hostel->translated_desc))
        {
          $translations->add_batch_translate($top_hostel->hostel_desc_en,$top_hostel->property_number);
        }
      }

      $desc_translations = $translations->end_batch_translate();

      if(!empty($desc_translations))
      {
        foreach($top_hostels as $top_hostel)
        {
          if(empty($top_hostel->translated_desc) && !empty($desc_translations[$top_hostel->property_number]))
          {
            $top_hostel->translated_desc = $desc_translations[$top_hostel->property_number];
          }
        }
      }
    }
    return $top_hostels;
  }

}
?>