<?php

use OblioSoftware\Request\WebhookCreate;
use OblioSoftware\Request\WebhookRead;
use OblioSoftware\Request\WebhookDelete;

require 'vendor/autoload.php';

try {
    $email = '';
    $secret = '';
    $issuerCif = '';
    $topic = 'stock';
    $endpoint = '';
    $webhookId = null;
    $api = new OblioSoftware\Api($email, $secret);

    // read all
    echo 'read all<br>';
    $result = $api->createRequest(
        new WebhookRead(null, ['cif' => $issuerCif, 'topic' => $topic])
    );
    $contents = json_decode($result->getBody()->getContents(), true);
    dump($contents);
    $webhookId = $contents['data'][0]['id'] ?? null;

    // create
    if ($webhookId === null) {
        echo 'create<br>';
        $result = $api->createRequest(
            new WebhookCreate([
                'cif'       => $issuerCif,
                'topic'     => 'stock',
                'endpoint'  => $endpoint,
            ])
        );
        $contents = json_decode($result->getBody()->getContents(), true);
        dump($contents);
        $webhookId = $contents['data']['id'] ?? null;
    }

    // read one
    echo 'read one<br>';
    $result = $api->createRequest(
        new WebhookRead($webhookId)
    );
    dump($result->getBody()->getContents());

    // delete
    echo 'delete<br>';
    $result = $api->createRequest(
        new WebhookDelete($webhookId)
    );
    dump($result->getBody()->getContents());
    
} catch (Exception $e) {
    // error handle
    dump($e->getMessage());
}