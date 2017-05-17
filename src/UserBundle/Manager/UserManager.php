<?php

namespace UserBundle\Manager;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Translation\DataCollectorTranslator;
use Symfony\Component\Translation\TranslatorInterface;

use UserBundle\Entity\User;
use UserBundle\Util\CanonicalFieldsUpdater;

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
   * @var object
   */
  private $mailer;

  /**
   * @var Router
   */
  private $router;

  /**
   * @var string
   */
  private $resetting_from_email;

  /**
   * @var CanonicalFieldsUpdater
   */
  private $canonicalFieldsUpdater;  

  /**
   * @param EntityManager $orm
   * @param TokenStorage  $token
   * @param string $resetting_from_email
   */
  public function __construct(EntityManager $orm, TokenStorage $token, $resetting_from_email, CanonicalFieldsUpdater $canonicalFieldsUpdater)
  {
      $this->orm = $orm;
      $this->currentUser = $token->getToken()->getUser();
      $this->resetting_from_email = $resetting_from_email;
      $this->canonicalFieldsUpdater = $canonicalFieldsUpdater;
  }

  /**
   * Set the user.
   *
   * @param User $user
   */
  public function setUser(User $user)
  {
      $this->user = $user;
  }

  /**
   * Returns the user.
   *
   * @return User
   */
  public function getUser()
  {
      return $this->user;
  }

  /**
   * Set the current user.
   *
   * @param user $user
   */
  public function setCurrentUser(User $user)
  {
      $this->currentUser = $user;
  }

  /**
   * Set the request.
   *
   * @param RequestStack $request
   */
  public function setRequest(RequestStack $request)
  {
      $this->request = $request->getCurrentRequest();
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
   * Set twig.
   *
   * @param Twig_Environment $twig
   */
  public function setTwig(\Twig_Environment $twig)
  {
      $this->twig = $twig;
  }

  /**
   * Set Mailer.
   *
   * @param object $mailer
   */
  public function setMailer($mailer)
  {
      $this->mailer = $mailer;
  }

  /**
   * Set Router.
   *
   * @param Router $router
   */
  public function setRouter($router)
  {
      $this->router = $router;
  }

  /**
   * @deprecated
   */
  public function getUsersByRole($role, $makeSureFieldIsNotNull = false, $makeSureFieldIsNull = false)
  {
      return $this
      ->orm
      ->getRepository('UserBundle:User')
      ->getUsersByRole($role, $this->currentUser->getNode(), $makeSureFieldIsNotNull, $makeSureFieldIsNull)
    ;
  }

    public function getAll()
    {
        return $this->orm
      ->getRepository('UserBundle:User')
      ->getAll();
    }

    public function createUser(User $user, $form, array $formData, array $roles, $activate = false)
    {
        if ($this->checkIsDuplicate($formData)) {
            try {
                $form->get('username')->addError(new FormError('The username already exists'));
            } catch (\OutOfBoundsException $e) {
                $form->get('User')->get('username')->addError(new FormError($this->translator->trans('The username already exists', array(), 'user')));
            }

            return false;
        }

    // @ToDo Registartion init
    // $event = new GetResponseUserEvent($user, $this->request);
    // $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
    // if (null !== $event->getResponse()) {
    //     return $event->getResponse();
    // }

    // @ToDo On user registartion success
    // $event = new FormEvent($form, $this->request);
    // $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);
    // $pUser = (array_key_exists('User', $formData)) ? $formData['User'] : $formData;

    if (is_array($pUser['password'])) {
        $user->setPlainPassword($pUser['password']['first']);
    } else {
        $user->setPlainPassword($pUser['password']);
    }
        foreach ($roles as $role) {
            $user->addRole($role);
        }
        if ($this->currentUser instanceof User) {
            $user->setNode($this->currentUser->getNode());
        }
        if ($activate) {
            $user->setEnabled(true);
        }

        $this->orm->persist($user);
        $this->orm->flush();

        if (isset($pUser['sendEmail']) && $pUser['sendEmail']) {
            $this->sendPasswordEmail($pUser);
        }

        return true;
    }

    private function checkIsDuplicate($data)
    {
        $username = (array_key_exists('User', $data)) ? $data['User']['username'] : $data['username'];
        $items = $this->orm->getRepository('UserBundle:User')->findBy(array('username' => $username));

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
                    'enabled' => $user['enabled'],
                )
            ),
            'text/html'
        )
    ;
        $this->mailer->send($message);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserByEmail($email)
    {
        return $this->orm->getRepository('UserBundle:User')->findOneBy(array('emailCanonical' => $this->canonicalFieldsUpdater->canonicalizeEmail($email)));
    }
    /**
     * {@inheritdoc}
     */
    public function findUserByUsername($username)
    {
        return $this->orm->getRepository('UserBundle:User')->findOneBy(array('usernameCanonical' => $this->canonicalFieldsUpdater->canonicalizeUsername($username)));
    }


    /**
     * Returns a user by either his username or his email address
     */
    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            return $this->findUserByEmail($usernameOrEmail);
        }
        return $this->findUserByUsername($usernameOrEmail);
    }

    public function generateToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function sendResettingEmailMessage(User $user)
    {
        $template = 'UserBundle:Resetting:email.txt.twig';
        $url = $this->router->generate('user_resetting_reset', array('token' => $user->getConfirmationToken()), UrlGeneratorInterface::ABSOLUTE_URL);
        $rendered = $this->twig->render($template, array(
            'user' => $user,
            'confirmationUrl' => $url,
        ));
        $this->sendEmailMessage($rendered, $this->resetting_from_email, (string) $user->getEmail());
    }

    /**
     * @param string $renderedTemplate
     * @param string $fromEmail
     * @param string $toEmail
     */
    protected function sendEmailMessage($renderedTemplate, $fromEmail, $toEmail)
    {
        // Render the email, use the first line as the subject, and the rest as the body
        $renderedLines = explode("\n", trim($renderedTemplate));
        $subject = array_shift($renderedLines);
        $body = implode("\n", $renderedLines);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body);
        $this->mailer->send($message);
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->orm->getRepository('UserBundle:User')->findOneBy(array('confirmationToken' => $token));
    }

    public function updateCanonicalFields(User $user)
    {
      $user->setUsernameCanonical($this->canonicalize($user->getUsername()));
      $user->setEmailCanonical($this->canonicalize($user->getEmail()));
    }

    protected function canonicalize($string)
    {
        if (null === $string) {
            return null;
        }

        $encoding = mb_detect_encoding($string);
        $result = $encoding
            ? mb_convert_case($string, MB_CASE_LOWER, $encoding)
            : mb_convert_case($string, MB_CASE_LOWER);

        return $result;
    }

    public function hashPassword(User $user, $encoder)
    {
        $plainPassword = $user->getPassword();

        if (0 === strlen($plainPassword)) {
            dump('no plain password given');
            return;
        }

        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
//         $user->eraseCredentials();
    }
}
