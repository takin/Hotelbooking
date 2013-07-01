<?php

set_time_limit(1200);

if(is_admin())
{

    $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));
  
    $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
    $aubergedb->hide_errors();
    
    
    $csv_terminated = "\n";
    $csv_separator = ";";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
 
    $start_date = date("Y")."-01-01";
    $end_date   = date("Y")."-".date("m")."-".date("d");

    if(!empty($_POST["transaction_year_from"]) &&
       !empty($_POST["transaction_month_from"]) &&
       !empty($_POST["transaction_day_from"]) &&
       !empty($_POST["transaction_year_to"]) &&
       !empty($_POST["transaction_month_to"]) &&
       !empty($_POST["transaction_day_to"]) )
    {
      $start_date = $_POST["transaction_year_from"]."-".$_POST["transaction_month_from"]."-".$_POST["transaction_day_from"];
      $end_date   = $_POST["transaction_year_to"]."-".$_POST["transaction_month_to"]."-".$_POST["transaction_day_to"];
    }
    
    $where = "WHERE DATE(booking_time) >= DATE('$start_date')  AND DATE(booking_time)<= DATE('$end_date')";
    
    // Gets the data from the database
    
    $sql_query = "SELECT s1.site_domain AS transaction_domain, API_booked, booking_time as book_time, transactions_hostelworld.email, first_name, last_name, gender ,home_country, 
                        phone_number, customer_booking_reference, arrival_date_time, transactions_hostelworld.property_number, 
                        transactions_hostelworld.property_name, num_nights,property_grand_total, amount_charged, c1.currency_code AS amount_charged_currency, 
                        property_amount_due, c2.currency_code AS property_currency, hw_country AS property_country, hw_city AS property_city 
                  FROM transactions_hostelworld
                  LEFT JOIN genders ON transactions_hostelworld.gender_id = genders.gender_id
                  LEFT OUTER JOIN currencies c1 ON transactions_hostelworld.charged_currency = c1.currency_id
                  LEFT OUTER JOIN currencies c2 ON transactions_hostelworld.property_currency = c2.currency_id
                  LEFT OUTER JOIN site_domains s1 ON transactions_hostelworld.site_domain_id = s1.site_domain_id
                  LEFT JOIN hw_hostel ON transactions_hostelworld.property_number = hw_hostel.property_number
                  LEFT JOIN hw_city ON hw_hostel.hw_city_id = hw_city.hw_city_id
                  LEFT JOIN hw_country ON hw_city.hw_country_id = hw_country.hw_country_id
                  $where
                  ORDER BY booking_time DESC";
//    print $sql_query;
    $results = $aubergedb->get_results($sql_query,ARRAY_N);
    
    $schema_insert = "\"ID\"".$csv_separator;
    $schema_insert = "\"Domaine\"".$csv_separator;
    $schema_insert.= "\"API\"".$csv_separator;
    $schema_insert.= "\"booking_time\"".$csv_separator;
    $schema_insert.= "\"email\"".$csv_separator;
    $schema_insert.= "\"Prenom\"".$csv_separator;
    $schema_insert.= "\"Nom\"".$csv_separator;
    $schema_insert.= "\"Sexe\"".$csv_separator;
    $schema_insert.= "\"Nationalite\"".$csv_separator;
    $schema_insert.= "\"Telephone\"".$csv_separator;
    $schema_insert.= "\"HW Reference\"".$csv_separator;
    $schema_insert.= "\"Arrivee\"".$csv_separator;
    $schema_insert.= "\"HW Numero propriete\"".$csv_separator;
    $schema_insert.= "\"HW Nom propriete\"".$csv_separator;
    $schema_insert.= "\"Nuits\"".$csv_separator;
    $schema_insert.= "\"Grand total\"".$csv_separator;
    $schema_insert.= "\"Montant charge\"".$csv_separator;
    $schema_insert.= "\"Devise du montant\"".$csv_separator;
    $schema_insert.= "\"Montant total du\"".$csv_separator;
    $schema_insert.= "\"Devise montant total\"".$csv_separator;
    $schema_insert.= "\"Property country\"".$csv_separator;
    $schema_insert.= "\"Property city\"".$csv_separator;
    //Adwords keywords
    $sql_query = "SELECT keyword FROM refer_keywords";
    $sql_query.= " ORDER BY keyword_id";

    $adwords = $aubergedb->get_results($sql_query);
    
    foreach($adwords as $adword)
    {
      $schema_insert .= $csv_enclosed . 
      str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $adword->keyword) . $csv_enclosed;
      $schema_insert.= $csv_separator;
      
    }
    $schema_insert = substr($schema_insert,0,-1);
    
    $out = $schema_insert;
    $out .= $csv_terminated;
 
    // Format the data
    foreach($results as $row)
    {
        $schema_insert = '';
        for ($j = 0; $j < count($row); $j++)
        {
            if ($row[$j] == '0' || $row[$j] != '')
            {
 
                if ($csv_enclosed == '')
                {
                    $schema_insert .= $row[$j];
                } else
                {
                    $schema_insert .= $csv_enclosed . 
          str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
                }
            }
            else
            {
                $schema_insert .= '';
            }
 
            $schema_insert .= $csv_separator;
        } // end for
        
        //Add adword relations
        $sql_query = "SELECT * FROM transaction_keyword WHERE transaction_id = ".$row[0];
        $sql_query.= " ORDER BY keyword_id";

        $adwords = $aubergedb->get_results($sql_query);
        if(count($adwords) > 0)
        {
          foreach($adwords as $adword)
          {
            $schema_insert .= $csv_enclosed . 
          str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $adword->keyword_value) . $csv_enclosed;
            $schema_insert.= $csv_separator;
            
          }
          
        }
        $schema_insert = substr($schema_insert,0,-1);
        
        $out .= $schema_insert;
        $out .= $csv_terminated;
    } // end while
 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    // Output to browser with appropriate mime type, you choose ;)
//    header("Content-type: text/x-csv");
    //header("Content-type: text/csv");
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=aj_trans_".$today.".csv");
    echo $out;
    exit;
  
  
  
  
}
?>