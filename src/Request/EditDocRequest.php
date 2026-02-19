<?php

namespace OblioSoftware\Request;

use OblioSoftware\ApiRequestInterface;

class EditDocRequest implements ApiRequestInterface {
    private string $type;
    private array $data;

    public function __construct(string $type, array $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }

    public function getMethod(): string
    {
        return 'PUT';
    }

    public function getUri(): string
    {
        return  'api/docs/' . $this->type;
    }

    public function getOptions(): array
    {
        return ['json' => $this->data];
    }
}