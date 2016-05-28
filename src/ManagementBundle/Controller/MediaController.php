<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use MediaBundle\Entity\Media;

use MediaBundle\Form\MediaType;

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

        $media = $em->getRepository('MediaBundle:Media')->findAll();

        $item = new Media();

        $uploadForm = $this->createForm(MediaType::class, $item, array(
            'action' => $this->generateUrl('management_media_upload'),
            'validation_groups' => array('Default','upload')
        ));

        $data = array(
            'media' => $media,
            'uploadForm' => $uploadForm->createView(),
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Media:index.html.twig', $data);
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

        $item = new Media();

        $uploadForm = $this->createForm(MediaType::class, $item, array(
            'action' => $this->generateUrl('management_media_upload'),
            'validation_groups' => array('Default','upload')
        ));

        $uploadForm->handleRequest($request);

        if ($uploadForm->isSubmitted() && $uploadForm->isValid()) {
            $em->persist($item);
            $em->flush();

            $url = $this->generateUrl('management_media_index');
            $response = new RedirectResponse($url);

            return $response;
        }

        $data = array(
            'media' => $media,
            'uploadForm' => $uploadForm->createView(),
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Media:index.html.twig', $data);
    }
}
