<?php

namespace BegelsTest;

use Begels\Begels;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    /**
     * @throws \Begels\Exception\BegelsDeniedException
     * @throws \Begels\Exception\BegelsUnavailableException
     */
    public function testIndex()
    {
        $begels = new Begels();

        $begels->setBaseUri(getenv('BEGELS_BASE_URI'));
        $begels->setFactory(getenv('BEGELS_FACTORY'));
        $begels->setEmail(getenv('BEGELS_EMAIL'));
        $begels->setPassword(getenv('BEGELS_PASSWORD'));

        $this->assertTrue($begels->check());
    }
}