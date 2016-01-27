<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Variety
 *
 * @ORM\Table(name="variety")
 * @ORM\Entity(repositoryClass="ProductBundle\Repository\VarietyRepository")
 */
class Variety
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
    * @var Family
    *
    * @ORM\ManyToOne(targetEntity="\ProductBundle\Entity\Family")
    */
    private $Family;


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
     * @return Variety
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
     * Set family
     *
     * @param \ProductBundle\Entity\Family $family
     *
     * @return Variety
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
}
