<?php

namespace ProducerBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Manager\LogManager;
use UserBundle\Entity\User;

class PropertyEventSubscriber
{
    protected $em;
    protected $mailer;
    protected $twig;
    protected $translator;
    protected $log;

    /**
     * Listener for new properties created by consumers
     *
     * @var Event $event
     *
     * @return void
     */
    public function onPropertyCreatedByConsumer(Event $event)
    {
        $this->log->log('ProducerEventSubscriber:onPropertyCreated', 'Executing listener...');

        $user = $event->getUser();

        if ($user->has_role(User::ROLE_PRODUCER)) {
            $this->log->log('ProducerEventSubscriber:onPropertyCreated', 'Already a producer... no action needed');
            return;
        }

        if ($user->has_role(User::ROLE_CONSUMER)) {
            $this->log->log('ProducerEventSubscriber:onPropertyCreated', 'Is not a consumer... will do nothing');
            return;
        }

        $user->addRole(User::ROLE_PRODUCER);

        $this->em->persist($user);
        $this->em->flush();
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
     * Set the translator.
     *
     * @param TranslatorInterface $trans
     */
    public function setTranslator(TranslatorInterface $trans)
    {
        $this->translator = $trans;
    }

    /**
     * Set the log manager.
     *
     * @param logManager $log
     */
    public function setLogManager(LogManager $log)
    {
        $this->log = $log;
    }

    /**
     * Set twig.
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
}
