<?php
if( ! defined( 'DATALIFEENGINE' ) ) {
die( "Hacking attempt!" );
}
@ini_set ('memory_limit',"128M");
@set_time_limit (0);
@ini_set ('max_execution_time',0);
define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include_once ENGINE_DIR.'/data/config.php';
require_once ENGINE_DIR . '/inc/plugins/ping/ping.func.php';

global $config,$config_rss,$db;

$ping_url = array_map("trim", file(ENGINE_DIR."/inc/plugins/ping/pingsite.txt"));  
if(count($ping_url)==""){
clear_cache();
exit();
}
if (@file_exists(ROOT_DIR . '/grabber.xml')){
$url = $config['http_home_url'] . "grabber.xml";
$pgg = weblog_ping($ping_url, $config['home_title'], $url);
}

if (count($pgg) != '0') echo '   <b><font color="orange">'.$lang_grabber['ping_msg'].' '.count($pgg).' '.$lang_grabber['ping_msg_all'].' '.count($ping_url).'</font></b>';

if (count($pgg) != '0') {
$month[1] = "�����";
$month[2] = "������";
$month[3] = "����";
$month[4] = "�����";
$month[5] = "��";
$month[6] = "���";
$month[7] = "���";
$month[8] = "������";
$month[9] = "�������";
$month[10] = "������";
$month[11] = "�����";
$month[12] = "������";
$dnum = date("w");
$mnum = date("n");
$daym = date("d");
$year = date("Y");
$textday = $day[$dnum];
$monthm = $month[$mnum];
if ($mnum==3||$mnum==8){$k="�";}else{$k="�";}
$time = date('H:i:s');
$entry_line = "$daym $monthm$k $year ����, � $time  | <a href=\"index.php?newsid=$news_id\" target=\"_blank\" >". stripslashes( stripslashes( $title ) ) ."</a>\n";
$fp = fopen(ENGINE_DIR."/cache/system/pinglogs.txt", "a");
fputs($fp, $entry_line);
fclose($fp);
}
?>