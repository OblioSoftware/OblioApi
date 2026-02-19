<?php

require 'vendor/autoload.php';

try {
    $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
    $api->setCif(getenv('OBLIO_API_CIF'));
    $result = $api->list('invoice', [
        // 'id'            => '',
        'issuedAfter'   => date('Y-m-d', time() - 3600 * 24 * 7),
        'issuedBefore'  => date('Y-m-d'),
        'limitPerPage'  => '100',
        'offset'        => '0',
        'orderBy'       => 'id',
        'orderDir'      => 'desc',
    ]);
} catch (Exception $e) {
    // error handle
}