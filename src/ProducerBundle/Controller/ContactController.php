<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Contact;
use ProducerBundle\Form\ContactType;
use ProducerBundle\Entity\Stock;
use ProducerBundle\Entity\Member;

/**
 * @Route("/productores/contacto")
 */
class ContactController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Products', $this->get('router')->generate('product_public_index'));
        $breadcrumbs->addItem('Contact', $this->get('router')->generate('producer_contact_index'));

        $em = $this->getDoctrine()->getManager();

        $contact = new Contact();

        if ($request->get('stock_id')) {
            $stock = $em->getRepository('ProducerBundle:Stock')->find($request->get('stock_id'));
            $contact->setReceiver($stock->getProducer()->getUser());
            // $contact->Stock = $stock->getId();
        } else {
            $stock = null;
        }
        if ($request->get('producer_id')) {
            $producer = $em->getRepository('ProducerBundle:Member')->find($request->get('producer_id'));
            $contact->setReceiver($producer->getUser());
        } else {
            $producer = null;
        }

        $form = $this->createForm(ContactType::class, $contact);

        if ($request->get('stock_id')) {
            // $form->get('Receiver')->setData($stock->getProducer()->getUser()->getId());
            $form->get('Stock')->setData($stock->getId());
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $contact->setReceived(new \DateTime());
            $em->persist($contact);
            $em->flush();

            $this->sendReceivedMail($contact, $stock, $producer);

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('Your contact request has been sent!', array(), 'contact')
            );

            $url = $this->generateUrl('app_contact_index');
            $response = new RedirectResponse($url);

            return $response;
        }

        $data = array(
            'form' => $form->createView(),
            'menu' => 'producer',
            'stock' => $stock,
        );

        return $this->render('ProducerBundle:Contact:index.html.twig', $data);
    }

    protected function sendReceivedMail(Contact $contact, Stock $stock = null, Member $producer = null)
    {
        $trans = $this->get('translator');

        $subject = $trans->trans('New contact request received', array(), 'contact');
        if ($stock) {
            $receiver = $stock->getProducer()->getUser()->getEmail();
        } elseif ($producer) {
            $receiver = $producer->getUser()->getEmail();
        }
        if (!$receiver) {
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('hello@raac.es')
            ->setTo($receiver)
            ->setBody(
                $this->renderView(
                    'ProducerBundle:Contact:producer_mail.html.twig',
                    array(
                        'contact' => $contact,
                        'stock' => $stock,
                    )
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
    }
}
