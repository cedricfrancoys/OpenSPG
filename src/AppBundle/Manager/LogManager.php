<?php

namespace AppBundle\Manager;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use AppBundle\Entity\Log;

class LogManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var User
     */
    private $user;

    /**
     * @param EntityManager $orm
     * @param TokenStorage  $token
     */
    public function __construct(EntityManager $orm, TokenStorage $token)
    {
        $this->em = $orm;
        $this->user = $token->getToken()->getUser();
    }

    public function log($action, $message)
    {
        $log = new Log();
        $log->setAction($action);
        $log->setMessage($message);
        if ($this->user) {
            $log->setUser($this->user->getUsername());
        } else {
            $log->setUser('ANONYMOUS');
        }

        $this->em->persist($log);
        $this->em->flush();
    }
}
