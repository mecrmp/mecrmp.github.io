<?php

    include( __DIR__ . "/Zapret_Email.php" );

    include_once ENGINE_DIR . '/modules/zapret/Zapret.php';

    $zapret_config = include_once ENGINE_DIR . '/modules/zapret/config.php';

    $ZapretEmail = new ZapretEmail();

    $result = $ZapretEmail->send( $subject, $message, $config['charset'], $email, $zapret_config[ 'my_partner_email' ], '' );