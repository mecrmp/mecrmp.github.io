<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
//тут пост
if(empty($_POST['news'])){ // переменная $_GET['id'] = NULL
echo "Не верный запрос. Вернутся на главную:  <a href=\"/\">".$_SERVER['SERVER_NAME']." </a>";
    exit;
} else {
$news_id = 	$_POST['news'];
include ('engine/api/api.class.php');
//echo "есть";
$multitracker = $dle_api->load_from_cache("multitracker_".$news_id, 86400);
if (!$multitracker) {
if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, "https://multitracker.info/?do=api");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,false);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, "news=".$news_id);
    $out = curl_exec($curl);
    curl_close($curl);
  }
  $multitracker = $out;
 //echo $rezult;
 //echo "я результат";
	$dle_api->save_to_cache ( "multitracker_".$news_id, $multitracker);
} 
 echo $multitracker;
// echo "я кеш";
 //тут результат
 exit;
}
} else {
echo "Не верный запрос. Вернутся на главную:  <a href=\"/\">".$_SERVER['SERVER_NAME']." </a>";
    exit;
}

?>