<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ProducerBundle\Entity\Member;
use ManagementBundle\Form\ProducerType;

/**
 * @Route("/management/producer")
 */
class ProducerController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('MemberBundle:Member')->findOneBy(array('User'=>$this->getUser()));

        $producers = $em
            ->getRepository('ProducerBundle:Member')
            ->createQueryBuilder('p')
            ->select('p,m')
            ->leftJoin('p.Member', 'm')
            ->where('p.Member IS NOT NULL')
            ->andWhere('m.Node = :node')
            ->setParameter('node', $currentMember->getNode())
            ->getQuery()
            ->getResult();

        $data = array(
            'producers' => $producers
        );

        return $this->render('ManagementBundle:Producer:index.html.twig', $data);
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
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_producer_add"));

        $em = $this->getDoctrine()->getManager();

        $producer = new Member();

        $form = $this->createForm(ProducerType::class, $producer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($producer);
            $em->flush();

            $url = $this->generateUrl('management_producer_edit', array('id'=>$producer->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Producer:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Member $producer, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_producer_edit",array('id'=>$producer->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProducerType::class, $producer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($producer);
            $em->flush();

            $url = $this->generateUrl('management_producer_edit', array('id'=>$producer->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Producer:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
