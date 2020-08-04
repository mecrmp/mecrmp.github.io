<!DOCTYPE html>
<html lang="ru">
<head>
{headers}
  <link rel="shortcut icon" href="{THEME}/images/favicon.ico" /> 
  <link href="{THEME}/css/style.css" type="text/css" rel="stylesheet" />
  <link href="{THEME}/css/engine.css" type="text/css" rel="stylesheet" />
  <link href="{THEME}/css/parent.css" type="text/css" rel="stylesheet" />
  <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
{AJAX}
<div id="all">
<div id="up">
<div id="logo">
	<a href="/"><img src="{THEME}/images/logo.jpg" alt="rutor.info logo" /></a>
</div>
<table id="news_table">
  <tr><td colspan="2"><strong>Новости трекера</strong></td></tr>
  <tr><td class="news_date">30-Дек</td><td class="news_title"><a href="/u-rutororg-novyy-adres-rutorinfo.html" target="_blank" style="color: orange; font-weight: bold;">У RUTOR.ORG - Новый Адрес: {home_url}</a></td></tr>
  <tr><td class="news_date">29-Ноя</td><td class="news_title"><a href="/vechnaya-blokirovka-v-rossii.html" target="_blank">Вечная блокировка в России</a></td></tr>
  <tr><td class="news_date">09-Окт</td><td class="news_title"><a href="/putevoditel-po-rutororg-pravila-rukovodstva-sekrety.html" target="_blank">Путеводитель по RUTOR.org: Правила, Руководства, Секреты</a></td></tr>
  </table>
</div>
<div id="menu">
<a href="/" class="menu_b" style="margin-left:10px;"><div>Главная</div></a>
<a href="/top.html" class="menu_b"><div>Топ</div></a>
<a href="/categories.html" class="menu_b"><div>Категории</div></a>
<a href="/lastnews" class="menu_b"><div>Всё</div></a>
<a href="/?do=newssearch" class="menu_b"><div>Поиск</div></a>
<a href="/?do=lastcomments" class="menu_b"><div>Комменты</div></a>
<a href="/addnews.html" class="menu_b"><div>Залить</div></a>
<a href="/jabber.html" class="menu_b"><div>Чат</div></a>
<div id="menu_right_side"></div>
{login}
</div>
</div>
<div id="ws">
<div id="content">
<!--noindex-->
[banner_header_tizer]
{banner_header_tizer}
[/banner_header_tizer]			
<div class="clear"></div>
<!--/noindex-->	
<div id="index">
[aviable=main]
<h2><a href="/lastnews">Показать новые раздачи, разделенные по категориям!</a></h2><h2>Торренты за последние 24 часа</h2>
<table width="100%" style="border-spacing: 0px;">
<tr class="backgr">
<td width="10px"><img src="{THEME}/images/ic24.gif" />
</td>
<td colspan="2">Название</td>
<td width="1px">Размер</td>
<td width="1px">Пиры</td>
</tr>
</table>
{info}
{custom category="1-16" order="date" limit="150" template="shortstory" cache="yes"}
[/aviable]
[aviable=lastnews|tags|cat]
<table width="100%" style="border-spacing: 0px;">
<tr class="backgr">
<td width="10px"><img src="{THEME}/images/ic24.gif" />
</td>
<td colspan="2">Название</td>
<td width="1px">Размер</td>
<td width="1px">Пиры</td>
</tr>
</table>
{info}
{content}
[/aviable]
</div>	
[not-available=main|lastnews|tags|cat]
{info}
{content}
[/not-available]
	<script type="text/javascript" src="{THEME}/js/encode.js"></script>
	<script type="text/javascript" src="{THEME}/js/lib.js"></script>
	<script type="text/javascript">
		jQuery(function($){
			$.get("{THEME}/images/sprite.svg", function(data) {
			  var div = document.createElement("div");
			  div.innerHTML = new XMLSerializer().serializeToString(data.documentElement);
			  document.body.insertBefore(div, document.body.childNodes[0]);
			});
		});
	</script>
<center><a href="#up"><img src="{THEME}/images/top.gif" alt="up" /></a></center>
[banner_schetchik]
{banner_schetchik}
[/banner_schetchik]

</div>
<div id="sidebar">
<div class="sideblock">
	<a id="fforum" href="/putevoditel-po-rutororg-pravila-rukovodstva-sekrety.html"><img src="{THEME}/images/forum.gif" alt="forum" /></a>
</div>
<div class="sideblock">
<center>
<table border="0" background="{THEME}/images/poisk_bg.gif" cellspacing="0" cellpadding="0" width="100%" height="56px">								
 <tr>
  <td scope="col" rowspan=2><img src="{THEME}/images/lupa.gif" border="0" alt="img" /></td>
  <td valign="middle"><input name="story" type="text" class="form-text" id="story" value="Простой поиск по сайту" onblur="if(this.value=='') this.value='Простой поиск по сайту';" onfocus="if(this.value=='Простой поиск по сайту') this.value='';" title="наберите ваш запрос и нажмите enter" />
</td>
 </tr>
 </table>
</center>
</div>
<div class="sideblock2">
<center>
<!--noindex-->
[banner_rightside]
{banner_rightside}
[/banner_rightside]
<!--/noindex-->
</center>
<div class="tag_list">{tags}</div>	
</div>
</div>
</div>
</body>
</html>