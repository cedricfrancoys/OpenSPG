<?php

namespace ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Family
 *
 * @ORM\Table(name="family", uniqueConstraints={@ORM\UniqueConstraint(name="family_idx", columns={"name","group_id"})})
 * @ORM\Entity(repositoryClass="ProductBundle\Repository\FamilyRepository")
 */
class Family
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
    * @var ProductGroup
    *
    * @ORM\ManyToOne(targetEntity="\ProductBundle\Entity\ProductGroup")
    */
    private $Group;

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
     * @return Family
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
     * @return Family
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
}
