<?php

namespace SoftUProjectBundle\Controller;

use SoftUProjectBundle\Entity\Role;
use SoftUProjectBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
        $users=$this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render('roles/allroles.html.twig', array('users'=>$users ));
    }

    /**
     * @Route("/profile/{id}/edit/roles", )
     * @param Request $request
     * @param $
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editRole(Request $request, int $id){
        $currentUser= $this->getUser();
        if(!$currentUser->isAdmin()){
            return $this->redirectToRoute('homepage');
        }
        $user=$this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if($user === null) {
            return $this->redirectToRoute('homepage');
        }
        $roles=$this->getDoctrine()->getRepository(Role::class)->findAll();


        return $this->render('roles/editrole.html.twig', array('user'=>$user, 'roles'=>$roles ));
    }

    /**
     * @Route("/profile/{id}/add/roles/{role}", )
     * @param Request $request
     * @param int $id
     * @param string $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addRole(Request $request, int $id, string $role){
        $currentUser= $this->getUser();
        if(!$currentUser->isAdmin()){
            return $this->redirectToRoute('homepage');
        }
        $user=$this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if($user === null) {
            return $this->redirectToRoute('homepage');
        }
        $newRole=$this->getDoctrine()->getRepository(Role::class)->findOneBy(array('name'=>$role));
        $user->addRole($newRole);
        $em= $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('roles');
    }
}
