<?php

namespace Begels;

use Begels\Exception\BegelsUnavailableException;

class Begels
{
    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var string|null
     */
    private $factory;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @var string|null
     */
    private $token;

    /**
     * @var string|null
     */
    private $cacheFile;

    /**
     * @param string $baseUri
     */
    public function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param string|null $factory
     */
    public function setFactory(?string $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @param string|null $cacheFile
     */
    public function setCacheFile(?string $cacheFile): void
    {
        $this->cacheFile = $cacheFile;
    }

    /**
     * @return string|null
     */
    private function getToken(): ?string
    {
        if (file_exists($this->cacheFile) && is_file($this->cacheFile)) {
            $token = file_get_contents($this->cacheFile);
            if ($token) {
                return $token;
            }
        }
        return $this->token;
    }

    /**
     * @param string|null $token
     * @return bool
     */
    private function setToken(?string $token): bool
    {
        if (file_exists($this->cacheFile) && is_file($this->cacheFile)) {
            file_put_contents($this->cacheFile, $token);
            return true;
        }
        $this->token = $token;
        return true;
    }

    /**
     * @param string $request
     * @return array|null
     * @throws BegelsUnavailableException
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
        } else if (!isset($me['factory']['reference'])) {
            return false;
        } else if ($me['factory']['reference'] !== $this->factory) {
            return false;
        }
        return true;
    }

    /**
     * @param string $request
     * @param array $params
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public function post(string $request, array $params = []) :? array
    {
        return $this->call('POST', $request, $params);
    }

    /**
     * @param string $request
     * @param array $params
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public function put(string $request, array $params = []) :? array
    {
        return $this->call('PUT', $request, $params);
    }


    /**
     * @param string $request
     * @param array $params
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public function patch(string $request, array $params = []) :? array
    {
        return $this->call('PATCH', $request, $params);
    }

    /**
     * @param $request
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public function delete(string $request) :? array
    {
        return $this->call('DELETE', $request);
    }

    /**
     * @param string $method
     * @param string $request
     * @param array $params
     * @param bool $tokenNeeded
     * @return array|null
     * @throws BegelsUnavailableException
     */
    private function call(string $method = 'GET', string $request = '/', array $params = [], bool $tokenNeeded = true) :? array
    {
        $ch = curl_init();

        if ($this->getToken() === null && $tokenNeeded === true) {
            $responseToken = $this->call('POST','/login', [
                'factory' => $this->factory,
                'email' => $this->email,
                'password' => $this->password
            ], false);
            if (isset($responseToken['auth'])) {
                $this->setToken($responseToken['auth']);
            }
        }

        $headers = [
            "Content-Type: application/json",
            "X-Requested-With: XMLHttpRequest",
            "x-api-key: ".Authenticate::getApiKey()
        ];

        if ($this->getToken()) {
            $headers[] = "Authentication: Bearer ".$this->getToken();
        }

        curl_setopt($ch, CURLOPT_URL, $this->baseUri.$request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
                break;
        }
        $res = curl_exec ($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if (in_array($info['http_code'], [401, 403, 404, 500])) {
            if (in_array($info['http_code'], [401])) {
                $this->setToken(null);
            }
            $content = json_decode($res, JSON_OBJECT_AS_ARRAY);

            if (isset($content['message'])) {
                throw new BegelsUnavailableException(print_r($res, true), $info['http_code']);
            }
            throw new BegelsUnavailableException(print_r($res, true), $info['http_code']);
        }

        return is_array($res) ? $res : json_decode($res, JSON_OBJECT_AS_ARRAY);
    }

}
