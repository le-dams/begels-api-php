<?php
/**
 * Created by IntelliJ IDEA.
 */

namespace Begels\Model;

use Begels\Request;

class Article
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $brand;

    /**
     * @var string|null
     */
    private $model;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var Category|null
     */
    private $category;

    /**
     * @var float|null
     */
    private $price;

    /**
     * @var float|null
     */
    private $priceWithTax;

    /**
     * @var float|null
     */
    private $taxRate;

    /**
     * @var int|null
     */
    private $stock;

    /**
     * @return null|int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null|int $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getBrand(): ?string
    {
        return $this->brand;
    }

    /**
     * @param string|null $brand
     */
    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @param string|null $model
     */
    public function setModel(?string $model): void
    {
        $this->model = $model;
    }

    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     */
    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    /**
     * @return float|null
     */
    public function getPriceWithTax(): ?float
    {
        return $this->priceWithTax;
    }

    /**
     * @param float|null $priceWithTax
     */
    public function setPriceWithTax(?float $priceWithTax): void
    {
        $this->priceWithTax = $priceWithTax;
    }

    /**
     * @return float|null
     */
    public function getTaxRate(): ?float
    {
        return $this->taxRate;
    }

    /**
     * @param float|null $taxRate
     */
    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @return int|null
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * @param int|null $stock
     */
    public function setStock(?int $stock): void
    {
        $this->stock = $stock;
    }


    /**
     * @param int $id
     * @return Article|null
     * @throws \Begels\Exception\BegelsUnavailableException
     */
    static public function get(int $id) :? Customer
    {
        $data =  Request::get(sprintf('/article/%s',$id));

        $article = isset($data['article']) ? $data['article'] : null;

        if (!$article) {
            return null;
        }

        return self::hydrate($article);
    }

    /**
     * @return Article[]|null
     * @throws \Begels\Exception\BegelsUnavailableException
     */
    static public function gets(): array
    {
        $result = [];
        $data =  Request::get('/articles');

        $articles = isset($data['articles']) ? $data['articles'] : null;

        if (!$articles) {
            return $result;
        }

        foreach ($articles as $article) {
            $result[] = self::hydrate($article);
        }

        return $result;
    }

    /**
     * @return Article[]|null
     * @throws \Begels\Exception\BegelsUnavailableException
     */
    static public function getsPublic(): array
    {
        $result = [];
        $data =  Request::get('/articles/public');

        $articles = isset($data['articles']) ? $data['articles'] : null;

        if (!$articles) {
            return $result;
        }

        foreach ($articles as $article) {
            $result[] = self::hydrate($article);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return Article
     */
    static private function hydrate(array $data) : Article
    {
        $articleObject = new Article();
        if (!empty($data['mapping']['category_id'])) {
            $categoryObject = new Category();

            $categoryObject->setId($data['mapping']['category_id']);
            $categoryObject->setName($data['categories']['category']);
            $articleObject->setCategory($categoryObject);
        }

        $articleObject->setId($data['id']);
        $articleObject->setName($data['name']);
        $articleObject->setBrand($data['brand']);
        if (isset($data['model'])) {
            $articleObject->setModel($data['model']);
        } else if (isset($data['modele'])) {
            $articleObject->setModel($data['modele']);
        }
        if (isset($data['price']) && isset($data['price']['amount'])) {
            $articleObject->setPrice($data['price']['amount']);
        }
        if (isset($data['price']) && isset($data['price']['amount_to_pay'])) {
            $articleObject->setPriceWithTax($data['price']['amount_to_pay']);
        }
        if (isset($data['price']) && isset($data['price']['vat_rate'])) {
            $articleObject->setTaxRate($data['price']['vat_rate']);
        }
        if (isset($data['stock']['nb']) && isset($data['stock']['nb'])) {
            $articleObject->setStock($data['stock']['nb']);
        }

        return $articleObject;
    }
}
