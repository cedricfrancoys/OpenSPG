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
 * @Route("/productor/propiedad")
 */
class PropertyController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
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
            ->select('p, v')
            ->leftJoin('p.Member', 'm')
            ->leftJoin('m.User', 'u')
            ->leftJoin('p.Visits', 'v')
            ->where('u.Node = :node')
            ->setParameter('node', $currentMember->getNode())
            ->addOrderBy('v.visitDate', 'DESC');

        if($filter = $request->get('filter')){
            if ($filter['Member']) {
                $sql->andWhere('u.id = :user')
                    ->setParameter('user', $filter['Member']);
            }
        }
            
        $query = $sql->getQuery();
        $properties = $query->getResult();

        $manager = $this->get('users.manager.user');
        $users = $manager->getUsersByRole(\UserBundle\Entity\User::ROLE_PRODUCER);

        return array(
            'properties' => $properties,
            'users' => $users,
            'menu' => 'management'
        );
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGER')")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        if(false){
            $name = $producer->getUser()->getName() . ' ' . $producer->getUser()->getSurname();
            $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
            $breadcrumbs->addItem($name, $this->get("router")->generate("management_producer_edit", array('id'=>$producer->getId())));
        }
        $breadcrumbs->addItem('Properties', $this->get("router")->generate("management_property_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_producer_add"));

        $em = $this->getDoctrine()->getManager();

        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property, array('node'=>$this->getUser()->getNode()));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($property);

            if ($property->getDocumentFile()) {
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $uploadableManager->markEntityToUpload($property, $property->getDocumentFile());
            }

            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The property has been added!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_property_index');
            }else{
                $url = $this->generateUrl('management_property_edit', array('id'=>$property->getId()));
            }
            return new RedirectResponse($url);
        }

        return array(
            'form' => $form->createView(),
            'menu' => 'management'
        );
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGER')")
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

        $form = $this->createForm(PropertyType::class, $property, array('node'=>$this->getUser()->getNode()));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($property);
            $em->flush();

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_property_index');
            }else{
                $url = $this->generateUrl('management_property_edit', array('id'=>$property->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView(),
            'menu' => 'management'
        );
    }

    /**
     * @Route("/{id}/delete")
     * @Security("has_role('ROLE_MANAGER')")
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
                'confirmation_key' => $confirmation_key,
                'menu' => 'management'
            );
        }
    }
}
