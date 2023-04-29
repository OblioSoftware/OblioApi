<?php

namespace OblioSoftware;

class AccessTokenHandlerFileStorage implements AccessTokenHandlerInterface {
    protected $_accessTokenFileHeader   = '<?php die;?>';
    protected $_accessTokenFilePath     = '';

    public function __construct($accessTokenFilePath = null)
    {
        $this->_accessTokenFilePath = $accessTokenFilePath === null
            ? dirname(__DIR__) . '/storage/access_token.php'
            : $accessTokenFilePath;
    }

    public function get(): ?AccessToken
    {
        if (file_exists($this->_accessTokenFilePath)) {
            $accessTokenFileContent = str_replace($this->_accessTokenFileHeader, '', file_get_contents($this->_accessTokenFilePath));
            $accessToken = new AccessToken(json_decode($accessTokenFileContent, true));
            if ($accessToken && $accessToken->request_time + $accessToken->expires_in > time()) {
                return $accessToken;
            }
        }
        return null;
    }

    public function set(AccessToken $accessToken): void
    {
        file_put_contents(
            $this->_accessTokenFilePath,
            $this->_accessTokenFileHeader . json_encode($accessToken->toArray())
        );
    }
}