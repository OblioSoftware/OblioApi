<?php

require 'vendor/autoload.php';

try {
    $issuerCif = ''; // your company CIF
    $api = new OblioSoftware\Api($email, $secret);
    // delete document:
    $api->setCif($issuerCif);
    $result = $api->delete('invoice', $seriesName, $number, [
        'deleteCollect'  => true,
        'idempotencyKey' => $orderId,
    ]);
} catch (Exception $e) {
    // error handle
}