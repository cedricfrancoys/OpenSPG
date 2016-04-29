<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductGroup;
use ProductBundle\Entity\Family;
use ProductBundle\Entity\Variety;
use ProducerBundle\Form\ProductType;
use ProducerBundle\Form\GroupType;
use ProducerBundle\Form\FamilyType;
use ProducerBundle\Form\VarietyType;

/**
 * @Route("/members/producer/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/add")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Stocks", $this->get("router")->generate("producer_stock_index"));
        $breadcrumbs->addItem("Add product", $this->get("router")->generate("producer_product_add"));

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $groupForm = $this->createForm(GroupType::class, null);
        $familyForm = $this->createForm(FamilyType::class, null);
        $varietyForm = $this->createForm(VarietyType::class, null);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $product->setProducer($member);

            $em->persist($product);
            $em->flush();

            $url = $this->generateUrl('producer_stock_index');
            $response = new RedirectResponse($url);

            return $response;
        }

        $groups = $em->
            getRepository('ProductBundle:ProductGroup')
            ->createQueryBuilder('g')
            ->select('g')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $families = array();
        $varieties = array();

        return $this->render('ProducerBundle:Product:add.html.twig', array(
            'form' => $form->createView(),
            'groupForm' => $groupForm->createView(),
            'familyForm' => $familyForm->createView(),
            'varietyForm' => $varietyForm->createView(),
            'groups' => json_encode($groups),
            'families' => json_encode($families),
            'varieties' => json_encode($varieties),
            'group_get_path' => $this->generateUrl('product_group_get'),
            'group_add_path' => $this->generateUrl('product_group_add'),
            'family_get_path' => $this->generateUrl('product_family_get'),
            'family_add_path' => $this->generateUrl('product_family_add'),
            'variety_get_path' => $this->generateUrl('product_variety_get'),
            'variety_add_path' => $this->generateUrl('product_variety_add'),
            'product_add_path' => $this->generateUrl('product_product_add')
        ));
    }

    /**
     * @Route("/addGroup", options={"expose":true})
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function addGroupAction(Request $request){

        $group = new ProductGroup();
        $form = $this->createForm(GroupType::class, $group);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            $response = new JsonResponse(array(
                'id' => $group->getId(),
                'name' => $group->getName()
            ));

            return $response;
        }

        throw new \Exception($form->getErrors());
    }

    /**
     * @Route("/addFamily", options={"expose":true})
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function addFamilyAction(Request $request){
        $family = new Family();
        $form = $this->createForm(FamilyType::class, $family);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($family);
            $em->flush();

            $response = new JsonResponse(array(
                'id' => $family->getId(),
                'name' => $family->getName()
            ));

            return $response;
        }

        throw new \Exception($form->getErrors());
    }

    /**
     * @Route("/addVariety", options={"expose":true})
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function addVarietyAction(Request $request){}

    /**
     * @Route("/getFamilies/{group}", options={"expose"=true})
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function getFamiliesAction(Request $request, ProductGroup $group)
    {
        
        $em = $this->getDoctrine()->getManager();

        $families = $em->
            getRepository('ProductBundle:Family')
            ->createQueryBuilder('f')
            ->select('f')
            ->where('f.Group = :group')
            ->setParameter('group', $group)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new Response(json_encode($families));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/getVarieties/{family}", options={"expose"=true})
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function getVarietiesAction(Request $request, Family $family)
    {
        
        $em = $this->getDoctrine()->getManager();

        $varieties = $em->
            getRepository('ProductBundle:Variety')
            ->createQueryBuilder('v')
            ->select('v')
            ->where('v.Family = :family')
            ->setParameter('family', $family)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new Response(json_encode($varieties));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
