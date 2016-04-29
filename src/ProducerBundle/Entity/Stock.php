<?php

namespace ProducerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity(repositoryClass="ProducerBundle\Repository\StockRepository")
 */
class Stock
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="caduce", type="date", nullable=true)
     */
    private $caduce;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var unit
     *
     * @ORM\Column(name="unit", type="string", length=10)
     */
    private $unit;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_exchangeable", type="boolean")
     */
    private $isExchangeable;

    /**
    * @var Product
    *
    * @ORM\ManyToOne(targetEntity="\ProductBundle\Entity\Product")
    */
    private $Product;

    /**
    * @var Producer
    *
    * @ORM\ManyToOne(targetEntity="\ProducerBundle\Entity\Member", inversedBy="Stocks")
    */
    private $Producer;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return Stock
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set caduce
     *
     * @param \DateTime $caduce
     *
     * @return Stock
     */
    public function setCaduce($caduce)
    {
        $this->caduce = $caduce;

        return $this;
    }

    /**
     * Get caduce
     *
     * @return \DateTime
     */
    public function getCaduce()
    {
        return $this->caduce;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Stock
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set isExchangeable
     *
     * @param boolean $isExchangeable
     *
     * @return Stock
     */
    public function setIsExchangeable($isExchangeable)
    {
        $this->isExchangeable = $isExchangeable;

        return $this;
    }

    /**
     * Get isExchangeable
     *
     * @return bool
     */
    public function getIsExchangeable()
    {
        return $this->isExchangeable;
    }

    /**
     * Set product
     *
     * @param \ProductBundle\Entity\Product $product
     *
     * @return Stock
     */
    public function setProduct(\ProductBundle\Entity\Product $product = null)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set producer
     *
     * @param \ProducerBundle\Entity\Member $producer
     *
     * @return Stock
     */
    public function setProducer(\ProducerBundle\Entity\Member $producer = null)
    {
        $this->Producer = $producer;

        return $this;
    }

    /**
     * Get producer
     *
     * @return \ProducerBundle\Entity\Member
     */
    public function getProducer()
    {
        return $this->Producer;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return Stock
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
