<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Csv extends I18n_site
{
  const CRON_CODE = 'aEc3FvF6f754Bjida2QMp7gR';

  public function __construct()
  {

    parent::__construct();

    show_404();
    exit;
  }

  public function import_room_types_translation()
  {
    show_404();
    exit;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');

    $this->load->library('Csv_lib');
    $this->load->model('i18n/db_translation_cache');

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    echo "starting CSV import";
    $this->csv_lib->format(",",'"');
    $this->csv_lib->init("cache_queries/","Room_type_sept13-2012.csv");
//     $this->csv_lib->init("cache_queries/","Amenities_Aug15-2012.csv");

    $limit = 0;
    $count = 0;

    $error = 0;

    $tag = '';
    while (($line = $this->csv_lib->line()) !== FALSE)
    {

      $count ++;
      //TODO !!!!at the end of file this is empty so it creates on infinit loop!
      if(empty($line["room_description_source"]))
//       if(empty($line["en"]))
      {
        debug_dump("error empty english room desc $count");
        debug_dump($line);
        $error++;
        if($error > 10)
        {
          echo "too much errors $error after count $count<br>";
          exit;
        }
//         continue;
      }
      else
      {
        set_time_limit(30);
//         debug_dump($line);
//         exit;
//         $this->db_translation_cache->cache_lang_array($line, "en","en", $tag, 13);
        $this->db_translation_cache->cache_lang_array($line, "room_description_source","en", $tag, 13);
      }
//       exit;

    }
    echo "Tried to add $count translations to DB";
    echo "but $error csv row contains error";
    exit;
  }

  public function import_hb_room_desc_translation()
  {
    show_404();
    exit;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');

    $this->load->library('Csv_lib');
    $this->load->model('i18n/db_translation_cache');
    $this->load->model('Db_districts');

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    echo "starting CSV import";
    $this->csv_lib->init("cache_queries/","HB_ROOM_FINAL_V2_CLOSED.csv");

    $limit = 0;
    $count = 0;

    $error = 0;

    while (($line = $this->csv_lib->line()) !== FALSE)
    {
      set_time_limit(30);
      $count ++;
      if(empty($line["en"]))
      {
        debug_dump("error empty english room desc $count");
        debug_dump($line);
        $error++;
        continue;
      }

//       $orig_lang = "en";
//       debug_dump($line);
//TODO method has changed pleade adjust parameter before using
//       $this->db_translation_cache->cache_lang_array($line, "en", "HB room type description", $source_id = 13);
//       exit;


    }
    echo "Tried to add $count translations to DB";
    echo "but $error csv row contains error";
    exit;
  }

  public function import_districts_translation()
  {
    show_404();
    exit;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');

    $this->load->library('Csv_lib');
    $this->load->model('i18n/db_translation_cache');
    $this->load->model('Db_districts');

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    echo "starting CSV import";
    $this->csv_lib->init("cache_queries/","districts_translation.csv");

    $limit = 0;
    $count = 0;
    while (($line = $this->csv_lib->line()) !== FALSE)
    {
      set_time_limit(30);
      $count ++;
      if(empty($line["en"]))
      {
        debug_dump("error empty name $count");
        //         debug_dump($line);
        continue;
      }

      $orig_lang = "en";
      if($line[$orig_lang] !== $line["original"])
      {
        foreach($line as $col => $val)
        {
          if($col === "original") continue;

          if($val === $line["original"])
          {
            $orig_lang = $col;
            break;
          }
        }
//         debug_dump($line);
//         debug_dump($orig_lang);
//         exit;
      }
//       else
//       {
//         continue;
//       }

/*
      //Fr ------------------------------------------
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["fr"]),
                                                            'fr',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["fr"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["en"]),
                                                            'en',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["fr"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["es"]),
                                                            'es',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["es"]);
      }

      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["de"]),
                                                            'de',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );

      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["de"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["pt"]),
                                                            'pt',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["pt"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["it"]),
                                                            'it',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["it"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["zh-CN"]),
                                                            'zh-CN',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["zh-CN"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["pl"]),
                                                            'pl',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["pl"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["ru"]),
                                                            'ru',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["ru"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["fi"]),
                                                            'fi',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["fi"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["cs"]),
                                                            'cs',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["cs"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["ko"]),
                                                            'ko',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["ko"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["ja"]),
                                                            'ja',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["ja"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line[$orig_lang]),
                                                            trim($line["hu"]),
                                                            'hu',
                                                            $orig_lang,
                                                            13,
                                                            "um district" );
      if($ok === FALSE)
      {
      debug_dump("erreur inserting:".$line["hu"]);
      }

*/
      //Add landmarks translation links
      $link_term            = $this->Db_districts->create_slug(trim($line["original"]));
      $link_term_translated = $this->Db_districts->create_slug(trim($line["fr"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'fr');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["en"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'en');

      $link_term_translated = $this->Db_districts->create_slug(trim($line["es"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'es');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["de"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'de');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["pt"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'pt');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["it"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'it');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["zh-CN"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'zh-CN');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["pl"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'pl');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["ru"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'ru');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["fi"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'fi');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["cs"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'cs');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["ko"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'ko');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["ja"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'ja');
      $link_term_translated = $this->Db_districts->create_slug(trim($line["hu"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'hu');

//       debug_dump($line);
//       exit;

    }
  }
  public function dis_setup()
  {
    show_404();
    exit;
    $this->load->model('Db_districts');
    $this->Db_districts->setup_district_slug();
    echo "done";
  }
  public function import_custom_landmarks_translation()
  {
    show_404();
    exit;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');

    $this->load->library('Csv_lib');
    $this->load->model('i18n/db_translation_cache');
    $this->load->model('Db_landmarks');

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    echo "starting CSV import";
    $this->csv_lib->init("cache_queries/","custom_landmarks.csv");

    $limit = 0;
    $count = 0;
    while (($line = $this->csv_lib->line()) !== FALSE)
    {
      set_time_limit(30);
      $count ++;
      if(empty($line["en"]))
      {
        debug_dump("error empty name $count");
//         debug_dump($line);
        continue;
      }


      /*
      //Fr ------------------------------------------
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["fr"]),
                                                           'fr',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["fr"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["es"]),
                                                           'es',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["es"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["de"]),
                                                           'de',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["de"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["pt"]),
                                                           'pt',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["pt"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["it"]),
                                                           'it',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["it"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["zh-CN"]),
                                                           'zh-CN',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["zh-CN"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["pl"]),
                                                           'pl',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["pl"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["ru"]),
                                                           'ru',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["ru"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["fi"]),
                                                           'fi',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["fi"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["cs"]),
                                                           'cs',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["cs"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["ko"]),
                                                           'ko',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["ko"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["ja"]),
                                                           'ja',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["ja"]);
      }
      $ok = $this->db_translation_cache->cache_translation(trim($line["en"]),
                                                           trim($line["hu"]),
                                                           'hu',
                                                           "en",
                                                           13,
                                                           "custom landmark" );
      if($ok === FALSE)
      {
        debug_dump("erreur inserting:".$line["hu"]);
      }
*/
      //Add landmarks translation links
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["fr"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'fr');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["es"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'es');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["de"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'de');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["pt"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'pt');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["it"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'it');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["zh-CN"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'zh-CN');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["pl"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'pl');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["ru"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'ru');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["fi"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'fi');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["cs"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'cs');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["ko"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'ko');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["ja"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'ja');
      $link_term            = $this->Db_landmarks->create_landmark_slug(trim($line["en"]));
      $link_term_translated = $this->Db_landmarks->create_landmark_slug(trim($line["hu"]));
      $this->Db_links->update_term_translation_link($link_term,$link_term_translated,'hu');

    }

    echo "done!";
  }
  public function import_custom_landmarks()
  {
    show_404();
    exit;
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    ini_set('memory_limit', '512M');

    $this->load->library('Csv_lib');
    $this->load->model('Db_landmarks');

    //disable query history because with multiple query it will cause memory problems
    $this->db->save_queries = false;

    echo "starting CSV import";
    $this->csv_lib->init("cache_queries/","custom_landmarks.csv");

    $source_id = $this->Db_landmarks->get_landmark_source_id('manual');

    $limit = 0;
    $count = 0;
    while (($line = $this->csv_lib->line()) !== FALSE)
    {
      set_time_limit(20);
      $count ++;
      if(empty($line["en"]))
      {
        debug_dump("error empty name $count");
        continue;
      }

      //set landmark object to add to DB
      $landmark->geometry->location->lat = $line["Latitude"];
      $landmark->geometry->location->lng = $line["Longitude"];
      $landmark->name                    = $line["en"];
      $landmark->id                      = $this->Db_landmarks->create_landmark_hash($landmark,$source_id);
      $landmark->slug                    = $this->Db_landmarks->create_landmark_slug($landmark->name);


      $landmark->types = explode('|',mb_strtolower($line["Type"], 'UTF-8'));
      //Update landmark DB
      $landmark_id = $this->Db_landmarks->update_landmark($landmark,'manual');

      //Link to closest properties
//       $this->Db_landmarks->update_landmark_close_properties($landmark_id);
    }

    echo "done!";
  }
}
?>