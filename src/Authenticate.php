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
     * @param string $factory
     * @param string $email
     * @param string $password
     * @return string
     * @throws BegelsUnavailableException
     * @throws BegelsDeniedException
     */
    static public function init(string $factory, string $email, string $password): string
    {
        $params = [
            'factory' => $factory,
            'email' => $email,
            'password' => $password
        ];

        $response = Begels::post('/login', $params, false);

        if (!isset($response['auth'])) {

            $errorMessage = 'Auth failed with Begels.';
            throw new BegelsDeniedException($errorMessage);
        }

        return $response['auth'];
    }

    /**
     * @return string
     */
    static public function getApiKey() : string
    {
        return self::$apiKey;
    }
}
