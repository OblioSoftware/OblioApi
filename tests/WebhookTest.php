<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;

final class WebhookTest extends TestCase
{
    private string $topic = 'stock';

    public function testWebhookReadAll(): int
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookRead(null, [
                'cif'   => getenv('OBLIO_API_CIF'),
                'topic' => $this->topic
            ])
        );
        $result = json_decode($response->getBody()->getContents(), true);
        $id = intval($result['data'][0]['id'] ?? 0);

        $this->assertSame(200, $response->getStatusCode());

        return $id;
    }

    #[Depends('testWebhookReadAll')]
    public function testWebhookCreate(int $id): int
    {
        if ($id !== 0) {
            return $id;
        }

        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookCreate([
                'cif'       => getenv('OBLIO_API_CIF'),
                'topic'     => $this->topic,
                'endpoint'  => getenv('WEBHOOK_TEST_ENDPOINT'),
            ])
        );

        $result = json_decode($response->getBody()->getContents(), true);
        $id = intval($result['data']['id'] ?? 0);

        $this->assertSame(201, $response->getStatusCode());

        return $id;
    }

    #[Depends('testWebhookCreate')]
    public function testWebhookReadOne(int $id): int
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookRead($id)
        );

        $this->assertSame(200, $response->getStatusCode());

        return $id;
    }

    #[Depends('testWebhookReadOne')]
    public function testWebhookDelete(int $id): int
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookDelete($id)
        );

        $this->assertSame(200, $response->getStatusCode());

        return $id;
    }
}