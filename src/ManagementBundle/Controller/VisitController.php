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
use ProducerBundle\Entity\Member;

use ProducerBundle\Event\VisitEvent;

/**
 * @Route("/visits")
 */
class VisitController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function indexAction(Request $request)
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser());

        $sql = $em
            ->getRepository('ProducerBundle:Visit')
            ->createQueryBuilder('v')
            ->select('v,p')
            ->leftJoin('v.Producer', 'p')
            ->leftJoin('p.User', 'u')
            ->leftJoin('v.Property', 'pr')
            ->andWhere('u.Node = :node')
            ->setParameter('node', $currentMember->getNode());

        if($filter = $request->get('filter')){
            $filter = array_merge(
                array(
                    'producer' => 0,
                    'property' => 0
                ),
                $filter
            );
            if ($filter['producer']) {
                $sql->andWhere('u.id = :user')
                    ->setParameter('user', $filter['producer']);
            }
            if ($filter['property']) {
                $sql->andWhere('pr.id = :property')
                    ->setParameter('property', $filter['property']);
            }
        }

        $query = $sql->getQuery();
        $visits = $query->getResult();

        $manager = $this->get('users.manager.user');
        $producers = $manager->getUsersByRole('ROLE_PRODUCER');
        $properties = $em
            ->getRepository('ProducerBundle:Property')
            ->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.Member', 'm')
            ->leftJoin('m.User', 'u')
            ->andWhere('u.Node = :node')
            ->setParameter('node', $currentMember->getNode())
            ->getQuery()
            ->getResult();

        $data = array(
            'visits' => $visits,
            'producers' => $producers,
            'properties' => $properties,
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Visit:index.html.twig', $data);
    }

    /**
     * @Route("/add")
     * @Route("/producer/{producer_id}/add", name="management_visit_add2")
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

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($visit);

            if ($visit->getDocumentFile()) {
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $uploadableManager->markEntityToUpload($visit, $visit->getDocumentFile());
            }

            $em->flush();

            $event = new VisitEvent($visit, 'add');
            $dispatcher = $this->get('event_dispatcher'); 
            $dispatcher->dispatch('producer.events.visitPersisted', $event);
            if ($visit->getAccepted() !== null) {
                $dispatcher->dispatch('producer.events.visitCompleted', $event);
            }

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_visit_index');
            }else{
                $url = $this->generateUrl('management_visit_edit', array('id'=>$visit->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Visit:add.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
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

        $visitAccepted = $visit->getAccepted();

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($visit);

            if ($visit->getDocumentFile()) {
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $uploadableManager->markEntityToUpload($visit, $visit->getDocumentFile());
            }

            $em->flush();

            $event = new VisitEvent($visit, 'edit');
            $dispatcher = $this->get('event_dispatcher'); 
            $dispatcher->dispatch('producer.events.visitPersisted', $event);
            if ($visit->getAccepted() !== null && $visitAccepted === null) {
                $dispatcher->dispatch('producer.events.visitCompleted', $event);
            }

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_visit_index');
            }else{
                $url = $this->generateUrl('management_visit_edit', array('id'=>$visit->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Visit:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }
}
