<?php

namespace ProducerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visit
 *
 * @ORM\Table(name="visit")
 * @ORM\Entity(repositoryClass="ProducerBundle\Repository\VisitRepository")
 */
class Visit
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
     * @var \DateTime
     *
     * @ORM\Column(name="visitDate", type="date", nullable=true)
     */
    private $visitDate;

    /**
     * @var \UserBundle\Entity\User
     *
     * @ORM\ManyToMany(targetEntity="\UserBundle\Entity\User", cascade={"persist","detach"})
     */
    private $Participants;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="time", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="time", nullable=true)
     */
    private $endTime;

    /**
     * @var \ProducerBundle\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="\ProducerBundle\Entity\Member", cascade={"persist","detach"})
     */
    private $Producer;

    /**
     * @var \ProducerBundle\Entity\Property
     *
     * @ORM\ManyToOne(targetEntity="\ProducerBundle\Entity\Property", inversedBy="Visits", cascade={"persist","detach"})
     */
    private $Property;

    /**
     * @var bool
     *
     * @ORM\Column(name="didFertilize", type="boolean", nullable=true)
     */
    private $didFertilize;

    /**
     * @var string
     *
     * @ORM\Column(name="fertlizeWith", type="string", length=255, nullable=true)
     */
    private $fertlizeWith;

    /**
     * @var string
     *
     * @ORM\Column(name="fertilizeQty", type="string", length=255, nullable=true)
     */
    private $fertilizeQty;

    /**
     * @var string
     *
     * @ORM\Column(name="fertilizeOrigin", type="string", length=255, nullable=true)
     */
    private $fertilizeOrigin;

    /**
     * @var string
     *
     * @ORM\Column(name="fertilizerObservations", type="text", nullable=true)
     */
    private $fertilizerObservations;

    /**
     * @var bool
     *
     * @ORM\Column(name="doesSoilConservation", type="boolean", nullable=true)
     */
    private $doesSoilConservation;

    /**
     * @var bool
     *
     * @ORM\Column(name="scGreenManure", type="boolean", nullable=true)
     */
    private $scGreenManure;

    /**
     * @var bool
     *
     * @ORM\Column(name="scMulching", type="boolean", nullable=true)
     */
    private $scMulching;

    /**
     * @var bool
     *
     * @ORM\Column(name="scNotPlow", type="boolean", nullable=true)
     */
    private $scNotPlow;

    /**
     * @var bool
     *
     * @ORM\Column(name="scHerbsState", type="boolean", nullable=true)
     */
    private $scHerbsState;

    /**
     * @var bool
     *
     * @ORM\Column(name="scHerbsDistribution", type="boolean", nullable=true)
     */
    private $scHerbsDistribution;

    /**
     * @var bool
     *
     * @ORM\Column(name="scHerbsControl", type="boolean", nullable=true)
     */
    private $scHerbsControl;

    /**
     * @var string
     *
     * @ORM\Column(name="scOberservations", type="text", nullable=true)
     */
    private $scOberservations;

    /**
     * @var bool
     *
     * @ORM\Column(name="pcPests", type="boolean", nullable=true)
     */
    private $pcPests;

    /**
     * @var string
     *
     * @ORM\Column(name="pcControl", type="text", nullable=true)
     */
    private $pcControl;

    /**
     * @var string
     *
     * @ORM\Column(name="pcPrevention", type="text", nullable=true)
     */
    private $pcPrevention;

    /**
     * @var string
     *
     * @ORM\Column(name="pcOberservations", type="text", nullable=true)
     */
    private $pcOberservations;

    /**
     * @var string
     *
     * @ORM\Column(name="pcPestsCrops", type="string", length=255, nullable=true)
     */
    private $pcPestsCrops;

    /**
     * @var string
     *
     * @ORM\Column(name="pcPestsDamage", type="string", length=255, nullable=true)
     */
    private $pcPestsDamage;

    /**
     * @var string
     *
     * @ORM\Column(name="PruningRests", type="string", length=255, nullable=true)
     */
    private $pruningRests;

    /**
     * @var string
     *
     * @ORM\Column(name="AgroquimicPresence", type="string", length=255, nullable=true)
     */
    private $agroquimicPresence;

    /**
     * @var string
     *
     * @ORM\Column(name="PlasticWaste", type="string", length=255, nullable=true)
     */
    private $plasticWaste;

    /**
     * @var string
     *
     * @ORM\Column(name="AgroquimicPackaging", type="string", length=255, nullable=true)
     */
    private $agroquimicPackaging;

    /**
     * @var string
     *
     * @ORM\Column(name="PesticideSupsition", type="string", length=255, nullable=true)
     */
    private $pesticideSupsition;

    /**
     * @var string
     *
     * @ORM\Column(name="observations", type="text", nullable=true)
     */
    private $observations;

    /**
     * @var bool
     *
     * @ORM\Column(name="accepted", type="boolean", nullable=true)
     */
    private $accepted;


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
     * Set visitDate
     *
     * @param \DateTime $visitDate
     *
     * @return Visit
     */
    public function setVisitDate($visitDate)
    {
        $this->visitDate = $visitDate;

        return $this;
    }

    /**
     * Get visitDate
     *
     * @return \DateTime
     */
    public function getVisitDate()
    {
        return $this->visitDate;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Visit
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return Visit
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set didFertilize
     *
     * @param boolean $didFertilize
     *
     * @return Visit
     */
    public function setDidFertilize($didFertilize)
    {
        $this->didFertilize = $didFertilize;

        return $this;
    }

    /**
     * Get didFertilize
     *
     * @return bool
     */
    public function getDidFertilize()
    {
        return $this->didFertilize;
    }

    /**
     * Set fertlizeWith
     *
     * @param string $fertlizeWith
     *
     * @return Visit
     */
    public function setFertlizeWith($fertlizeWith)
    {
        $this->fertlizeWith = $fertlizeWith;

        return $this;
    }

    /**
     * Get fertlizeWith
     *
     * @return string
     */
    public function getFertlizeWith()
    {
        return $this->fertlizeWith;
    }

    /**
     * Set fertilizeQty
     *
     * @param string $fertilizeQty
     *
     * @return Visit
     */
    public function setFertilizeQty($fertilizeQty)
    {
        $this->fertilizeQty = $fertilizeQty;

        return $this;
    }

    /**
     * Get fertilizeQty
     *
     * @return string
     */
    public function getFertilizeQty()
    {
        return $this->fertilizeQty;
    }

    /**
     * Set fertilizeOrigin
     *
     * @param string $fertilizeOrigin
     *
     * @return Visit
     */
    public function setFertilizeOrigin($fertilizeOrigin)
    {
        $this->fertilizeOrigin = $fertilizeOrigin;

        return $this;
    }

    /**
     * Get fertilizeOrigin
     *
     * @return string
     */
    public function getFertilizeOrigin()
    {
        return $this->fertilizeOrigin;
    }

    /**
     * Set fertilizerObservations
     *
     * @param string $fertilizerObservations
     *
     * @return Visit
     */
    public function setFertilizerObservations($fertilizerObservations)
    {
        $this->fertilizerObservations = $fertilizerObservations;

        return $this;
    }

    /**
     * Get fertilizerObservations
     *
     * @return string
     */
    public function getFertilizerObservations()
    {
        return $this->fertilizerObservations;
    }

    /**
     * Set doesSoilConservation
     *
     * @param boolean $doesSoilConservation
     *
     * @return Visit
     */
    public function setDoesSoilConservation($doesSoilConservation)
    {
        $this->doesSoilConservation = $doesSoilConservation;

        return $this;
    }

    /**
     * Get doesSoilConservation
     *
     * @return bool
     */
    public function getDoesSoilConservation()
    {
        return $this->doesSoilConservation;
    }

    /**
     * Set scGreenManure
     *
     * @param boolean $scGreenManure
     *
     * @return Visit
     */
    public function setScGreenManure($scGreenManure)
    {
        $this->scGreenManure = $scGreenManure;

        return $this;
    }

    /**
     * Get scGreenManure
     *
     * @return bool
     */
    public function getScGreenManure()
    {
        return $this->scGreenManure;
    }

    /**
     * Set scMulching
     *
     * @param boolean $scMulching
     *
     * @return Visit
     */
    public function setScMulching($scMulching)
    {
        $this->scMulching = $scMulching;

        return $this;
    }

    /**
     * Get scMulching
     *
     * @return bool
     */
    public function getScMulching()
    {
        return $this->scMulching;
    }

    /**
     * Set scNotPlow
     *
     * @param boolean $scNotPlow
     *
     * @return Visit
     */
    public function setScNotPlow($scNotPlow)
    {
        $this->scNotPlow = $scNotPlow;

        return $this;
    }

    /**
     * Get scNotPlow
     *
     * @return bool
     */
    public function getScNotPlow()
    {
        return $this->scNotPlow;
    }

    /**
     * Set scHerbsState
     *
     * @param boolean $scHerbsState
     *
     * @return Visit
     */
    public function setScHerbsState($scHerbsState)
    {
        $this->scHerbsState = $scHerbsState;

        return $this;
    }

    /**
     * Get scHerbsState
     *
     * @return bool
     */
    public function getScHerbsState()
    {
        return $this->scHerbsState;
    }

    /**
     * Set scHerbsDistribution
     *
     * @param boolean $scHerbsDistribution
     *
     * @return Visit
     */
    public function setScHerbsDistribution($scHerbsDistribution)
    {
        $this->scHerbsDistribution = $scHerbsDistribution;

        return $this;
    }

    /**
     * Get scHerbsDistribution
     *
     * @return bool
     */
    public function getScHerbsDistribution()
    {
        return $this->scHerbsDistribution;
    }

    /**
     * Set scHerbsControl
     *
     * @param boolean $scHerbsControl
     *
     * @return Visit
     */
    public function setScHerbsControl($scHerbsControl)
    {
        $this->scHerbsControl = $scHerbsControl;

        return $this;
    }

    /**
     * Get scHerbsControl
     *
     * @return bool
     */
    public function getScHerbsControl()
    {
        return $this->scHerbsControl;
    }

    /**
     * Set scOberservations
     *
     * @param string $scOberservations
     *
     * @return Visit
     */
    public function setScOberservations($scOberservations)
    {
        $this->scOberservations = $scOberservations;

        return $this;
    }

    /**
     * Get scOberservations
     *
     * @return string
     */
    public function getScOberservations()
    {
        return $this->scOberservations;
    }

    /**
     * Set pcPests
     *
     * @param boolean $pcPests
     *
     * @return Visit
     */
    public function setPcPests($pcPests)
    {
        $this->pcPests = $pcPests;

        return $this;
    }

    /**
     * Get pcPests
     *
     * @return bool
     */
    public function getPcPests()
    {
        return $this->pcPests;
    }

    /**
     * Set pcControl
     *
     * @param string $pcControl
     *
     * @return Visit
     */
    public function setPcControl($pcControl)
    {
        $this->pcControl = $pcControl;

        return $this;
    }

    /**
     * Get pcControl
     *
     * @return string
     */
    public function getPcControl()
    {
        return $this->pcControl;
    }

    /**
     * Set pcPrevention
     *
     * @param string $pcPrevention
     *
     * @return Visit
     */
    public function setPcPrevention($pcPrevention)
    {
        $this->pcPrevention = $pcPrevention;

        return $this;
    }

    /**
     * Get pcPrevention
     *
     * @return string
     */
    public function getPcPrevention()
    {
        return $this->pcPrevention;
    }

    /**
     * Set pcOberservations
     *
     * @param string $pcOberservations
     *
     * @return Visit
     */
    public function setPcOberservations($pcOberservations)
    {
        $this->pcOberservations = $pcOberservations;

        return $this;
    }

    /**
     * Get pcOberservations
     *
     * @return string
     */
    public function getPcOberservations()
    {
        return $this->pcOberservations;
    }

    /**
     * Set pcPestsCrops
     *
     * @param string $pcPestsCrops
     *
     * @return Visit
     */
    public function setPcPestsCrops($pcPestsCrops)
    {
        $this->pcPestsCrops = $pcPestsCrops;

        return $this;
    }

    /**
     * Get pcPestsCrops
     *
     * @return string
     */
    public function getPcPestsCrops()
    {
        return $this->pcPestsCrops;
    }

    /**
     * Set pcPestsDamage
     *
     * @param string $pcPestsDamage
     *
     * @return Visit
     */
    public function setPcPestsDamage($pcPestsDamage)
    {
        $this->pcPestsDamage = $pcPestsDamage;

        return $this;
    }

    /**
     * Get pcPestsDamage
     *
     * @return string
     */
    public function getPcPestsDamage()
    {
        return $this->pcPestsDamage;
    }

    /**
     * Set pruningRests
     *
     * @param string $pruningRests
     *
     * @return Visit
     */
    public function setPruningRests($pruningRests)
    {
        $this->pruningRests = $pruningRests;

        return $this;
    }

    /**
     * Get pruningRests
     *
     * @return string
     */
    public function getPruningRests()
    {
        return $this->pruningRests;
    }

    /**
     * Set agroquimicPresence
     *
     * @param string $agroquimicPresence
     *
     * @return Visit
     */
    public function setAgroquimicPresence($agroquimicPresence)
    {
        $this->agroquimicPresence = $agroquimicPresence;

        return $this;
    }

    /**
     * Get agroquimicPresence
     *
     * @return string
     */
    public function getAgroquimicPresence()
    {
        return $this->agroquimicPresence;
    }

    /**
     * Set plasticWaste
     *
     * @param string $plasticWaste
     *
     * @return Visit
     */
    public function setPlasticWaste($plasticWaste)
    {
        $this->plasticWaste = $plasticWaste;

        return $this;
    }

    /**
     * Get plasticWaste
     *
     * @return string
     */
    public function getPlasticWaste()
    {
        return $this->plasticWaste;
    }

    /**
     * Set agroquimicPackaging
     *
     * @param string $agroquimicPackaging
     *
     * @return Visit
     */
    public function setAgroquimicPackaging($agroquimicPackaging)
    {
        $this->agroquimicPackaging = $agroquimicPackaging;

        return $this;
    }

    /**
     * Get agroquimicPackaging
     *
     * @return string
     */
    public function getAgroquimicPackaging()
    {
        return $this->agroquimicPackaging;
    }

    /**
     * Set pesticideSupsition
     *
     * @param string $pesticideSupsition
     *
     * @return Visit
     */
    public function setPesticideSupsition($pesticideSupsition)
    {
        $this->pesticideSupsition = $pesticideSupsition;

        return $this;
    }

    /**
     * Get pesticideSupsition
     *
     * @return string
     */
    public function getPesticideSupsition()
    {
        return $this->pesticideSupsition;
    }

    /**
     * Set observations
     *
     * @param string $observations
     *
     * @return Visit
     */
    public function setObservations($observations)
    {
        $this->observations = $observations;

        return $this;
    }

    /**
     * Get observations
     *
     * @return string
     */
    public function getObservations()
    {
        return $this->observations;
    }

    /**
     * Set producer
     *
     * @param \ProducerBundle\Entity\Member $producer
     *
     * @return Visit
     */
    public function setProducer(\ProducerBundle\Entity\Member $producer = null)
    {
        $this->Producer = $producer;

        return $this;
    }

    /**
     * Get producer
     *
     * @return \ProducerBundle\Entity\Member
     */
    public function getProducer()
    {
        return $this->Producer;
    }

    /**
     * Set property
     *
     * @param \ProducerBundle\Entity\Property $property
     *
     * @return Visit
     */
    public function setProperty(\ProducerBundle\Entity\Property $property = null)
    {
        $this->Property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \ProducerBundle\Entity\Property
     */
    public function getProperty()
    {
        return $this->Property;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->Participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add participant
     *
     * @param \UserBundle\Entity\User $participant
     *
     * @return Visit
     */
    public function addParticipant(\UserBundle\Entity\User $participant)
    {
        $this->Participants[] = $participant;

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \UserBundle\Entity\User $participant
     */
    public function removeParticipant(\UserBundle\Entity\User $participant)
    {
        $this->Participants->removeElement($participant);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->Participants;
    }

    /**
     * Set accepted
     *
     * @param boolean $accepted
     *
     * @return Visit
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean
     */
    public function getAccepted()
    {
        return $this->accepted;
    }
}
