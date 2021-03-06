<?php

namespace MemberBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Member
*
 * @ORM\Table(name="member")
 * @ORM\Entity(repositoryClass="MemberBundle\Repository\MemberRepository")
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=100)
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=25, nullable=true)
     */
    protected $phone;

    /**
    * @var User
    *
    * @ORM\OneToOne(targetEntity="\UserBundle\Entity\User")
    */
    protected $User;

    /**
    * @var Node
    *
    * @ORM\ManyToOne(targetEntity="\NodeBundle\Entity\Node")
    */
    protected $Node;

    public function __toString()
    {
        return $this->getName() . ' ' . $this->getSurname();
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
     * @return Member
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
     * Set surname
     *
     * @param string $surname
     *
     * @return Member
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
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

    /**
     * Set node
     *
     * @param \NodeBundle\Entity\Node $node
     *
     * @return Member
     */
    public function setNode(\NodeBundle\Entity\Node $node = null)
    {
        $this->Node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \NodeBundle\Entity\Node
     */
    public function getNode()
    {
        return $this->Node;
    }
}
