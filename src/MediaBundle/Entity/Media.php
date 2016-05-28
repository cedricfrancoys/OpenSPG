<?php

namespace MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Media
 *
 * @ORM\Table(name="media")
 * @ORM\Entity(repositoryClass="MediaBundle\Repository\MediaRepository")
 * @Vich\Uploadable
 * @Gedmo\Loggable
 */
class Media
{

    const TYPE_IMAGE = 'Image';
    const TYPE_DOCUMENT = 'Document';
    const TYPE_VIDEO = 'Video';
    const TYPE_AUDIO = 'Audio';
    const TYPE_FILE = 'File';

    const MIMES = array(
        'application/msword' => array(
            'type' => self::TYPE_DOCUMENT,
            'icon' => 'file_extension_doc.png'
        ),
        'application/pdf' => array(
            'type' => self::TYPE_DOCUMENT,
            'icon' => 'file_extension_pdf.png'
        ),

        'image/jpeg' => array(
            'type' => self::TYPE_IMAGE,
            'icon' => 'file_extension_jpg.png'
        ),
        'image/pjpeg' => array(
            'type' => self::TYPE_IMAGE,
            'icon' => 'file_extension_jpg.png'
        ),
        'image/png' => array(
            'type' => self::TYPE_IMAGE,
            'icon' => 'file_extension_png.png'
        ),
        'image/gif' => array(
            'type' => self::TYPE_IMAGE,
            'icon' => 'file_extension_gif.png'
        ),

        'application/octet-stream' => array(
            'type' => self::TYPE_FILE,
            'icon' => 'Crystal_Clear_mimetype_binary.png'
        )
    );

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
     * @ORM\Column(name="title", type="string", length=255)
     * @Gedmo\Versioned
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"filename"})
     * @ORM\Column(name="slug", type="string", length=255, unique=true)
     * @Gedmo\Versioned
     */
    private $slug;

    /**
     * @var \UserBundle\Entity\User
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User", cascade={"persist","detach"})
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \UserBundle\Entity\User
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="\UserBundle\Entity\User", cascade={"persist","detach"})
     * @Gedmo\Versioned
     */
    private $modifiedBy;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="modifiedAt", type="datetime", nullable=true)
     * @Gedmo\Versioned
     */
    private $modifiedAt;

    /**
     * @ORM\Column(type="string", length=255, name="filename")
     * @Gedmo\Versioned
     */
    private $filename;

    /**
     * @ORM\Column(type="string", length=255, name="media", nullable=true)
     * @Gedmo\Versioned
     */
    private $media;
    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="media", fileNameProperty="media")
     * @Assert\NotBlank(groups={"upload"})
     * 
     * @var File
     */
    private $mediaFile;

    /**
     * @var int
     *
     * @ORM\Column(name="filesize", type="integer", nullable=true)
     * @Gedmo\Versioned
     */
    private $filesize;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10, nullable=true)
     * @Gedmo\Versioned
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=30, nullable=true)
     * @Gedmo\Versioned
     */
    private $mime;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="smallint", nullable=true)
     * @Gedmo\Versioned
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="smallint", nullable=true)
     * @Gedmo\Versioned
     */
    private $height;


    public function __construct() {
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
     * Set title
     *
     * @param string $title
     *
     * @return News
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return News
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return News
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return News
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set modifiedAt
     *
     * @param \DateTime $modifiedAt
     *
     * @return News
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * Get modifiedAt
     *
     * @return \DateTime
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * Set createdBy
     *
     * @param \UserBundle\Entity\User $createdBy
     *
     * @return News
     */
    public function setCreatedBy(\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set modifiedBy
     *
     * @param \UserBundle\Entity\User $modifiedBy
     *
     * @return News
     */
    public function setModifiedBy(\UserBundle\Entity\User $modifiedBy = null)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * Get modifiedBy
     *
     * @return \UserBundle\Entity\User
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * Set media
     *
     * @param string $media
     *
     * @return Media
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get media
     *
     * @return string
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return Media
     */
    public function setMediaFile(File $file = null)
    {
        $this->mediaFile = $file;

        if ($file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->modifiedAt = new \DateTime('now');
        }

        return $this;
    }

    /**
     * @return File
     */
    public function getMediaFile()
    {
        return $this->mediaFile;
    }

    /**
     * Set filename
     *
     * @param string $filename
     *
     * @return Media
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filesize
     *
     * @param integer $filesize
     *
     * @return Media
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get filesize
     *
     * @return integer
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Media
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set mime
     *
     * @param string $mime
     *
     * @return Media
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return Media
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return Media
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }
}
