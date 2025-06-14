<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleType;
use App\Form\UserRoleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RoleController extends AbstractController
{
    #[Route('/roles', name: 'roles')]
    public function showRoles(EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $roles = $entityManager->getRepository(Role::class)->findAll();

        return $this->render('roles/allroles.html.twig', [
            'roles' => $roles
        ]);
    }

    #[Route('/roles/users', name: 'roles_users')]
    public function showUsersWithRoles(EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('roles/users_with_roles.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/roles/{id}/edit', name: 'role_edit')]
    public function editRole(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $role = $entityManager->getRepository(Role::class)->find($id);
        
        if ($role === null) {
            return $this->redirectToRoute('roles');
        }

        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($role);
            $entityManager->flush();

            return $this->redirectToRoute('roles');
        }

        return $this->render('roles/edit.html.twig', [
            'roleForm' => $form->createView(),
            'role' => $role
        ]);
    }

    #[Route('/roles/create', name: 'role_create')]
    public function createRole(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($role);
            $entityManager->flush();

            return $this->redirectToRoute('roles');
        }

        return $this->render('roles/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/roles/user/{id}/edit', name: 'user_roles_edit')]
    public function editUserRoles(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        /** @var User|null $currentUser */
        $currentUser = $this->getUser();
        if (!$currentUser || !$currentUser->isAdmin()) {
            return $this->redirectToRoute('homepage');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user === null) {
            return $this->redirectToRoute('users');
        }

        $form = $this->createForm(UserRoleType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('users');
        }

        return $this->render('roles/editUserRoles.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
