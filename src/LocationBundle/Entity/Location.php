<?php

namespace LocationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use mhauptma73\GoogleMapFormTypeBundle\Validator\Constraints as OhAssert;

/**
 * LocationBundle\Entity\Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="LocationBundle\Entity\LocationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Location
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    private $latitude;

    private $longitude;

    /**
     * @var string $location
     *
     * @ORM\Column(name="location", type="point", nullable=false)
     */
    private $location;

    /**
     * @var date $created
     *
     * @ORM\Column(name="created", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var date $modified
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified = null;

    public function __construct( $repo = null){
        $this->created = new \DateTime();
        $this->location = array('lat'=>0,'lon'=>0);
    }

    public function setLatLng($latlng)
    {
        $this->setLatitude($latlng['lat']);
        $this->setLongitude($latlng['lng']);
        return $this;
    }
    /**
     * @Assert\NotBlank()
     * @OhAssert\LatLng()
     */
    public function getLatLng()
    {
        return array('lat'=>$this->getLatitude(),'lng'=>$this->getLongitude());
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->location['lat'];
    }
     public function setLatitude( $lat )
    {
        $this->location['lat'] = $lat;
    }

    public function __toString()
    {
        return sprintf('POINT(%f %f)', $this->latitude, $this->longitude);
    }

    public function setLongitude( $long )
    {
        $this->location['lon'] = $long;
    }

    public function getLongitude()
    {
        return $this->location['lon'];
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Venue
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     * @return Venue
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * Set location
     *
     * @param \point $location
     *
     * @return Location
     */
    public function setLocation(\point $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \point
     */
    public function getLocation()
    {
        return $this->location;
    }
}
