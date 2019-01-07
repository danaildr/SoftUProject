<?php

namespace SoftUProjectBundle\Controller;

use Doctrine\DBAL\Connection;
use SoftUProjectBundle\Entity\Evaluation;
use SoftUProjectBundle\Entity\Role;
use SoftUProjectBundle\Entity\User;
use SoftUProjectBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, Connection $connection)
    {
        $errorMsg='';
        $database=$this->getDoctrine()->getConnection()->getDatabase();
        $query= "select r.user_id from $database.users_roles r  WHERE  r.role_id =1";
        $stmt=$connection->prepare($query);
        $stmt->execute();
        $users=$stmt->fetchAll();
        if(count($users)>0){
            return $this->redirectToRoute("security_login");
        }else{
            $user=new User();
            $form =  $this->createForm( UserType::class, $user);
            $form->handleRequest($request);
            if($form->isSubmitted()){
                try{
                    $password= $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getPassword());
                    $user->setPassword($password);
                    $role=$this->getDoctrine()->getRepository(Role::class)->findOneBy(['name'=>'ROLES_ADMIN']);
                    $user->addRole($role);
                    $em= $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    return $this->redirectToRoute("security_login");
                }catch (\Exception $e){
                    $errorMsg = "Please fill in all fields correctly";
                    return $this->render('users/register.html.twig',["registerForm"=> $form->createView(), 'errorMsg'=>$errorMsg]);
                }
            }

            return $this->render('users/register.html.twig',["registerForm"=> $form->createView(), 'errorMsg'=>$errorMsg]);
        }
    }

    /**
     * @Route("/profile/{id}", name="user_profile")
     */
    public function profile(int $id){
        $user=$this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if($user===null){
            $this->redirectToRoute('users');
        }
        $evaluations=$this->getDoctrine()->getRepository(Evaluation::class)->findBy(["recipient"=>$id]);
        $giveEvaluation=$this->getDoctrine()->getRepository(Evaluation::class)->findBy(["authorId"=>$id]);

        return $this->render('users/profile.html.twig', ["user"=> $user, "evaluations"=>$evaluations, "giveEvaluation"=>$giveEvaluation]);
    }

    /**
     * @Route("/profile/{id}/edit")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editUser(Request $request, int $id){

        $user=$this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
        if($user === null){
            return $this->redirectToRoute('homepage');
        }

        $currentUser= $this->getUser();
        if(!$currentUser->isAdmin() && !$currentUser->getId() === $id){
            return $this->redirectToRoute('homepage');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("users");
        }
        return $this->render('users/editUser.html.twig', ["user"=>$user, "userForm"=>$form->createView()]);
    }


    /**
     * @Route("/register/new", name="newuser_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createNewUser(Request $request){
        $errorMsg ="";
        $user=new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            try{
                $password= $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $em= $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute("users");
            }catch (\Exception $e){
                $errorMsg = "Please fill in all fields correctly";
                return $this->render('users/register.html.twig',["registerForm"=> $form->createView(), 'errorMsg'=>$errorMsg]);
            }
        }

        return $this->render('users/registerUser.html.twig', [ "userForm"=>$form->createView(), 'errorMsg'=>$errorMsg]);
    }



}
