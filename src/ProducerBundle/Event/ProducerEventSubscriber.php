<?php
namespace ProducerBundle\Event;
 
use Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Manager\LogManager;

class ProducerEventSubscriber
{
    protected $em;
    protected $mailer;
    protected $twig;
    protected $translator;
    protected $log;

    public function onProducerCreated(Event $event)
    {
        $this->log->log('ProducerEventSubscriber:onProducerCreated', 'Execting listener...');

        $producer = $event->getProducer();

        $subscribers = $this->getSubscribedUsers();

        foreach ($subscribers as $subscriber) {
            $this->sendEmail($subscriber, $producer);
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
       * Set the log manager
       *
       * @param logManager $log
       * @return void
       */
    public function setLogManager(LogManager $log) {
        $this->log = $log;
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

        return $em->getRepository('UserBundle:User')->findBy(array('receiveEmailNewProducer'=>true));
    }

    protected function sendEmail($subscriber, $producer)
    {
        $trans = $this->translator;
        $tpl = $this->twig;

        $producerUser = $producer->getUser();

        $message = \Swift_Message::newInstance()
            ->setSubject($trans->trans('New.producer.email.subject', array(), 'user'))
            ->setFrom('noreply@raac.tobeonthe.net')
            ->setTo($subscriber->getEmail())
            ->setBody(
                $tpl->render(
                    'UserBundle:Emails:newProducer.html.twig',
                    array(
                        'name' => $subscriber->getName(),
                        'producer_name' => $producerUser->getName() . ' ' . $producerUser->getSurname(),
                        'producer_id' => $producer->getId(),
                        'profile_path' => ($subscriber->getProducer()) ? 'producer_member_profile' : 'consumer_member_profile'
                    )
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);

        $this->log->log('ProducerEventSubscriber:sendEmail', 'Sent email to ' . $subscriber->getEmail());
    }
}