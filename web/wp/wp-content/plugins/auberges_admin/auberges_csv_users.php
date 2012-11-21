<?php
if(is_admin())
{

  require "exportcsv.inc.php";
  
  $today = date("Y-m-d",mktime(0, 0, 0, date("m") , date("d"), date("Y")));
  $sql_query = "SELECT created,email, user_level,first_name,last_name,home_country,currency_code,mail_subscription,";
  $sql_query.= "       s1.site_domain AS registered_domain, s2.locale AS favorite_lang";
  $sql_query.= " FROM users";
  $sql_query.= " JOIN user_profiles ON users.id = user_profiles.user_id";
  $sql_query.= " LEFT JOIN user_levels ON user_profiles.user_level_id = user_levels.user_level_id";
  $sql_query.= " LEFT JOIN currencies ON user_profiles.favorite_currency = currencies.currency_id";
  $sql_query.= " LEFT OUTER JOIN site_domains s1 ON user_profiles.site_domain_registered_id = s1.site_domain_id";
  $sql_query.= " LEFT OUTER JOIN site_domains s2 ON user_profiles.favorite_lang_id = s2.site_domain_id";
  $sql_query.= " ORDER BY created";
  
  $header = "\"Creation\";\"email\";\"user level\";\"Prenom\";\"Nom\";\"Nationalite\";\"Devise\";\"newsletter\";\"Enregistrer sur\";\"Langue prefere\"";
  
  
  exportMysqlToCsv($sql_query,$header,"aj_users_".$today.".csv");
}
?>