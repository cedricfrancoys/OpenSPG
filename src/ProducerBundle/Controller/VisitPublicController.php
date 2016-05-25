<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Visit;
use ProducerBundle\Entity\Member;
use ProducerBundle\Entity\RejectApproval;

use ProducerBundle\Form\VisitType;
use ProducerBundle\Form\SignMeUpType;
use ProducerBundle\Form\RejectApprovalType;

/**
 * @Route("/visitas")
 */
class VisitPublicController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visitpublic_index"));

        $em = $this->getDoctrine()->getManager();

        $visits = $em->
            getRepository('ProducerBundle:Visit')
            ->createQueryBuilder('v')
            ->select('v')
            ->leftJoin('v.Producer', 'p')
            ->leftJoin('p.User', 'u')
            ->leftJoin('v.Property', 'pr')
            ->leftJoin('pr.Member', 'm2')
            ->leftJoin('m2.User', 'u2')
            ->getQuery()
            ->getResult();

        return $this->render('ProducerBundle:VisitPublic:index.html.twig', array(
            'visits' => $visits,
            'menu' => 'producer'
        ));
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Visit $visit, Request $request)
    {
        $producer = $visit->getProducer();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visitpublic_index"));
        $breadcrumbs->addItem("Show", $this->get("router")->generate("producer_visitpublic_show", array('id'=>$visit->getId())));

        return $this->render('ProducerBundle:VisitPublic:show.html.twig', array(
            'visit' => $visit,
            'menu' => 'producer'
        ));
    }

    /**
     * @Route("/{id}/rejectApproval")
     */
    public function rejectApprovalAction(Visit $visit, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visitpublic_index"));
        $breadcrumbs->addItem("Reject approval", $this->get("router")->generate("producer_visitpublic_rejectapproval", array('id'=>$visit->getId())));

        $rejectApproval = new RejectApproval();
        $rejectApproval->setUser($this->getUser());
        $form = $this->createForm(RejectApprovalType::class, $rejectApproval);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $rejectApproval->setVisit($visit);

            $em->persist($rejectApproval);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('Your rejection has been recorded!', array(), 'visit')
            );

            $url = $this->generateUrl('producer_visitpublic_show', array('id'=>$visit->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:VisitPublic:rejectApproval.html.twig', array(
            'form' => $form->createView(),
            'visit' => $visit,
            'menu' => 'producer'
        ));
    }
}
