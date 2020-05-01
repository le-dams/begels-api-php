<?php

namespace Begels;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Begels
{
    /**
     * @var string
     */
    private $baseUri = 'https://api.begels.com';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $secretKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * Begels constructor.
     * @param string $apiKey
     * @param string $secretKey
     * @param bool $debug
     * @param string|null $baseUri
     */
    public function __construct(string $apiKey, string $secretKey, bool $debug = false, ?string $baseUri = null)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->debug = $debug;
        if ($baseUri) {
            $this->baseUri = $baseUri;
        }
    }

    /**
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        if ($this->logger === null || $this->debug === null) {
            return new NullLogger();
        }
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        if (!$this->client instanceof Client) {
            $headers = [
                "Content-Type' => 'application/json",
                'x-api-key' => 'x9uZ5rWmCv2fJtLt6tFC',
                'Authorization' => 'Basic '.base64_encode($this->apiKey.':'.$this->secretKey),
            ];
            $this->client = new Client([
                'base_uri' => $this->baseUri,
                'headers' => $headers,
            ]);
        }

        return $this->client;
    }

    /**
     * @param string $request
     * @return array|null
     */
    public function get(string $request) :? array
    {
        return $this->call('GET', $request);
    }

    public function check(): bool
    {
        $me = $this->get('/me');
        if (!is_array($me)) {
            return false;
        } else if (!isset($me['apps'])) {
            return false;
        }
        return true;
    }

    /**
     * @param string $request
     * @param array $params
     * @return array|null
     */
    public function post(string $request, array $params = []) :? array
    {
        return $this->call('POST', $request, $params);
    }

    /**
     * @param string $request
     * @param array $params
     * @return array|null
     */
    public function put(string $request, array $params = []) :? array
    {
        return $this->call('PUT', $request, $params);
    }


    /**
     * @param string $request
     * @param array $params
     * @return array|null
     */
    public function patch(string $request, array $params = []) :? array
    {
        return $this->call('PATCH', $request, $params);
    }

    /**
     * @param string $request
     * @return array|null
     */
    public function delete(string $request) :? array
    {
        return $this->call('DELETE', $request);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return array|null
     */
    private function call(string $method = 'GET', string $uri = '/', array $params = []) :? array
    {
        try {
            switch (strtolower($method)) {
                case 'get':
                    $response = $this->getClient()->get($uri);
                    break;
                default:
                    $response = $this->getClient()->request($method, $uri, [
                        'json' => $params
                    ]);
                    break;
            }
            return \json_decode($response->getBody()->getContents(), true);
        } catch (ServerException $e) {
            $this->getLogger()->error($e);
            throw $e;
        } catch (ClientException $e) {
            $this->getLogger()->warning($e);
            throw $e;
        }
    }
}
