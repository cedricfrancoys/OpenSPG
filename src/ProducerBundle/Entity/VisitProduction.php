<?php

namespace ProducerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VisitProduction
 *
 * @ORM\Table(name="visit_production")
 * @ORM\Entity(repositoryClass="ProducerBundle\Repository\VisitProductionRepository")
 */
class VisitProduction
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
     * @var Visit
     *
     * @ORM\ManyToOne(targetEntity="Visit", inversedBy="Production")
     */
    private $Visit;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="estimatedYield", type="string", length=255)
     */
    private $estimatedYield;

    /**
     * @var bool
     *
     * @ORM\Column(name="originLocal", type="boolean", nullable=true)
     */
    private $originLocal;

    /**
     * @var bool
     *
     * @ORM\Column(name="originComercial", type="boolean", nullable=true)
     */
    private $originComercial;

    /**
     * @var bool
     *
     * @ORM\Column(name="originOwn", type="boolean", nullable=true)
     */
    private $originOwn;

    /**
     * @var bool
     *
     * @ORM\Column(name="originBought", type="boolean", nullable=true)
     */
    private $originBought;

    /**
     * @var string
     *
     * @ORM\Column(name="originReference", type="string", length=255, nullable=true)
     */
    private $originReference;


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
     * @return VisitProduction
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
     * Set estimatedYield
     *
     * @param string $estimatedYield
     *
     * @return VisitProduction
     */
    public function setEstimatedYield($estimatedYield)
    {
        $this->estimatedYield = $estimatedYield;

        return $this;
    }

    /**
     * Get estimatedYield
     *
     * @return string
     */
    public function getEstimatedYield()
    {
        return $this->estimatedYield;
    }

    /**
     * Set originLocal
     *
     * @param boolean $originLocal
     *
     * @return VisitProduction
     */
    public function setOriginLocal($originLocal)
    {
        $this->originLocal = $originLocal;

        return $this;
    }

    /**
     * Get originLocal
     *
     * @return bool
     */
    public function getOriginLocal()
    {
        return $this->originLocal;
    }

    /**
     * Set originComercial
     *
     * @param boolean $originComercial
     *
     * @return VisitProduction
     */
    public function setOriginComercial($originComercial)
    {
        $this->originComercial = $originComercial;

        return $this;
    }

    /**
     * Get originComercial
     *
     * @return bool
     */
    public function getOriginComercial()
    {
        return $this->originComercial;
    }

    /**
     * Set originOwn
     *
     * @param boolean $originOwn
     *
     * @return VisitProduction
     */
    public function setOriginOwn($originOwn)
    {
        $this->originOwn = $originOwn;

        return $this;
    }

    /**
     * Get originOwn
     *
     * @return bool
     */
    public function getOriginOwn()
    {
        return $this->originOwn;
    }

    /**
     * Set originBought
     *
     * @param boolean $originBought
     *
     * @return VisitProduction
     */
    public function setOriginBought($originBought)
    {
        $this->originBought = $originBought;

        return $this;
    }

    /**
     * Get originBought
     *
     * @return bool
     */
    public function getOriginBought()
    {
        return $this->originBought;
    }

    /**
     * Set originReference
     *
     * @param string $originReference
     *
     * @return VisitProduction
     */
    public function setOriginReference($originReference)
    {
        $this->originReference = $originReference;

        return $this;
    }

    /**
     * Get originReference
     *
     * @return string
     */
    public function getOriginReference()
    {
        return $this->originReference;
    }

    /**
     * Set visit
     *
     * @param \ProducerBundle\Entity\Visit $visit
     *
     * @return VisitProduction
     */
    public function setVisit(\ProducerBundle\Entity\Visit $visit = null)
    {
        $this->Visit = $visit;

        return $this;
    }

    /**
     * Get visit
     *
     * @return \ProducerBundle\Entity\Visit
     */
    public function getVisit()
    {
        return $this->Visit;
    }
}
