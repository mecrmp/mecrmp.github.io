<?php

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}
  
$newsID = $xfieldsdata['id-multitracker.info'];//значения дополнительного поля ID multitracker.info
//echo $xfieldsdata['id-multitracker.info']; //проверка,если у вас другое-замените,чистим кеш
if(isset($newsID) && $newsID !== '')  {
$sha1_news_id = sha1($newsID);
include ('engine/api/api.class.php');
$multitracker = $dle_api->load_from_cache("multitracker_".$sha1_news_id, 86400);//кеш сутки
if (!$multitracker) {
if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, "https://multitracker.info/?do=api");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "news=".$newsID);
    $out = curl_exec($curl);
    curl_close($curl);
  }
  $multitracker = $out;
	$dle_api->save_to_cache ( "multitracker_".$sha1_news_id, $multitracker);
} 
 if (!json_decode($multitracker)) {
    $tr_torrent_error = "data-multitracker=".$newsID;//ошибка если нет ответа от multitracker.info, для обработки js
} else {
	$tr = json_decode($multitracker, true);
	if ($tr["status"] == 'OK') {
	$tr_id_news = $tr["id_news"] ;
	$tr_name_tor = stripslashes($tr["name_tor"]);
	$tr_size_file = $tr["size_file"] ;
	$tr_torrent_all_leechers = $tr["torrent_all_leechers"] ;
	$tr_torrent_all_seeders = $tr["torrent_all_seeders"];
	$tr_torrent_all_completed = $tr["torrent_all_completed"];
	$tr_magnet_link = $tr["magnet_link"];
	}else{
	   $tr_torrent_error = "data-multitracker=".$newsID;//ошибка если торрент файл не найден, для обработки js
	}
}   //вывод
    $tpl2->copy_template = str_replace(  '{torrent_error}', $tr_torrent_error , $tpl2->copy_template );	
	$tpl2->copy_template = str_replace(  '{torrent_id}', $tr_id_news , $tpl2->copy_template );//
	$tpl2->copy_template = str_replace(  '{torrent_name}', $tr_name_tor , $tpl2->copy_template );
	$tpl2->copy_template = str_replace(  '{torrent_size}', $tr_size_file , $tpl2->copy_template );
	$tpl2->copy_template = str_replace(  '{torrent_leechers}', $tr_torrent_all_leechers , $tpl2->copy_template );
	$tpl2->copy_template = str_replace(  '{torrent_seeders}', $tr_torrent_all_seeders , $tpl2->copy_template );
	$tpl2->copy_template = str_replace(  '{torrent_completed}', $tr_torrent_all_completed , $tpl2->copy_template );
	$tpl2->copy_template = str_replace( '{torrent_magnet}', $tr_magnet_link, $tpl2->copy_template );	
}
?>