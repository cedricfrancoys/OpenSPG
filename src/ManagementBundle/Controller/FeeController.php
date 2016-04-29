<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use FeeBundle\Entity\Fee;
use ManagementBundle\Form\FeeType;

/**
 * @Route("/fees")
 */
class FeeController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Fee", $this->get("router")->generate("management_fee_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $fees = $em
            ->getRepository('FeeBundle:Fee')
            ->createQueryBuilder('f')
            ->select('f')
            ->leftJoin('f.User', 'u')
            ->where('u.Node = :node')
            ->setParameter('node', $currentMember->getNode())
            ->getQuery()
            ->getResult();

        $manager = $this->get('users.manager.user');
        $users = $manager->getUsersByRole('ROLE_MEMBER');

        $data = array(
            'fees' => $fees,
            'users' => $users
        );

        return $data;
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Fee", $this->get("router")->generate("management_fee_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_fee_add"));

        $em = $this->getDoctrine()->getManager();

        $fee = new Fee();
        $fee->setStartDate(new \DateTime());
        $fee->setCode('member:anual');
        $fee->setName('Cuota anual');
        $fee->setAmount(10);

        $form = $this->createForm(FeeType::class, $fee);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($fee);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The fee has been added!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_fee_index');
            }else{
                $url = $this->generateUrl('management_fee_edit', array('id'=>$fee->getId()));
            }
            return new RedirectResponse($url);
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function editAction(Fee $fee, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Fee", $this->get("router")->generate("management_fee_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_fee_edit",array('id'=>$fee->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(feeType::class, $fee);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($fee);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The fee has been updated!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_fee_index');
            }else{
                $url = $this->generateUrl('management_fee_edit', array('id'=>$fee->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/markPaid")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function markPaidAction(Fee $fee, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Fees", $this->get("router")->generate("management_fee_index"));
        $breadcrumbs->addItem("Mark as paid", $this->get("router")->generate("management_fee_markpaid",array('id'=>$fee->getId())));

        if (!$fee) {
            throw $this->createNotFoundException('Fee not found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/fee/markPaid')){
            $session->remove('confirmation/management/fee/markPaid');

            $user = $fee->getUser();
            if ($user->getNode() !== $this->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $fee->setStatus(Fee::STATUS_PAID);

            $em = $this->getDoctrine()->getManager();
            $em->persist($fee);
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The fee has been marked as paid!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_fee_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/fee/markPaid', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key
            );
        }
    }
}
