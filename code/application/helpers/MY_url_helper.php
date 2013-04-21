<?php

if ( ! function_exists('mb_url_title'))
{
  function mb_cleanURL ( $title )
  {
    $search  = explode(",","', ,/");
    array_push($search,",");
    $replace = explode(",","-,-,-");
    array_push($replace,"-");
    return str_replace($search, $replace, $title);
  }
}
if ( ! function_exists('mb_url_title'))
{
  function mb_url_title($str, $separator = 'dash', $lowercase = FALSE)
	{
		if ($separator == 'dash')
		{
			$search		= '_';
			$replace	= '-';
		}
		else
		{
			$search		= '-';
			$replace	= '_';
		}

		$trans = array(
						'&\#\d+?;'				=> '',
						'&\S+?;'				=> '',
						'\s+'					=> $replace,
// 						'[^a-z0-9\-\._]'		=> '',
						$replace.'+'			=> $replace,
						$replace.'$'			=> $replace,
						'^'.$replace			=> $replace,
						'\.+$'					=> ''
					  );

		$str = strip_tags($str);

		foreach ($trans as $key => $val)
		{
			$str = preg_replace("#".$key."#i", $val, $str);
		}

		//Multi byte replace
		$trans = array(
								'[^a-z0-9\-\._]'		=> ''
		         );


		foreach ($trans as $key => $val)
		{
			$str = mb_ereg_replace("#".$key."#i", $val, $str);
		}

		if ($lowercase === TRUE)
		{
			$str = mb_strtolower($str);
		}

		return trim(stripslashes($str));
	}
}
/**
 * SSL functions
 *
 * Reference: http://sajjadhossain.com/2008/10/27/ssl-https-urls-and-codeigniter/
 */

  if( ! function_exists('secure_site_url') )
  {
      function secure_site_url($uri = '')
      {
          $CI =& get_instance();
          return $CI->config->secure_site_url($uri);
      }
  }

  if( ! function_exists('secure_base_url') )
  {
      function secure_base_url()
      {
          $CI =& get_instance();
          return $CI->config->slash_item('secure_base_url');
      }
  }

  if ( ! function_exists('secure_anchor'))
  {
      function secure_anchor($uri = '', $title = '', $attributes = '')
      {
          $title = (string) $title;

          if ( ! is_array($uri))
          {
              $secure_site_url = ( ! preg_match('!^\w+://! i', $uri)) ? secure_site_url($uri) : $uri;
          }
          else
          {
              $secure_site_url = secure_site_url($uri);
          }

          if ($title == '')
          {
              $title = $secure_site_url;
          }

          if ($attributes != '')
          {
              $attributes = _parse_attributes($attributes);
          }
  //      return '<a href="'.$secure_site_url.'" rel="nofollow">'.$title.'</a>';
          return '<a href="'.$secure_site_url.'">'.$title.'</a>';
      }
  }

  if ( ! function_exists('secure_redirect'))
  {
      function secure_redirect($uri = '', $method = 'location', $http_response_code = 302)
      {
          switch($method)
          {
              case 'refresh'    : header("Refresh:0;url=".secure_site_url($uri));
                  break;
              default            : header("Location: ".secure_site_url($uri), TRUE, $http_response_code);
                  break;
          }
          exit;
      }
  }

  /**
   *
   */
  if (! function_exists('ssl_redirect'))
  {
    function ssl_redirect()
    {
        if ($_SERVER["SERVER_PORT"] != 443)
        {
            redirect(str_replace("http://", "https://" , current_url()), "refresh");
        }
    }
  }

  function is_https()
  {
    foreach (getallheaders() as $name => $value)
    {
      if((strcasecmp($name,'HTTPS')==0)&&(strcasecmp($value,'on')==0))
      {
        return TRUE;
      }
    }
    return FALSE;
  }
/**
 *
 * One other thing that came in handy was overriding CI’s base_url() method in MY_url_helper; other libraries
 * like Template call base_url() when you do things like add_js() or add_css(), which causes warnings to pop up
 * in IE if you’re on a secure page. This was the fix:
 */

//  function base_url() {
//   $framework =& get_instance();
//
//   if (isset($_SERVER['HTTPS'])) {
//      return $framework->config->slash_item('secure_base_url');
//   } else {
//      return $framework->config->slash_item('base_url');
//   }
//  }
?>