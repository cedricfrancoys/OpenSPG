<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use ProducerBundle\Entity\Property;
use ManagementBundle\Form\PropertyType;

use ProducerBundle\Entity\Member;

/**
 * @Route("/producer/property")
 */
class PropertyController extends Controller
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
        // if($producer){
        //     $name = $producer->getUser()->getName() . ' ' . $producer->getUser()->getSurname();
        //     $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        //     $breadcrumbs->addItem($name, $this->get("router")->generate("management_producer_edit", array('id'=>$producer->getId())));
        // }
        $breadcrumbs->addItem('Properties', $this->get("router")->generate("management_property_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $sql = $em
            ->getRepository('ProducerBundle:Property')
            ->createQueryBuilder('p')
            ->select('p')
            ->leftJoin('p.Member', 'm')
            ->leftJoin('m.User', 'u')
            ->where('u.Node = :node')
            ->setParameter('node', $currentMember->getNode());

        // if ($producer) {
        //     $sql
        //         ->where('p.Member = :producer')
        //         ->setParameter('producer', $producer);
        // }

        if($filter = $request->get('filter')){
            if ($filter['Member']) {
                $sql->andWhere('u.id = :user')
                    ->setParameter('user', $filter['Member']);
            }
        }
            
        $query = $sql->getQuery();
        $properties = $query->getResult();

        $manager = $this->get('users.manager.user');
        $users = $manager->getUsersByRole('ROLE_PRODUCER');

        return array(
            'properties' => $properties,
            // 'producer' => $producer,
            'users' => $users
        );
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     * @ParamConverter("producer", class="ProducerBundle:Member", options={"id" = "producer_id"})
     */
    public function addAction(Member $producer, Request $request)
    {
        $name = $producer->getUser()->getName() . ' ' . $producer->getUser()->getSurname();
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem($name, $this->get("router")->generate("management_producer_edit", array('id'=>$producer->getId())));
        $breadcrumbs->addItem('Properties', $this->get("router")->generate("management_property_index", array('producer_id'=>$producer->getId())));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_producer_add"));

        $em = $this->getDoctrine()->getManager();

        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $property->setMember($producer);
            $em->persist($property);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The property has been added!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_property_edit', array('producer_id'=>$producer->getId(), 'id'=>$property->getId())));
        }

        return array(
            'form' => $form->createView(),
            'producer' => $producer
        );
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function editAction(Property $property, Request $request)
    {
        // $name = $producer->getUser()->getName() . ' ' . $producer->getUser()->getSurname();
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        // $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        // $breadcrumbs->addItem($name, $this->get("router")->generate("management_producer_edit", array('id'=>$producer->getId())));
        $breadcrumbs->addItem('Properties', $this->get("router")->generate("management_property_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_producer_edit", array('id'=>0)));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PropertyType::class, $property);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($property);
            $em->flush();

            $url = $this->generateUrl('management_property_edit', array('id'=>$property->getId(), 'producer_id'=>$producer->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/delete")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     * @ParamConverter("producer", class="ProducerBundle:Member", options={"id" = "producer_id"})
     */
    public function deleteAction(Member $producer, Property $property, Request $request)
    {
        $name = $producer->getUser()->getName() . ' ' . $producer->getUser()->getSurname();
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem($name, $this->get("router")->generate("management_producer_edit", array('id'=>$producer->getId())));
        $breadcrumbs->addItem('Properties', $this->get("router")->generate("management_property_index", array('producer_id'=>$producer->getId())));
        $breadcrumbs->addItem("Delete", $this->get("router")->generate("management_producer_delete", array('producer_id'=>0, 'id'=>0)));

        if (!$producer) {
            throw $this->createNotFoundException('No producer found');
        }
        if (!$property) {
            throw $this->createNotFoundException('No property found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/property/delete')){
            $session->remove('confirmation/management/property/delete');

            if ($this->getUser()->getNode() !== $producer->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($property);
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The property has been deleted!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_property_index', array('producer_id'=>$producer->getId())));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/property/delete', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key
            );
        }
    }
}
