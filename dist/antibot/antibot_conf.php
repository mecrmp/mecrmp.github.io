<?php
// ВНИМАНИЕ! ЭТИ НАСТРОЙКИ ОПТИМАЛЬНЫ, ДЛЯ НАЧАЛА ОТРЕДАКТИРУЙТЕ ТОЛЬКО СПИСОК БЕЛЫХ БОТОВ.

// адреса скриптов проверки, можно изменить путь к своим ab.php скриптам.
// основной сервер проверки, по умолчанию: https://antibot.cloud/content/ab.php:
$check_url_main = 'https://antibot.cloud/content/ab.php';
// резервный, на случай недоступности основного, по умолчанию https://alt.antibot.cloud/content/ab.php:
$check_url_alt = 'https://alt.antibot.cloud/content/ab.php';

// если сайт работает на https c поддержкой http/2.0
// 1 - пускать только продвинутых юзеров.
// 0 - пускать всех прошедших проверку куки.
$ab_config['http2only'] = 0;

// сохранять в белый список ip белых ботов по маске /24, т.е 123.123.123.* вместо 123.123.123.123
// вручную в белые/черные списки можно сохранять ip в обоих вариантах (в полном и сокращенном).
$ab_config['short_mask'] = 1;

// включить лог попавших на заглушку (временно, для отладки).
// 1 - включить лог, 0 - не вести лог.
$ab_config['antibot_log'] = 0; 

// включить лог юзеров прошедших заглушку (временно, для отладки).
// 1 - включить лог, 0 - не вести лог.
$ab_config['antibot_log2'] = 0; 

// если хотите считать статистику LiveInternet без ботов.
// имя LiveInternet счетчика (если групповой), если обычный, то оставить пустым = ''; 
// если вообще не грузить счетчик = '0'; 
$ab_config['li'] = ''; 

// если хотите считать статистику Яндекс.Метрика без ботов.
// номер счетчика метрики, если не нужна, то = '0';
$ab_config['metrika'] = '0';

// код счетчика статистики или любой другой html/js код для отображения в шаблоне заглушки.
// этот код будут также видеть боты и все увидевшие заглушку:
$ab_config['counter'] = '';

// Список белых ботов в формате: сигнатура (признак) из User-Agent => массив PTR записей:
// если PTR запись пустая или неинформативная, то указывать array('.');
// тогда все боты с этим юзерагентом будут пропускаться как белые боты,
// но ip в базу белых ботов добавляться не будут.
// если бот ходит из малого количества подсетей, то можно указать часть ip адреса.
$ab_se['yandex.com'] = array('yandex.ru', 'yandex.net', 'yandex.com'); // все боты Яндекса
$ab_se['Googlebot'] = array('googlebot.com', 'google.com'); // только индексатор гугла
$ab_se['Google-Site-Verification'] = array('googlebot.com', 'google.com'); // гуглобот при добавлении в вебмастер
$ab_se['Mail.RU_Bot'] = array('mail.ru', 'smailru.net'); // все боты индексаторы Mail.ru
$ab_se['bingbot'] = array('search.msn.com'); // индексатор Bing.com
$ab_se['AppEngine-Google'] = array('.googleusercontent.com'); // бот проверяльщик freenom.com
// соцсети (боты предпросмотра и т.п.)
$ab_se['vkShare'] = array('.vk.com'); // Вконтакте
$ab_se['facebookexternalhit'] = array('31.13.'); // Facebook
$ab_se['OdklBot'] = array('.odnoklassniki.ru'); // Однокласники
$ab_se['MailRuConnect'] = array('.smailru.net'); // Мой мир (mail.ru)
$ab_se['Twitterbot'] = array('199.16.15'); // Twitter
$ab_se['TelegramBot'] = array('149.154.16'); // Telegram

// еще боты, которых возможно можно допустить, если они вам надо:
//$ab_se['googleweblight'] = array('google.com'); // эта категорически нельзя разрешать
//$ab_se['BingPreview'] = array('search.msn.com'); // проверка адаптации моб страниц Bing
//$ab_se['uptimerobot'] = array('uptimerobot.com');
//$ab_se['pingdom'] = array('pingdom.com');
//$ab_se['HostTracker'] = array('.'); // он не имеет нормального PTR
//$ab_se['Yahoo! Slurp'] = array('.yahoo.net'); // боты Yahoo
//$ab_se['SeznamBot'] = array('.seznam.cz'); // поисковик seznam.cz
//$ab_se['Pinterestbot'] = array('.pinterest.com'); // 
//$ab_se['Mediapartners'] = array('googlebot.com', 'google.com'); // AdSense bot
