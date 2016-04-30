<?php

namespace NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Member
 *
 * @ORM\Table(name="node")
 * @ORM\Entity(repositoryClass="NodeBundle\Repository\NodeRepository")
 */
class Node
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     */
    protected $address;

    /**
    * @var Location
    *
    * @ORM\OneToOne(targetEntity="\LocationBundle\Entity\Location", cascade={"persist"})
    */
    private $Location;

    /**
    * @var Admin
    *
    * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
    */
    private $Admin;


    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Get id
     *
     * @return integer
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
     * @return Node
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
     * Set address
     *
     * @param string $address
     *
     * @return Node
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set location
     *
     * @param \LocationBundle\Entity\Location $location
     *
     * @return Node
     */
    public function setLocation(\LocationBundle\Entity\Location $location = null)
    {
        $this->Location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \LocationBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->Location;
    }

    /**
     * Set admin
     *
     * @param \UserBundle\Entity\User $admin
     *
     * @return Node
     */
    public function setAdmin(\UserBundle\Entity\User $admin = null)
    {
        $this->Admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \UserBundle\Entity\User
     */
    public function getAdmin()
    {
        return $this->Admin;
    }
}
