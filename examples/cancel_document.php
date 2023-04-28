<?php

require 'vendor/autoload.php';

try {
    $issuerCif = ''; // your company CIF
    $api = new OblioSoftware\Api($email, $secret);
    // cancel/restore document:
    $api->setCif($issuerCif);
    $result = $api->cancel('invoice', $seriesName, $number, true/false);
} catch (Exception $e) {
    // error handle
}