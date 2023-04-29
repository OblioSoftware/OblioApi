<?php

namespace OblioSoftware;

interface AccessTokenHandlerInterface {
    /**
     *  @return ?AccessToken $accessToken
     */
    public function get(): ?AccessToken;
    
    /**
     *  @param AccessToken $accessToken
     */
    public function set(AccessToken $accessToken): void;
}