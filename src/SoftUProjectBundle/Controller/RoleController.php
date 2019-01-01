<?php

namespace SoftUProjectBundle\Controller;

use SoftUProjectBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class RoleController extends Controller
{
    /**
     * @Route("/roles", name="roles")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAllUser()
    {

    }

    /**
     * @Route("/profile/{id}/edit/roles", name="user_roles")
     * @param int $id
     */
    public function seRole(int $id){
        $user=$this->getDoctrine()->getRepository(User::class)->find($id);


    }
}
