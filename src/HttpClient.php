<?php

namespace SergPopov\CloudMailRu;

use GuzzleHttp\{Client, Cookie\CookieJar, Exception\GuzzleException};
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClient
 * @package SergPopov\CloudMailRu
 */
class HttpClient
{
    public Client $client;

    private CookieJar $cookie;

    public function __construct()
    {
        $this->client = new Client();
        $this->cookie = new CookieJar();
    }

    /**
     * @param string $url
     * @param array $postParams
     * @param array $headers
     * @return ResponseInterface
     * @throws CloudMailRuException
     */
    public function requestPost(string $url, array $postParams, array $headers = []): ResponseInterface
    {
        try {
            return $this->client->request('POST', $url, [
                'form_params' => $postParams,
                'cookies' => $this->cookie,
                'verify' => false,
                'headers' => $headers,
            ]);
        } catch (GuzzleException $e) {
            throw new CloudMailRuException($e->getMessage());
        }
    }

    /**
     * @param string $url
     * @param mixed $body
     * @param array $headers
     * @return ResponseInterface
     * @throws CloudMailRuException
     */
    public function requestPut(string $url, $body, array $headers = []): ResponseInterface
    {
        try {
            return $this->client->request('PUT', $url, [
                'body' => $body,
                'cookies' => $this->cookie,
                'verify' => false,
                'headers' => $headers,
            ]);
        } catch (GuzzleException $e) {
            throw new CloudMailRuException($e->getMessage());
        }
    }

    /**
     * @param string $url
     * @param array $headers
     * @return ResponseInterface
     * @throws CloudMailRuException
     */
    public function requestGet(string $url, array $headers = []): ResponseInterface
    {
        try {
            return $this->client->request('GET', $url, [
                'cookies' => $this->cookie,
                'verify' => false,
                'headers' => $headers,
            ]);
        } catch (GuzzleException $e) {
            throw new CloudMailRuException($e->getMessage());
        }
    }

    /**
     * @param ResponseInterface $response
     * @return string
     * @throws CloudMailRuException
     */
    public function checkResponse(ResponseInterface $response): string
    {
        if ($response->getStatusCode() == 200) {
            return $response->getBody()->getContents();
        } else {
            throw new CloudMailRuException('Bad response ' . $response->getStatusCode());
        }
    }
}