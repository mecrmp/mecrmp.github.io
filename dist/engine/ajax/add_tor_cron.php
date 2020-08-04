<?php 
$expire_time = 86400;  // Время через которое файл считается устаревшим (в сек.)
$dir = $_SERVER['DOCUMENT_ROOT']."/cron/cron_cache/";
// проверяем, что $dir - каталог
if (is_dir($dir)) {
// открываем каталог
if ($dh = opendir($dir)) {
// читаем и выводим все элементы
// от первого до последнего
$filetxt = $_SERVER['DOCUMENT_ROOT'].'/cron/cron_cache/cache.txt';
if (file_exists($filetxt)) {
    //echo "есть ";
} else {
    if (!file_exists($filetxt)) {
    $fp = fopen($filetxt, "w");
    fclose($fp);
}
}
while (($file = readdir($dh)) !== false) {
// текущее время
$time_sec=time();
// время изменения файла
$time_file=filemtime($dir . $file);
// тепрь узнаем сколько прошло времени (в секундах)
$time=$time_sec-$time_file;
$unlink = $_SERVER['DOCUMENT_ROOT'].'/cron/cron_cache/'.$file;
if (is_file($unlink)){
if ($time>$expire_time){
if (unlink($unlink)){
if (!file_exists($filetxt)) {
    $fp = fopen($filetxt, "w");
    fclose($fp);
}  
   $url = "http://".$_SERVER['SERVER_NAME']."/cron/tor.rss.php";
   //echo $url;
   $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
	curl_setopt($curl, CURLOPT_HEADER, true); 
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $out = curl_exec($curl);
    curl_close($curl);
 //$tor_rss = file_get_contents( '//'.$_SERVER['SERVER_NAME'].'/cron/tor.rss.php');
exit;
}else{
//echo 'Ошибка при удалении файла';
}
}

}
}
// закрываем каталог
closedir($dh);
}
}

//}
?>