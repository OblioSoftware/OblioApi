# OblioApi
 Oblio.eu API implementation for PHP

# Examples

```
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
            'measuringUnit' => '',
            'currency'      => 'RON',
            'vatName'       => 'Normala',
            'vatPercentage' => 19,
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
