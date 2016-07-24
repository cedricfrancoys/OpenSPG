<?php

namespace MediaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

    /**
     * @Route("/media/upload", options={"expose":true})
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $media = $request->request->get('media');
        $parent = $request->request->get('parent');

        $parentNode = $em->getRepository('MediaBundle:Media')->find($parent);

        $post_data = $request->request->get('media');

        $item = new Media();
        $uploadForm = $this->createForm(MediaType::class, $item, array(
            'action' => $this->generateUrl('media_media_upload'),
            'validation_groups' => array('Default','upload')
        ));

        $uploadForm->handleRequest($request);

        $directory = new Media();
        $createDirForm = $this->createForm(DirectoryType::class, $directory, array(
            'action' => $this->generateUrl('management_media_createdir'),
            'validation_groups' => array('Default','create')
        ));

        if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
            $em->persist($item);
            $em->flush();

            $em->getRepository('MediaBundle:Media')->reorder($item->getParent(), 'filename');
            $em->getRepository('MediaBundle:Media')->reorder($item->getParent(), 'type');

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('File has been uploaded!', array(), 'media')
            );

            $data = array(
                'path' => $item->getParent()->getId(),
                'uploaded' => $item->getId()
            );

            $url = $this->generateUrl('management_media_index', $data);
            $response = new RedirectResponse($url);

            return $response;
        }

        $data = array(
            'media' => $media,
            'uploadForm' => $uploadForm->createView(),
            'createDirForm' => $createDirForm->createView(),
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Media:index.html.twig', $data);
    }
}
