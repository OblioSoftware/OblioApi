<?php

namespace OblioSoftware\AccessToken;

use OblioSoftware\AccessToken;

interface HandlerInterface {
    /**
     *  @return ?AccessToken $accessToken
     */
    public function get(): ?AccessToken;
    
    /**
     *  @param AccessToken $accessToken
     */
    public function set(AccessToken $accessToken): void;
}