<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/register', name: 'user_register')]
    public function register(Request $request, Connection $connection, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $errorMsg = '';
        $database = $connection->getDatabase();
        $query = "SELECT r.user_id FROM $database.users_roles r WHERE r.role_id = 1";
        $stmt = $connection->prepare($query);
        $result = $stmt->executeQuery();
        $users = $result->fetchAllAssociative();
        
        if (count($users) > 0) {
            return $this->redirectToRoute("security_login");
        } else {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                    $user->setPassword($hashedPassword);
                    
                    $role = $entityManager->getRepository(Role::class)->findOneBy(['name' => 'ADMIN']);
                    if ($role) {
                        $user->addUserRole($role);
                    }
                    
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute("security_login");
                } catch (\Exception $e) {
                    $errorMsg = "Please fill in all fields correctly";
                    return $this->render('users/register.html.twig', [
                        "registerForm" => $form->createView(), 
                        'errorMsg' => $errorMsg
                    ]);
                }
            }

            return $this->render('users/register.html.twig', [
                "registerForm" => $form->createView(), 
                'errorMsg' => $errorMsg
            ]);
        }
    }

    #[Route('/profile/{id}', name: 'user_profile')]
    public function profile(int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        if ($user === null) {
            return $this->redirectToRoute('users');
        }
        
        $evaluations = $entityManager->getRepository(Evaluation::class)->findBy(["recipient" => $id]);
        $giveEvaluation = $entityManager->getRepository(Evaluation::class)->findBy(["authorId" => $id]);

        return $this->render('users/profile.html.twig', [
            "user" => $user, 
            "evaluations" => $evaluations, 
            "giveEvaluation" => $giveEvaluation
        ]);
    }

    #[Route('/profile/{id}/edit', name: 'user_edit')]
    public function editUser(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);
        
        if ($user === null) {
            return $this->redirectToRoute('homepage');
        }

        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || (!$currentUser->isAdmin() && $currentUser->getId() !== $id)) {
            return $this->redirectToRoute('homepage');
        }
        
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute("users");
        }

        // Ако формата е изпратена, но има грешки, те ще се покажат автоматично
        return $this->render('users/editUser.html.twig', [
            "user" => $user,
            "userForm" => $form->createView()
        ]);
    }

    #[Route('/register/new', name: 'newuser_register')]
    public function createNewUser(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $errorMsg = "";
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);
                
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute("users");
            } catch (\Exception $e) {
                $errorMsg = "Please fill in all fields correctly";
                return $this->render('users/register.html.twig', [
                    "registerForm" => $form->createView(), 
                    'errorMsg' => $errorMsg
                ]);
            }
        }

        return $this->render('users/registerUser.html.twig', [
            "userForm" => $form->createView(), 
            'errorMsg' => $errorMsg
        ]);
    }
}
