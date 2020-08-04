<div class="box_in">
		<div class="text">
		<div class="download"> 
<a href="{torrent_magnet}"><img src="{THEME}/images/magnet.gif" alt="M" style="cursor: pointer;" ></a>
<a href="#!" id ="down" data-down_id="{torrent_id}"><img src="{THEME}/images/down.png" alt="D" />{title}</a>
<br>
<a href="/kak-tut-kachat.html" class="d_small" target="_blank">Как тут качать?</a>
<a href="/index.php?do=search&subaction=search&story={title}" class="d_small" >Добавить в поисковую строку</a>
</div><br />

			<div id="fullstory">
		  {full-story}
            [catlist=6,9,14,15,16]
			<br>
<!--noindex-->
<fieldset style="border: 1px solid #3C95D1;"><legend style="font-weight: bold;"><h2 class="title">Смотреть онлайн {title limit="100"}...</h2></legend>
<div id="parent">
<div id="child" class="child-no-ads">
<div id="main-block-title">Плеер</div>
<div style="height: 300px;">
<div id="kinoplayertop" data-title="{title}" data-kinoheight="100%" data-kinowidth="100%"></div> 
<script src="//kinoplayer.top/top.js"></script>
</div>
</div>
<div id="child" class="child-ads"><div id="main-block-title">Дополнительный</div>
<div style="height: 300px;">
[banner_onlayn]
{banner_onlayn}
[/banner_onlayn]
</div>
</div>
</div>
</fieldset>
<!--/noindex-->
[/catlist]
</div>
			<br>
			<!--noindex-->
[banner_tizer-banner]
{banner_tizer-banner}
[/banner_tizer-banner]
			<!--/noindex-->
			<br>
		 <fieldset style="border: 1px solid #3C95D1;"><legend style="font-weight: bold;">Похожие торрент раздачи</legend>
	<table width="100%">
<tr class="backgr">
<td width="10px">Добавлен
</td>
<td colspan="2">Название</td>
<td width="1px">Размер</td>
<td width="1px">Пиры</td>
</tr>
        {related-news}
		</table>
		
	<div style="width: 100%; text-align: right; margin-top: 4px; margin-bottom: -5px;"><a href="/index.php?do=search" target="_blank">Искать ещё похожие раздачи</a>
	</div>
	</fieldset>
			[edit-date]<p class="editdate grey">Новость отредактировал: <b>{editor}</b> - {edit-date}<br>
			[edit-reason]Причина: {edit-reason}[/edit-reason]</p>[/edit-date]
		</div>
		</div>
		{pages}
		<div class="story_tools ignore-select">
			<div class="category">
				<svg class="icon icon-cat"><use xlink:href="#icon-cat"></use></svg>
				{link-category}
				</div>
			
		</div>
	


	
<div class="comments ignore-select">
	<div class="box">
		[comments]<h4 class="heading">Комментарии <span class="grey hnum">{comments-num}</span></h4>[/comments]
		<div class="com_list">
			{comments}
		</div>
	</div>
	{navigation}
	<div class="box">
		{addcomments}
	</div>
</div>