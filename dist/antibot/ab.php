<?php
// Этот скрипт не сравнится с облачной проверкой AntiBot.Cloud, потому что он не облачный =)))

header('Content-Type: application/javascript; charset=UTF-8');
header('X-Robots-Tag: noindex');
header('X-Frame-Options: DENY');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Если ваш скрипт ab.php размещен на сайте с https поддерживающим протокол http/2.0
// но это должен быть https с поддержкой http/2.0 именно вашим сервером, без cloudflare
// и других прокси серверов и без связки nginx + apache + php, т.е. нужно nginx + php.
// то раскомментируйте эту строку, это значительно улучшит защиту от ботов:
//if ($_SERVER['SERVER_PROTOCOL'] != 'HTTP/2.0') die('ab=1');

$id = isset($_GET['id']) ? trim($_GET['id']) : '0';

// реальный сайт с которого пришел запрос:
$referer = isset($_SERVER['HTTP_REFERER']) ? trim(strip_tags($_SERVER['HTTP_REFERER'])) : '';
if ($referer == '') die('ab=1');
// юзерагент:
$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? trim(strip_tags($_SERVER['HTTP_USER_AGENT'])) : '';
if ($useragent == '') die('ab=1');
// продвинутое определение IP v4 посетителя:
if (isset($_SERVER['HTTP_FORWARDED'])) {$ip = $_SERVER['HTTP_FORWARDED'];} 
elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {$ip = $_SERVER['HTTP_CLIENT_IP'];} 
elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];} 
elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {$ip = $_SERVER['HTTP_X_FORWARDED'];
} else {$ip = $_SERVER['REMOTE_ADDR'];}
$ip = strip_tags($ip);
if (mb_stripos($ip, ',', 0, 'utf-8')!== false) {$ip = explode(',', $ip); $ip = trim($ip[0]);}
if (mb_stripos($ip, ':', 0, 'utf-8')!== false) {$ip = explode(':', $ip); $ip = trim($ip[0]);}
$ip = preg_replace("/[^0-9.]/","",$ip);
// работаем только с ipv4 адресами:
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) die('ab=1');
// домен с которого вызвали скрипт:
$refhost = parse_url($referer, PHP_URL_HOST);

// это хороший настоящий юзер:
$antibot = md5($refhost.$useragent.$ip);

// если юзер не валиден:
if ($id != crc32($antibot)) die('ab=1');

echo 'ab=1;
var d = new Date();
d.setTime(d.getTime() + (1*24*60*60*1000));
var expires = "expires="+ d.toUTCString();
document.cookie = "antibot='.$antibot.'; " + expires + "; path=/;";
setTimeout(location.reload.bind(location), 3000);
';

// LiveInternet
$li = isset($_GET['l']) ? preg_replace("/[^0-9a-zA-Z\-\_\.\/]/","", $_GET['l']) : '0';
if ($li != '0') {
if ($li == $refhost) {$li = '';} else {$li = ';'.$li;}
echo 'new Image().src = "//counter.yadro.ru/hit'.$li.'?r"+
escape(document.referrer)+((typeof(screen)=="undefined")?"":
";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?
screen.colorDepth:screen.pixelDepth))+";u"+escape(document.URL)+
";h"+escape(document.title.substring(0,150))+
";"+Math.random();
';
}

// Metrika
$metrika = isset($_GET['m']) ? preg_replace("/[^0-9]/","", $_GET['m']) : '0';
if ($metrika != '0') {
echo '(function (d, w, c) { (w[c] = w[c] || []).push(function() { try { w.yaCounter'.$metrika.' = new Ya.Metrika({ id:'.$metrika.', clickmap:true, trackLinks:true, accurateTrackBounce:true }); } catch(e) { } }); var n = d.getElementsByTagName("script")[0], s = d.createElement("script"), f = function () { n.parentNode.insertBefore(s, n); }; s.type = "text/javascript"; s.async = true; s.src = "https://cdn.jsdelivr.net/npm/yandex-metrica-watch/watch.js"; if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); } })(document, window, "yandex_metrika_callbacks");
';
}
