<?php

namespace CartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="CartBundle\Repository\CartRepository")
 */
class Cart
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
    * @var Product
    *
    * @ORM\ManyToOne(targetEntity="\ProducerBundle\Entity\Stock")
    */
    protected $Product;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
    * @var User
    *
    * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
    */
    protected $User;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified = null;


    public function __construct()
    {
        $this->created = new \DateTime();
    }

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
     * @return Cart
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Cart
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return Cart
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set product
     *
     * @param \ProducerBundle\Entity\Stock $product
     *
     * @return Cart
     */
    public function setProduct(\ProducerBundle\Entity\Stock $product = null)
    {
        $this->Product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \ProducerBundle\Entity\Stock
     */
    public function getProduct()
    {
        return $this->Product;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Cart
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->User = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->User;
    }
}
