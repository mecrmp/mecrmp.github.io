<?php
/*
=====================================================
 Файл: /inc/search_advanced.php
-----------------------------------------------------
 Назначение: Админка модуля
=====================================================
*/

//Если чо, умри
	if(!defined('DATALIFEENGINE') OR !defined('LOGGED_IN')) die("Hackingattempt!");
	if($member_id['user_group'] != 1) msg("error", $lang['addnews_denied'], $lang['db_denied']);

//Проверка конфигов, их прав
$bak_config = ENGINE_DIR . '/inc/search_advanced/search_config.php.bak';
$new_config = "<?php\n\n
	//SearchAdvanced v1.1 for DLE\n
	\$search_config = array (\n\n
		'input_min' => '4',\n
		'input_max' => '55',\n
		'result_num' => '5',\n
		'cache_time' => '172800',\n
		'wrong_layout' => '0',\n
		'text_language' => '0',\n
		'type_foreign_name' => 'with_year',\n
		'field_kpid' => '',\n
		'interval_sleep' => '0000000',\n
		'result_null' => '0',\n
		'related_num' => '1',\n
		'related_mode' => '0',\n
		'related_manual' => '',\n\n
	);\n\n
	?>";
if(!file_exists(ENGINE_DIR . 		 '/data/search_config.php')) {
	require_once (ROOT_DIR . 	 		 '/language/Russian/search_advanced.lng');
	require_once (ENGINE_DIR . 		 '/data/config.php');
	if (file_exists($bak_config)) {
		copy($bak_config, ENGINE_DIR . '/data/search_config.php.bak');
		rename(ENGINE_DIR . '/data/search_config.php.bak', ENGINE_DIR . '/data/search_config.php');
		$config_start = '<div class="alert alert-info">' . $lang_search['config_start_bak'] . '</div>';
	} else {
		file_put_contents(ENGINE_DIR . '/data/search_config.php', $new_config);
		$config_start = '<div class="alert alert-info">' . $lang_search['config_start'] . '</div>';
	}
} else {
	require_once (ENGINE_DIR . '/data/config.php');
	require_once (ENGINE_DIR . '/data/search_config.php');
	require_once (ROOT_DIR . 	 '/language/Russian/search_advanced.lng');
}

if (!is_writable(ENGINE_DIR . '/data/search_config.php')) {
	chmod( ENGINE_DIR . 				'/data/search_config.php', 0777 );
	$rights_status = '<div class="alert">' . $lang_search['rights_error'] . '</div>';
}

//Функции для админки
if ($config['version_id'] >= '9.2' && $config['version_id'] < '10.2') {
	echoheader($lang_search['search_advanced'], $lang_search['search_version']);
	function showRow($title = "", $description = "", $field = "") {echo "<tr><td style=\"padding:4px\" class=\"option\"><b>$title</b><br /><span class=small>$description</span><td width=394 align=middle >$field</tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";$bg = "";$i ++;}
	function makeDropDown($options, $name, $selected) {$output = "<select name=\"$name\">\r\n";foreach ( $options as $value => $description ) {$output .= "<option value=\"$value\"";if( $selected == $value ) {$output .= " selected ";}$output .= ">$description</option>\n";}$output .= "</select>";return $output;}
	function makeCheckBox($name, $selected) {$selected = $selected ? "checked" : "";return "<input class=\"iButton-icons-tab\" type=\"checkbox\" name=\"$name\" value=\"1\" {$selected}>";}
} else {
	echoheader("<i class=\"icon-cog\"></i>" . $lang_search['search_advanced'], $lang_search['search_version']);
	function showRow($title = "", $description = "", $field = "", $class = "") {echo "<tr><td class=\"col-xs-10 col-sm-6 col-md-7 {$class}\"><h6>{$title}</h6><span class=\"note large\">{$description}</span></td><td class=\"col-xs-2 col-md-5 settingstd {$class}\">{$field}</td></tr>";}
	function makeDropDown($options, $name, $selected) {$output = "<select class=\"uniform\" style=\"min-width:100px;\" name=\"$name\">\r\n";foreach ( $options as $value => $description ) {$output .= "<option value=\"$value\"";if( $selected == $value ) {$output .= " selected ";}$output .= ">$description</option>\n";}$output .= "</select>";return $output;}
	function makeCheckBox($name, $selected) {$selected = $selected ? "checked" : "";return "<input class=\"iButton-icons-tab\" type=\"checkbox\" name=\"$name\" value=\"1\" {$selected}>";}
}

echo $config_start . $rights_status . $lic_error;

//Инициализация дополнительных полей
$fields = array('kinopoisk'=>array(''=>'Не выбрано'));
$xfields = xfieldsload();
if ($xfields) foreach ($xfields as $key => $value) $fields['kinopoisk']["{$value[0]}"] = " {$value[1]} ";

if (!$lic_error) {
if ($config['version_id'] >= '9.2' && $config['version_id'] < '10.2') {
echo <<<HTML
<script type="text/javascript">
function savesetting() {
	ShowLoading('Сохранение...');
	$.post("{$config['http_home_url']}engine/inc/search_advanced/functions.php?settings=save", $('#configure').serialize()+'&key={$dle_login_hash}',
		function(data){
			
		if ( data == "" ) {
			DLEalert('Настройки успешно сохранены!', 'Информация');
			HideLoading('');
		} else {
			DLEalert(data, 'Информация');
			HideLoading('');
		}
	});
}
function savefile( file ){
	var content = editor.getValue();
	ShowLoading('Сохранение...');		
	$.post('engine/ajax/templates.php', { action: "save", file: file, content: content, user_hash: "{$dle_login_hash}" }, function(data){
		if ( data == "ok" ) {
			DLEalert('{$lang['template_saved']}', '{$lang['p_info']}');
		} else {
			DLEalert( data, '{$lang['p_info']}');
		}
		HideLoading('');
	});
};
function add_foreign_name() {
	ShowLoading('Дождитесь окончания операции!');
	$.post("{$config['http_home_url']}engine/inc/search_advanced/functions.php", {foreign_name:"add", key:"{$dle_login_hash}"},
		function(data){
			
		if ( data == "" ) {
			DLEalert('Оригинальные названия были успешно добавлены в базу данных!', 'Информация');
			HideLoading('');
		} else {
			DLEalert(data, 'Информация');
			HideLoading('');
		}
	});
}
function HelpMePlease() {
	$("#dleuserpopup").remove();
  $("body").append("<div id='dleuserpopup' title='Помощь по настройке' style='display:none'></div>");
  $('#dleuserpopup').dialog({
		autoOpen: true,
    width: 750,
    height: 600,
    resizable: true,
    buttons: {
			"Закрыть": function() {
				$(this).dialog("close");
				$("#dleuserpopup").remove();
      }
    },
    open: function(event, ui) {
			$("#dleuserpopup").html("<iframe width='100%' height='490' src='/engine/inc/search_advanced/help.html' frameborder='0' marginwidth='0' marginheight='0' allowtransparency='true'></iframe>");
    },
    beforeClose: function(event, ui) {
			$("#dleuserpopup").html("");
    }
  });
  $('#dleuserpopup').css("-webkit-overflow-scrolling", "touch");
  return false;
}
$(document).ready( function() {
	$('#form_tpl').click( function() {
		ShowLoading('Загрузка шаблона...');		
		$.post('engine/ajax/templates.php', { action: "load", file: '/{$config['skin']}/search_advanced/form.tpl', user_hash: "{$dle_login_hash}" },
		function(data){
			HideLoading('');
			$('#fileedit').html(data);
		}, 'html');
	});
	$('#links_tpl').click( function() {
		ShowLoading('Загрузка шаблона...');		
		$.post('engine/ajax/templates.php', { action: "load", file: '/{$config['skin']}/search_advanced/search.tpl', user_hash: "{$dle_login_hash}" },
		function(data){
			HideLoading('');
			$('#fileedit').html(data);
		}, 'html');
	});
	$('#info_tpl').click( function() {
		ShowLoading('Загрузка шаблона...');		
		$.post('engine/ajax/templates.php', { action: "load", file: '/{$config['skin']}/search_advanced/info.tpl', user_hash: "{$dle_login_hash}" },
		function(data){
			HideLoading('');
			$('#fileedit').html(data);
		}, 'html');
	});
});
</script>
HTML;
} else {
echo <<<HTML
<script type="text/javascript">
function savefile (file) {
	ShowLoading('Сохранение...');
	var content = editor.getValue();
	$.post('engine/ajax/templates.php', { action: "save", file: file, content: content, user_hash: "{$dle_login_hash}" },
	function(data){
		if ( data === 'ok' ) {
			Growl.info({
				title: 'Информация',
				text: 'Сохранение прошло успешно!'
			});
		} else {
			Growl.info({
				title: 'Ошибка!',
				text: data
			});
		}
		HideLoading('');
});
}
function add_foreign_name() {
	ShowLoading('Дождитесь окончания операции!');
	$.post("{$config['http_home_url']}engine/inc/search_advanced/functions.php", {foreign_name:"add", key:"{$dle_login_hash}"},
		function(data){
			if( data === '' )  {
				Growl.info({
					title: 'Информация',
					text: 'Оригинальные названия были успешно добавлены в базу данных!',
				});
				$('#wrong').attr('style','display:none;');
				$('#good').removeAttr('style');
			} else {
				Growl.info({
					title: 'Ошибка!',
					text: data,
				});
				$('#good').attr('style','display:none;');
				$('#wrong').removeAttr('style');
				$('#answer_server').html(data);
			}
			HideLoading('');
		});
}
function HelpMePlease() {
	$("#dleuserpopup").remove();
  $("body").append("<div id='dleuserpopup' title='Помощь по настройке' style='display:none'></div>");
  $('#dleuserpopup').dialog({
		autoOpen: true,
    width: 750,
    height: 600,
    resizable: true,
    buttons: {
			"Закрыть": function() {
				$(this).dialog("close");
				$("#dleuserpopup").remove();
      }
    },
    open: function(event, ui) {
			$("#dleuserpopup").html("<iframe width='100%' height='490' src='/engine/inc/search_advanced/help.html' frameborder='0' marginwidth='0' marginheight='0' allowtransparency='true'></iframe>");
    },
    beforeClose: function(event, ui) {
			$("#dleuserpopup").html("");
    }
  });
  $('#dleuserpopup').css("-webkit-overflow-scrolling", "touch");
  return false;
}
$(document).ready( function() {
	$('#form_tpl').click( function() {
		ShowLoading('Загрузка шаблона...');		
		$.post('engine/ajax/templates.php', { action: "load", file: '/{$config['skin']}/search_advanced/form.tpl', user_hash: "{$dle_login_hash}" },
		function(data){
			HideLoading('');
			$('#fileedit').html(data);
		}, 'html');
	});
	$('#links_tpl').click( function() {
		ShowLoading('Загрузка шаблона...');		
		$.post('engine/ajax/templates.php', { action: "load", file: '/{$config['skin']}/search_advanced/search.tpl', user_hash: "{$dle_login_hash}" },
		function(data){
			HideLoading('');
			$('#fileedit').html(data);
		}, 'html');
	});
	$('#info_tpl').click( function() {
		ShowLoading('Загрузка шаблона...');		
		$.post('engine/ajax/templates.php', { action: "load", file: '/{$config['skin']}/search_advanced/info.tpl', user_hash: "{$dle_login_hash}" },
		function(data){
			HideLoading('');
			$('#fileedit').html(data);
		}, 'html');
	});
});
function savesetting() {
	ShowLoading('Сохранение...');
	$.post("{$config['http_home_url']}engine/inc/search_advanced/functions.php?settings=save", $('#configure').serialize()+'&key={$dle_login_hash}',
		function(data){
			if( data === '' )  {
				Growl.info({
					title: 'Информация',
					text: 'Настройки сохранены!',
				});
			} else {
				Growl.info({
					title: 'Ошибка!',
					text: data,
				});
			}
			HideLoading('');
		});
}
</script>
HTML;
}
if ($config['version_id'] >= '9.2' && $config['version_id'] < '10.2') echo '<link rel="stylesheet" type="text/css" href="engine/skins/codemirror/css/default.css"><script type="text/javascript" src="engine/skins/codemirror/js/code.js"></script>';
else echo '<link rel="stylesheet" type="text/css" href="/engine/skins/codemirror/css/default.css"><script type="text/javascript" src="/engine/skins/codemirror/js/code.js"></script>';
echo <<<HTML
<div id="good" class="alert alert-success" style="display:none;">
	<b>Успех!</b><br>
	<p>Оригинальные названия были успешно добавленны в базу данных!</p>
</div>
<div id="wrong" class="alert alert-error" style="display:none;">
	<b>Внимание!</b><br>
	<p>Не удалось выполнить операцию с кэшем, <b>перепроверьте настройки</b> и повторите попытку еще раз… В случае повторной неудачи обратитесь к разработчику.</p>
	<br><b>Ответ сервера:</b>	<p id="answer_server"></p>
</div>
<form id="configure">

HTML;
if ($config['version_id'] >= '9.2' && $config['version_id'] < '10.2') {
echo <<<HTML

	<input type="button" onclick="savesetting()" class="buttons" value="  {$lang['user_save']}  ">
	<input type="button" onclick="add_foreign_name()" class="buttons" value="  Добавить в базу оригинальные названия  ">


<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tbody><tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tbody><tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Обязательные настройки</div></td>
    </tr>
</tbody></table>
<div class="unterline"></div><table width="100%"><tbody><tr><td width="100%">
<table width="100%">
    <tbody>
HTML;
			showRow('Минимальное количество символов в запросе:', 		$lang_search['input_min'],"<input type='text' style='width:100%;' name='save_con[input_min]' value='{$search_config['input_min']}'>");
			showRow('Максимальное количество символов при запросе:',  $lang_search['input_max'], "<input type='text' style='width:100%;' name='save_con[input_max]' value='{$search_config['input_max']}'>");
			showRow('Максимальное количество результатов в выдаче:', '',												 makeDropDown( array ("1" => '1', "2" => '2', "3" => '3', "4" => '4', "5" => '5', "6" => '6', "7" => '7', "8" => '8', "9" => '9', "10" => '10', "11" => '11', "12" => '12', "13" => '13', "14" => '14', "15" => '15'), "save_con[result_num]", "{$search_config['result_num']}" ));
			showRow('Время жизни кэша:',															$lang_search['cache_time'],makeDropDown( array ("86400" => 'Сутки', "43200" => 'Половина суток', "172800" => 'Двое суток'), "save_con[cache_time]", "{$search_config['cache_time']}" ));
			showRow('Информация о модуле', '<b>Версия — v1.1</b><br>Возможности модуля:<br>— При поиске не учитывается различие между Е и Ё, находятся оба варианта (Болезненно для кодировки windows).<br>— Кэширование в модуле(Сильно снижает нагрузку).<br>— Распознавание коротких циферных названий фильмов (Будет искать фильм 2012 или ничего не найдет).<br>— Гибкая настройка функциональности модуля.<br>— Более точное вхождение ответов к запросам пользователей.<br>— Возможность гибко и индивидуально настраивать шаблоны для разного поведения поиска.<br>И т.д.',  '<b>Автор:</b> Intention (<a href="https://vk.com/exclusive_bk" target="_blank" title="Моя страница Вконтакте" style="text-decoration:underline;">ссылка</a>)<br><b>E-mail</b> — fen9.bkamen@gmail.com<br><b>Skype</b> — fen9.bkamen<br><b>ICQ</b> — 678027145');
echo <<<HTML
</tbody></table>
</td></tr></tbody></table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</tbody></table>
</div>

<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tbody><tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tbody><tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Дополнительная настройка</div></td>
    </tr>
</tbody></table>
<div class="unterline"></div><table width="100%"><tbody><tr><td width="100%">
<table width="100%">
    <tbody>
HTML;
			showRow('Исправлять неправильную раскладку?', 						 $lang_search['wrong_layout'], 		makeCheckBox( "save_con[wrong_layout]", "{$search_config['wrong_layout']}" ));
			showRow('Использовать поиск по оригиальным названиям?',		 $lang_search['text_language'], 	makeCheckBox( "save_con[text_language]", "{$search_config['text_language']}" ));
			showRow('Формат оригинальных названий:',				 					 $lang_search['type_foreign_name'],makeDropDown( array ("with_hyphen" => 'Jurassic World - 2015', "with_double_dot" => 'Jurassic World: 2015', "with_slash" => 'Jurassic World / 2015', "with_year" => 'Jurassic World (2015)', "with_year_clear" => 'Jurassic World 2015', "without_year" => 'Jurassic World'), "save_con[type_foreign_name]", "{$search_config['type_foreign_name']}" ));
			showRow('ID кинопоиска (Оригинальные названия):', 				 $lang_search['kpid'], 						makeDropDown( ( $fields['kinopoisk'] ), "save_con[field_kpid]", "{$search_config['field_kpid']}" ));
			showrow('Интервал запросов в БД (Оригинальные названия):', $lang_search['interval_sleep'], 	makeDropDown( array ("1500000" => '1,5 сек', "1000000" => '1 сек', "0750000" => '7,5 мс', "0500000" => '5 мс', "0200000" => '2 мс', "0100000" => '1 мс', "0050000" => '0,5 мс', "0000000" => '0 мс'), "save_con[interval_sleep]", "{$search_config['interval_sleep']}" ));
echo <<<HTML
</tbody></table>
</td></tr></tbody></table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</tbody></table>
</div>

<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tbody><tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tbody><tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Настройка рекомендаций</div></td>
    </tr>
</tbody></table>
<div class="unterline"></div><table width="100%"><tbody><tr><td width="100%">
<table width="100%">
    <tbody>
HTML;
			showRow('Показывать рекомендации?', 	$lang_search['result_null'], 		makeCheckBox( "save_con[result_null]", "{$search_config['result_null']}" ));
			showRow('Количество рекомендаций:', 	'', 														makeDropDown( array ("1" => '1', "2" => '2', "3" => '3', "4" => '4', "5" => '5', "6" => '6', "7" => '7', "8" => '8', "9" => '9', "10" => '10', "11" => '11', "12" => '12', "13" => '13', "14" => '14', "15" => '15', "16" => '16', "17" => '17', "18" => '18', "19" => '19', "20" => '20', "21" => '21', "22" => '22', "23" => '23', "24" => '24', "25" => '25', "26" => '26', "27" => '27', "28" => '28', "29" => '29', "30" => '30'), "save_con[related_num]", "{$search_config['related_num']}" ));
			showRow('Фильтр для рекомендаций:', 	$lang_search['related_mode'], 	makeDropDown( array ("1" => 'Фильтровать по просмотрам', "2" => 'Фильтровать по комментариям', "3" => 'Указать рекомендации в ручную', "3" => 'Указать рекомендации в ручную', "4" => 'Выбирать рандомно(Случайно)'), "save_con[related_mode]", "{$search_config['related_mode']}" ));
			showRow('Ручные рекомендации:', 			$lang_search['related_manual'], "<input type='text' style='width:100%;' name='save_con[related_manual]' value='{$search_config['related_manual']}'>");
echo <<<HTML
</tbody></table>
</td></tr></tbody></table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</tbody></table>
</div>


<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tbody><tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tbody><tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">Настройка шаблонов</div></td>
    </tr>
</tbody></table>
<div class="unterline"></div><table width="100%"><tbody><tr><td width="100%">
<table width="100%">
    <tbody><tr><td>

			<div class="box-content">
		<table class="table table-normal"><center>
			<input type="button" onclick="javascript:HelpMePlease(); return false;" style="margin:10px" id="template_edit_button" class="buttons" value="  Инструкция по настройке шаблонов  ">
			<input type="button" onclick="return false;" style="margin:10px" id="form_tpl" class="buttons" value="  Шаблон формы поиска  ">
			<input type="button" onclick="return false;" style="margin:10px" id="links_tpl" class="buttons" value="  Шаблон ссылок для выдачи  ">
			<input type="button" onclick="return false;" style="margin:10px" id="info_tpl" class="buttons" value="  Шаблон информационных сообщений  "></center>
			<div id="fileedit" style="border: solid 1px #BBB;height: 560px; padding:5px;"></div>
		</table></div>
		
</td></tr></tbody></table>
</td></tr></tbody></table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</tbody></table></div>
HTML;
} else {
echo <<<HTML
<div style="margin-bottom:10px;">
	<button type="button" onclick="savesetting()" class="btn btn-green"><i class="icon-save"></i> {$lang['user_save']}</button>
	<button type="button" onclick="add_foreign_name()" class="btn btn-gold"><i class="icon-retweet"></i> Добавить в базу оригинальные названия</button>
</div>

<div class="box">
  <div class="box-header">
    <div class="title">Обязательные параметры</div>
  </div>

	<div class="box-content">
		<table class="table table-normal">
HTML;
	showRow('Минимальное количество символов в запросе:', 		$lang_search['input_min'],"<input type='text' style='width:100%;' name='save_con[input_min]' value='{$search_config['input_min']}'>");
	showRow('Максимальное количество символов при запросе:',  $lang_search['input_max'], "<input type='text' style='width:100%;' name='save_con[input_max]' value='{$search_config['input_max']}'>");
	showRow('Максимальное количество результатов в выдаче:', '',												 makeDropDown( array ("1" => '1', "2" => '2', "3" => '3', "4" => '4', "5" => '5', "6" => '6', "7" => '7', "8" => '8', "9" => '9', "10" => '10', "11" => '11', "12" => '12', "13" => '13', "14" => '14', "15" => '15'), "save_con[result_num]", "{$search_config['result_num']}" ));
	showRow('Время жизни кэша:',															$lang_search['cache_time'],makeDropDown( array ("86400" => 'Сутки', "43200" => 'Половина суток', "172800" => 'Двое суток'), "save_con[cache_time]", "{$search_config['cache_time']}" ));
	showRow('Информация о модуле', '<b>Версия — v1.1</b><br>Возможности модуля:<br>— При поиске не учитывается различие между Е и Ё, находятся оба варианта (Болезненно для кодировки windows).<br>— Кэширование в модуле(Сильно снижает нагрузку).<br>— Распознавание коротких циферных названий фильмов (Будет искать фильм 2012 или ничего не найдет).<br>— Гибкая настройка функциональности модуля.<br>— Более точное вхождение ответов к запросам пользователей.<br>— Возможность гибко и индивидуально настраивать шаблоны для разного поведения поиска.<br>И т.д.',  '<b>Автор:</b> Intention (<a href="https://vk.com/exclusive_bk" target="_blank" title="Моя страница Вконтакте" style="text-decoration:underline;">ссылка</a>)<br><b>E-mail</b> — fen9.bkamen@gmail.com<br><b>Skype</b> — fen9.bkamen<br><b>ICQ</b> — 678027145');
echo <<<HTML
		</table>
	</div>
</div>

<div class="box">
  <div class="box-header">
    <div class="title">Дополнительная настройка</div>
  </div>

	<div class="box-content">
		<table class="table table-normal">
HTML;
	showRow('Исправлять неправильную раскладку?', 						 $lang_search['wrong_layout'], 		makeCheckBox( "save_con[wrong_layout]", "{$search_config['wrong_layout']}" ));
	showRow('Использовать поиск по оригиальным названиям?',		 $lang_search['text_language'], 	makeCheckBox( "save_con[text_language]", "{$search_config['text_language']}" ));
	showRow('Формат оригинальных названий:',				 					 $lang_search['type_foreign_name'],makeDropDown( array ("with_hyphen" => 'Jurassic World - 2015', "with_double_dot" => 'Jurassic World: 2015', "with_slash" => 'Jurassic World / 2015', "with_year" => 'Jurassic World (2015)', "with_year_clear" => 'Jurassic World 2015', "without_year" => 'Jurassic World'), "save_con[type_foreign_name]", "{$search_config['type_foreign_name']}" ));
  showRow('ID кинопоиска (Оригинальные названия):', 				 $lang_search['kpid'], 						makeDropDown( ( $fields['kinopoisk'] ), "save_con[field_kpid]", "{$search_config['field_kpid']}" ));
	showrow('Интервал запросов в БД (Оригинальные названия):', $lang_search['interval_sleep'], 	makeDropDown( array ("1500000" => '1,5 сек', "1000000" => '1 сек', "0750000" => '7,5 мс', "0500000" => '5 мс', "0200000" => '2 мс', "0100000" => '1 мс', "0050000" => '0,5 мс', "0000000" => '0 мс'), "save_con[interval_sleep]", "{$search_config['interval_sleep']}" ));
echo <<<HTML
		</table>
	</div>
</div>

<div class="box">
  <div class="box-header">
    <div class="title">Настройка рекомендаций</div>
  </div>

	<div class="box-content">
		<table class="table table-normal">
HTML;
	showRow('Показывать рекомендации?', 	$lang_search['result_null'], 		makeCheckBox( "save_con[result_null]", "{$search_config['result_null']}" ));
	showRow('Количество рекомендаций:', 	'', 														makeDropDown( array ("1" => '1', "2" => '2', "3" => '3', "4" => '4', "5" => '5', "6" => '6', "7" => '7', "8" => '8', "9" => '9', "10" => '10', "11" => '11', "12" => '12', "13" => '13', "14" => '14', "15" => '15', "16" => '16', "17" => '17', "18" => '18', "19" => '19', "20" => '20', "21" => '21', "22" => '22', "23" => '23', "24" => '24', "25" => '25', "26" => '26', "27" => '27', "28" => '28', "29" => '29', "30" => '30'), "save_con[related_num]", "{$search_config['related_num']}" ));
	showRow('Фильтр для рекомендаций:', 	$lang_search['related_mode'], 	makeDropDown( array ("1" => 'Фильтровать по просмотрам', "2" => 'Фильтровать по комментариям', "3" => 'Указать рекомендации в ручную', "3" => 'Указать рекомендации в ручную', "4" => 'Выбирать рандомно(Случайно)'), "save_con[related_mode]", "{$search_config['related_mode']}" ));
	showRow('Ручные рекомендации:', 			$lang_search['related_manual'], "<input type='text' style='width:100%;' name='save_con[related_manual]' value='{$search_config['related_manual']}'>");
echo <<<HTML
		</table>
	</div>
</div>

<div class="box">
  <div class="box-header">
    <div class="title">Настройка шаблона</div>
  </div>

	<div class="box-content">
		<table class="table table-normal"><center>
			<button onclick="javascript:HelpMePlease(); return false;" style="margin:10px" id="template_edit_button" class="btn btn-blue"><i class="icon-flag"></i> Инструкция по настройке шаблонов</button>
			<button onclick="return false;" style="margin:10px" id="form_tpl" class="btn btn-gold"> Шаблон формы поиска</button>
			<button onclick="return false;" style="margin:10px" id="links_tpl" class="btn btn-gold"> Шаблон ссылок для выдачи</button>
			<button onclick="return false;" style="margin:10px" id="info_tpl" class="btn btn-gold"> Шаблон информационных сообщений</button></center>
			<div id="fileedit" style="border: solid 1px #BBB;height: 560px; padding:5px;"></div>
		</table></div>
</div>
HTML;
}
echo <<<HTML
</form>
HTML;
} else {
echo <<<HTML
<div class="box">
  <div class="box-header">
    <div class="title">Доступ запрещен!</div>
  </div>
  <div class="box-content">
		<center>
			<img src="/engine/skins/images/search_advanced_stop.png" alt="">
		</center>
	</div>
</div>
HTML;
}
echofooter();
?>