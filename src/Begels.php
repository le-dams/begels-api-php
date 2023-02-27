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
    private string $baseUri = 'https://api.begels.com';

    /**
     * @var string|null
     */
    private ?string $clientName = null;

    /**
     * @var string|null
     */
    private ?string $secretKey = null;

    /**
     * @var Client|null
     */
    private ?Client $client = null;

    /**
     * @var LoggerInterface|null
     */
    private ?LoggerInterface $logger = null;

    /**
     * @var bool
     */
    private bool $debug = false;

    /**
     * Begels constructor.
     * @param string $clientName
     * @param string $secretKey
     * @param bool $debug
     * @param string|null $baseUri
     */
    public function __construct(string $clientName, string $secretKey, bool $debug = false, ?string $baseUri = null)
    {
        $this->clientName = $clientName;
        $this->secretKey = $secretKey;
        $this->debug = $debug;
        if ($baseUri) {
            $this->baseUri = $baseUri;
        }
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger(): LoggerInterface
    {
        if ($this->logger instanceof LoggerInterface) {
            return $this->logger;
        }
        return new NullLogger();
    }

    /**
     * @param LoggerInterface|null $logger
     */
    public function setLogger(?LoggerInterface $logger = null): void
    {
        $this->logger = $logger;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return Client
     */
    private function getClient(): Client
    {
        if (!$this->client instanceof Client) {
            $headers = [
                'Content-Type' => 'application/json',
                'x-api-key' => 'x9uZ5rWmCv2fJtLt6tFC',
                'x-api-client' => $this->clientName,
                'Authorization' => 'Bearer '.$this->secretKey,
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

    public function ping(): bool
    {
        try {
            $this->get('/me');
            return true;
        } catch (ClientException $clientException) {
            $this->getLogger()->error($clientException);
            return false;
        } catch (\Exception $exception) {
            $this->getLogger()->emergency($exception);
            throw $exception;
        }
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
            $this->getLogger()->alert($e);
            throw $e;
        } catch (ClientException $e) {
            $this->getLogger()->error($e);
            throw $e;
        }
    }
}
