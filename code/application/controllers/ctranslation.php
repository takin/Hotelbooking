<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ctranslation extends I18n_site
{
  const CRON_CODE = 'aEc3FvF6f754Bjida2QMp7gR';
  const CSV_DIR = "/srv/d_mcweb2/csv/";

  public function Ctranslation()
  {
    parent::I18n_site();

    //Ensure this controller is called by server and that cron code is good
//    if(  (strcmp($_SERVER["REMOTE_ADDR"],$_SERVER["SERVER_ADDR"])!=0) OR
//       (strcmp($this->uri->segment(3,""),self::CRON_CODE)!=0))
   if( strcmp($this->uri->segment(3,""),self::CRON_CODE)!=0 )
   {
     show_404();
     exit();
   }

  }

  public function parse_lang_csv()
  {
    show_404();
    echo "test";
    exit;
    $csv_file = self::CSV_DIR."Roomtype_aftercutoverJan82012.csv";
    print $csv_file."<br>";
    $row = 1;

    $columns = array();
    $key_column = 'english';

    $column_def = array(
                      0 => "tag_id",
                      1 => "replace_number",
                      2 => "english",
                      3 => "fr",
                      4 => "es",
                      5 => "de",
                      6 => "pt",
                      7 => "zh-CN",
                      8 => "it",
                      9 => "pl",
                      10 => "ru",
                      11 => "fi",
                      12 => "cs",
                      13 => "ko",
                      14 => "ja",
                      15 => "hu"
                  );

    if (($handle = fopen($csv_file, "r")) !== FALSE)
    {
      $this->load->model('i18n/db_translation_cache');
      $this->load->model('hw_api_translate');

      while (($columns = fgetcsv($handle, 0, ";","\"")) !== FALSE)
      {
        if($row>1)
        {
//           debug_dump($columns);
          foreach($columns as $n => $translation)
          {
            $tag_text       = trim($columns[0]);
            $replace_action = trim($columns[1]);
            $english_text   = trim($columns[2]);
            //IF row is not tag or replace number filed or english original text AND translation not empty
            // proceed with adding translation in DB
            if(($n > 2) && !empty($translation))
            {

              $english_text = mb_strtolower($english_text, 'UTF-8');

              //Auto number detect on column 1 being auto or not no:
              //If original text as a number in it that is higher than 1
              // translate number to %d in original text and translation
              // Example: room for 4 people: room for %d people => chambre poour %d
              //Else
              // Keep number in translation (means no number in original text or number is 1)
              // Example: quad room => chambre pour 4

              if(((strcasecmp($replace_action,"no")!=0) || strcasecmp($replace_action,"auto")==0))
              {
                $number = $this->hw_api_translate->extract_number($english_text);
                if(!is_null($number) && ($number > 1))
                {
                  //Replace all number by %d
                  $english_text = preg_replace("/(\d+)/", '%d',$english_text);
                  $translation = preg_replace("/(\d+)/", '%d',$translation);
                }
              }
              $translation = trim($translation);
//               debug_dump("en => ".$english_text);
//               debug_dump($column_def[$n]." => ".$translation);
//               debug_dump("tag => ".$tag_text);
//               debug_dump("source => 13");
//               debug_dump("-------------------------------------");
              //Source ID 13: human translation
              $ok = $this->db_translation_cache->cache_translation($english_text,
                                                             $translation,
                                                             $column_def[$n],
                                                             "en",
                                                             13,
                                                             $tag_text );
              if($ok === FALSE)
              {
                debug_dump("erreur inserting:");
                debug_dump($columns);
              }
            }
          }
//           exit;
//           if($row > 1) break;
        }
        $row++;
      }

//       debug_dump($columns);
//       debug_dump($column_def);
    }
    else
    {
      echo "could not open";
    }
  }
}
?>