<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use MediaBundle\Entity\Media;

use MediaBundle\Form\MediaType;
use MediaBundle\Form\DirectoryType;

/**
 * @Route("/media")
 */
class MediaController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Media", $this->get("router")->generate("management_media_index"));

        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository('MediaBundle:Media')->children(null, true, 'type');

        $item = new Media();

        $uploadForm = $this->createForm(MediaType::class, $item, array(
            'action' => $this->generateUrl('management_media_upload'),
            'validation_groups' => array('Default','upload')
        ));

        $directory = new Media();
        $directory->setType(Media::TYPE_DIRECTORY);
        $createDirForm = $this->createForm(DirectoryType::class, $directory, array(
            'action' => $this->generateUrl('management_media_createdir'),
            'validation_groups' => array('Default','create')
        ));

        $data = array(
            'media' => $media,
            'uploadForm' => $uploadForm->createView(),
            'createDirForm' => $createDirForm->createView(),
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Media:index.html.twig', $data);
    }

    /**
     * @Route("/load", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function load(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $path = $request->query->get('path');

        $parent = $em->getRepository('MediaBundle:Media')->find($path);
        $media = $em->getRepository('MediaBundle:Media')->childrenHierarchy($parent, true);

        $response = new JsonResponse();
        $response->setData(array(
            'media' => $media
        ));

        return $response;
    }

    /**
     * @Route("/upload")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function uploadAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Media", $this->get("router")->generate("management_media_index"));

        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository('MediaBundle:Media')->findAll();

        $post_data = $request->request->get('media');

        $item = new Media();
        $uploadForm = $this->createForm(MediaType::class, $item, array(
            'action' => $this->generateUrl('management_media_upload'),
            'validation_groups' => array('Default','upload')
        ));

        $uploadForm->handleRequest($request);

        $directory = new Media();
        $directory->setType(Media::TYPE_DIRECTORY);
        $createDirForm = $this->createForm(DirectoryType::class, $directory, array(
            'action' => $this->generateUrl('management_media_createdir'),
            'validation_groups' => array('Default','create')
        ));

        if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
            $em->persist($item);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('File has been uploaded!', array(), 'media')
            );

            $url = $this->generateUrl('management_media_index');
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

    /**
     * @Route("/createDir")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function createDirAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Media", $this->get("router")->generate("management_media_index"));
        $breadcrumbs->addItem("Create directory", $this->get("router")->generate("management_media_createdir"));

        $em = $this->getDoctrine()->getManager();

        $media = $em->getRepository('MediaBundle:Media')->findAll();

        $item = new Media();
        $uploadForm = $this->createForm(MediaType::class, $item, array(
            'action' => $this->generateUrl('management_media_upload'),
            'validation_groups' => array('Default','upload')
        ));

        $directory = new Media();
        $directory->setType(Media::TYPE_DIRECTORY);
        $createDirForm = $this->createForm(DirectoryType::class, $directory, array(
            'action' => $this->generateUrl('management_media_createdir'),
            'validation_groups' => array('Default','create')
        ));

        $createDirForm->handleRequest($request);

        if ($createDirForm->isSubmitted() && $createDirForm->isValid()) {
            $directory->setTitle($directory->getFilename());
            $directory->setMime('inode/directory');
            $post_data = $request->request->get('directory');
            $directory->setMedia($directory->getFilename());
            $em->persist($directory);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('Directory has been created!', array(), 'media')
            );

            $url = $this->generateUrl('management_media_index');
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

    /**
     * @Route("/download", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function downloadAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $id = $request->query->get('id');
        $ids = $request->query->get('ids');

        if($id){
            $media = $em->getRepository('MediaBundle:Media')->find($id);
            $directory = $em->getRepository('MediaBundle:Media')->getPathArray($media);
            $file = join('/', $directory);
            $filename = $media->getFilename();
        }else if(is_array($ids) && count($ids)){
            $medias = $em->getRepository('MediaBundle:Media')->findById($ids);
            $zip = new \ZipArchive();
            $file = 'Archivos-'.time().".zip";
            $filename = $file;
            $zip->open(dirname(dirname(dirname(__DIR__))).'/web/downloads/' . $file,  \ZipArchive::CREATE);
            foreach ($medias as $media) {
                $directory = $em->getRepository('MediaBundle:Media')->getPathArray($media);
                $fileToAdd = dirname(dirname(dirname(__DIR__))).'/web/downloads/' . join('/', $directory);
                $zip->addFromString(basename($fileToAdd),  file_get_contents($fileToAdd)); 
            }
            $zip->close();
        }

        $filePath = dirname(dirname(dirname(__DIR__))).'/web/downloads/' . $file;

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return $response;
    }
}
