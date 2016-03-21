<?php

namespace ProducerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Member
 *
 * @ORM\Table(name="producer")
 * @ORM\Entity(repositoryClass="ProducerBundle\Repository\MemberRepository")
 */
class Member
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
    * @var User
    *
    * @ORM\OneToOne(targetEntity="\UserBundle\Entity\User", cascade={"persist","remove"}, mappedBy="Producer")
    */
    protected $User;

    /**
    * @var Properties
    *
    * @ORM\OneToMany(targetEntity="\ProducerBundle\Entity\Property", mappedBy="Member", cascade={"persist"})
    */
    protected $Properties;

    public function __toString()
    {
        return ($this->getUser()) ? $this->getUser()->getName() . ' ' . $this->getUser()->getSurname() : '';
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
     * Constructor
     */
    public function __construct()
    {
        $this->Properties = new ArrayCollection();
    }

    /**
     * Add property
     *
     * @param \ProducerBundle\Entity\Property $property
     *
     * @return Member
     */
    public function addProperty(\ProducerBundle\Entity\Property $property)
    {
        $property->setMember($this);
        $this->Properties[] = $property;

        return $this;
    }

    /**
     * Remove property
     *
     * @param \ProducerBundle\Entity\Property $property
     */
    public function removeProperty(\ProducerBundle\Entity\Property $property)
    {
        $this->Properties->removeElement($property);
    }

    /**
     * Get properties
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProperties()
    {
        return $this->Properties;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return Member
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
