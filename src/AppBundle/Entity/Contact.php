<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Fee
 *
 * @ORM\Table(name="contact")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContactRepository")
 */
class Contact
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="received", type="datetime", nullable=true)
     */
    private $received;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent", type="datetime", nullable=true)
     */
    private $sent;

    /**
    * @var \UserBundle\Entity\User
    *
    * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
    */
    private $Receiver;

    /**
    * @var \UserBundle\Entity\User
    *
    * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User")
    */
    private $Sender;

    /**
    * @var \AppBundle\Entity\Contact
    *
    * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Contact")
    * @ORM\JoinColumn(onDelete="SET NULL")
    */
    private $Parent;

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
     * @return Contact
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
     * Set email
     *
     * @param string $email
     *
     * @return Contact
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Contact
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set received
     *
     * @param \DateTime $received
     *
     * @return Contact
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get received
     *
     * @return \DateTime
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * Set receiver
     *
     * @param \UserBundle\Entity\User $receiver
     *
     * @return Contact
     */
    public function setReceiver(\UserBundle\Entity\User $receiver = null)
    {
        $this->Receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return \UserBundle\Entity\User
     */
    public function getReceiver()
    {
        return $this->Receiver;
    }

    /**
     * Set sender
     *
     * @param \UserBundle\Entity\User $sender
     *
     * @return Contact
     */
    public function setSender(\UserBundle\Entity\User $sender = null)
    {
        $this->Sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \UserBundle\Entity\User
     */
    public function getSender()
    {
        return $this->Sender;
    }

    /**
     * Set parent
     *
     * @param \AppBundle\Entity\Contact $parent
     *
     * @return Contact
     */
    public function setParent(\AppBundle\Entity\Contact $parent = null)
    {
        $this->Parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \AppBundle\Entity\Contact
     */
    public function getParent()
    {
        return $this->Parent;
    }

    /**
     * Set sent
     *
     * @param \DateTime $sent
     *
     * @return Contact
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return \DateTime
     */
    public function getSent()
    {
        return $this->sent;
    }
}
