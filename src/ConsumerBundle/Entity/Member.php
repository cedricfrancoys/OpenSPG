<?php

namespace ConsumerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Member
 *
 * @ORM\Table(name="consumer")
 * @ORM\Entity(repositoryClass="ConsumerBundle\Repository\MemberRepository")
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
    * @ORM\OneToOne(targetEntity="\UserBundle\Entity\User", cascade={"persist","remove"}, mappedBy="Consumer")
    */
    protected $User;

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
