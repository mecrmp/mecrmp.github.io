<?php

    $log_dir = ROOT_DIR . '/baza';

    if( !file_exists( $log_dir ) )
        mkdir( $log_dir );

    include_once ENGINE_DIR . '/modules/zapret/Zapret.php';

    $zapret_config = include_once ENGINE_DIR . '/modules/zapret/config.php';

    $block_name = 'hide-for-bad-user';

    function getNormalUrl( $url )
    {
        $r = array(
            'http://',
            'https://',
        );

        $url = str_replace( $r, '', $url );

        if( mb_substr( $url, -1, 1 ) == '/' )
            $url = mb_substr( $url, 0, mb_strlen( $url ) - 1 );

        return $url;
    }

    function getUserIp()
    {
        $_SERVER['REMOTE_ADDR'] = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

        if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1")
            return $_SERVER['REMOTE_ADDR'];

        if (isset ($_SERVER['HTTP_X_FORWARD_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARD_FOR'];
        else
            $ip = $_SERVER['REMOTE_ADDR'];

        if ($ip == "127.0.0.1")
        {
            if (isset ($_SERVER["HTTP_X_REAL_IP"]))
                $ip = $_SERVER["HTTP_X_REAL_IP"];
        }

        return $ip;
    }

    if ( !function_exists( 'http_response_code' ) )
    {
        function http_response_code( $new_code = NULL )
        {
            static $code = 200;

            if( $new_code !== NULL )
            {
                header( 'X-PHP-Response-Code: '. $new_code, true, $new_code );
                if( !headers_sent() )
                    $code = $new_code;
            }

            return $code;
        }
    }

    $ip = getUserIp();

    if ( isset( $_GET[ "client_ip" ] ) )
        $ip = $_GET[ "client_ip" ];

    $class = new Zapret("");

    if( $class->isBadIp( $ip, $zapret_config[ 'api_key' ] ) )
    {
        $tpl->set_block( "'\\[" . $block_name . "\\](.*?)\\[/" . $block_name . "\\]'si", "" );

        switch ( $zapret_config[ 'action_type' ] )
        {
            case 1:
                http_response_code( intval( $zapret_config[ 'code' ] ) );

                $tpl->copy_template = $zapret_config[ 'html' ];
                break;

            case 2:
                $current_url = getNormalUrl( $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ] );
                $config_url = getNormalUrl( $zapret_config[ 'url' ] );

                if( $config_url == $current_url )
                    break;

                http_response_code( 301 );

                header( 'Location: ' . $zapret_config[ 'url' ] );
                break;

            case 3:
                break;
        }
    }
    else
    {
        $tpl->set( '[' . $block_name . ']', '' );
        $tpl->set( '[/' . $block_name . ']', '' );
    }