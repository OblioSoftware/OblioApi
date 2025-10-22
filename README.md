# OblioApi

 ![test workflow](https://github.com/OblioSoftware/OblioApi/actions/workflows/php.yml/badge.svg)
 [![Latest Version on Packagist](https://img.shields.io/packagist/v/obliosoftware/oblio-api.svg?style=flat-square)](https://packagist.org/packages/obliosoftware/oblio-api)
[![Total Downloads](https://img.shields.io/packagist/dt/obliosoftware/oblio-api.svg?style=flat-square)](https://packagist.org/packages/obliosoftware/oblio-api)

 Oblio.eu API implementation for PHP
 
 Install using composer
 ```
 composer require obliosoftware/oblio-api
 ```

## Create invoice

```php
$defaultData = array(
    'cif'                => '',
    'client'             => [
        'cif'           => '',
        'name'          => '',
        'rc'            => '',
        'code'          => '',
        'address'       => '',
        'state'         => '',
        'city'          => '',
        'country'       => '',
        'iban'          => '',
        'bank'          => '',
        'email'         => '',
        'phone'         => '',
        'contact'       => '',
        'vatPayer'      => '',
    ],
    // 'idempotencyKey'     => $orderId, // used to avoid double invoicing
    'issueDate'          => 'yyyy-mm-dd',
    'dueDate'            => '',
    'deliveryDate'       => '',
    'collectDate'        => '',
    'seriesName'         => '',
    'collect'            => [],
    'referenceDocument'  => [],
    'language'           => 'RO',
    'precision'          => 2,
    'currency'           => 'RON',
    'products'           => [
        [
            'name'          => 'Abonament',
            'code'          => '',
            'description'   => '',
            'price'         => '100',
            'measuringUnit' => 'buc',
            'currency'      => 'RON',
            'vatName'       => 'Normala',
            'vatPercentage' => 21,
            'vatIncluded'   => true,
            'quantity'      => 2,
            'productType'   => 'Serviciu',
        ]
    ],
    'issuerName'         => '',
    'issuerId'           => '',
    'noticeNumber'       => '',
    'internalNote'       => '',
    'deputyName'         => '',
    'deputyIdentityCard' => '',
    'deputyAuto'         => '',
    'selesAgent'         => '',
    'mentions'           => '',
    'value'              => 0,
    'workStation'        => 'Sediu',
    'useStock'           => 0,
);
try {
    $api = new OblioSoftware\Api($email, $secret);
    // create invoice:
    $result = $api->createInvoice($defaultData);
} catch (Exception $e) {
    // error handle
}
```

## Cancel invoice
```php
try {
    $issuerCif = ''; // your company CIF
    $api = new OblioSoftware\Api($email, $secret);
    // cancel/restore document:
    $api->setCif($issuerCif);
    $result = $api->cancel('invoice', $seriesName, $number, true/false);
} catch (Exception $e) {
    // error handle
}
```

## Nomenclature
```php
try {
    $issuerCif = ''; // your company CIF
    $type = 'products'; // companies, vat_rates, products, clients, series, languages, management
    $name = '';
    $filters = [
        'workStation'  => '',
        'management'   => '',
        'limitPerPage' => 250,
        'offset'       => 0,
    ];
    $api = new OblioSoftware\Api($email, $secret);
    $api->setCif($issuerCif);
    $result = $api->nomenclature($type, $name, $filters);
} catch (Exception $e) {
    // error handle
}
```

## Collect invoice
```php
try {
    $issuerCif = ''; // your company CIF
    $seriesName = '';
    $number = '';
    $collect = [
        'type'                => 'Ordin de plata',
        'documentNumber'      => 'OP 7001',
    ];
    $api = new OblioSoftware\Api($email, $secret);
    $api->setCif($issuerCif);
    $result = $api->collect($seriesName, $number, $collect);
} catch (Exception $e) {
    // error handle
}
```

## Create custom AccessTokenHandler example
```php
use OblioSoftware\AccessToken;
use OblioSoftware\AccessTokenHandlerInterface;

class CustomAccessTokenHandler implements AccessTokenHandlerInterface {
    private $cacheKey = 'oblio_access_token';
    
    public function get(): ?AccessToken
    {
        $data = Cache::get($this->cacheKey);
        if ($data !== null) {
            $accessToken = new AccessToken($data);
            if ($accessToken && $accessToken->request_time + $accessToken->expires_in > time()) {
                return $accessToken;
            }
        }
        return null;
    }
    
    public function set(AccessToken $accessToken): void
    {
        Cache::set($this->cacheKey, $accessToken->toArray());
    }
}
```

## Create webhook
```php
use OblioSoftware\Request\WebhookCreate;

try {
    $issuerCif = ''; // your company CIF
    $endpoint = ''; // a valid webhook endpoint
    $api = new OblioSoftware\Api($email, $secret);
    $response = $api->createRequest(
        new WebhookCreate([
            'cif'       => $issuerCif,
            'topic'     => 'stock',
            'endpoint'  => $endpoint,
        ])
    );
    $result = json_decode($response->getBody()->getContents(), true);
} catch (Exception $e) {
    // error handle
}
```
