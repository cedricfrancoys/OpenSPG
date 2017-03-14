<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccountController extends Controller
{
    /**
     * @Route("/account")
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        if (null === $user) {
            throw new AccessDeniedException();
        }
        $roles = $user->getRoles();
        if (in_array(\UserBundle\Entity\User::ROLE_PRODUCER, $roles)) {
            return $this->redirectToRoute('producer_member_index');
        } elseif (in_array(\UserBundle\Entity\User::ROLE_CONSUMER, $roles)) {
            return $this->redirectToRoute('consumer_member_index');
        } elseif (in_array(\UserBundle\Entity\User::ROLE_MANAGER, $roles) || in_array(\UserBundle\Entity\User::ROLE_ADMIN, $roles)) {
            return $this->redirectToRoute('management_default_index');
        }

        throw new AccessDeniedException();
    }
}
