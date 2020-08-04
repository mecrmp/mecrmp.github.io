<?PHP
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004-2018 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: search.php
-----------------------------------------------------
 Назначение: поиск и замена текста в базе данных
=====================================================
*/
if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) ) {
  die("Hacking attempt!");
}
if (isset($_POST) and $_SERVER["REQUEST_METHOD"]=="POST"){

if ($_POST['antibot'] == '') {
     $new_config = "<?php\n
\$antibot_config = array (\n
'antibot_included' => '0',\n
);\n
?>";	
file_put_contents(ENGINE_DIR . '/data/antibot_config.php', $new_config);
} elseif ($_POST['antibot'] == '1') {
    $new_config = "<?php\n
\$antibot_config = array (\n
'antibot_included' => '1',\n
);\n
?>";	
file_put_contents(ENGINE_DIR . '/data/antibot_config.php', $new_config);
} 


}
echoheader( "<i class=\"fa fa-exchange position-left\"></i><span class=\"text-semibold\">AntiBot.Cloud</span>", 'AntiBot.Cloud');
require_once (ENGINE_DIR . '/data/antibot_config.php');
if(!empty($antibot_config["antibot_included"])) {
$checked = 'checked';
}
echo <<<HTML
<form action="" method="post" class="form-horizontal">
<div class="panel panel-default">
  <div class="panel-heading">
    Включения / Отключения
  </div>
	<div class="panel-body">
		Модуль для защиты сайта от плохих ботов. AntiBot.Cloud заметно снижает нагрузку на сервер и защищает от парсинга. Полное описания: <a href="https://antibot.cloud/" target="_blank">antibot.cloud</a>
	</div>
	<div class="panel-body">
<form action="/admin.php?mod=antibot" method="POST">
    <div>
        <label>Использовать модуль: <input name="antibot" class="switch" type="checkbox" value="1" $checked></label>
     </div>
    <br>
       <button type="submit" class="btn bg-teal btn-sm btn-raised position-left"></i>Сохранить</button>
	</form>
	
	</div>
	
</div>
</form>
HTML;


echofooter();

?>