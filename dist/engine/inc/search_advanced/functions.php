<?php
/*
=====================================================
 Файл: /inc/functions.php
-----------------------------------------------------
 Назначение: Административные функции
=====================================================
*/

@error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
@ini_set("display_errors", true);
@ini_set("html_errors", false);
@ini_set("error_reporting", E_ALL ^ E_WARNING ^ E_NOTICE);
set_time_limit(0);
define("DATALIFEENGINE", true);
define('ROOT_DIR', substr(dirname( __FILE__ ), 0, -27));
define('ENGINE_DIR', ROOT_DIR . '/engine');

include (ENGINE_DIR . '/data/config.php');
include (ENGINE_DIR . '/data/search_config.php');

if($config["http_home_url"] == '') {
	 $config["http_home_url"] = explode('engine/inc/search_advanced/functions.php', $_SERVER["PHP_SELF"]);
	 $config["http_home_url"] = reset($config["http_home_url"]);
	 $config["http_home_url"] = 'http://' . $_SERVER["HTTP_HOST"] . $config["http_home_url"];
}

include ENGINE_DIR . '/classes/mysql.php';
include ENGINE_DIR . '/data/dbconfig.php';
include ENGINE_DIR . '/inc/include/init.php';

if($_REQUEST["key"] != $dle_login_hash && $member_id['user_group'] != 1) {
	die("Hacking attempt!");
}

if ($_POST["foreign_name"] == "add" && $search_config['text_language'] == 1) {
	if ($search_config['text_language'] == 1 && empty($search_config['field_kpid'])) die('Укажите дополнительное поле ID кинопоиска!');

	$db->query('SHOW COLUMNS FROM `' . PREFIX . '_post`'); 
	while($row = $db->get_row()) {
		if ($row['Field'] == 'srchadv_foreign') $db->query ("ALTER TABLE `" . PREFIX . "_post` DROP `srchadv_foreign`");
	}
	$db->query ("ALTER TABLE `" . PREFIX . "_post` ADD `srchadv_foreign` VARCHAR(255) NOT NULL");

	$db_count = count($db->super_query ("SELECT id FROM " . PREFIX . "_post WHERE approve = 1", true));

	if ($db_count > 0) {
		$i=0;
		while (++$i < $db_count) {
		  $xf_row =	$db->super_query ("SELECT id, xfields FROM " . PREFIX . "_post WHERE id=" . $i . "");
			if ($xf_row != NULL) {
				$xfieldsdata = xfieldsdataload($xf_row['xfields']);
				$kpid = $xfieldsdata[$search_config['field_kpid']];
				if ($kpid == NULL || empty($kpid)) continue;
				if (!is_numeric($kpid)) die ('Указаное доп. поле ID кинопоиска не является цифровым!');
				$kp_api = file_get_contents('http://api.kinopoisk.cf/getFilm?filmID='.$kpid);
				if ($kp_api != NULL && $kpid != 0) {
					$kp_api = json_decode($kp_api, true);
					if ($kp_api['nameEN'] != '') {
						if ($search_config['type_foreign_name']) $kp_api['year'] = preg_replace('/[^0-9\s]/u', '', $kp_api['year']);
						if ($search_config['type_foreign_name'] 		== 'with_year') 			$type_foreign_name = " (" . $kp_api['year'] = substr($kp_api['year'], -4) . ")";
						elseif ($search_config['type_foreign_name'] == 'with_slash') 			$type_foreign_name = " / " . $kp_api['year'] = substr($kp_api['year'], -4);
						elseif ($search_config['type_foreign_name'] == 'with_double_dot') $type_foreign_name = ": " . $kp_api['year'] = substr($kp_api['year'], -4);
						elseif ($search_config['type_foreign_name'] == 'with_hyphen') 		$type_foreign_name = " - " . $kp_api['year'] = substr($kp_api['year'], -4);
						elseif ($search_config['type_foreign_name'] == 'with_year_clear') $type_foreign_name = $kp_api['year'] = substr($kp_api['year'], -4);

						$db->query( "UPDATE " . PREFIX . "_post SET srchadv_foreign='" . addslashes($kp_api['nameEN']) . $type_foreign_name . "' WHERE id = '" . $xf_row['id'] . "'" );
					} else {$title_en = '';}
				}
			}
			usleep($search_config['interval_sleep']);
		}
	} else echo 'Error!';

} elseif ($_POST["foreign_name"] == "add" && $search_config['text_language'] < 1) {
		echo 'Включите возможность поиска по оригинальным названиям!';
} elseif ($_GET["settings"] == 'save') {

	$save_con = array();$save_con = $_POST['save_con'];
	$find = array();$replace = array();
	$find[] = "'\r'";$replace[] = "";$find[] = "'\n'";$replace[] = "";
	
	$handler = fopen( ENGINE_DIR . '/data/search_config.php', "w+" );

	fwrite  ($handler, "<?php\n\n//SearchAdvanced v1.1 for DLE\n\$search_config = array (\n\n" );
	foreach ($save_con as $name => $value) fwrite($handler, "'{$name}' => '".addslashes($value)."',\n");
	fwrite  ($handler, "\n);\n\n?>");
	fclose  ($handler);
	
	$config 	  = ENGINE_DIR . '/data/search_config.php';
	$bak_config = ENGINE_DIR . '/inc/search_advanced/search_config.php.bak';
	copy($config, $bak_config);
	
} else {
	die("Hacking attempt!");
}

?>