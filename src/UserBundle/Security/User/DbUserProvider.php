<?php

namespace UserBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use UserBundle\Entity\User;

class DbUserProvider implements UserProviderInterface
{
    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository('UserBundle:User')->findOneByUsername($username);

        if ($user) {
            return $user;
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'UserBundle\Security\User\DbUser';
    }
}