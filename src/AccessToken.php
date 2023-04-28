<?php

namespace OblioSoftware;

class AccessToken {
    public int $request_time;
    public int $expires_in;
    public string $token_type;
    public string $access_token;

    public function __construct(array $data = [])
    {
        $this->request_time = $data['request_time'] ?? 0;
        $this->expires_in   = $data['expires_in'] ?? 0;
        $this->token_type   = $data['token_type'] ?? '';
        $this->access_token = $data['access_token'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'request_time' => $this->request_time,
            'expires_in'   => $this->expires_in,
            'token_type'   => $this->token_type,
            'access_token' => $this->access_token,
        ];
    }
}