<?php

namespace Begels;

use Begels\Exception\BegelsDeniedException;
use Begels\Exception\BegelsUnavailableException;

class Authenticate
{
    /**
     * @var string
     */
    private static $apiKey = 'OjcWQ1eUEAJ7GTk4';

    /**
     * @var string
     */
    private static $customerApiKey = null;

    /**
     * @var bool
     */
    private static $live = true;

    /**
     * @param string $factory
     * @param string $email
     * @param string $password
     * @param bool $live
     * @return bool
     * @throws BegelsUnavailableException
     * @throws BegelsDeniedException
     */
    static public function init(string $factory, string $email, string $password, bool $live = true)
    {
        self::$live = $live;
        if (self::$customerApiKey) {
            return true;
        }

        $params = [
            'factory' => $factory,
            'email' => $email,
            'password' => $password
        ];

        $response = Request::post('/login', $params);

        if (!isset($response['auth'])) {

            $errorMessage = 'Auth failed with Begels.';
            throw new BegelsDeniedException($errorMessage);
        }

        self::$customerApiKey = $response['auth'];

        return true;
    }

    static public function getApiKey() : string
    {
        return self::$apiKey;
    }

    static public function getCustomerApiKey() :? string
    {
        return self::$customerApiKey;
    }

    /**
     * @return bool
     */
    public static function isLive() : bool
    {
        return self::$live;
    }
}
