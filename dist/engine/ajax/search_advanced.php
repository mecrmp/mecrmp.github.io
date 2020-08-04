<?php
/*
=====================================================
 Файл: /ajax/search_advanced.php
-----------------------------------------------------
 Назначение: Поиск
=====================================================
*/

@error_reporting (E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set ('display_errors', true);
@ini_set ('html_errors', false);
@ini_set ('error_reporting', E_ALL ^ E_WARNING ^ E_NOTICE);
header("Content-type: text/html; charset=utf-8");

define('DATALIFEENGINE', true);
define('ROOT_DIR', substr(dirname( __FILE__ ), 0, -12));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include ENGINE_DIR . '/data/config.php'; $charset = 'utf-8';
include ENGINE_DIR . '/data/search_config.php';
include ENGINE_DIR . '/api/api.class.php';

date_default_timezone_set ($config['date_adjust']);
if($config['http_home_url'] == '') {
	 $config['http_home_url'] = explode('engine/ajax/search_advanced.php', $_SERVER['PHP_SELF']);
	 $config['http_home_url'] = reset($config['http_home_url']);
	 $config['http_home_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
}
//опредиления наличия категории
$query_cat	= trim($_POST['cat']);
if (empty($query_cat)) {
$query_cat_or = "";
}else {
$query_cat_or = "AND (category = '$query_cat')";
}

//опредиления наличия сортировки
$x = trim($_POST['sort']);
if ($x <= 0) {
    $query_sort_or = 'date'; //если нет или 0 по умолчанию 0
    } elseif ($x == 1) { //параметер получен как 1  
    $query_sort_or = 'title';       //, истественно $query_sort_or равен 1 и так далие
    } elseif ($x == 2) {
    $query_sort_or = 'revel';
} else {
    $query_sort_or = 'date';//если больше ,то по умолчанию 0
}
//echo $query_sort_or;
//опредиления наличия Упорядочить
$x_sad = trim($_POST['sad']);
if ($x_sad <= 0) {
    $query_sad_or = 'desc'; //если нет или 0 по умолчанию 0
    } elseif ($x_sad == 1) { //параметер получен как 1  
    $query_sad_or = '';       //, истественно $query_sort_or равен 1 и так далие  
} else {
    $query_sad_or = 'desc';//если больше ,то по умолчанию 0
}
//echo $query_sad_or;

$query = $db->safesql(htmlspecialchars(trim($_POST['query'])),ENT_QUOTES,$charset);
if (iconv_strlen($query, $charset) > $search_config['input_max']) die('<tr class="gai"><script type="text/javascript">var response =("<img src=\"/templates/Default/images/warning-animated.gif\" alt=\"Warning\" style=\"width: 16px; height: 16px;\" > Слишком большой запрос, попробуйте уменьшить его.");$("#eror").html(response);</script></tr>');
if (iconv_strlen($query, $charset) < $search_config['input_min'] || $query == '') die('<tr class="gai"><script type="text/javascript">var response =("<img src=\"/templates/Default/images/warning-animated.gif\" alt=\"Warning\" style=\"width: 16px; height: 16px;\" > Слишком маленький запрос, попробуйте изменить его.");$("#eror").html(response);</script></tr>');
if (is_numeric($query)) {$no_words = true; $search_config['text_language'] = 0;} else {$no_words = false;}
$bad_words = array('/ с /', '/ к /', '/ на /', '/ от /', '/ над /', '/ по /', '/ у /', '/ о /', '/ под /', '/ из /', '/ без /', '/ для /', '/ до /', '/ в /', '/ об /', '/ за /', '/ а /', '/ но /', '/ да /', '/ или /', '/ что /', '/ как /');
$query = preg_replace($bad_words, ' ', $query);
$cache_query = $query.$query_cat_or.$query_sort_or.trim($_POST['full']).$query_sad_or;

if (file_exists(ENGINE_DIR . '/cache/' . md5($cache_query) . '.tmp')) {echo $dle_api->load_from_cache (md5($cache_query), $search_config['cache_time'], text);}
else {
	require_once ENGINE_DIR . '/classes/mysql.php';
	require_once ENGINE_DIR . '/data/dbconfig.php';
	require_once ENGINE_DIR . '/modules/functions.php';
	require_once ENGINE_DIR . '/classes/templates.class.php';
	
//Загружаем основной шаблон, инициализируем дополнительные поля
$tpl = new dle_template();
$tpl->dir = ROOT_DIR . '/templates/' . $config['skin'] . '/search_advanced';
define( 'TEMPLATE_DIR', $tpl->dir );
$tpl->load_template( 'search.tpl' );
if(strpos($tpl->copy_template, "xfvalue") !== false || strpos($tpl->copy_template, "[xfgiven_") !== false) {
	$xfound = true; $xfdb = ', xfields'; $xfields = xfieldsload();
} else $xfound = false;

//Инициализация категорий
if(!is_array($cat_info)) {
	$cat_info = get_vars("category");
	$cat_info = array ();
	$db->query("SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC");
	while ($row = $db->get_row()) {
		$cat_info[$row['id']] = array ();
		foreach ($row as $key => $value) $cat_info[$row['id']][$key] = stripslashes($value);
	}
	set_vars("category", $cat_info);
	$db->free();
}

//Конверт раскладки
if ($search_config['wrong_layout'] == 1 && $no_words == false) {
	function translate_en($string) {
    $letter = array(
			'`' => 'ё',	'q' => 'й',	'w' => 'ц',	'e' => 'у',	'r' => 'к',	't' => 'е',	'y' => 'н',	'u' => 'г',	
			'i' => 'ш',	'o' => 'щ',	'p' => 'з',	'[' => 'х',	']' => 'ъ',	'a' => 'ф',	's' => 'ы',	'd' => 'в',	
			'f' => 'а',	'g' => 'п',	'h' => 'р',	'j' => 'о',	'k' => 'л',	'l' => 'д',	';' => 'ж',	'&#039;'=> 'э',	
			'z' => 'я',	'x' => 'ч',	'c' => 'с',	'v' => 'м',	'b' => 'и',	'n' => 'т',	'm' => 'ь',	',' => 'б',	
			'.' => 'ю',
			'~' => 'ё',	'Q' => 'й',	'W' => 'ц',	'E' => 'у',	'R' => 'к',	'T' => 'е',	'Y' => 'н',	'U' => 'г',	
			'I' => 'ш',	'O' => 'щ',	'P' => 'з',	'{' => 'х',	'}' => 'ъ',	'A' => 'ф',	'S' => 'ы',	'D' => 'в',	
			'F' => 'а',	'G' => 'п',	'H' => 'р',	'J' => 'о',	'K' => 'л',	'L' => 'д',	':' => 'ж',	'&quot;' => 'э',	
			'Z' => 'я',	'X' => 'ч',	'C' => 'с',	'V' => 'м',	'B' => 'и',	'N' => 'т',	'M' => 'ь',	'&lt;' => 'б',	
			'&gt;' => 'ю'
		);
    return strtr($string, $letter);
	}
	function translate_ru($string) {
    $letter = array(
			'ё' => '`',	'й' => 'q',	'ц' => 'w',	'у' => 'e',	'к' => 'r',	'е' => 't',	'н' => 'y',	'г' => 'u',	
			'ш' => 'i',	'щ' => 'o',	'з' => 'p',	'х' => '[',	'ъ' => ']',	'ф' => 'a',	'ы' => 's',	'в' => 'd',	
			'а' => 'f',	'п' => 'g',	'р' => 'h',	'о' => 'j',	'л' => 'k',	'д' => 'l',	'ж' => ';',	'э' => '&#039;',	
			'я' => 'z',	'ч' => 'x',	'с' => 'c',	'м' => 'v',	'и' => 'b',	'т' => 'n',	'ь' => 'm',	'б' => ',',	
			'.' => 'ю',
			'Ё' => '~',	'Й' => 'Q',	'Ц' => 'W',	'У' => 'E',	'К' => 'R',	'Е' => 'T',	'Н' => 'Y',	'Г' => 'U',	
			'Ш' => 'I',	'Щ' => 'O',	'З' => 'P',	'Х' => '{',	'Ъ' => '}',	'Ф' => 'A',	'Ы' => 'S',	'В' => 'D',	
			'А' => 'F',	'П' => 'G',	'Р' => 'H',	'О' => 'J',	'Л' => 'K',	'Д' => 'L',	'Ж' => ':',	'Э' => '&quot;',	
			'Я' => 'Z',	'Ч' => 'X',	'С' => 'C',	'М' => 'V',	'И' => 'B',	'Т' => 'N',	'Ь' => 'M',	'Б' => '<',	
			'>' => 'Ю'
    );
    return strtr($string, $letter);
  }
	$en = translate_en($query);
	$ru = translate_ru($query);
}

//Запрос в БД
$query_full	= trim($_POST['full']);
if (empty($query_full)) { //если нету переменной $query_full то выполняем поиск по title
if ($search_config['wrong_layout'] == 1 && $no_words == false) {
		$en = explode(" ", $en);
		$ru = explode(" ", $ru);
		foreach ($en as $en_part)	$sql_en[] = "title LIKE '%" . $en_part . "%'" ;
		foreach ($ru as $ru_part) $sql_ru[] = "title LIKE '%" . $ru_part . "%'";
		if ($search_config['text_language'] == 1 && $no_words == false) {
			$foreign_place = ', srchadv_foreign';
			foreach ($ru as $foreign_search) $sql_foreign[] = "srchadv_foreign LIKE '%" . $foreign_search . "%'";
			$text_language = " OR " . implode(" AND ", $sql_foreign);
			$text_language_chance = " OR " . implode(" OR ", $sql_foreign);
		}
		$to_sql = implode(" AND ", $sql_ru) . $text_language . " OR " . implode(" AND ", $sql_en);
		$to_sql_chance = implode(" OR ", $sql_ru) . $text_language_chance . " OR " . implode(" OR ", $sql_en);
	} elseif ($search_config['wrong_layout'] < 1 && $no_words == false) {
		$query = explode(" ", $query);
		foreach ($query as $part) $sql[] = "title LIKE '%" . $part . "%'";
		if ($search_config['text_language'] == 1 && $no_words == false) {
			$foreign_place = ', srchadv_foreign';
			foreach ($query as $foreign_search) $sql_foreign[] = "srchadv_foreign LIKE '%" . $foreign_search . "%'";
			$text_language = " OR " . implode(" AND ", $sql_foreign);
			$text_language_chance = " OR " . implode(" OR ", $sql_foreign);
		}
		$to_sql = implode(" AND ", $sql) . $text_language;
		$to_sql_chance = implode(" OR ", $sql) . $text_language_chance;
	} elseif ($no_words == true) {
		$to_sql = "title LIKE '" . $query . "%'";
		$to_sql_chance = "title LIKE '" . $query . "%'";
	}
	$revel_query = "title"; 
}else { //если есть переменная $query_full то выполняем поиск по full_story
if ($search_config['wrong_layout'] == 1 && $no_words == false) {
		$en = explode(" ", $en);
		$ru = explode(" ", $ru);
		foreach ($en as $en_part)	$sql_en[] = "full_story LIKE '%" . $en_part . "%'" ;
		foreach ($ru as $ru_part) $sql_ru[] = "full_story LIKE '%" . $ru_part . "%'";
		if ($search_config['text_language'] == 1 && $no_words == false) {
			$foreign_place = ', srchadv_foreign';
			foreach ($ru as $foreign_search) $sql_foreign[] = "srchadv_foreign LIKE '%" . $foreign_search . "%'";
			$text_language = " OR " . implode(" AND ", $sql_foreign);
			$text_language_chance = " OR " . implode(" OR ", $sql_foreign);
		}
		$to_sql = implode(" AND ", $sql_ru) . $text_language . " OR " . implode(" AND ", $sql_en);
		$to_sql_chance = implode(" OR ", $sql_ru) . $text_language_chance . " OR " . implode(" OR ", $sql_en);
	} elseif ($search_config['wrong_layout'] < 1 && $no_words == false) {
		$query = explode(" ", $query);
		foreach ($query as $part) $sql[] = "full_story LIKE '%" . $part . "%'";
		if ($search_config['text_language'] == 1 && $no_words == false) {
			$foreign_place = ', srchadv_foreign';
			foreach ($query as $foreign_search) $sql_foreign[] = "srchadv_foreign LIKE '%" . $foreign_search . "%'";
			$text_language = " OR " . implode(" AND ", $sql_foreign);
			$text_language_chance = " OR " . implode(" OR ", $sql_foreign);
		}
		$to_sql = implode(" AND ", $sql) . $text_language;
		$to_sql_chance = implode(" OR ", $sql) . $text_language_chance;
	} elseif ($no_words == true) {
		$to_sql = "full_story LIKE '" . $query . "%'";
		$to_sql_chance = "full_story LIKE '" . $query . "%'";
	}
	$revel_query = "full_story"; 
}
if($query_sort_or == 'revel') {
	$query_sort_or = "CASE WHEN ".$revel_query." LIKE '" . $query . "' THEN 0 WHEN ".$revel_query." LIKE '" . $query . " %' THEN 1 WHEN ".$revel_query." LIKE '" . $query . "%' THEN 2 WHEN ".$revel_query." LIKE '% " . $query . "%' THEN 3 ELSE 4 END"; 	
	
}

//Отменяем новости с ненаступившей датой
$_TIME = time();
$this_date = date("Y-m-d H:i:s", $_TIME);
if($config['no_date'] && !$config['news_future']) $this_date = " AND " . PREFIX . "_post.date < '" . $this_date . "'"; else $this_date = '';

	$db->query("SELECT id, title, alt_name, xfields, category, short_story, full_story, date{$foreign_place}{$xfdb} FROM " . PREFIX . "_post WHERE approve=1" . $this_date . " AND ({$to_sql})  ".$query_cat_or."  ORDER by ".$query_sort_or." ".$query_sad_or." LIMIT {$search_config['result_num']}");
		if ($db->num_rows() < 1) {
			$db->query("SELECT id, title, alt_name, xfields, category, short_story, full_story, date{$foreign_place}{$xfdb} FROM " . PREFIX . "_post WHERE approve=1" . $this_date . " AND ({$to_sql_chance}) ".$query_cat_or." ORDER by ".$query_sort_or." ".$query_sad_or." LIMIT {$search_config['result_num']}");
			if ($db->num_rows() != 0) $youmean = true;
		}

//Рекомендации
	if ($search_config['result_null'] == 1 && $db->num_rows() == null) {
		if ($search_config['related_mode'] == 1)	{
			$db->query("SELECT news_id, news_read FROM " . PREFIX . "_post_extras ORDER BY " . PREFIX . "_post_extras.news_read DESC LIMIT {$search_config['related_num']}");
				while($rows = $db->get_row()) $related_rows[] = $rows['news_id'];
				$related_rows = implode(",", $related_rows);
			$db->query("SELECT id, title, alt_name, xfields, category, short_story, full_story, date{$xfdb} FROM " . PREFIX . "_post WHERE `id` IN ({$related_rows})");
		} elseif ($search_config['related_mode'] == 2) $db->query("SELECT id, title, alt_name, xfields, category, short_story, full_story, date{$xfdb} FROM " . PREFIX . "_post ORDER BY " . PREFIX . "_post.comm_num DESC LIMIT {$search_config['related_num']}");
			elseif ($search_config['related_mode'] == 3) $db->query("SELECT id, title, alt_name, xfields, category, short_story, full_story, date{$xfdb} FROM " . PREFIX . "_post WHERE `id` IN ({$search_config['related_manual']})");
			elseif ($search_config['related_mode'] == 4) $db->query("SELECT " . PREFIX . "_post.id, title, alt_name, xfields, category, short_story, full_story, date{$xfdb} FROM (SELECT id FROM " . PREFIX . "_post ORDER BY RAND() LIMIT {$search_config['related_num']}) AS ids JOIN " . PREFIX . "_post ON " . PREFIX . "_post.id = ids.id");
		$recomendation = true;
	}

//Формирование ссылок
	while($row = $db->get_row()) {
		//var_dump($row );
		$date  = strtotime($row['date']);
		$cat   = intval($row['category']);
		$id    = $row['id'];
		$short = $row['short_story'];
		$full  = $row['full_story'];
		$name  = $row['alt_name'];
		$home  = $config['http_home_url'];
		$title = $row['title'];
		$foreign_title = stripslashes($row['srchadv_foreign']);
		$category = $cat_info[$cat]['name'];
    //$row['xfields'] = $row['xfields'];
     include ENGINE_DIR . '/modules/multi-short-info.php';
		if ($config['allow_alt_url'] == 1 || $config['allow_alt_url'] == "yes") {
			if ($cat && $config['seo_type'] == 2)  $link = $home . get_url($cat) . "/{$id}-{$name}.html";
			if (!$cat || $config['seo_type'] == 1) $link = "{$home}{$id}-{$name}.html";
			if ($config['seo_type'] == 0) 				 $link = $home . date('Y/m/d/', $date) . "{$name}.html";
		} else 																	 $link = "{$home}index.php?newsid={$id}";
//Если нужно вывести описания
  if (preg_match("#\\{full-story limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches) || strpos($tpl->copy_template, "{full-story}") !== false) {
		$count= intval($matches[1]);
		if(!$count) $count = 150;
		$full = preg_replace( "#<!--TBegin(.+?)<!--TEnd-->#is", "", $full );
	  $full = preg_replace( "#<!--MBegin(.+?)<!--MEnd-->#is", "", $full );
		$full = preg_replace( "'\[attachment=(.*?)\]'si", "", $full );
		$full = preg_replace ( "#\[hide\](.+?)\[/hide\]#ims", "", $full );
		$full = str_replace( "</p><p>", " ", $full );
		$full = strip_tags( $full, "<br>" );
		$full = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $full ) ) ) ));
		if( $count && dle_strlen( $full, $charset ) > $count ) {
			$full = dle_substr( $full, 0, $count, $charset );
			if( ($temp_dmax = dle_strrpos( $full, ' ', $charset )) ) $full = dle_substr( $full, 0, $temp_dmax, $charset );
		}
		$tpl->set( $matches[0], $full );
	}
	if (preg_match("#\\{short-story limit=['\"](.+?)['\"]\\}#i", $tpl->copy_template, $matches) || strpos($tpl->copy_template, "{short-story}") !== false) {
    $count= intval($matches[1]);
		if(!$count) $count = 150;
		$short = preg_replace( "#<!--TBegin(.+?)<!--TEnd-->#is", "", $short );
	  $short = preg_replace( "#<!--MBegin(.+?)<!--MEnd-->#is", "", $short );
		$short = preg_replace( "'\[attachment=(.*?)\]'si", "", $short );
		$short = preg_replace ( "#\[hide\](.+?)\[/hide\]#ims", "", $short );
		$short = str_replace( "</p><p>", " ", $short );
		$short = strip_tags( $short, "<br>" );
		$short = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $short ) ) ) ));
		if( $count AND dle_strlen( $short, $charset ) > $count ) {
			$short = dle_substr( $short, 0, $count, $charset );
			if( ($temp_dmax = dle_strrpos( $short, ' ', $charset )) ) $short = dle_substr( $short, 0, $temp_dmax, $charset );
		}
		$tpl->set( $matches[0], $short );
	}
//Проверка на изображения с описания
    if (stripos ( $tpl->copy_template, "{image-" ) !== false) {
	    $images = array();
		if (preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $short, $media)) {
			preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $short, $media);
		} else preg_match_all('/(img|src)=("|\')[^"\'>]+/i', $full, $media);
		$data=preg_replace('/(img|src)("|\'|="|=\')(.*)/i',"$3",$media[0]);
		foreach($data as $url) {
			$info = pathinfo($url);
			if (isset($info['extension'])) {
				if ($info['filename'] == "spoiler-plus" || $info['filename'] == "spoiler-minus" ) continue;
				$info['extension'] = strtolower($info['extension']);
				if (($info['extension'] == 'jpg') || ($info['extension'] == 'jpeg') || ($info['extension'] == 'gif') || ($info['extension'] == 'png')) array_push($images, $url);
			}
		}
		if ( count($images) ) {
			$i=0;
			foreach($images as $url) {
		    $i++;
				$tpl->copy_template = str_replace( '{image-'.$i.'}', $url, $tpl->copy_template );
				$tpl->copy_template = str_replace( '[image-'.$i.']', "", $tpl->copy_template );
				$tpl->copy_template = str_replace( '[/image-'.$i.']', "", $tpl->copy_template );
			}
			
		}
	    $tpl->copy_template = preg_replace( "#\[image-(.+?)\](.+?)\[/image-(.+?)\]#is", "", $tpl->copy_template );			
      $tpl->copy_template = preg_replace( "#\\{image-(.+?)\\}#i", "{THEME}/dleimages/no_image.jpg", $tpl->copy_template );
	}
//Инициализация дополнительных полей
		if($xfound) {
			$xfieldsdata = xfieldsdataload($row['xfields']);
			foreach ($xfields as $value) {
				$preg_safe_name = preg_quote($value[0], "'");
				$xfieldsdata[$value[0]] = stripslashes($xfieldsdata[$value[0]]);
				if ($value[3] == "yesorno") {
				    if( intval($xfieldsdata[$value[0]]) ) {
						$xfgiven = true;
						$xfieldsdata[$value[0]] = $lang['xfield_xyes'];
					} else {
						$xfgiven = false;
						$xfieldsdata[$value[0]] = $lang['xfield_xno'];
					}
				} else if($xfieldsdata[$value[0]] == "") $xfgiven = false; else $xfgiven = true;
				if(!$xfgiven) {
					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
					$tpl->copy_template = str_replace( "[xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
					$tpl->copy_template = str_replace( "[/xfnotgiven_{$value[0]}]", "", $tpl->copy_template );
				} else {
					$tpl->copy_template = preg_replace( "'\\[xfnotgiven_{$preg_safe_name}\\](.*?)\\[/xfnotgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
					$tpl->copy_template = str_replace( "[xfgiven_{$value[0]}]", "", $tpl->copy_template );
					$tpl->copy_template = str_replace( "[/xfgiven_{$value[0]}]", "", $tpl->copy_template );
				}
				if(strpos( $tpl->copy_template, "[ifxfvalue" ) !== false ) {
					$tpl->copy_template = preg_replace_callback ( "#\\[ifxfvalue(.+?)\\](.+?)\\[/ifxfvalue\\]#is", "check_xfvalue", $tpl->copy_template );
				}
				if ( $value[6] AND !empty( $xfieldsdata[$value[0]] ) ) {
					$temp_array = explode( ",", $xfieldsdata[$value[0]] );
					$value3 = array();
					foreach ($temp_array as $value2) {
						$value2 = trim($value2);
						$value2 = str_replace("&#039;", "'", $value2);
						if( $config['allow_alt_url'] ) $value3[] = "<a href=\"" . $config['http_home_url'] . "xfsearch/" .$value[0]."/". urlencode( $value2 ) . "/\">" . $value2 . "</a>";
						else $value3[] = "<a href=\"$PHP_SELF?do=xfsearch&amp;xfname=".$value[0]."&amp;xf=" . urlencode( $value2 ) . "\">" . $value2 . "</a>";
					}
					$xfieldsdata[$value[0]] = implode(", ", $value3);
					unset($temp_array,$value2,$value3);
				}
				if($value[3] == "image" AND $xfieldsdata[$value[0]] ) {
					$path_parts = @pathinfo($xfieldsdata[$value[0]]);
					if( $value[12] AND file_exists(ROOT_DIR . "/uploads/posts/" .$path_parts['dirname']."/thumbs/".$path_parts['basename']) ) {
						$thumb_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/thumbs/".$path_parts['basename'];
						$img_url = $config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
					} else {
						$img_url = 	$config['http_home_url'] . "uploads/posts/" . $path_parts['dirname']."/".$path_parts['basename'];
						$thumb_url = "";
					}
					if($thumb_url) {
						$xfieldsdata[$value[0]] = "<a href=\"$img_url\" rel=\"highslide\" class=\"highslide\" target=\"_blank\"><img class=\"xfieldimage {$value[0]}\" src=\"$thumb_url\" alt=\"\" /></a>";
					} else $xfieldsdata[$value[0]] = "<img class=\"xfieldimage {$value[0]}\" src=\"{$img_url}\" alt=\"\" />";
				}
				$tpl->copy_template = str_replace( "[xfvalue_{$value[0]}]", $xfieldsdata[$value[0]], $tpl->copy_template );
				if ( preg_match( "#\\[xfvalue_{$preg_safe_name} limit=['\"](.+?)['\"]\\]#i", $tpl->copy_template, $matches ) ) {
					$count= intval($matches[1]);
					$xfieldsdata[$value[0]] = str_replace( "</p><p>", " ", $xfieldsdata[$value[0]] );
					$xfieldsdata[$value[0]] = strip_tags( $xfieldsdata[$value[0]], "<br>" );
					$xfieldsdata[$value[0]] = trim(str_replace( "<br>", " ", str_replace( "<br />", " ", str_replace( "\n", " ", str_replace( "\r", "", $xfieldsdata[$value[0]] ) ) ) ));
					if( $count AND dle_strlen( $xfieldsdata[$value[0]], $charset ) > $count ) {
						$xfieldsdata[$value[0]] = dle_substr( $xfieldsdata[$value[0]], 0, $count, $charset );
						if( ($temp_dmax = dle_strrpos( $xfieldsdata[$value[0]], ' ', $charset )) ) $xfieldsdata[$value[0]] = dle_substr( $xfieldsdata[$value[0]], 0, $temp_dmax, $charset );
					}
					$tpl->set( $matches[0], $xfieldsdata[$value[0]] );
				}
			}
		}
		
		
//Инициализация дополнительных полей
        $tpldate = date('d M y', $date);
        $tpl->set('{date}', $tpldate);
		$tpl->set('{title}', $title);
		$tpl->set('{link}',  $link);
    $tpl->set('{short-story}', $short);
		$tpl->set('{full-story}', $full);
		$tpl->set('{category}', $category);
		if ($foreign_title) {
			$tpl->set('{title-orig}', $foreign_title);
			$tpl->set( '[title-orig]', '' );
			$tpl->set( '[/title-orig]', '' );
		} else {
			$tpl->set('{title-orig}', '');
			$tpl->set_block( "'\\[title-orig\\](.*?)\\[/title-orig\\]'si", '' );
		}
		if ($recomendation == false) {
			$tpl->set( '[not-recomend]', '' );
			$tpl->set( '[/not-recomend]', '' );
			$tpl->set_block( "'\\[recomend\\](.*?)\\[/recomend\\]'si", '' );
		} elseif ($recomendation == true) {
			$tpl->set( '[recomend]', '' );
			$tpl->set( '[/recomend]', '' );
			$tpl->set_block( "'\\[not-recomend\\](.*?)\\[/not-recomend\\]'si", '' );
		}
		$tpl->compile("search_advanced");
	}

//Вывод
if (!$tpl->result['search_advanced'] && !$search_config['result_null'] == 1) {
	$tpl1 = new dle_template();
	$tpl1->dir = TEMPLATE_DIR;
	$tpl1->load_template( 'info.tpl' );
	$tpl1->set( '[not-found]', '' );
	$tpl1->set( '[/not-found]', '' );
	$tpl1->set_block( "'\\[you-mean\\](.*?)\\[/you-mean\\]'si", '' );
	$tpl1->set_block( "'\\[recomendation\\](.*?)\\[/recomendation\\]'si", '' );
	$tpl1->compile('info');
	$tpl1->clear();
} elseif ($youmean == true) {
	$tpl1 = new dle_template();
	$tpl1->dir = TEMPLATE_DIR;
	$tpl1->load_template( 'info.tpl' );
	$tpl1->set( '[you-mean]', '' );
	$tpl1->set( '[/you-mean]', '' );
	$tpl1->set_block( "'\\[not-found\\](.*?)\\[/not-found\\]'si", '' );
	$tpl1->set_block( "'\\[recomendation\\](.*?)\\[/recomendation\\]'si", '' );
	$tpl1->compile('info');
	$tpl1->clear();
} elseif ($recomendation == true) {
	$tpl1 = new dle_template();
	$tpl1->dir = TEMPLATE_DIR;
	$tpl1->load_template( 'info.tpl' );
	$tpl1->set_block( "'\\[you-mean\\](.*?)\\[/you-mean\\]'si", '' );
	$tpl1->set_block( "'\\[not-found\\](.*?)\\[/not-found\\]'si", '' );
	$tpl1->set( '[recomendation]', '' );
	$tpl1->set( '[/recomendation]', '' );
	$tpl1->compile('info');
	$tpl1->clear();
}

$tpl1->result['info'] = str_replace('{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl1->result['info']);
$tpl->result['search_advanced'] = str_replace('{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'], $tpl->result['search_advanced']);


echo $tpl_result = $tpl->result['search_advanced'];
echo $tpl_info = $tpl1->result['info'];
$dle_api->save_to_cache (md5($cache_query), $tpl_result.$tpl_info);

$tpl->clear();
$db->free();
}

?>