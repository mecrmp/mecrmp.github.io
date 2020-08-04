<?php

    if( in_array( $_POST[ 'mode' ], array( 'all_time', '2mon', 'mon', 'week', 'flow' ) ) )
    {
        $api = @file_get_contents( 'http://zapcdn.space/service2/getProtectedContent/' . $_POST[ 'mode' ] . '?dom=' . $_SERVER[ 'HTTP_HOST' ] . '&enc=' . $config[ 'charset' ] . '&key=' . $zapret_config[ 'api_key' ] );

        $protected_content = '';

        if( !empty( $api ) )
        {
            $api = json_decode( $api );

            $protected_content .= <<<HTML
<div class="row headers">
    <div class="col-xs-3">
        Название
    </div>
    
    <div class="col-xs-3">
        Тип
    </div>
    
    <div class="col-xs-3">
        Год
    </div>
    
    <div class="col-xs-3">
        Количество блокировок
    </div>
</div>
HTML;

            foreach( $api->data as $item )
            {
                $item->type = $item->type == 'film' ? 'Фильм' : 'Сериал';

                if( $config[ 'charset' ] != 'utf-8' )
                {
                    header( 'Content-Type: text/html; charset=' . $config[ 'charset' ] );

                    $item->name = mb_convert_encoding( $item->name, $config[ 'charset' ], 'utf-8' );
                }

                $protected_content .= <<<HTML
<div class="row">
    <div class="col-xs-3">
        {$item->name}
    </div>
    
    <div class="col-xs-3">
        {$item->type}
    </div>
    
    <div class="col-xs-3">
        {$item->year}
    </div>
    
    <div class="col-xs-3">
        {$item->num}
    </div>
</div>
HTML;
            }

            if( !empty( $api->errors ) )
                $protected_content .= <<<HTML
<div class="api">
    Для просмотра большего количества, пожалуйста, <a href="http://zapret-rf.org/buy_apikey">приобретите apikey</a>
</div>
HTML;


            die( $protected_content );
        }
    }