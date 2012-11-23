<?php
if ( ! function_exists('url_title'))
{
  function url_title($str)
  {
    return cleanURL_UTFsafe(stripAccent_UTFsafe($str));
  }
}
if ( ! function_exists('stripAccent_UTFsafe'))
{
  function stripAccent_UTFsafe ( $title )
  {
    $search  = explode(",",'À,Á,Â,Ã,Ä,Å,Æ,Ç,È,É,Ê,Ë,Ì,Í,Î,Ï,Ð,Ñ,Ò,Ó,Ô,Õ,Ö,Ø,Ù,Ú,Û,Ü,Ý,Þ,ß,à,á,â,ã,ä,å,æ,ç,è,é,ê,ë,ì,í,î,ï,ð,ñ,ò,ó,ô,õ,ö,ø,ù,ú,û,ý,ý,þ,ÿ,Ŕ,ŕ,й');
    $replace = explode(",",'a,a,a,a,a,a,a,c,e,e,e,e,i,i,i,i,d,n,o,o,o,o,o,o,u,u,u,u,y,b,s,a,a,a,a,a,a,a,c,e,e,e,e,i,i,i,i,d,n,o,o,o,o,o,o,u,u,u,y,y,b,y,R,r,и');
    return str_replace($search, $replace, $title);
  }
}

if ( ! function_exists('cleanURL_UTFsafe'))
{
  function cleanURL_UTFsafe ( $title )
  {
    $search  = explode(",","', ,/");
    array_push($search,",");
    $replace = explode(",","-,+,-");
    array_push($replace,"-");
    return str_replace($search, $replace, $title);
  }
}
?>