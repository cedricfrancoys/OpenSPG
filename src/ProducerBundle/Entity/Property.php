<?php

namespace ProducerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Property
 *
 * @ORM\Table(name="property")
 * @ORM\Entity(repositoryClass="ProducerBundle\Repository\PropertyRepository")
 */
class Property
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
     * @ORM\Column(name="areaName", type="string", length=255)
     */
    private $areaName;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255)
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="regNr", type="string", length=25, nullable=true)
     */
    private $regNr;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="tenure", type="string", length=50, nullable=true)
     */
    private $tenure;

    /**
     * @var float
     *
     * @ORM\Column(name="size", type="float", nullable=true)
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="previousUses", type="text", nullable=true)
     */
    private $previousUses;

    /**
     * @var string
     *
     * @ORM\Column(name="waterTypeOrigin", type="text", nullable=true)
     */
    private $waterTypeOrigin;

    /**
     * @var string
     *
     * @ORM\Column(name="surroundings", type="text", nullable=true)
     */
    private $surroundings;

    /**
     * @var string
     *
     * @ORM\Column(name="surroundingProblems", type="text", nullable=true)
     */
    private $surroundingProblems;

    /**
     * @var string
     *
     * @ORM\Column(name="crops", type="text")
     */
    private $crops;

    /**
     * @var bool
     *
     * @ORM\Column(name="certified", type="boolean")
     */
    private $certified;

    /**
     * @var int
     *
     * @ORM\Column(name="certifiedYear", type="integer", nullable=true)
     */
    private $certifiedYear;

    /**
     * @var string
     *
     * @ORM\Column(name="certifiedProvider", type="string", length=100, nullable=true)
     */
    private $certifiedProvider;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastAgroquimicUsage", type="date", nullable=true)
     */
    private $lastAgroquimicUsage;

    /**
     * @var string
     *
     * @ORM\Column(name="fertilizer", type="text", nullable=true)
     */
    private $fertilizer;

    /**
     * @var string
     *
     * @ORM\Column(name="phytosanitary", type="text", nullable=true)
     */
    private $phytosanitary;

    /**
     * @var bool
     *
     * @ORM\Column(name="ownerLivesHere", type="boolean")
     */
    private $ownerLivesHere;

    /**
     * @var int
     *
     * @ORM\Column(name="ownerDistance", type="integer", nullable=true)
     */
    private $ownerDistance;

    /**
     * @var string
     *
     * @ORM\Column(name="workforce", type="text", nullable=true)
     */
    private $workforce;

    /**
     * @var int
     *
     * @ORM\Column(name="productSellingDistance", type="integer", nullable=true)
     */
    private $productSellingDistance;

    /**
     * @var int
     *
     * @ORM\Column(name="productSellingTime", type="integer", nullable=true)
     */
    private $productSellingTime;

    /**
     * @var bool
     *
     * @ORM\Column(name="productConservation", type="boolean")
     */
    private $productConservation;

    /**
     * @var string
     *
     * @ORM\Column(name="productConservationDetails", type="text", nullable=true)
     */
    private $productConservationDetails;

    /**
    * @var Member
    *
    * @ORM\ManyToOne(targetEntity="\ProducerBundle\Entity\Member", inversedBy="Properties", cascade={"persist","detach"})
    */
    private $Member;

    public function __toString()
    {
        return $this->getName();
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
     * Set areaName
     *
     * @param string $areaName
     *
     * @return Property
     */
    public function setAreaName($areaName)
    {
        $this->areaName = $areaName;

        return $this;
    }

    /**
     * Get areaName
     *
     * @return string
     */
    public function getAreaName()
    {
        return $this->areaName;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return Property
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
     * Set regNr
     *
     * @param string $regNr
     *
     * @return Property
     */
    public function setRegNr($regNr)
    {
        $this->regNr = $regNr;

        return $this;
    }

    /**
     * Get regNr
     *
     * @return string
     */
    public function getRegNr()
    {
        return $this->regNr;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Property
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
     * Set tenure
     *
     * @param string $tenure
     *
     * @return Property
     */
    public function setTenure($tenure)
    {
        $this->tenure = $tenure;

        return $this;
    }

    /**
     * Get tenure
     *
     * @return string
     */
    public function getTenure()
    {
        return $this->tenure;
    }

    /**
     * Set size
     *
     * @param float $size
     *
     * @return Property
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return float
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set previousUses
     *
     * @param string $previousUses
     *
     * @return Property
     */
    public function setPreviousUses($previousUses)
    {
        $this->previousUses = $previousUses;

        return $this;
    }

    /**
     * Get previousUses
     *
     * @return string
     */
    public function getPreviousUses()
    {
        return $this->previousUses;
    }

    /**
     * Set waterTypeOrigin
     *
     * @param string $waterTypeOrigin
     *
     * @return Property
     */
    public function setWaterTypeOrigin($waterTypeOrigin)
    {
        $this->waterTypeOrigin = $waterTypeOrigin;

        return $this;
    }

    /**
     * Get waterTypeOrigin
     *
     * @return string
     */
    public function getWaterTypeOrigin()
    {
        return $this->waterTypeOrigin;
    }

    /**
     * Set surroundings
     *
     * @param string $surroundings
     *
     * @return Property
     */
    public function setSurroundings($surroundings)
    {
        $this->surroundings = $surroundings;

        return $this;
    }

    /**
     * Get surroundings
     *
     * @return string
     */
    public function getSurroundings()
    {
        return $this->surroundings;
    }

    /**
     * Set surroundingProblems
     *
     * @param string $surroundingProblems
     *
     * @return Property
     */
    public function setSurroundingProblems($surroundingProblems)
    {
        $this->surroundingProblems = $surroundingProblems;

        return $this;
    }

    /**
     * Get surroundingProblems
     *
     * @return string
     */
    public function getSurroundingProblems()
    {
        return $this->surroundingProblems;
    }

    /**
     * Set crops
     *
     * @param string $crops
     *
     * @return Property
     */
    public function setCrops($crops)
    {
        $this->crops = $crops;

        return $this;
    }

    /**
     * Get crops
     *
     * @return string
     */
    public function getCrops()
    {
        return $this->crops;
    }

    /**
     * Set certified
     *
     * @param boolean $certified
     *
     * @return Property
     */
    public function setCertified($certified)
    {
        $this->certified = $certified;

        return $this;
    }

    /**
     * Get certified
     *
     * @return bool
     */
    public function getCertified()
    {
        return $this->certified;
    }

    /**
     * Set certifiedYear
     *
     * @param integer $certifiedYear
     *
     * @return Property
     */
    public function setCertifiedYear($certifiedYear)
    {
        $this->certifiedYear = $certifiedYear;

        return $this;
    }

    /**
     * Get certifiedYear
     *
     * @return int
     */
    public function getCertifiedYear()
    {
        return $this->certifiedYear;
    }

    /**
     * Set certifiedProvider
     *
     * @param string $certifiedProvider
     *
     * @return Property
     */
    public function setCertifiedProvider($certifiedProvider)
    {
        $this->certifiedProvider = $certifiedProvider;

        return $this;
    }

    /**
     * Get certifiedProvider
     *
     * @return string
     */
    public function getCertifiedProvider()
    {
        return $this->certifiedProvider;
    }

    /**
     * Set lastAgroquimicUsage
     *
     * @param \DateTime $lastAgroquimicUsage
     *
     * @return Property
     */
    public function setLastAgroquimicUsage($lastAgroquimicUsage)
    {
        $this->lastAgroquimicUsage = $lastAgroquimicUsage;

        return $this;
    }

    /**
     * Get lastAgroquimicUsage
     *
     * @return \DateTime
     */
    public function getLastAgroquimicUsage()
    {
        return $this->lastAgroquimicUsage;
    }

    /**
     * Set fertilizer
     *
     * @param string $fertilizer
     *
     * @return Property
     */
    public function setFertilizer($fertilizer)
    {
        $this->fertilizer = $fertilizer;

        return $this;
    }

    /**
     * Get fertilizer
     *
     * @return string
     */
    public function getFertilizer()
    {
        return $this->fertilizer;
    }

    /**
     * Set phytosanitary
     *
     * @param string $phytosanitary
     *
     * @return Property
     */
    public function setPhytosanitary($phytosanitary)
    {
        $this->phytosanitary = $phytosanitary;

        return $this;
    }

    /**
     * Get phytosanitary
     *
     * @return string
     */
    public function getPhytosanitary()
    {
        return $this->phytosanitary;
    }

    /**
     * Set ownerLivesHere
     *
     * @param boolean $ownerLivesHere
     *
     * @return Property
     */
    public function setOwnerLivesHere($ownerLivesHere)
    {
        $this->ownerLivesHere = $ownerLivesHere;

        return $this;
    }

    /**
     * Get ownerLivesHere
     *
     * @return bool
     */
    public function getOwnerLivesHere()
    {
        return $this->ownerLivesHere;
    }

    /**
     * Set ownerDistance
     *
     * @param integer $ownerDistance
     *
     * @return Property
     */
    public function setOwnerDistance($ownerDistance)
    {
        $this->ownerDistance = $ownerDistance;

        return $this;
    }

    /**
     * Get ownerDistance
     *
     * @return int
     */
    public function getOwnerDistance()
    {
        return $this->ownerDistance;
    }

    /**
     * Set workforce
     *
     * @param string $workforce
     *
     * @return Property
     */
    public function setWorkforce($workforce)
    {
        $this->workforce = $workforce;

        return $this;
    }

    /**
     * Get workforce
     *
     * @return string
     */
    public function getWorkforce()
    {
        return $this->workforce;
    }

    /**
     * Set productSellingDistance
     *
     * @param integer $productSellingDistance
     *
     * @return Property
     */
    public function setProductSellingDistance($productSellingDistance)
    {
        $this->productSellingDistance = $productSellingDistance;

        return $this;
    }

    /**
     * Get productSellingDistance
     *
     * @return int
     */
    public function getProductSellingDistance()
    {
        return $this->productSellingDistance;
    }

    /**
     * Set productSellingTime
     *
     * @param integer $productSellingTime
     *
     * @return Property
     */
    public function setProductSellingTime($productSellingTime)
    {
        $this->productSellingTime = $productSellingTime;

        return $this;
    }

    /**
     * Get productSellingTime
     *
     * @return int
     */
    public function getProductSellingTime()
    {
        return $this->productSellingTime;
    }

    /**
     * Set productConservation
     *
     * @param boolean $productConservation
     *
     * @return Property
     */
    public function setProductConservation($productConservation)
    {
        $this->productConservation = $productConservation;

        return $this;
    }

    /**
     * Get productConservation
     *
     * @return bool
     */
    public function getProductConservation()
    {
        return $this->productConservation;
    }

    /**
     * Set productConservationDetails
     *
     * @param string $productConservationDetails
     *
     * @return Property
     */
    public function setProductConservationDetails($productConservationDetails)
    {
        $this->productConservationDetails = $productConservationDetails;

        return $this;
    }

    /**
     * Get productConservationDetails
     *
     * @return string
     */
    public function getProductConservationDetails()
    {
        return $this->productConservationDetails;
    }

    /**
     * Set member
     *
     * @param \ProducerBundle\Entity\Member $member
     *
     * @return Property
     */
    public function setMember(\ProducerBundle\Entity\Member $member = null)
    {
        $this->Member = $member;

        return $this;
    }

    /**
     * Get member
     *
     * @return \ProducerBundle\Entity\Member
     */
    public function getMember()
    {
        return $this->Member;
    }
}
