<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
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
    * @var Node
    *
    * @ORM\ManyToOne(targetEntity="\NodeBundle\Entity\Node")
    */
    protected $Node;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    public function __toString()
    {
        return $this->getName() . ' ' . $this->getSurname();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
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
     * @return User
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
     * Set phone
     *
     * @param string $phone
     *
     * @return User
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
     * Set node
     *
     * @param \NodeBundle\Entity\Node $node
     *
     * @return User
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
