<div class="dropdown">
[not-group=5]
<div class="login_mal profiles">
<div class="top_border_proff"></div>
<div class="you_name">{login}</div>
<div class="you_group">{group}</div>
<div class="ava_position"><img src="{foto}" alt="{login}" class="ava"><div class="online"></div></div>
<div class="border_miniprof"></div>
<div class="menu_prof">
<div><a href="{profile-link}">Персональная страница</a></div>
<div><a href="{pm-link}">Личные сообщения&nbsp;&nbsp;+{new-pm}</a></div>
<div><a href="{favorites-link}">Мои закладки</a></div>
</div>
<div class="border_miniprof"></div>
<a href="{logout-link}" class="logout">выход</a>
<div class="top_border"></div>
</div>
<div class="overlay"></div>
[/not-group]
[group=5]
<div class="login_mal">
<form method="post" action="">
<div class="top_border"></div>
<div class="rocket"></div>
Логин<input type="text" class="login" name="login_name" id="login_name" placeholder="Ваш логин">
Пароль<input type="password" class="password" name="login_password" id="login_password" placeholder="Ваш пароль">
<ul class="login_button">

<li class="log_in"><input type="submit" value="Войти"></li>
</ul>
<div class="clears"></div>
<div class="footer_login">
<div class="bottom_border"></div>
<ul>
<li class="for_pass"><a href="{lostpassword-link}">Забыли пароль?</a></li>
<li class="register">Нет аккаунта? <a href="{registration-link}">Регистрация</a></li>
</ul>
</div>
<input name="login" type="hidden" id="login" value="submit" />
</form>
</div>
<div class="overlay"></div>
[/group]
<a href="javascript://" id="open_modal">[group=5]<img src="{THEME}/images/zaiti.gif" alt="logout" />[/group][not-group=5]<img src="{THEME}/images/viti.gif" alt="logout" />[/not-group]</a>
<script>
$( "#open_modal" ).click(function() {
$('.login_mal').addClass('md-content');
$('.overlay').css({'display':'block'});
});
$( ".overlay" ).click(function() {
$('.login_mal').removeClass('md-content');
$(this).css({'display':'none'});
});
</script>
		
</div>		
		
		
		
		
		
		
		
		
		
		
		
		