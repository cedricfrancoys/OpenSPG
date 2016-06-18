<?php
namespace ProducerBundle\Manager;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use \Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormError;

use Symfony\Component\Form\Exception\OutOfBoundsException;

use ProducerBundle\Entity\Member as Producer;

use ProducerBundle\Event\ProducerEvent;

class ProducerManager
{
	/**
	* @var EntityManager
	*/
	private $orm;

	/**
	* @var Producer
	*/
	private $producer;

	/**
	* @var User
	*/
	private $currentUser;

	/**
	* @var Request
	*/
	private $request;

	/**
	* @var DataCollectorTranslator
	*/
	private $translator;

	/**
	* @var Twig
	*/
	private $twig;

	/**
	* @var Object
	*/
	private $mailer;

	protected $eventDispatcher;

	/**
	* @param EntityManager $orm
	* @param TokenStorage $token
	*/
	public function __construct(EntityManager $orm, TokenStorage $token, $eventDispatcher, $mailer, \Twig_Environment $twig, TranslatorInterface $trans,RequestStack $request) {
		$this->orm = $orm;
		$this->currentUser = $token->getToken()->getUser();
		$this->eventDispatcher = $eventDispatcher;
		$this->mailer = $mailer;
		$this->twig = $twig;
		$this->translator = $trans;
		$this->request = $request->getCurrentRequest();
	}

  	/**
   	* Set the producer
   	*
   	* @param Producer $producer
   	* @return void
   	*/
  	public function setProducer(Producer $producer) {
    	$this->producer = $producer;
  	}

  	/**
   	* Returns the producer
   	*
   	* @return Producer
   	*/
  	public function getProducer() {
    	return $this->producer;
  	}

  	/**
   	* Set the current user
   	*
   	* @param user $user
   	* @return void
   	*/
  	public function setCurrentUser(User $user) {
    	$this->currentUser = $user;
  	}	

	public function setApproved()
  	{
		$wereApproved = $this->producer->getActiveAsProducer();
      	$this->producer->setActiveAsProducer(true);
      	$this->orm->persist($this->producer);
      	$this->orm->flush();

      	$event = new ProducerEvent($this->producer, 'event');
      	$dispatcher = $this->eventDispatcher; 

      	if (!$wereApproved) {
        	$dispatcher->dispatch('producer.events.producerApproved', $event);
      	}
  	}
}