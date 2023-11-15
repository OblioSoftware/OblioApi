<?php

namespace OblioSoftware;

interface ApiRequestInterface {
    /**
     * @return string GET/POST/PUT/PATCH/DELETE
     */
    public function getMethod(): string;

    public function getUri(): string;

    public function getOptions(): array;
}