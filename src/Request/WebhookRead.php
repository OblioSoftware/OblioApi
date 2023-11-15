<?php

namespace OblioSoftware\Request;

use OblioSoftware\ApiRequestInterface;

class WebhookRead implements ApiRequestInterface {
    private ?int $id;
    private array $params;

    public function __construct(?int $id = null, array $params = [])
    {
        $this->id = $id;
        $this->params = $params;
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUri(): string
    {
        $uri = 'api/webhooks';
        if ($this->id !== null) {
            $uri .= '/' . $this->id;
        }
        if (!empty($this->params)) {
            $uri .= '?' . http_build_query($this->params);
        }
        return $uri;
    }

    public function getOptions(): array
    {
        return [];
    }
}