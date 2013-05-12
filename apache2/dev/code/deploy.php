<?php

   // Turn off error reporting
   error_reporting(0);

   exec("/opt/deployment/deploy2dev.sh ".$_GET['branch']);

   echo "Branch ".$_GET['branch']." deployed.";
?>
