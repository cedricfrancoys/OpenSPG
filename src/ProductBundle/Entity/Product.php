<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product2
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="ProductBundle\Repository\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
    * @var ProductGroup
    *
    * @ORM\ManyToOne(targetEntity="\ProductBundle\Entity\ProductGroup")
    */
    private $Group;

    /**
    * @var Family
    *
    * @ORM\ManyToOne(targetEntity="\ProductBundle\Entity\Family")
    */
    private $Family;

    /**
    * @var Variety
    *
    * @ORM\ManyToOne(targetEntity="\ProductBundle\Entity\Variety")
    */
    private $Variety;


    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set group
     *
     * @param \ProductBundle\Entity\ProductGroup $group
     *
     * @return Product
     */
    public function setGroup(\ProductBundle\Entity\ProductGroup $group = null)
    {
        $this->Group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \ProductBundle\Entity\ProductGroup
     */
    public function getGroup()
    {
        return $this->Group;
    }

    /**
     * Set family
     *
     * @param \ProductBundle\Entity\Family $family
     *
     * @return Product
     */
    public function setFamily(\ProductBundle\Entity\Family $family = null)
    {
        $this->Family = $family;

        return $this;
    }

    /**
     * Get family
     *
     * @return \ProductBundle\Entity\Family
     */
    public function getFamily()
    {
        return $this->Family;
    }

    /**
     * Set variety
     *
     * @param \ProductBundle\Entity\Variety $variety
     *
     * @return Product
     */
    public function setVariety(\ProductBundle\Entity\Variety $variety = null)
    {
        $this->Variety = $variety;

        return $this;
    }

    /**
     * Get variety
     *
     * @return \ProductBundle\Entity\Variety
     */
    public function getVariety()
    {
        return $this->Variety;
    }
}
