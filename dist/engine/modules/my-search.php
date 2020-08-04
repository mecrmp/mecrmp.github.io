<?php


/* Проверяем на существование константы '<i>DATALIFEENGINE</i>'. Эта константа определяется в index.php 
и ее значение TRUE символизирует о том, что файл подключен с помощью  include/require, а не просто запущен. */

if(!defined('DATALIFEENGINE'))
{
  	die("Hacking attempt!");
}

$tpl->load_template('search_advanced/form.tpl');

$tpl->compile('content');
$tpl->clear();
//-------------------------------------------------====
//    Вывод
//-------------------------------------------------====


?>