<?php
/**
 * Created by IntelliJ IDEA.
 */

namespace BegelsTest;

use Begels\Authenticate;
use Begels\Model\Address;
use Begels\Model\Article;
use Begels\Model\Customer;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        Authenticate::init('demo', 'demo', 'demo', false);

        $this->assertTrue(count(Article::gets())>0);
    }
}