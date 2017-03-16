<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ProductBundle\Entity\Famiy;
use ProductBundle\Form\BaseProductFamilyType;
use ProductBundle\Entity\ProductGroup;

/**
 * @Route("/producto/familia")
 */
class ProductFamilyController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $families = $em->createQuery('SELECT f.id, f.name FROM ProductBundle:Family f')->getArrayResult();

        return new JsonResponse($families);
    }

    /**
     * @Route("/{group}")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexGroupAction(Request $request, ProductGroup $group){

        $em = $this->getDoctrine()->getManager();

        $families = $em
            ->createQuery('SELECT f.id, f.name FROM ProductBundle:Family f WHERE f.Group = :group')
            ->setParameter('group', $group)
            ->getArrayResult();

        return new JsonResponse($families);
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Management', $this->get('router')->generate('management_default_index'));
        $breadcrumbs->addItem('ProductFamily');
        $breadcrumbs->addItem('Add family', $this->get('router')->generate('management_productfamily_add'));

        $em = $this->getDoctrine()->getManager();

        $family = new Family();

        $form = $this->createForm(BaseProductFamilyType::class, $family);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($family);
            $em->flush();

            $response = new JsonResponse(
                array(
                    'status' => 'ok',
                    'data' => array(
                        'id' => $family->getId(),
                        'name' => $family->getName()
                    )
                )
            );

            return $response;
        }

        throw new \Exception('Error Processing Request', 1);
    }
}
