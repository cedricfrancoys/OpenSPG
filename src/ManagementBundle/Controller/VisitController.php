<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ProducerBundle\Entity\Visit;
use ManagementBundle\Form\VisitType;

/**
 * @Route("/management/visits")
 */
class VisitController extends Controller
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
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('MemberBundle:Member')->findOneBy(array('User'=>$this->getUser()));

        $visits = $em
            ->getRepository('ProducerBundle:Visit')
            ->createQueryBuilder('v')
            ->select('v,p,m')
            ->leftJoin('v.Producer', 'p')
            ->leftJoin('p.Member', 'm')
            ->andWhere('m.Node = :node')
            ->setParameter('node', $currentMember->getNode())
            ->getQuery()
            ->getResult();

        $data = array(
            'visits' => $visits
        );

        return $this->render('ManagementBundle:Visit:index.html.twig', $data);
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
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_visit_add"));

        $em = $this->getDoctrine()->getManager();

        $visit = new Visit();
        $visit->setVisitDate(new \DateTime());
        $visit->setStartTime(new \DateTime());
        $endTime = new \DateTime();
        $endTime->add(new \DateInterval('PT4H'));
        $visit->setEndTime($endTime);

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($visit);
            $em->flush();

            $url = $this->generateUrl('management_visit_edit', array('id'=>$visit->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Visit:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Visit $visit, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_visit_edit",array('id'=>$visit->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($visit);
            $em->flush();

            $url = $this->generateUrl('management_visit_edit', array('id'=>$visit->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Visit:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
