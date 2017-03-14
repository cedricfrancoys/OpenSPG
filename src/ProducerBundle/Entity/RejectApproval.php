<?php

namespace ProducerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VisitProduction.
 *
 * @ORM\Table(name="visit_reject_approval")
 * @ORM\Entity(repositoryClass="ProducerBundle\Repository\RejectApprovalRepository")
 */
class RejectApproval
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
     * @var Visit
     *
     * @ORM\ManyToOne(targetEntity="Visit", inversedBy="RejectApproval")
     */
    private $Visit;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $User;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text")
     */
    private $reason;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set reason.
     *
     * @param string $reason
     *
     * @return RejectApproval
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set visit.
     *
     * @param \ProducerBundle\Entity\Visit $visit
     *
     * @return RejectApproval
     */
    public function setVisit(\ProducerBundle\Entity\Visit $visit = null)
    {
        $this->Visit = $visit;

        return $this;
    }

    /**
     * Get visit.
     *
     * @return \ProducerBundle\Entity\Visit
     */
    public function getVisit()
    {
        return $this->Visit;
    }

    /**
     * Set user.
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return RejectApproval
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->User = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->User;
    }
}
