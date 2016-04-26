<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ProductBundle\Entity\Product;
use ProductBundle\Entity\ProductGroup;
use ProductBundle\Entity\Family;
use ProductBundle\Entity\Variety;
use ManagementBundle\Form\ProductType;

/**
 * @Route("/producto")
 */
class ProductController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("management_product_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser());

        $sql = $em
            ->getRepository('ProductBundle:Product')
            ->createQueryBuilder('p')
            ->select('p,f,g,v')
            ->leftJoin('p.Group', 'g')
            ->leftJoin('p.Family', 'f')
            ->leftJoin('p.Variety', 'v')
        ;

        if($filter = $request->request->get('filter')){
            $filter = array_merge(
                array(
                    'family' => 0,
                    'group' => 0,
                    'variety' => 0
                ),
                $filter
            );
            if ($filter['family']) {
                $sql->andWhere('f.id = :family')
                    ->setParameter('family', $filter['family']);
            }
            if ($filter['group']) {
                $sql->andWhere('g.id = :group')
                    ->setParameter('group', $filter['group']);
            }
            if ($filter['variety']) {
                $sql->andWhere('v.id = :variety')
                    ->setParameter('variety', $filter['variety']);
            }
        }

        $query = $sql->getQuery();
        $products = $query->getResult();

        $families = $em->getRepository('ProductBundle:Family')->findAll();
        $groups = $em->getRepository('ProductBundle:ProductGroup')->findAll();
        $varieties = $em->getRepository('ProductBundle:Variety')->findAll();

        $data = array(
            'products' => $products,
            'families' => $families,
            'groups' => $groups,
            'varieties' => $varieties,
        );

        return $data;
    }

    /**
     * @Route("/getFamilies/{group}", options={"expose"=true})
     * @Security("has_role('ROLE_MANAGEMENT')")
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
     * @Security("has_role('ROLE_MANAGEMENT')")
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

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Stock", $this->get("router")->generate("management_stock_index"));
        $breadcrumbs->addItem("Add product", $this->get("router")->generate("management_product_add"));

        $em = $this->getDoctrine()->getManager();

        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($product);
            $em->flush();

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_product_index');
            }else{
                $url = $this->generateUrl('management_product_edit', array('id'=>$product->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Product:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Product $product, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Stock", $this->get("router")->generate("management_stock_index"));
        $breadcrumbs->addItem("Edit product", $this->get("router")->generate("management_product_edit",array('id'=>$product->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($product);
            $em->flush();

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_product_index');
            }else{
                $url = $this->generateUrl('management_product_edit', array('id'=>$product->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Product:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
