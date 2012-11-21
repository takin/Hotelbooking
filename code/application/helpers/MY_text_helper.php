<?php

function highlightTerm($full_string, $match)
{
    $full_string_ascii = preg_replace_callback('/[\w]+/ui', 'callbackHighlightTerm', strtolower($full_string));
    $match_ascii = preg_replace_callback('/[\w]+/ui', 'callbackHighlightTerm', strtolower($match));

    $start = stripos($full_string_ascii, $match_ascii);

    if($start===FALSE)
    {
        return $full_string;
    }
    else
    {
        $length = mb_strlen($match);

        return
            htmlspecialchars( mb_substr($full_string, 0, $start)) .
            '<strong>' . htmlspecialchars( mb_substr($full_string, $start, $length) ) . '</strong>' .
            htmlspecialchars( mb_substr($full_string, $start+$length) );
    }
}


function callbackHighlightTerm($matches)
{
    return preg_replace('/[^\w]/i', '', iconv('UTF-8', 'ASCII//TRANSLIT', $matches[0]));
}

function normalize_string($string) {
  $ascii = iconv("utf-8","ascii//TRANSLIT", $string);
  return str_replace(array('!', "'", '?'), '', $ascii);

  // or

//   return preg_replace('/[!\'?]/', '', $ascii);

  // or depending on how much you do want to replace... \W => any "non-word" character

//   return preg_replace('/\W/', '', $ascii);
}

?>