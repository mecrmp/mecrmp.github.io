<?php
$urlpage = isset($urlpage) && !is_null($urlpage) && is_scalar($urlpage) ? trim(strip_tags(stripslashes($urlpage))) : false;
if(!$urlpage) return;

$resurlleech = $config['http_home_url'] . "engine/go.php?url=" . rawurlencode( base64_encode( $urlpage ) );
echo $resurlleech;
?>