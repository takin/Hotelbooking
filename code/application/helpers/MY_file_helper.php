<?php

function stream_copy($src, $dest) 
{ 
    $len = 0;
    $fsrc = fopen($src,'r'); 
    if($fsrc !== false)
    {
      $fdest = fopen($dest,'w+'); 
      $len = stream_copy_to_stream($fsrc,$fdest); 
      fclose($fsrc); 
      fclose($fdest);
    } 
    return $len; 
} 

function limit_file_count_of_name($backupDir,$filename,$nbFilesToKeep)
  {
    // Grab all backup files of database
    $files = glob( "$backupDir/$filename*" );
    
    $countTodelete = count($files) - $nbFilesToKeep;
    
    if($countTodelete > 0 )
    {
      // Sort files by modified time, oldest to earliest
      // Use SORT_ASC in place of SORT_DESC for earliest to latest
      array_multisort( array_map( 'filemtime', $files ), SORT_NUMERIC, SORT_ASC, $files );
    
      for($i = 0;$i < $countTodelete;$i++)
      {
        if(unlink($files[$i])===FALSE)
        {
          return false;
        }
      }
    }
    return true;
  }
?>