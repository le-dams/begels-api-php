<?php
/**
 * Created by IntelliJ IDEA.
 * User: damien
 * Date: 14.06.18
 * Time: 06:38
 */

namespace Begels\Model;

use Begels\Request;
use Begels\Serialize;

class Customer
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $mobile;

    /**
     * @var string
     */
    private $dob;

    /**
     * @var Address
     */
    private $address;

    /**
     * @var string
     */
    private $vatNumber;

    /**
     * @var string
     */
    private $bankIban;

    /**
     * @var string
     */
    private $bankBic;

    /**
     * @return int
     */
    public function getId():? int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id = null): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getMobile():? string
    {
        return $this->mobile;
    }

    /**
     * @param string $mobile
     */
    public function setMobile(string $mobile = null): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string
     */
    public function getDob()
    {
        return $this->dob;
    }

    /**
     * @return string
     */
    public function getVatNumber():? string
    {
        return $this->vatNumber;
    }

    /**
     * @param string $vatNumber
     */
    public function setVatNumber(string $vatNumber = null): void
    {
        $this->vatNumber = $vatNumber;
    }

    /**
     * @return string
     */
    public function getBankIban():? string
    {
        return $this->bankIban;
    }

    /**
     * @param string $bankIban
     */
    public function setBankIban(string $bankIban = null): void
    {
        $this->bankIban = $bankIban;
    }

    /**
     * @return string
     */
    public function getBankBic():? string
    {
        return $this->bankBic;
    }

    /**
     * @param string $bankBic
     */
    public function setBankBic(string $bankBic = null): void
    {
        $this->bankBic = $bankBic;
    }

    /**
     * @param $dob
     * @return null
     */
    public function setDob($dob)
    {
        $dateTime = null;
        if ($dob instanceof \DateTime) {
            $dateTime = $dob;
        } else if (is_array($dob) && isset($dob['timestamp'])) {
            $dateTime = new \DateTime(date('Y-m-d H:i:s', $dob['timestamp']));
        } else if (is_string($dob)) {
            $dateTime = new \DateTime(date('Y-m-d H:i:s', strtotime($dob)));
        } else if (is_numeric($dob)) {
            $dateTime = new \DateTime(date('Y-m-d H:i:s', $dob));
        }

        if (!$dateTime) {
            $this->dob = null;
            return null;
        }

        $this->dob = $dateTime;
        return true;
    }

    /**
     * @return Address
     */
    public function getAddress() :? Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address) : void
    {
        $this->address = $address;
    }

    /**
     * @param int $id
     * @return Customer|null
     * @throws \Begels\Exception\BegelsUnavailableException
     */
    static public function get(int $id) :? Customer
    {
        $data =  Request::get(sprintf('/customer/%s',$id));

        $customer = isset($data['customer']) ? $data['customer'] : null;

        if (!$customer) {
            return null;
        }

        return self::hydrate($customer);
    }

    /**
     * @param Customer $customer
     * @return Customer|null
     * @throws \Begels\Exception\BegelsUnavailableException
     * @throws \ReflectionException
     */
    static public function create(Customer &$customer) :? Customer
    {
        if ($customer->getId()) {
            return null;
        }

        $serializedData = Serialize::serialize($customer);

        if (isset($serializedData['dob']['timestamp'])) {
            $timestamp = $serializedData['dob']['timestamp'];
            $serializedData['dob'] = date('Y-m-d H:i:s', $timestamp);
        }

        $begelsResponse = Request::post('/customer', $serializedData);

        if (!isset($begelsResponse['customer'])) {
            return null;
        }

        $customer = self::hydrate($begelsResponse['customer']);

        return $customer;
    }

    /**
     * @param Customer $customer
     * @return Customer|null
     * @throws \Begels\Exception\BegelsUnavailableException
     * @throws \ReflectionException
     */
    static public function update(Customer &$customer) :? Customer
    {
        if (!$customer->getId()) {
            return null;
        }

        $serializedData = Serialize::serialize($customer);

        if (isset($serializedData['dob']['timestamp'])) {
            $timestamp = $serializedData['dob']['timestamp'];
            $serializedData['dob'] = date('Y-m-d H:i:s', $timestamp);
        }

        if (is_array($serializedData['dob'])) {
            $serializedData['dob'] = null;
        }

        $begelsResponse = Request::put('/customer/'.$customer->getId(), $serializedData);

        if (!isset($begelsResponse['customer'])) {
            return null;
        }

        $customer = self::hydrate($begelsResponse['customer']);

        return $customer;
    }

    /**
     * @param array $data
     * @return Customer
     */
    static private function hydrate(array $data) : Customer
    {
        $customerObject = new Customer();
        if (!empty($data['mapping']['address_id'])) {
            $addressObject = new Address();

            $addressObject->setStreet($data['address']['street']);
            $addressObject->setCity($data['address']['city']);
            $addressObject->setNumber($data['address']['number']);
            $addressObject->setZipcode($data['address']['zipcode']);
            $customerObject->setAddress($addressObject);
        }

        $customerObject->setId($data['id']);
        $customerObject->setFirstName($data['first_name']);
        $customerObject->setLastName($data['last_name']);
        $customerObject->setEmail($data['email']);
        $customerObject->setPhone($data['phone']);
        $customerObject->setMobile($data['mobile']);
        $customerObject->setDob($data['date_of_birth']);
        $customerObject->setVatNumber($data['vat_number']);
        $customerObject->setBankBic($data['bank_bic']);
        $customerObject->setBankIban($data['bank_iban']);

        return $customerObject;
    }
}