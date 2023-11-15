<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Depends;

final class WebhookTest extends TestCase
{
    private ?int $id;
    private string $topic = 'stock';

    public function testWebhookReadAll(): void
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookRead(null, [
                'cif'   => getenv('OBLIO_API_CIF'),
                'topic' => $this->topic
            ])
        );
        $result = json_decode($response->getBody()->getContents(), true);
        $this->id = $result['data'][0]['id'] ?? null;

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Depends('testWebhookReadAll')]
    public function testWebhookCreate(): void
    {
        if ($this->id !== null) {
            return;
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
        $this->id = $result['data']['id'] ?? null;

        $this->assertSame(201, $response->getStatusCode());
    }

    #[Depends('testWebhookCreate')]
    public function testWebhookReadOne(): void
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookRead($this->id)
        );

        $this->assertSame(200, $response->getStatusCode());
    }

    #[Depends('testWebhookReadOne')]
    public function testWebhookDelete(): void
    {
        $api = new OblioSoftware\Api(getenv('OBLIO_API_EMAIL'), getenv('OBLIO_API_SECRET'));
        $response = $api->createRequest(
            new OblioSoftware\Request\WebhookDelete($this->id)
        );

        $this->assertSame(200, $response->getStatusCode());
    }
}