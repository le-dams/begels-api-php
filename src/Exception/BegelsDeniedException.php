<?php

namespace Begels\Exception;

use Throwable;

class BegelsDeniedException extends \Exception
{
    public function __construct($message = "Begels return denied access", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}