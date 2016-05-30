<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpFoundation\BinaryFileResponse;
use \Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/documentos")
 */
class DocumentController extends Controller
{
    protected $files;

    public function __construct()
    {
        $this->files = array(
            array(
                'name' => 'Declaración de compromiso',
                'description' => '',
                'file' => 'declaracion_compromiso.pdf',
                'updated' => new \DateTime('17.04.2016')
            ),
            array(
                'name' => 'Reglamento',
                'description' => '',
                'file' => 'reglamento.pdf',
                'updated' => new \DateTime('17.04.2016')
            ),
            array(
                'name' => 'Ficha de entrada para consumidores',
                'description' => '',
                'file' => 'ficha_entrada_consumidor.pdf',
                'updated' => new \DateTime('17.04.2016')
            ),
            array(
                'name' => 'Ficha de entrada para productores',
                'description' => '',
                'file' => 'ficha_entrada_productor.pdf',
                'updated' => new \DateTime('17.04.2016')
            ),
            array(
                'name' => 'Guía de visita',
                'description' => '',
                'file' => 'guia_de_visita.pdf',
                'updated' => new \DateTime('17.04.2016')
            ),
            array(
                'name' => 'Logo (Fichero GIMP)',
                'description' => '',
                'file' => 'logo.xcf',
                'updated' => new \DateTime('30.05.2016')
            ),
            array(
                'name' => 'Logo (Fichero PNG)',
                'description' => '',
                'file' => 'logo.png',
                'updated' => new \DateTime('30.05.2016')
            )
        );
    }

    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Documents", $this->get("router")->generate("app_document_index"));

        $em = $this->getDoctrine()->getManager();

        $data = array(
            'files' => $this->files
        );

        return $this->render('AppBundle:Document:index.html.twig', $data);
    }

    /**
     * @Route("/{file}")
     */
    public function downloadAction($file)
    {
        $filePath = dirname(dirname(dirname(__DIR__))).'/web/downloads/' . $file;

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}
