<?php

namespace Begels;

use Begels\Exception\BegelsUnavailableException;

class Request
{
    /**
     * @param string $request
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public static function get(string $request) :? array
    {
        return self::call('GET', $request);
    }

    /**
     * @param string $request
     * @param array $params
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public static function post(string $request, array $params = []) :? array
    {
        return self::call('POST', $request, $params);
    }

    /**
     * @param string $request
     * @param array $params
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public static function put(string $request, array $params = []) :? array
    {
        return self::call('PUT', $request, $params);
    }


    /**
     * @param string $request
     * @param array $params
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public static function patch(string $request, array $params = []) :? array
    {
        return self::call('PATCH', $request, $params);
    }

    /**
     * @param $request
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public static function delete(string $request) :? array
    {
        return self::call('DELETE', $request);
    }

    /**
     * @return array|null
     * @throws BegelsUnavailableException
     */
    public static function check(): array
    {
        return self::call();
    }

    /**
     * @return string
     */
    private static function getEntryPoint(): string
    {
        if (Authenticate::isLive()) {
           return 'https://api.begels.com';
        }
        return'https://api.begels.ovh';
    }

    /**
     * @param string $method
     * @param string $request
     * @param array $params
     * @return mixed
     * @throws BegelsUnavailableException
     */
    private static function call(string $method = 'GET', string $request = '/', array $params = []) :? array
    {
        $ch = curl_init();

        $headers = [
            "Content-Type: application/json",
            "X-Requested-With: XMLHttpRequest",
            "x-api-key: ".Authenticate::getApiKey()
        ];

        if (Authenticate::getCustomerApiKey()) {
            $headers[] = "x-auth-begels: ".Authenticate::getCustomerApiKey();
        }

        curl_setopt($ch, CURLOPT_URL, self::getEntryPoint().$request);
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
            $content = json_decode($res, JSON_OBJECT_AS_ARRAY);

            if (isset($content['message'])) {
                throw new BegelsUnavailableException(print_r($res, true), $info['http_code']);
            }
            throw new BegelsUnavailableException(print_r($res, true), $info['http_code']);
        }

        return is_array($res) ? $res : json_decode($res, JSON_OBJECT_AS_ARRAY);
    }

}
