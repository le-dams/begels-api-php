<?php
/**
 * Created by IntelliJ IDEA.
 */

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
        $begels = new Begels(getenv('BEGELS_BASE_URI'), getenv('BEGELS_FACTORY'), getenv('BEGELS_EMAIL'), getenv('BEGELS_PASSWORD'));
        $this->assertTrue($begels->check());
    }
}