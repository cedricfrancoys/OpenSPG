<?php

namespace MediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use MediaBundle\Entity\Media;

class MediaController extends Controller
{
    /**
     * @Route("/media/view/{slug}")
     */
    public function viewAction(Media $media)
    {
        $response = new Response();

        if ($media->getType() !== Media::TYPE_IMAGE) {
            $mime = $media->getMime();
            $root_dir = $this->get('kernel')->getRootDir() . '/../web';
            $response->headers->set('Content-Type', 'image/png');
            $response->headers->set('Content-Disposition', 'inline; filename="'.Media::MIMES[$mime]['icon'].'"');
            $response->sendHeaders();
            $response->setContent(file_get_contents($root_dir.'/bundles/media/img/'.Media::MIMES[$mime]['icon']));
        }else{
            $response->headers->set('Content-Type', $media->getMime());
            $response->headers->set('Content-Disposition', 'inline; filename="'.$media->getSlug().'"');
            $response->sendHeaders();
            $response->setContent(file_get_contents($media->getMediaFile()->getRealPath()));
        }

        return $response;
    }

    /**
     * @Route("/media/download/{slug}")
     */
    public function downloadAction(Media $media)
    {
        $downloadHandler = $this->get('vich_uploader.download_handler');

        return $downloadHandler->downloadObject($media, 'mediaFile');
    }
}
