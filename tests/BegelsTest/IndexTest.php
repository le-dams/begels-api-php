<?php
/**
 * Created by IntelliJ IDEA.
 */

namespace BegelsTest;

use Begels\Authenticate;
use Begels\Model\Address;
use Begels\Model\Customer;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function testIndex()
    {
        Authenticate::init('demo', 'demo', 'demo', false);

        $address = new Address();
        $address->setCity('Humain');
        $address->setStreet('Rue de Thys');

        $customer = Customer::get(49173);

        print_r($customer);
        $this->assertTrue(true);
    }
}