<?php

namespace SoftUProjectBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use SoftUProjectBundle\Entity\Evaluation;
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
        $evaluaions=[];
        $giveEvaluaions=[];
        $currentUser = $this->getUser();
        if($currentUser !== null){
            $currentUserId=$currentUser->getId();
                $evaluaions=$this->getDoctrine()->getRepository(Evaluation::class)->findBy(array("recipient"=>$currentUserId), array("courseid"=>'DESC'));
            $giveEvaluaions=$this->getDoctrine()->getRepository(Evaluation::class)->findBy(array("authorId"=>$currentUserId), array("courseid"=>'ASC', "dateAdded"=>'DESC'));
        }


        return $this->render('default/dashboard.html.twig', array('evaluations'=>$evaluaions, 'giveEvaluation'=>$giveEvaluaions));
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
