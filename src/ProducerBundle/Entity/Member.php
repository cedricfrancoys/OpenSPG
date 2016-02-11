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
class Member extends \MemberBundle\Entity\Member
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
     * @ORM\Column(name="phone", type="string", length=25, nullable=true)
     */
    protected $phone;

    /**
    * @var Properties
    *
    * @ORM\OneToMany(targetEntity="\ProducerBundle\Entity\Property", mappedBy="Member", cascade={"persist"})
    */
    protected $Properties;

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
     * Set phone
     *
     * @param string $phone
     *
     * @return Member
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
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
}
