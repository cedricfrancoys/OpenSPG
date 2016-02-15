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
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=25, nullable=true)
     */
    protected $phone;

    /**
    * @var Member
    *
    * @ORM\OneToOne(targetEntity="\MemberBundle\Entity\Member", cascade={"persist"})
    */
    protected $Member;

    public function __toString()
    {
        return ($this->getMember()) ? $this->getMember()->getName() . ' ' . $this->getMember()->getSurname() : '';
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
    }

    /**
     * Set member
     *
     * @param \MemberBundle\Entity\Member $member
     *
     * @return Member
     */
    public function setMember(\MemberBundle\Entity\Member $member = null)
    {
        $this->Member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \MemberBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->Member;
    }
}
