<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;

final class ApiTest extends TestCase
{
    public function testCreateInvoice(): array
    {
        $data = array(
            'cif'                => getenv('OBLIO_API_CIF'),
            'client'             => [
                'cif'           => '19',
                'name'          => 'BUCUR OBOR SA',
                'rc'            => 'J40/365/1991',
                'code'          => '',
                'address'       => 'Bdul. Pache Protopopescu 109',
                'state'         => 'Bucuresti',
                'city'          => 'Sectorul 2',
                'country'       => 'RO',
                'iban'          => '',
                'bank'          => '',
                'email'         => '',
                'phone'         => '',
                'contact'       => '',
                'vatPayer'      => '',
            ],
            'issueDate'          => date('Y-m-d'),
            'dueDate'            => '',
            'deliveryDate'       => '',
            'collectDate'        => '',
            'seriesName'         => getenv('OBLIO_SERIES_NAME'),
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
        
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $result = $api->createInvoice($data);

        $this->assertSame(200, $result['status']);

        return $result['data'];
    }

    #[Depends('testCreateInvoice')]
    public function testGetInvoice(array $data): array
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $api->setCif(getenv('OBLIO_API_CIF'));
        $result = $api->get('invoice', $data['seriesName'], $data['number']);

        $this->assertSame(200, $result['status']);

        return $data;
    }

    #[Depends('testGetInvoice')]
    public function testCancelInvoice(array $data): array
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $api->setCif(getenv('OBLIO_API_CIF'));
        $result = $api->cancel('invoice', $data['seriesName'], $data['number'], true);

        $this->assertSame(200, $result['status']);

        return $data;
    }

    #[Depends('testCancelInvoice')]
    public function testRestoreInvoice(array $data): array
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $api->setCif(getenv('OBLIO_API_CIF'));
        $result = $api->cancel('invoice', $data['seriesName'], $data['number'], false);

        $this->assertSame(200, $result['status']);

        return $data;
    }

    #[Depends('testRestoreInvoice')]
    public function testDeleteInvoice(array $data): void
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $api->setCif(getenv('OBLIO_API_CIF'));
        $result = $api->delete('invoice', $data['seriesName'], $data['number']);

        $this->assertSame(200, $result['status']);
    }

    public function testListInvoice(): void
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $api->setCif(getenv('OBLIO_API_CIF'));
        $result = $api->list('invoice', [
            'issuedAfter'   => date('Y-m-d', time() - 3600 * 24 * 7),
            'issuedBefore'  => date('Y-m-d'),
            'orderBy'       => 'id',
            'orderDir'      => 'desc',
        ]);
        print_r($result);

        $this->assertSame(200, $result['status']);
    }
}
