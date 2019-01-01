<?php

namespace SoftUProjectBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use SoftUProjectBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/dashboard.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/users", name="users"))
     */
    public function showUsers(){

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $admins=[];
        $teachers=[];
        $students=[];
        foreach ($users as $user){
            $roles=$user->getRoles();
            foreach ($roles as $role){
                if($role === "ROLES_ADMIN"){
                    $admins[]=$user;
                }
                if($role === "ROLES_STUDENT"){
                    $students[]=$user;
                }
                if($role === "ROLES_TEACHER"){
                    $teachers[]=$user;
                }
            }
        }
        return $this->render("users/showall.html.twig", ["admins"=>$admins, "teachers"=>$teachers, "students"=>$students]);
    }
}
