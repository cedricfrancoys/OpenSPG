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
        $roles = $user->getRoles();
        if(in_array('ROLE_MANAGER', $roles)){
            return $this->redirectToRoute('management_default_index');
        }else if(in_array('ROLE_PRODUCER', $roles)){
            return $this->redirectToRoute('producer_member_index');
        }else if(in_array('ROLE_CONSUMER', $roles)){
            return $this->redirectToRoute('consumer_member_index');
        }

        throw new AccessDeniedException();
    }
}
