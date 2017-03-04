<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 */
class User implements UserInterface
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    protected $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Versioned
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    protected $emailCanonical;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    protected $enabled;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Versioned
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $lastLogin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true, unique=true)
     * @Gedmo\Versioned
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $passwordRequestedAt;

    /**
     /* @var Collection
     */
    // protected $groups;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    protected $locked;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    protected $expired;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * @ORM\Column(type="array")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    protected $roles;

    /**
     * @ORM\Column(type="boolean")
     * @Gedmo\Versioned
     *
     * @var bool
     */
    protected $credentialsExpired;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $credentialsExpireAt;

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

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    protected $registered;

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->enabled = false;
        $this->locked = false;
        $this->expired = false;
        $this->roles = array();
        $this->credentialsExpired = false;
        $this->registered = new \DateTime();
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

    /**
     * {@inheritdoc}
     */
    public function addRole($role)
    {
        $role = strtoupper($role);
        if ($role === static::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        if (13 === count($data)) {
            // Unserializing a User object from 1.3.x
            unset($data[4], $data[5], $data[6], $data[9], $data[10]);
            $data = array_values($data);
        } elseif (11 === count($data)) {
            // Unserializing a User from a dev version somewhere between 2.0-alpha3 and 2.0-beta1
            unset($data[4], $data[7], $data[8]);
            $data = array_values($data);
        }

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->enabled,
            $this->id,
            $this->email,
            $this->emailCanonical
        ) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Gets the last login time.
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;

        // foreach ($this->getGroups() as $group) {
        //     $roles = array_merge($roles, $group->getRoles());
        // }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function hasRole($role)
    {
        return in_array(strtoupper($role), $this->getRoles(), true);
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperAdmin()
    {
        return $this->hasRole(static::ROLE_SUPER_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function removeRole($role)
    {
        if (false !== $key = array_search(strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->emailCanonical = $emailCanonical;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEnabled($boolean)
    {
        $this->enabled = (bool) $boolean;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setSuperAdmin($boolean)
    {
        if (true === $boolean) {
            $this->addRole(static::ROLE_SUPER_ADMIN);
        } else {
            $this->removeRole(static::ROLE_SUPER_ADMIN);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastLogin(\DateTime $time = null)
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPasswordRequestedAt(\DateTime $date = null)
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * Gets the timestamp that the user requested a password reset.
     *
     * @return null|\DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
               $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * {@inheritdoc}
     */
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    // public function getGroups()
    // {
    //     return $this->groups ?: $this->groups = new ArrayCollection();
    // }

    /**
     * {@inheritdoc}
     */
    // public function getGroupNames()
    // {
    //     $names = array();
    //     foreach ($this->getGroups() as $group) {
    //         $names[] = $group->getName();
    //     }

    //     return $names;
    // }

    /**
     * {@inheritdoc}
     */
    // public function hasGroup($name)
    // {
    //     return in_array($name, $this->getGroupNames());
    // }

    /**
     * {@inheritdoc}
     */
    // public function addGroup(GroupInterface $group)
    // {
    //     if (!$this->getGroups()->contains($group)) {
    //         $this->getGroups()->add($group);
    //     }

    //     return $this;
    // }

    /**
     * {@inheritdoc}
     */
    // public function removeGroup(GroupInterface $group)
    // {
    //     if ($this->getGroups()->contains($group)) {
    //         $this->getGroups()->removeElement($group);
    //     }

    //     return $this;
    // }

    /**
     * {@inheritdoc}
     */
    public function setLocked($boolean){
        $this->locked = $boolean;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return User
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set expiresAt
     *
     * @param \DateTime $expiresAt
     *
     * @return User
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set credentialsExpired
     *
     * @param boolean $credentialsExpired
     *
     * @return User
     */
    public function setCredentialsExpired($credentialsExpired)
    {
        $this->credentialsExpired = $credentialsExpired;

        return $this;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Set credentialsExpireAt
     *
     * @param \DateTime $credentialsExpireAt
     *
     * @return User
     */
    public function setCredentialsExpireAt($credentialsExpireAt)
    {
        $this->credentialsExpireAt = $credentialsExpireAt;

        return $this;
    }

    /**
     * Get credentialsExpireAt
     *
     * @return \DateTime
     */
    public function getCredentialsExpireAt()
    {
        return $this->credentialsExpireAt;
    }

    /**
     * Set registered
     *
     * @param \DateTime $registered
     *
     * @return User
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;

        return $this;
    }

    /**
     * Get registered
     *
     * @return \DateTime
     */
    public function getRegistered()
    {
        return $this->registered;
    }
}
