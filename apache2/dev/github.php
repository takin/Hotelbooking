<?php

   // Turn off error reporting
   error_reporting(0);

   // configuration
   $log = "/opt/logs/github.log";
   $payloadLog = "/opt/logs/github_payload.log";
   $repository = "https://github.com/mcwebmanagement/source";
   
   $allowedUsernames = array("jcurlier", "CHRISMORISSEAU");
   $master = "refs/heads/master";
   $deployProdString = "deploy to prod";
   $deployProdScript = "/opt/deployment/deploy2prod.sh";
   $deployProdMessage = "deployed to production";
   $deployDevString = "deploy to dev";
   $deployDevScript = "/opt/deployment/deploy2dev.sh";
   $deployDevMessage = "deployed to dev";
   $noDeployMessage = "no deployment";

   try
   {
      // Decode the payload json string
      $payload = json_decode($_REQUEST['payload']);
      $reference = $payload->ref;
      $url = $payload->repository->url;
      $username = $payload->head_commit->committer->username;
      $commit = $payload->head_commit->id;
      $commitMessage =  $payload->head_commit->message;

      // Log the payload object
      @file_put_contents($payloadLog, print_r($payload, TRUE), FILE_APPEND);

      if ($reference === $master && $url === $repository && (in_array ($username, $allowedUsernames)))
      {

         if (strpos($commitMessage, $deployProdString) != FALSE)
         {
            @file_put_contents($log, date("Y-m-d H:i:s")."\t".$repository."\t".$username."\t".$commit."\t".$deployProdMessage."\n", FILE_APPEND);
            exec("$deployProdScript");
         }

         if (strpos($commitMessage, $deployDevString) != FALSE)
         {
            @file_put_contents($log, date("Y-m-d H:i:s")."\t".$repository."\t".$username."\t".$commit."\t".$deployDevMessage."\n", FILE_APPEND);
            exec("$deployDevScript");
         }

      }

      if ((strpos($commitMessage, $deployProdString) === FALSE) && (strpos($commitMessage, $deployDevString) === FALSE))
      {
         @file_put_contents($log, date("Y-m-d H:i:s")."\t".$repository."\t".$username."\t".$commit."\t".$noDeployMessage."\n", FILE_APPEND);
      }
   }
   catch (Exception $e)
   {
      exit;
   }
?>
