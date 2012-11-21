<?php
function memory_usage_in_mb()
{
   $memusage = memory_get_usage(TRUE);
   return ($memusage / 1048576.2);
}
?>