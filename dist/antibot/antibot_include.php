<?php
// v. 4.03

$ab_se = array();
require_once(__DIR__.'/antibot_conf.php');

if (!isset($check_url_main)) {$check_url_main = 'https://antibot.cloud/content/ab.php';}
if (!isset($check_url_alt)) {$check_url_alt = 'https://alt.antibot.cloud/content/ab.php';}

$ab_config['host'] = isset($_SERVER['HTTP_HOST']) ? preg_replace("/[^0-9a-z-.:]/","", $_SERVER['HTTP_HOST']) : '';
$ab_config['useragent'] = isset($_SERVER['HTTP_USER_AGENT']) ? trim(strip_tags($_SERVER['HTTP_USER_AGENT'])) : '';
$ab_config['uri'] = trim(strip_tags($_SERVER['REQUEST_URI']));
$ab_config['referer'] = isset($_SERVER['HTTP_REFERER']) ? trim(strip_tags($_SERVER['HTTP_REFERER'])) : '/';

if ($ab_config['useragent'] == '') die(); // ибо нехуй.

// продвинутое определение IP v4 посетителя:
if (isset($_SERVER['HTTP_FORWARDED'])) {$ab_config['ip'] = $_SERVER['HTTP_FORWARDED'];} 
elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {$ab_config['ip'] = $_SERVER['HTTP_CLIENT_IP'];} 
elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {$ab_config['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];} 
elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {$ab_config['ip'] = $_SERVER['HTTP_X_FORWARDED'];
} else {$ab_config['ip'] = $_SERVER['REMOTE_ADDR'];}
$ab_config['ip'] = strip_tags($ab_config['ip']);
if (mb_stripos($ab_config['ip'], ',', 0, 'utf-8')!== false) {$ab_config['ip'] = explode(',', $ab_config['ip']); $ab_config['ip'] = trim($ab_config['ip'][0]);}
if (mb_stripos($ab_config['ip'], ':', 0, 'utf-8')!== false) {$ab_config['ip'] = explode(':', $ab_config['ip']); $ab_config['ip'] = trim($ab_config['ip'][0]);}
$ab_config['ip'] = preg_replace("/[^0-9.]/","",$ab_config['ip']);

// если в итоге получилась какая-то срань вместо ipv4
if (filter_var($ab_config['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) die('IPv4 only');


$ab_config_ip_array = explode('.', $ab_config['ip']);
$ab_config['ip_short'] = $ab_config_ip_array[0].'.'.$ab_config_ip_array[1].'.'.$ab_config_ip_array[2].'.*';


if (file_exists(__DIR__.'/blackbot/'.$ab_config['ip'].'.txt') OR file_exists(__DIR__.'/blackbot/'.$ab_config['ip_short'].'.txt')) die('BlackBot');

// для начала всех считаем людьми:
$ab_config['whitebot'] = 0;

// самый белый бот, если нашли, то дальше уже не проверяем:
if (file_exists(__DIR__.'/whitebot/'.$ab_config['ip'].'.txt') OR file_exists(__DIR__.'/whitebot/'.$ab_config['ip_short'].'.txt')) {
$ab_config['whitebot'] = 1;
}


if ($ab_config['whitebot'] == 0) {
// проверяем юзерагент на принадлежность к белым ботам:
foreach ($ab_se as $ab_line => $ab_sign) {
if (mb_stripos($ab_config['useragent'], $ab_line, 0, 'utf-8') !== false) {
$ab_config['whitebot'] = 1; break;
}
}
// если по юзерагенту это белый бот:
if ($ab_config['whitebot'] == 1) {
$ab_config['whitebot'] = 0;
$ab_config['ptr'] = gethostbyaddr($ab_config['ip']);
foreach ($ab_sign as $ab_line) {
if (mb_stripos($ab_config['ptr'], $ab_line, 0, 'utf-8') !== false) {
if ($ab_line != '.') {
// сохраняем ip в белый список только тем у кого полноценный идентифицируемый ptr:
if ($ab_config['short_mask'] != 1) {$ab_config['ip_short'] = $ab_config['ip'];}
file_put_contents(__DIR__.'/whitebot/'.$ab_config['ip_short'].'.txt', $ab_config['ip'].' '.$ab_config['ptr'].' '.$ab_config['useragent'], LOCK_EX);
}
$ab_config['whitebot'] = 1; break;
}
}
}
}

// хэш для куки таким должен быть:
$ab_config['antibot_ok'] = md5($ab_config['host'].$ab_config['useragent'].$ab_config['ip']);

// получаем куки юзера:
$ab_config['antibot'] = isset($_COOKIE['antibot']) ? trim($_COOKIE['antibot']) : '';

// проверка пост запросом:
if(isset($_POST['submit']) AND isset($_POST['antibot'])) {
$ab_config['antibot'] = isset($_POST['antibot']) ? trim(strip_tags($_POST['antibot'])) : 0;
setcookie('antibot', $ab_config['antibot'], time()+86400, '/', $ab_config['host']);
if (!isset($ab_config['ptr'])) {$ab_config['ptr'] = gethostbyaddr($ab_config['ip']);}
//file_put_contents(__DIR__.'/postclick.txt', $ab_config['ip'].' '.$ab_config['ptr'].' '.$ab_config['host'].' '.$ab_config['useragent']."\n", FILE_APPEND | LOCK_EX);
}

// проверка HTTP/2.0 если включена:
if ($ab_config['whitebot'] == 0 AND $ab_config['http2only'] == 1 AND $_SERVER['SERVER_PROTOCOL'] != 'HTTP/2.0') die('HTTP/2.0 only');

if ($ab_config['li'] == '') {$ab_config['li'] = $ab_config['host'];}

// отдаем юзеру заглушку для проверки:
if ($ab_config['whitebot'] == 0 AND $ab_config['antibot_ok'] != $ab_config['antibot']) {
header('Content-Type: text/html; charset=UTF-8');
header('X-Powered-CMS: Antibot.Cloud (See: https://antibot.cloud/)');
header('X-Robots-Tag: noindex');
header('X-Frame-Options: DENY');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
require_once(__DIR__.'/antibot_tpl.txt');
if ($ab_config['antibot_log'] == 1) {
if (!isset($ab_config['ptr'])) {$ab_config['ptr'] = gethostbyaddr($ab_config['ip']);}
file_put_contents(__DIR__.'/botlog1.txt', $ab_config['ip'].' '.$ab_config['ptr'].' '.$ab_config['host'].' '.$ab_config['useragent']."\n", FILE_APPEND | LOCK_EX);
}
die();
}

if ($ab_config['antibot_log2'] == 1) {
if ($ab_config['whitebot'] == 0 AND $ab_config['antibot_ok'] == $ab_config['antibot']) {
if (!isset($ab_config['ptr'])) {$ab_config['ptr'] = gethostbyaddr($ab_config['ip']);}
file_put_contents(__DIR__.'/botlog2.txt', $ab_config['ip'].' '.$ab_config['ptr'].' '.$ab_config['host'].' '.$ab_config['useragent']."\n", FILE_APPEND | LOCK_EX);
}
}
