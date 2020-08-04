<?php

    if( !defined( 'DATALIFEENGINE' ) OR !defined( 'LOGGED_IN' ) )
        die("Hacking attempt!");

    /*
     * Создание и проверка Log-папки
     */
    $log_dir = ROOT_DIR . '/baza';

    if( !file_exists( $log_dir ) )
        mkdir( $log_dir );

    /*
     * Проверка и создание раздела в ПУ
     */
    $admin_section = $db->super_query( "SELECT * FROM " . PREFIX . "_admin_sections WHERE name = 'zapret'" );

    if( empty( $admin_section ) )
    {
        $values = array(
            '\'zapret\'',
            '\'Zapret\'',
            '\'Zapret\'',
            1,
        );

        $db->super_query( "INSERT INTO " . PREFIX . "_admin_sections ( `name`, title, descr, allow_groups ) VALUES (" . implode( ', ', $values ) . ")" );
    }

    if( $member_id[ 'user_group' ] != 1 )
        msg("error", 'Ошибка доступа', 'У Вас недостаточно прав, чтобы всспользоваться модулем "Запрет"');

    $zapret_config_file = ENGINE_DIR . '/modules/zapret/config.php';
    $zapret_config = include_once $zapret_config_file;

    if( !empty( $_GET[ 'check_api_key' ] ) )
        $_POST[ 'check_api_key' ] = $_GET[ 'check_api_key' ];

    if( !empty( $_GET[ 'mode' ] ) )
        $_POST[ 'mode' ] = $_GET[ 'mode' ];

    if( isset( $_POST[ 'check_api_key' ] ) )
        include_once __DIR__ . '/check_api_key.php';

    if( isset( $_POST[ 'mode' ] ) )
        include_once __DIR__ . '/protected_content.php';

    /*
     * Сохранение настроек
     */
    if ( $_POST['action'] == "save" )
    {
        foreach( $zapret_config as $param => $value )
        {
            if( isset( $_POST[ $param ] ) )
                $zapret_config[ $param ] = $_POST[ $param ];
        }

        $zapret_config[ 'code' ] = intval( $zapret_config[ 'code' ] );

        $error = array();

        switch ( $zapret_config[ 'action_type' ] )
        {
            case 1:
                if( empty( $zapret_config[ 'code' ] ) )
                    $error[] = 'Код ответа сервера не может быть пустым';

                if( empty( $zapret_config[ 'html' ] ) )
                    $error[] = 'Необходимо указать HTML-код заглушки';
                break;

            case 2:
                if( empty( $zapret_config[ 'url' ] ) )
                    $error[] = 'Укажите адрес для 301-редиректа';
                break;

            case 3:
                break;
        }

        /*
         * Если ошибок нет
         */
        if( empty( $error ) )
        {
            clear_cache ();

            $content = var_export( $zapret_config, true );

            $content = <<<PHP
<?php

return {$content};
PHP;

            if( is_writable( $zapret_config_file ) )
            {
                file_put_contents( $zapret_config_file, $content );


                msg("info", 'Сохранено', 'Настройки успешно сохранены', "?mod=" . basename( __FILE__, '.php' ) );
            }
            else
                $error[] = 'Нет прав на перезапись файла ' . $zapret_config_file . '<br/>Выставите права 777 для файла /engine/modules/zapret/config.php';
        }

        msg("error", 'Ошибка при сохранении', implode( '<br/>', $error ), "?mod=" . basename( __FILE__, '.php' ) );

    }

    /*
     * Выбор активного типа модуля
     */
    for( $i = 1; $i <= 3; $i++ )
        $selected[ $i ] = ( intval( $zapret_config[ 'action_type' ] ) == $i ) ? ' selected' : '';

    /*
     * Выбор активного типа модуля
     */
    for( $i = 1; $i <= 3; $i++ )
        $selected_email[ $i ] = ( intval( $zapret_config[ 'email' ] ) == $i ) ? ' selected' : '';

    /*
     * Формирование списка лог-файлов
     */
    $log_dir = ROOT_DIR . '/baza';
    $log_list = '';
    foreach( glob( $log_dir . '/*.{txt}', GLOB_BRACE ) as $image )
    {
        $url = $config[ 'http_home_url' ] . 'baza/' . basename( $image );

        $log_list .= '<li><a href="' . $url . '" target="_blank">' . $url . '</a></li>';
    }

    if( empty( $log_list ) )
        $log_list = 'Лог-файлы не найдены';


    /*
     * Вывод
     */
    echoheader("<i class=\"icon-zapret\"></i>Модуль \"Запрет\"", '');

    $old_styles = '';

    if( floatval( $config[ 'version_id' ] ) < 11 )
        $old_styles = <<<HTML
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<script src="https://code.jquery.com/jquery-3.2.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<style>
.box-header
{
    padding: 10px;
    
    background-color: #fff;
    
    margin-top: 25px;
    
    border-radius: 25px;
}

.box-header .title
{
    font-size: 14px;
    font-weight: 600;
}
.box-content
{
    padding: 10px;
    
    background-color: #fff;
    
    margin-bottom: 15px;
}
</style>
HTML;

    echo <<<HTML
{$old_styles}
<style>
.icon-zapret
{
    width: 24px;
    height: 24px;
    
    background-image: url('data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTguMS4xLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDE2IDE2IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCAxNiAxNjsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHdpZHRoPSIxNnB4IiBoZWlnaHQ9IjE2cHgiPgo8Zz4KCTxwYXRoIGQ9Ik04LDBDMy41ODksMCwwLDMuNTg5LDAsOHMzLjU4OSw4LDgsOHM4LTMuNTg5LDgtOFMxMi40MTEsMCw4LDB6IE0zLDggICBjMC0wLjgzMiwwLjIyNC0xLjYwNCwwLjU4NC0yLjI5NWw2LjcxMSw2LjcxMUM5LjYwNCwxMi43NzYsOC44MzIsMTMsOCwxM0M1LjI0MywxMywzLDEwLjc1NywzLDh6IE0xMi40MTYsMTAuMjk1TDUuNzA1LDMuNTg0ICAgQzYuMzk2LDMuMjI0LDcuMTY4LDMsOCwzYzIuNzU3LDAsNSwyLjI0Myw1LDVDMTMsOC44MzIsMTIuNzc2LDkuNjA0LDEyLjQxNiwxMC4yOTV6IiBmaWxsPSIjODg4ODg4Ii8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==');
    background-size: 100%;
    background-repeat: no-repeat;
    background-position: center left;
}

.log-files .col-lg-2
{
    text-align: right
}

.log-files ul
{
    list-style: decimal;
}

.log-files ul li a
{
    text-decoration: underline;
}

ul.nav
{
    list-style: none;
}

.tab-pane
{
    padding: 15px 0;
}

.check-api
{
    display: none;
    
    padding: 15px;
    
    max-width: 300px;
}

.check-api.error,
.copy-info
{
    display: block;
    
    background-color: #eab1b1;
}

.copy-info
{
    display: none;
    
    padding: 15px;
    
    margin-top: 5px;
}

.check-api.ok
{
    display: block;
    
    background-color: #bceab1;
}

.protected_content .row.headers
{
    padding: 5px 0;
    
    font-size: 15px;
    font-weight: bold;
}


.protected_content .row
{
    padding: 5px 0;
    
    font-size: 14px;
}

.protected_content .api
{
    margin-top: 10px;
}

.protected_content .api a
{
    text-decoration: underline;
}
</style>
<script type="text/javascript">
    $( document ).on({
        'change': function()
        {
            $( '.action-param' ).css( 'display', 'none' );
            $( '.action-' + $( this ).val() ).css( 'display', 'block' );
        }
    }, '[name=action_type]' );
    
    $( document ).on({
        'change': function()
        {
            if( $( this ).val() == "1" )
                $( '.copy-info' ).slideDown();
            else
                $( '.copy-info' ).slideUp();
        }
    }, '[name=email]' );
    
    $( document ).ready( function(){
        $( '[name=action_type]' ).change();
        $( '[name=email]' ).change();
        
        $( '.protected_content ul.nav li:first-child' ).click();
    });
    
    function checkApiKey()
    {
        $.ajax({
            'url': window.location.href,
            
            'data': {
                'check_api_key': $( '[name=api_key]' ).val()
            },
            'method': 'post',
            
            'dataType': 'json',
            
            success: function( r ){
                var text;
                
                $( '.check-api' ).removeClass( 'ok' ).addClass( 'error' );
                
                if( r.status == 'empty' )
                    text = 'Введите Apikey'
                else if( r.status == 'ok' )
                {
                    $( '.check-api' ).removeClass( 'error' ).addClass( 'ok' );
                    
                    text = 'Apikey сохранен';
                }
                else if( r.status == 'server' )
                    text = 'Сервер проверки недоступен. Попробуйте позже';
                else
                    text = r.status;
                
                $( '.check-api' ).text( text );
            }
        });
        
        return false;
    }
    
    $( document ).on( 'click', '.protected_content ul.nav li', function(){
        var obj = $( $( this ).find( 'a' ).attr( 'href' ) );
        
        $.ajax({
            'url': window.location.href,
            
            'data': {
                'mode': $( this ).find( 'a' ).attr( 'href' ).replace( '#', '' )
            },
            'method': 'post',
            
            success: function( r ){
                obj.html( r );
            }
        });
        
        return false;
    });
</script>
<form action="" method="post" class="form-horizontal">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="user_hash" value="{$dle_login_hash}">
    <div class="box">
        <div class="box-header">
            <div class="title">Настройки</div>
        </div>
        <div class="box-content">
            <div class="row box-section">
                <div class="form-group">
                    <label class="control-label col-lg-2">Apikey</label>
                  
                    <div class="col-lg-10">
                        <input type="text" name="api_key" value="{$zapret_config['api_key']}" style="width:100%;max-width:450px;"/> <a href="#" onclick="return checkApiKey();" class="btn btn-green">сохранить</a>
                        <div class="check-api"></div>
                        <div class="note">Без указания Apikey база обновляется с задержкой в 2 недели. Если вы хотите максимально актуальную базу <a href="http://zapret-rf.org/buy_apikey">купите Apikey</a>.<br/>
    <b>Внимание!</b> Если вы не приобретали Apikey, то не заполняйте данное поле. После нескольких неверных попыток ввода ключа ip может быть забанен и отключаться даже бесплатные обновления.</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-lg-2">Режим работы модуля</label>
                  
                    <div class="col-lg-10">
                        <select name="action_type" style="width:100%;max-width:450px;">
                            <option value="1"{$selected[1]}>
                                HTML-заглушка и код ответа                            
                            </option>
                               
                            <option value="2"{$selected[2]}>
                                301-редирект                            
                            </option>
                               
                            <option value="3"{$selected[3]}>
                                Не показывать блок для плохого IP                       
                            </option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group action-param action-1">
                    <label class="control-label col-lg-2">HTML-код заглушки</label>
                  
                    <div class="col-lg-10">
                        <textarea name="html" style="width:100%;max-width:450px;height:150px;">{$zapret_config['html']}</textarea>
                    </div>
                </div>
                
                <div class="form-group action-param action-1">
                    <label class="control-label col-lg-2">Код ответа</label>
                  
                    <div class="col-lg-10">
                        <input type="number" name="code" value="{$zapret_config['code']}" min="100" max="599" style="width:100%;max-width:450px;"/>
                    </div>
                </div>
                
                <div class="form-group action-param action-2">
                    <label class="control-label col-lg-2">Адрес для 301-редиректа</label>
                  
                    <div class="col-lg-10">
                        <input type="text" name="url" value="{$zapret_config['url']}" style="width:100%;max-width:450px;"/>
                        <div class="note">Ссылка должна быть полной, даже, если ведет на внутреннюю страницу сайта, например: <b>{$config['http_home_url']}/index.php?do=register</b></div>
                    </div>
                </div>
                
                <div class="form-group action-param action-3">
                    <label class="control-label col-lg-2"></label>
                  
                    <div class="col-lg-10">
                        <div>
                            Если вы не хотите отображать какой-то блок для плохих пользователей оберните его в тег [hide-for-bad-user].
                        </div>
                        <div>
                            Пример: [hide-for-bad-user] &lt;div&gt;контент который нужно скрыть&lt;/div&gt; [/hide-for-bad-user]
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-lg-2"></label>
                    
                    <div class="col-lg-10">
                        <input type="submit" class="btn btn-red" value="Сохранить изменения">
                    </div>
                </div>		
            </div>
        </div>
    </div>

    <div class="box log-files">
        <div class="box-header">
            <div class="title">Помощь в сборе базы: отправлять копии писем от правообладателей на наш сервис</div>
        </div>
        
        <div class="box-content">
            <div class="box-section row">
                <div class="note col-lg-12">
                    Если вы активно участвуете в сборе базы, обратитесь за бесплатным ключем в техподдержку. Данные о статистике отправленных писем доступна по ссылке: <a href="http://zapret-rf.org/partner/help" target="_blank">http://zapret-rf.org/partner/help</a> (вкладка Сбор Ip 
                    правообладателей)
                </div>
            </div>
        </div>
        <div class="box-content">
            <div class="row box-section">
                <div class="form-group">
                    <label class="control-label col-lg-2">
                        Отправлять копии писем правообладателей на zapret-rf.org
                    </label>
                  
                    <div class="col-lg-10">
                        <select name="email" style="width:100%;max-width:450px;">
                            <option value="1"{$selected_email[1]}>
                                Да                            
                            </option>
                               
                            <option value="2"{$selected_email[2]}>
                                Нет                           
                            </option>
                        </select>
                        
                        <div class="copy-info">
                        <b>Важно!</b><br/>
                        В файлах /engine/modules/feedback.php и /engine/ajax/feedback.php найти строку<br/>
                        <b>msgbox( &dollar;lang['feed_ok_1']</b><br/>
                        вставить перед ней:<br/>
                        <b>include ENGINE_DIR . '/modules/zapret/email.php';</b><br/>
                        </div>
                    </div>
                </div>
               
                <div class="form-group">
                    <label class="control-label col-lg-2">
                        Email партнера
                    </label>
                  
                    <div class="col-lg-10">
                        <input type="text" name="my_partner_email" value="{$zapret_config['my_partner_email']}" style="width:100%;max-width:450px;"/>
                        <div class="note">Email партнера - тот, что Вы использовали для входа в http://zapret-rf.org/partner/help</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="control-label col-lg-2"></label>
                    
                    <div class="col-lg-10">
                        <input type="submit" class="btn btn-red" value="Сохранить изменения">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="box log-files">
    <div class="box-header">
        <div class="title">Лог-файлы</div>
    </div>
    
    <div class="box-content">
        <div class="row box-section">
            <div class="col-lg-2" style="text-align: right">Список лог-файлов</div>
                
            <ul class="col-lg-10">
                {$log_list}      
            </ul>
        </div>
    </div>
</div>

<div class="box protected-content">
    <div class="box-header">
        <div class="title">Блокируемый контент в РФ</div>
    </div>
    
    <div class="box-content">
        <div class="row box-section">                
            <div class="col-lg-12 protected_content">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#all_time">
                            За всё время
                        </a>
                    </li>
                    
                    <li>
                        <a data-toggle="tab" href="#2mon">
                            За 2 месяца
                        </a>
                    </li>
                    
                    <li>
                        <a data-toggle="tab" href="#mon">
                            За месяц
                        </a>
                    </li>
                    
                    <li>
                        <a data-toggle="tab" href="#week">
                            За неделю
                        </a>
                    </li>
                    
                    <li>
                        <a data-toggle="tab" href="#flow">
                            Последний блокируемый
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div id="all_time" class="tab-pane fade in active">
                        Загрузка..
                    </div>
                    
                    <div id="2mon" class="tab-pane fade">
                        Загрузка..
                    </div>
                    
                    <div id="mon" class="tab-pane fade">
                        Загрузка..
                    </div>
                    
                    <div id="week" class="tab-pane fade">
                        Загрузка..
                    </div>
                    
                    <div id="flow" class="tab-pane fade">
                        Загрузка..
                    </div>
                </div>   
            </div>
        </div>
    </div>
</div>

<div class="box contacts">
    <div class="box-header">
        <div class="title">Контакты и новости</div>
    </div>
    
    <div class="box-content">
        <div class="row box-section">                
            <div class="col-lg-12">
                <iframe width="100%" height="300px" src="http://zapcdn.space/partner/contact" frameborder="0" scrolling="yes"></iframe>
            </div>
        </div>
    </div>
</div>
HTML;


    echofooter();
?>