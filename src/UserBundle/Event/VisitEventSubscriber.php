<?php
namespace UserBundle\Event;
 
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Translation\TranslatorInterface;

class VisitEventSubscriber
{
    protected $em;
    protected $mailer;
    protected $twig;
    protected $translator;

    public function onVisitSaved(Event $event)
    {
        $visit = $event->getVisit();
        $action = $event->getAction();

        if ($action == 'add') {
            $subscribers = $this->getSubscribedUsers();
            foreach ($subscribers as $subscriber) {
                $this->sendEmail($subscriber, $visit);
            }
        }
    }

    public function onVisitCompleted(Event $event)
    {
        $visit = $event->getVisit();

        $subscribers = $this->getCompletedSubscribedUsers();
        foreach ($subscribers as $subscriber) {
            $this->sendEmailCompleted($subscriber, $visit);
        }
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }
    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }
    /**
       * Set the translator
       *
       * @param TranslatorInterface $trans
       * @return void
       */
    public function setTranslator(TranslatorInterface $trans) {
        $this->translator = $trans;
    }   
    /**
       * Set twig
       *
       * @param Twig_Environment $twig
       * @return void
    */
    public function setTwig(\Twig_Environment $twig) {
        $this->twig = $twig;
    }

    protected function getSubscribedUsers()
    {
        $em = $this->em;

        return $em->getRepository('UserBundle:User')->findBy(array('receiveEmailNewVisit'=>true));
    }

    protected function sendEmail($subscriber, $visit)
    {
        $trans = $this->translator;
        $tpl = $this->twig;

        $producer = $visit->getProducer();
        $visitUser = $producer->getUser();
        $property = $visit->getProperty();

        $message = \Swift_Message::newInstance()
            ->setSubject($trans->trans('New.visit.email.subject', array(), 'user'))
            ->setFrom('hello@raac.tobeonthe.net')
            ->setTo($subscriber->getEmail())
            ->setBody(
                $tpl->render(
                    'UserBundle:Emails:newVisit.html.twig',
                    array(
                        'name' => $subscriber->getName(),
                        'visit' => $visit,
                        'producer' => $producer,
                        'producer_name' => $visitUser->getName().' '.$visitUser->getSurname(),
                        'property' => $property,
                        'profile_path' => ($subscriber->getProducer()) ? 'producer_member_profile' : 'consumer_member_profile'
                    )
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    protected function getCompletedSubscribedUsers()
    {
        $em = $this->em;

        return $em->getRepository('UserBundle:User')->findBy(array('receiveEmailCompletedVisit'=>true));
    }

    protected function sendEmailCompleted($subscriber, $visit)
    {
        $trans = $this->translator;
        $tpl = $this->twig;

        $producer = $visit->getProducer();
        $visitUser = $producer->getUser();
        $property = $visit->getProperty();

        $message = \Swift_Message::newInstance()
            ->setSubject($trans->trans('Completed.visit.email.subject', array(), 'user'))
            ->setFrom('hello@raac.tobeonthe.net')
            ->setTo($subscriber->getEmail())
            ->setBody(
                $tpl->render(
                    'UserBundle:Emails:completedVisit.html.twig',
                    array(
                        'name' => $subscriber->getName(),
                        'visit' => $visit,
                        'producer' => $producer,
                        'producer_name' => $visitUser->getName().' '.$visitUser->getSurname(),
                        'property' => $property,
                        'profile_path' => ($subscriber->getProducer()) ? 'producer_member_profile' : 'consumer_member_profile'
                    )
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}