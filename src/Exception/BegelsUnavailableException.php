<?php

namespace Begels\Exception;

use Throwable;

class BegelsUnavailableException extends \Exception
{
    public function __construct($message = "Begels is unavailable", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}