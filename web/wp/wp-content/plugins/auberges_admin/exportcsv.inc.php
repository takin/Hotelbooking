<?php
 
function exportMysqlToCsv($sql_query,$header="",$filename = 'export.csv')
{
    $aubergedb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME_AUBERGE, DB_HOST);
    $aubergedb->hide_errors();
    
    
    $csv_terminated = "\n";
    $csv_separator = ";";
    $csv_enclosed = '"';
    $csv_escaped = "\\";
 
    // Gets the data from the database
    $results = $aubergedb->get_results($sql_query,ARRAY_N);
  
    $schema_insert = $header;
// 
//    for ($i = 0; $i < $fields_cnt; $i++)
//    {
//        $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
//            stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
//        $schema_insert .= $l;
//        $schema_insert .= $csv_separator;
//    } // end for
 
//    $out = trim(substr($schema_insert, 0, -1));
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
            } else
            {
                $schema_insert .= '';
            }
 
            if ($j < count($row) - 1)
            {
                $schema_insert .= $csv_separator;
            }
        } // end for
 
        $out .= $schema_insert;
        $out .= $csv_terminated;
    } // end while
 
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Length: " . strlen($out));
    // Output to browser with appropriate mime type, you choose ;)
//    header("Content-type: text/x-csv");
    //header("Content-type: text/csv");
    header("Content-type: application/csv");
    header("Content-Disposition: attachment; filename=$filename");
    echo $out;
    exit;
 
}
 
?>