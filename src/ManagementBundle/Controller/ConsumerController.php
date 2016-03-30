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

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $manager = $this->get('users.manager.user');
        $consumers = $manager->getUsersByRole('ROLE_CONSUMER');

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
            $manager = $this->get('users.manager.user');
            $manager->setCurrentUser($this->getUser());
            $userCreated = $manager->createUser($consumer, $form, $request->request->get('consumer'), array('ROLE_MEMBER','ROLE_CONSUMER'));
            if($userCreated)
            {
                $session = $this->get('session');
                $trans = $this->get('translator');

                // add flash messages
                $session->getFlashBag()->add(
                    'success',
                    $trans->trans('The consumer data has been updated!', array(), 'management')
                );

                $url = $this->generateUrl('management_consumer_edit', array('id'=>$user->getId()));
                $response = new RedirectResponse($url);

                return $response;
            }
        }

        return $this->render('ManagementBundle:Consumer:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template
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

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The consumers data has been updated!', array(), 'management')
            );

            $url = $this->generateUrl('management_consumer_edit', array('id'=>$consumer->getId()));
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
     */
    public function deleteAction(Member $consumer, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem("Delete", $this->get("router")->generate("management_producer_delete",array('id'=>$producer->getId())));

        if (!$producer) {
            throw $this->createNotFoundException('No producer found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/producer/delete')){
            $session->remove('confirmation/management/producer/delete');

            if ($this->getUser()->getNode() !== $producer->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($producer);
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The producer has been deleted!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_producer_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/producer/delete', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key
            );
        }
    }

    protected function sendPasswordEmail($user){
        $trans = $this->get('translator');
        $tpl = $this->get('twig');

        $message = \Swift_Message::newInstance()
            ->setSubject($trans->trans('Your account on SPG', array(), 'user'))
            ->setFrom('mhauptma73@gmail.com')
            ->setTo($user['email'])
            ->setBody(
                $tpl->render(
                    'UserBundle:Emails:registration.html.twig',
                    array(
                        'password' => $user['password'],
                        'name' => $user['name'],
                        'surname' => $user['surname'],
                        'username' => $user['username'],
                        'phone' => $user['phone'],
                        'email' => $user['email'],
                        'enabled' => $user['enabled']
                    )
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
    }
}
