<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evaluations = [];
        $giveEvaluations = [];
        $currentUser = $this->getUser();
        if ($currentUser !== null) {
            $currentUserId = $currentUser->getId();
            $evaluations = $entityManager->getRepository(Evaluation::class)->findBy(
                ["recipient" => $currentUserId], 
                ["courseid" => 'DESC']
            );
            $giveEvaluations = $entityManager->getRepository(Evaluation::class)->findBy(
                ["authorId" => $currentUserId], 
                ["courseid" => 'ASC', "dateAdded" => 'DESC']
            );
        }

        return $this->render('default/dashboard.html.twig', [
            'evaluations' => $evaluations, 
            'giveEvaluation' => $giveEvaluations
        ]);
    }

    #[Route('/users', name: 'users')]
    public function showUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        $admins = [];
        $teachers = [];
        $students = [];
        $usersWithoutRoles = [];
        $usersWithOtherRoles = [];

        foreach ($users as $user) {
            $stringRoles = $user->getStringRoles();
            $hasStandardRole = false;
            $hasOtherRole = false;

            foreach ($stringRoles as $role) {
                if ($role === "ADMIN") {
                    $admins[] = $user;
                    $hasStandardRole = true;
                }
                if ($role === "STUDENT") {
                    $students[] = $user;
                    $hasStandardRole = true;
                }
                if ($role === "TEACHER") {
                    $teachers[] = $user;
                    $hasStandardRole = true;
                }
                // Проверяваме за други роли (не ADMIN, TEACHER, STUDENT)
                if (!in_array($role, ['ADMIN', 'TEACHER', 'STUDENT'])) {
                    $hasOtherRole = true;
                }
            }

            // Ако потребителят има други роли, но не стандартните
            if ($hasOtherRole && !$hasStandardRole) {
                $usersWithOtherRoles[] = $user;
            }

            // Ако потребителят няма никакви роли
            if (empty($stringRoles)) {
                $usersWithoutRoles[] = $user;
            }
        }

        return $this->render("users/showall.html.twig", [
            "admins" => $admins,
            "teachers" => $teachers,
            "students" => $students,
            "usersWithoutRoles" => $usersWithoutRoles,
            "usersWithOtherRoles" => $usersWithOtherRoles
        ]);
    }

    #[Route('/help', name: 'help')]
    public function help(): Response
    {
        return $this->render('default/help.html.twig');
    }
}
