<?php
namespace UserBundle\Manager;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use \Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Form\FormError;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Component\Form\Exception\OutOfBoundsException;

use UserBundle\Entity\User;

class UserManager
{
  /**
   * @var EntityManager
   */
  private $orm;

  /**
   * @var User
   */
  private $user;

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
  private $fos_user_manager;

  /**
   * @var Object
   */
  private $fos_event_dispatcher;

  /**
   * @var Object
   */
  private $mailer;

  /**
   * @param EntityManager $orm
   * @param TokenStorage $token
   */
  public function __construct(EntityManager $orm, TokenStorage $token) {
    $this->orm = $orm;
    $this->currentUser = $token->getToken()->getUser();
  }

  /**
   * Set the user
   *
   * @param User $user
   * @return void
   */
  public function setUser(User $user) {
    $this->user = $user;
  }

  /**
   * Returns the user
   *
   * @return User
   */
  public function getUser() {
    return $this->user;
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

  /**
   * Set the request
   *
   * @param RequestStack $request
   * @return void
   */
  public function setRequest(RequestStack $request) {
    $this->request = $request->getCurrentRequest();
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

  /**
   * Set FOS User Manager
   *
   * @param Object $mng
   * @return void
   */
  public function setFosUserManager($mng) {
    $this->fos_user_manager = $mng;
  }

  /**
   * Set FOS Event Dispatcher
   *
   * @param Object $dispatcher
   * @return void
   */
  public function setFosEventDispatcher($dispatcher) {
    $this->fos_event_dispatcher = $dispatcher;
  }

  /**
   * Set Mailer
   *
   * @param Object $mailer
   * @return void
   */
  public function setMailer($mailer) {
    $this->mailer = $mailer;
  }

  public function getUsersByRole($role, $makeSureFieldIsNotNull = false)
  {
    return $this
      ->orm
      ->getRepository('UserBundle:User')
      ->findUsersByRole($role, $this->currentUser->getNode(), $makeSureFieldIsNotNull)
    ;
  }

  public function getAll()
  {
    return $this->orm
      ->getRepository('UserBundle:User')
      ->createQueryBuilder('u')
      ->select('u')
      ->where('u.Node = :node')
      ->setParameter('node', $this->currentUser->getNode())
      ->getQuery()
      ->getResult();
  }

  public function createUser(User $user, $form, array $formData, array $roles)
  {
    if($this->checkIsDuplicate($formData)){
      try{
        $form->get('username')->addError(new FormError('The username already exists'));
      }catch(\OutOfBoundsException $e){
        $form->get('User')->get('username')->addError(new FormError('The username already exists'));
      }
      return false;
    }

    /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
    $userManager = $this->fos_user_manager;
    /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
    $dispatcher = $this->fos_event_dispatcher;
    $event = new GetResponseUserEvent($user, $this->request);
    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
    if (null !== $event->getResponse()) {
        return $event->getResponse();
    }

    $event = new FormEvent($form, $this->request);
    $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
    $pUser = $formData;

    $user->setPlainPassword($pUser['password']);
    foreach ($roles as $role) {
      $user->addRole($role);
    }
    $user->setNode($this->currentUser->getNode());

    $this->orm->persist($user);
    $this->orm->flush();

    if (isset($pUser['sendEmail']) && $pUser['sendEmail']) {
        $this->sendPasswordEmail($pUser);
    }

    return true;
  }

  private function checkIsDuplicate($data)
  {
    $items = $this->orm->getRepository('UserBundle:User')->findBy(array('username'=>$data['username']));
    return count($items);
  }

  protected function sendPasswordEmail($user)
  {
    $trans = $this->translator;
    $tpl = $this->twig;

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
    $this->mailer->send($message);
  }
}