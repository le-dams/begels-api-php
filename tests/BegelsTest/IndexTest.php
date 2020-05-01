<?php

namespace BegelsTest;

use Begels\Begels;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        $appKey = getenv('BEGELS_APP_KEY');
        $secretKey = getenv('BEGELS_SECRET_KEY');
        $baseUrl = getenv('BEGELS_BASE_URI');
        $begels = new Begels($appKey, $secretKey, true, $baseUrl);

        $this->assertTrue($begels->check());
    }
}