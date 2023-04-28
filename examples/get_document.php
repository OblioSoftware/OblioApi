<?php

require 'vendor/autoload.php';

try {
    $issuerCif = ''; // your company CIF
    $api = new OblioSoftware\Api($email, $secret);
    // get document:
    $api->setCif($issuerCif);
    $result = $api->get('invoice', $seriesName, $number);
} catch (Exception $e) {
    // error handle
}