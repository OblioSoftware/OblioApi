<?php

namespace OblioSoftware;

use ValueError;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class Api {
    protected $_cif                 = '';
    protected $_email               = '';
    protected $_secret              = '';
    protected $_accessTokenHandler  = null;
    protected $_baseURL             = 'https://www.oblio.eu';

    /**
     *  API constructor
     *  @param string $email - account login email
     *  @param string $secret - find token in: account settings > API secret
     *  @param AccessTokenHandlerInterface $accessTokenHandler (optional)
     */
    public function __construct(string $email, string $secret, ?AccessTokenHandlerInterface $accessTokenHandler = null)
    {
        $this->_email  = $email;
        $this->_secret = $secret;
        
        if (!$accessTokenHandler) {
            $accessTokenHandler = new AccessTokenHandlerFileStorage();
        }
        $this->_accessTokenHandler = $accessTokenHandler;
    }

    /**
     *  @param array $data
     *  @return array $response
     */
    public function createInvoice(array $data): array
    {
        return $this->_createDoc('invoice', $data);
    }

    /**
     *  @param array $data
     *  @return array $response
     */
    public function createProforma(array $data): array
    {
        return $this->_createDoc('proforma', $data);
    }

    /**
     *  @param array $data
     *  @return array $response
     */
    public function createNotice(array $data): array
    {
        return $this->_createDoc('notice', $data);
    }

    /**
     *  $_cif needs to be set
     *  @param string $type - invoice/notice/proforma/receipt
     *  @param string $seriesName
     *  @param int $number
     *  @return array $response
     */
    public function get($type, $seriesName, $number): array
    {
        $this->_checkType($type);
        $cif = $this->_getCif();
        $request = $this->buildRequest();
        $response = $request->get("/api/docs/{$type}", [
            'query' => compact('cif', 'seriesName', 'number')
        ]);
        $this->_checkErrorResponse($response);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     *  $_cif needs to be set
     *  @param string $type - invoice/notice/proforma/receipt
     *  @param string $seriesName
     *  @param int $number
     *  @param bool $cancel - Cancel(true)/Restore(false)
     *  @return array $response
     */
    public function cancel($type, $seriesName, $number, $cancel = true): array
    {
        $this->_checkType($type);
        $cif = $this->_getCif();
        $request = $this->buildRequest();
        $url = '/api/docs/' . $type . '/' . ($cancel ? 'cancel' : 'restore');
        $response = $request->put($url , ['json' => compact('cif', 'seriesName', 'number')]);
        $this->_checkErrorResponse($response);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     *  $_cif needs to be set
     *  @param string $type - invoice/notice/proforma/receipt
     *  @param string $seriesName
     *  @param int $number
     *  @param array $options
     *  @return array $response
     */
    public function delete($type, $seriesName, $number, $options = []): array
    {
        $this->_checkType($type);
        $cif = $this->_getCif();
        $deleteCollect  = $options['deleteCollect'] ?? null;
        $idempotencyKey = $options['idempotencyKey'] ?? null;
        $request = $this->buildRequest();
        $response = $request->delete("/api/docs/{$type}", [
            'json' => compact('cif', 'seriesName', 'number', 'deleteCollect', 'idempotencyKey')
        ]);
        $this->_checkErrorResponse($response);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     *  $_cif needs to be set
     *  @param string $seriesName
     *  @param int $number
     *  @param array $collect
     *  @return array $response
     */
    public function collect($seriesName, $number, $collect): array
    {
        $cif = $this->_getCif();
        $request = $this->buildRequest();
        $response = $request->put('/api/docs/invoice/collect', [
            'json' => compact('cif', 'seriesName', 'number', 'collect')
        ]);
        $this->_checkErrorResponse($response);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     *  $_cif needs to be set
     *  @param string $type : companies, vat_rates, products, clients, series, languages, management
     *  @param string $name : filter by name
     *  @param array $filters : custom filter
     *  @return array $response
     */
    public function nomenclature($type = null, $name = '', array $filters = []): array
    {
        $cif = '';
        switch ($type) {
            case 'companies':
                break;
            case 'vat_rates':
            case 'products':
            case 'clients':
            case 'series':
            case 'languages':
            case 'management':
                $cif = $this->_getCif();
                break;
            default:
                throw new Exception('Type not implemented');
        }
        $request = $this->buildRequest();
        $response = $request->get("/api/nomenclature/{$type}", [
            'query' => compact('cif', 'name') + $filters
        ]);
        $this->_checkErrorResponse($response);
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $cif : company cif
     */
    public function setCif($cif): void
    {
        $this->_cif = $cif;
    }

    protected function _createDoc($type, $data): array
    {
        $this->_checkType($type);
        if (empty($data['cif']) && $this->_cif) {
            $data['cif'] = $this->_cif;
        }
        if (empty($data['cif'])) {
            throw new ValueError('Empty cif');
        }
        $request = $this->buildRequest();
        $response = $request->post("/api/docs/{$type}", ['json' => $data]);
        $this->_checkErrorResponse($response);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function buildRequest(): Client
    {
        $accessToken = $this->getAccessToken();
        return new Client([
            'base_uri' => $this->_baseURL,
            'headers'  => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' =>  $accessToken->token_type . ' ' . $accessToken->access_token,
            ],
        ]);
    }

    public function createRequest(ApiRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $client = $this->buildRequest();
        $response = $client->request($request->getMethod(), $request->getUri(), $request->getOptions());
        $this->_checkErrorResponse($response);
        return $response;
    }

    public function getAccessToken(): AccessToken
    {
        $accessToken = $this->_accessTokenHandler->get();
        if (!$accessToken) {
            $accessToken = $this->_generateAccessToken();
            $this->_accessTokenHandler->set($accessToken);
        }
        return $accessToken;
    }

    protected function _generateAccessToken(): AccessToken
    {
        if (!$this->_email || !$this->_secret) {
            throw new Exception('Email or secret are empty!');
        }
        $request = new Client([
            'base_uri' => $this->_baseURL,
            'headers'  => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
        ]);
        $response = $request->post('/api/authorize/token', [
            'json' => [
                'client_id'     => $this->_email,
                'client_secret' => $this->_secret,
                'grant_type'    => 'client_credentials',
            ]
        ]);
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            throw new Exception(sprintf('Error authorize token! HTTP status: %d', $response->getStatusCode()), $response->getStatusCode());
        }
        return new AccessToken(json_decode($response->getBody()->getContents(), true));
    }

    protected function _checkType($type): void
    {
        if (!in_array($type, ['invoice', 'proforma', 'notice', 'receipt'])) {
            throw new ValueError('Type not supported');
        }
    }
    
    protected function _getCif(): string
    {
        if (!$this->_cif) {
            throw new ValueError('Empty cif');
        }
        return $this->_cif;
    }

    protected function _checkErrorResponse(Response $response): void
    {
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            $message = json_decode($response->getBody()->getContents(), true);

            if (empty($message)) {
                $message = [
                    'statusMessage' => sprintf('Error! HTTP response status: %d', $response->getStatusCode())
                ];
            }
            throw new Exception($message['statusMessage'], $response->getStatusCode());
        }
    }
}
