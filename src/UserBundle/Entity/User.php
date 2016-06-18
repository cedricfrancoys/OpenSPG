<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="email",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="username",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="usernameCanonical",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      ),
 *      @ORM\AttributeOverride(name="password",
 *          column=@ORM\Column(
 *              nullable = true
 *          )
 *      )
 * })
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 */
class User extends BaseUser
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_VISITGROUP = 'ROLE_VISITGROUP';
    const ROLE_PRODUCER = 'ROLE_PRODUCER';
    const ROLE_CONSUMER = 'ROLE_CONSUMER';
    const ROLE_MEMBER = 'ROLE_MEMBER';
    const ROLE_GUEST = 'ROLE_GUEST';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     * @Gedmo\Versioned
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=100, nullable=true)
     * @Gedmo\Versioned
     */
    protected $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=25, nullable=true)
     * @Gedmo\Versioned
     */
    protected $phone;

    /**
    * @var Node
    *
    * @ORM\ManyToOne(targetEntity="\NodeBundle\Entity\Node")
    * @ORM\JoinColumn(onDelete="SET NULL")
    * @Gedmo\Versioned
    */
    protected $Node;

    /**
    * @var Producer
    *
    * @ORM\OneToOne(targetEntity="\ProducerBundle\Entity\Member", cascade={"persist","remove"}, inversedBy="User")
    * @Gedmo\Versioned
    */
    protected $Producer;

    /**
    * @var Consumer
    *
    * @ORM\OneToOne(targetEntity="\ConsumerBundle\Entity\Member", cascade={"persist","remove"}, inversedBy="User")
    * @Gedmo\Versioned
    */
    protected $Consumer;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     *
     * @var string
     *
     * @Assert\File(mimeTypes={ "image/jpeg", "image/gif", "image/png", "image/tiff" })
     */
    protected $image;
    protected $file;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    private $receiveEmailNewProducer = true;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    private $receiveEmailNewConsumer = true;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    private $receiveEmailNewVisit = true;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    private $receiveEmailCompletedVisit = true;

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

    /**
     * Set producer
     *
     * @param \ProducerBundle\Entity\Member $producer
     *
     * @return User
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
     * Set consumer
     *
     * @param \ConsumerBundle\Entity\Member $consumer
     *
     * @return User
     */
    public function setConsumer(\ConsumerBundle\Entity\Member $consumer = null)
    {
        $this->Consumer = $consumer;

        return $this;
    }

    /**
     * Get consumer
     *
     * @return \ConsumerBundle\Entity\Member
     */
    public function getConsumer()
    {
        return $this->Consumer;
    }

    /**
     * @param UploadedFile $image
     *
     * @return User
     */
    public function setImage(UploadedFile $image = null)
    {
        if( null === $image ) return $this;
        
        $this->image = $image;

        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getImage()
    {
        $path = $this->getRootPath().'/web/imgs/user_imgs/'.$this->image;
        return (!$this->image || !file_exists($path))
            ? null 
            : new File($path);
        // return $this->image;
    }

    protected function getRootPath(){
        return dirname(dirname(dirname(dirname(__FILE__))));
    }

    /**
     * Called before saving the entity
     * 
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {   
        if (null !== $this->image && !is_string($this->image)) {
            // do whatever you want to generate a unique name
            $this->file = $this->image;
            $filename = sha1(uniqid(mt_rand(), true));
            $this->image = $filename.'.'.$this->file->guessExtension();
        }
    }

    /**
     * Called before entity removal
     *
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file); 
        }
    }

    /**
     * Called after entity persistence
     *
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        // The file property can be empty if the field is not required
        if (null === $this->file) {
            return;
        }

        // Use the original file name here but you should
        // sanitize it at least to avoid any security issues

        // move takes the target directory and then the
        // target filename to move to
        $this->file->move(
            $this->getUploadRootDir(),
            $this->image
        );

        // Set the path property to the filename where you've saved the file
        //$this->path = $this->file->getClientOriginalName();

        // Clean up the file property as you won't need it anymore
        $this->file = null;
    }

    public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir();
    }

    public function getWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'.$this->image;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'imgs/user_imgs';
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set receiveEmailNewProducer
     *
     * @param boolean $receiveEmailNewProducer
     *
     * @return User
     */
    public function setReceiveEmailNewProducer($do)
    {
        $this->receiveEmailNewProducer = $do;

        return $this;
    }

    /**
     * Get receiveEmailNewProducer
     *
     * @return bool
     */
    public function getReceiveEmailNewProducer()
    {
        return $this->receiveEmailNewProducer;
    }

    /**
     * Set receiveEmailNewConsumer
     *
     * @param boolean $receiveEmailNewConsumer
     *
     * @return User
     */
    public function setReceiveEmailNewConsumer($do)
    {
        $this->receiveEmailNewConsumer = $do;

        return $this;
    }

    /**
     * Get receiveEmailNewConsumer
     *
     * @return bool
     */
    public function getReceiveEmailNewConsumer()
    {
        return $this->receiveEmailNewConsumer;
    }

    /**
     * Set receiveEmailNewVisit
     *
     * @param boolean $receiveEmailNewVisit
     *
     * @return User
     */
    public function setReceiveEmailNewVisit($do)
    {
        $this->receiveEmailNewVisit = $do;

        return $this;
    }

    /**
     * Get receiveEmailNewVisit
     *
     * @return bool
     */
    public function getReceiveEmailNewVisit()
    {
        return $this->receiveEmailNewVisit;
    }

    /**
     * Set receiveEmailCompletedVisit
     *
     * @param boolean $receiveEmailCompletedVisit
     *
     * @return User
     */
    public function setReceiveEmailCompletedVisit($do)
    {
        $this->receiveEmailCompletedVisit = $do;

        return $this;
    }

    /**
     * Get receiveEmailCompletedVisit
     *
     * @return bool
     */
    public function getReceiveEmailCompletedVisit()
    {
        return $this->receiveEmailCompletedVisit;
    }
}
