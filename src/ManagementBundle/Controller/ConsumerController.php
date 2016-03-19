<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ConsumerBundle\Entity\Member;
use ManagementBundle\Form\ConsumerType;

/**
 * @Route("/management/consumer")
 */
class ConsumerController extends Controller
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
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->findOneBy(array('User'=>$this->getUser()));

        $consumers = $em
            ->getRepository('ConsumerBundle:Member')
            ->createQueryBuilder('p')
            ->select('p,m')
            ->leftJoin('p.User', 'u')
            ->where('p.User IS NOT NULL')
            ->andWhere('u.Node = :node')
            ->setParameter('node', $currentMember->getNode())
            ->getQuery()
            ->getResult();

        $data = array(
            'consumers' => $consumers
        );

        return $this->render('ManagementBundle:Consumer:index.html.twig', $data);
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
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_consumer_add"));

        $em = $this->getDoctrine()->getManager();

        $consumer = new Member();

        $form = $this->createForm(ConsumerType::class, $consumer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($consumer);
            $em->flush();

            $url = $this->generateUrl('management_consumer_edit', array('id'=>$consumer->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Consumer:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Member $consumer, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_consumer_edit",array('id'=>$consumer->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConsumerType::class, $consumer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($consumer);
            $em->flush();

            $url = $this->generateUrl('management_consumer_edit', array('id'=>$consumer->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Consumer:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
