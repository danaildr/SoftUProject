<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Evaluation;
use App\Entity\User;
use App\Form\EvaluationType;
use App\Form\DeleteConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EvaluationController extends AbstractController
{
    #[Route('/evaluations', name: 'all_evaluation')]
    public function showEvaluations(EntityManagerInterface $entityManager): Response
    {
        $evaluations = $entityManager->getRepository(Evaluation::class)->findAll();
        
        return $this->render('evaluation/showevaluations.html.twig', [
            'evaluations' => $evaluations
        ]);
    }

    #[Route('/evaluation/{id}', name: 'evaluation_show')]
    public function showEvaluation(int $id, EntityManagerInterface $entityManager): Response
    {
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);
        
        if ($evaluation === null) {
            return $this->redirectToRoute('all_evaluation');
        }

        return $this->render('evaluation/evalluation.html.twig', [
            'evaluation' => $evaluation
        ]);
    }

    #[Route('/evaluation/create/{studentId}', name: 'evaluation_create')]
    public function createEvaluation(Request $request, int $studentId, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser || !$currentUser->isTeacher()) {
            return $this->redirectToRoute('homepage');
        }

        $student = $entityManager->getRepository(User::class)->find($studentId);
        if ($student === null) {
            return $this->redirectToRoute('users');
        }

        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setTeacher($currentUser);
            $evaluation->setStudent($student);
            $evaluation->setAuthorId($currentUser->getId());
            $evaluation->setRecipient($student->getId());
            $evaluation->setCourseid($evaluation->getCourse()->getId());

            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('all_evaluation');
        }

        return $this->render('evaluation/create.html.twig', [
            'evform' => $form->createView(),
            'currentStudent' => $student
        ]);
    }

    #[Route('/evaluation/{id}/edit', name: 'evaluation_edit')]
    public function editEvaluation(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);
        
        if ($evaluation === null) {
            return $this->redirectToRoute('all_evaluation');
        }

        $currentUser = $this->getUser();
        if (!$currentUser || ($evaluation->getAuthorId() !== $currentUser->getId() && !$currentUser->isAdmin())) {
            return $this->redirectToRoute('all_evaluation');
        }

        $form = $this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evaluation->setCourseid($evaluation->getCourse()->getId());
            
            $entityManager->persist($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('all_evaluation');
        }

        return $this->render('evaluation/edit.html.twig', [
            'evform' => $form->createView(),
            'evaluation' => $evaluation
        ]);
    }

    #[Route('/evaluation/{id}/delete', name: 'evaluation_delete')]
    public function deleteEvaluation(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $evaluation = $entityManager->getRepository(Evaluation::class)->find($id);
        
        if ($evaluation === null) {
            return $this->redirectToRoute('all_evaluation');
        }

        $currentUser = $this->getUser();
        if (!$currentUser || ($evaluation->getAuthorId() !== $currentUser->getId() && !$currentUser->isAdmin())) {
            return $this->redirectToRoute('all_evaluation');
        }

        $form = $this->createForm(DeleteConfirmationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->remove($evaluation);
            $entityManager->flush();

            return $this->redirectToRoute('all_evaluation');
        }

        return $this->render('evaluation/delete.html.twig', [
            'deleteForm' => $form->createView(),
            'evaluation' => $evaluation
        ]);
    }
}
