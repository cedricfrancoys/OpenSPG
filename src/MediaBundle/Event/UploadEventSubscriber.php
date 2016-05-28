<?php
namespace MediaBundle\Event;
 
use Symfony\Component\EventDispatcher\Event;
use MediaBundle\Entity\Media;

class UploadEventSubscriber
{
    protected $em;

    public function onPostUpload(Event $event)
    {
        $media = $event->getObject();
        $file = $media->getMediaFile();

        $filesize = $file->getSize();
        $mime = mime_content_type($file->getRealPath());
        if (!array_key_exists($mime, Media::MIMES)) {
            $mime = 'application/octet-stream';
        }
        $type = Media::MIMES[$mime]['type'];

        $media->setFilesize($filesize);
        $media->setMime($mime);
        $media->setType($type);

        if ($type == Media::TYPE_IMAGE) {
            $dimensions = getimagesize($file->getRealPath());
            if( $dimensions !== false ){
                $media->setWidth($dimensions[0]);
                $media->setHeight($dimensions[1]);
            }
        }
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }
}