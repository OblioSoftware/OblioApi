<?php

namespace OblioSoftware\Request;

use OblioSoftware\ApiRequestInterface;

class WebhookCreate implements ApiRequestInterface {
    private array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getUri(): string
    {
        return 'api/webhooks';
    }

    public function getOptions(): array
    {
        return [
            'json' => [
                'cif'       => $this->params['cif'] ?? null,
                'topic'     => $this->params['topic'] ?? null,
                'endpoint'  => $this->params['endpoint'] ?? null,
            ]
        ];
    }
}