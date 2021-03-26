<?php
/**
 * Массовое добавление доменов в ISP Manager 5
 *
 * @link http://geograph.us/massovoe-dobavlenie-domenov-ispmanager-5.html
 */
  // Адрес панели ISP Manager
  $isp_url = "https://192.168.0.1:1500/ispmgr";
  // Пользователь ISP
  $isp_login = "root";
  // Его пароль
  $isp_pass = "";
  // Это пятая версия ISP?
  $isp5 = true;
  // Пользователь-владелец домена
  $domain_owner = "user";
  // IP домена, можно оставить пустым
  $domain_ip = "192.168.0.2";
  // Файл с доменами для добавления
  $domains = "domains.txt";

  if($isp_pass == "")
  {
    print "Please enter the ISP password for $isp_login: ";
    $isp_pass = trim(fgets(STDIN));
    if($isp_pass == "") die("Password is empty\r\n");
  }

  $domain_array = file($domains);

  $ch = curl_init();
  curl_setopt_array($ch, array(CURLOPT_RETURNTRANSFER => true, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false));

  foreach($domain_array as $domain)
  {
    $domain = trim($domain);

    if ($isp5)
    {
      $params['out'] = 'json';
      $params['func'] = 'webdomain.edit';
      $params['sok'] = 'yes';
      $params['name'] = $domain;
      $params['aliases'] = (strpos($domain, 'www.') === 0 ? substr($domain, 4) : 'www.' . $domain);
      $params['owner'] = $domain_owner;
      $params['php'] = 'on';
      $params['php_enable'] = 'on';
      $params['php_mode'] = 'php_mode_mod';
      $params['cgi'] = (strpos($php, 'cgi') !== null ? 'on' : 'off');
      $params['ipaddrs'] = $domain_ip;
      $params['ipsrc'] = ($domain_ip == '') ? 'auto' : 'manual';
      $params['email'] = 'webmaster@' . $domain;
    }
    else
    {
      $params['out'] = 'json';
      $params['func'] = 'wwwdomain.edit';
      $params['sok'] = 'yes';
      $params['domain'] = $domain;
      $params['alias'] = (strpos($domain, 'www.') === 0 ? substr($domain, 4) : 'www.' . $domain);
      $params['owner'] = $domain_owner;
      $params['docroot'] = 'auto';
      $params['php'] = 'phpmod'; // phpmod (php как модуль Apache); phpcgi (php как CGI); phpfcgi (php как fastCGI)
      $params['cgi'] = (strpos($php, 'cgi') !== null ? 'on' : 'off');
      $params['ip'] = $domain_ip;
      $params['admin'] = 'webmaster@' . $domain;
    }
    $url = $isp_url . '?authinfo=' . urlencode($isp_login) . ':' . urlencode($isp_pass) . '&' . http_build_query($params);
    curl_setopt($ch, CURLOPT_URL, $url);

    $response = (array)json_decode(curl_exec($ch), true);

    $result = "ERROR";

    if(isset($response['error']))
    {
      $result = $response['error']['msg'];
    }
    else if((isset($response['result']) && $response['result'] == 'OK') || isset($response['ok']))
    {
      $result = "OK";
    }
    
    echo "$domain\t$result\r\n";
  }

  curl_close($ch);

?>