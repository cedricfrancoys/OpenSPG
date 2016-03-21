<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use ProducerBundle\Entity\Member;
use ManagementBundle\Form\ProducerType;

/**
 * @Route("/management/producer/{producer_id}/property")
 */
class PropertyController extends Controller
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

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $producers = $em
            ->getRepository('UserBundle:User')
            ->createQueryBuilder('u')
            ->select('p,u')
            ->leftJoin('u.Producer', 'p')
            ->where('u.Producer IS NOT NULL')
            ->andWhere('u.Node = :node')
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
            /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');
            $user = $userManager->createUser();
            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
            $pUser = $request->request->get('producer');
            $pUser = $pUser['User'];
            $user->setEmail($pUser['email']);
            $user->setPlainPassword($pUser['password']);
            $user->setUsername($pUser['username']);
            $user->setName($pUser['name']);
            $user->setSurname($pUser['surname']);
            $user->setPhone($pUser['phone']);
            $user->addRole('ROLE_MEMBER');
            $user->addRole('ROLE_CONSUMER');
            $user->addRole('ROLE_PRODUCER');
            $user->setNode($this->getUser()->getNode());
            $user->setProducer($producer);
            $userManager->updateUser($user);

            $em->persist($producer);
            $em->flush();

            if ($pUser['sendEmail']) {
                $this->sendPasswordEmail($pUser);
            }

            $url = $this->generateUrl('management_producer_edit', array('id'=>$producer->getId()));
            $response = new RedirectResponse($url);

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

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

    /**
     * @Route("/{id}/delete")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function deleteAction(Member $producer, Request $request)
    {}

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
