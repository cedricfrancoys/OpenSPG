<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ProductBundle\Entity\ProductGroup;
use ProductBundle\Form\BaseProductGroupType;

/**
 * @Route("/producto/grupo")
 */
class ProductGroupController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexAction(Request $request){

        $em = $this->getDoctrine()->getManager();

        $groups = $em->createQuery('SELECT g.id, g.name FROM ProductBundle:ProductGroup g')->getArrayResult();

        return new JsonResponse($groups);
    }

    /**
     * @Route("/create")
     * @Security("has_role('ROLE_MANAGER')")
     * @Method({"POST"})
     */
    public function createAction(Request $request)
    {
        $name = $request->request->get('name');

        $notBlankValidator = new Assert\NotBlank();
        $notBlankValidator->message = 'The value can not be empty';

        $errors = $this->get('validator')->validate(
            $name,
            $notBlankValidator
        );

        if (0 != count($errors)) {
            return new JsonResponse(array(
                'status' => 'ko',
                'msg' => $errors[0]->getMessage()
            ),
            400);
        }

        $group = new ProductGroup();
        $group->setName($name);

        $em = $this->getDoctrine()->getManager();
        $em->persist($group);
        $em->flush();

        return new JsonResponse(
            array(
                'status' => 'ok',
                'data' => array(
                    'id' => $group->getId(),
                    'name' => $group->getName()
                )
            )
        );
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
        $breadcrumbs->addItem('ProductGroup');
        $breadcrumbs->addItem('Add group', $this->get('router')->generate('management_productgroup_add'));

        $em = $this->getDoctrine()->getManager();

        $group = new ProductGroup();

        $form = $this->createForm(BaseProductGroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($group);
            $em->flush();

            $response = new JsonResponse(
                array(
                    'status' => 'ok',
                    'data' => array(
                        'id' => $group->getId(),
                        'name' => $group->getName()
                    )
                )
            );

            return $response;
        }

        throw new \Exception('Error Processing Request', 1);
    }
}
