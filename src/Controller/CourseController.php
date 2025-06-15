<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Evaluation;
use App\Form\CourseType;
use App\Form\DeleteConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CourseController extends AbstractController
{
    #[Route('/courses', name: 'courses')]
    public function showCourses(EntityManagerInterface $entityManager): Response
    {
        $courses = $entityManager->getRepository(Course::class)->findAll();
        
        if (empty($courses)) {
            return $this->redirectToRoute('course_create');
        }
        
        return $this->render('courses/showall.html.twig', ['courses' => $courses]);
    }

    #[Route('/courses/create', name: 'course_create')]
    public function createCourse(Request $request, EntityManagerInterface $entityManager): Response
    {
        $errorMsg = '';
        $course = new Course();
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if course with same name already exists
            $existingCourse = $entityManager->getRepository(Course::class)
                ->findOneBy(['name' => $course->getName()]);
            
            if ($existingCourse) {
                $errorMsg = "The course: {$course->getName()} already exists! Course name must be unique!";
                return $this->render('courses/create.html.twig', [
                    'courseForm' => $form->createView(), 
                    'errorMsg' => $errorMsg
                ]);
            }

            try {
                $entityManager->persist($course);
                $entityManager->flush();
                return $this->redirectToRoute("courses");
            } catch (\Exception $e) {
                $errorMsg = "Please fill in all fields correctly! Course name must be unique!";
                return $this->render('courses/create.html.twig', [
                    'courseForm' => $form->createView(), 
                    'errorMsg' => $errorMsg
                ]);
            }
        }

        return $this->render('courses/create.html.twig', [
            'courseForm' => $form->createView(), 
            'errorMsg' => $errorMsg
        ]);
    }

    #[Route('/courses/{id}/edit', name: 'course_edit')]
    public function editCourse(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $errorMsg = '';
        $course = $entityManager->getRepository(Course::class)->find($id);
        
        if ($course === null) {
            return $this->redirectToRoute('courses');
        }

        $currentUser = $this->getUser();
        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('courses');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if another course with same name already exists
            $existingCourse = $entityManager->getRepository(Course::class)
                ->createQueryBuilder('c')
                ->where('c.name = :name AND c.id != :id')
                ->setParameter('name', $course->getName())
                ->setParameter('id', $course->getId())
                ->getQuery()
                ->getOneOrNullResult();
            
            if ($existingCourse) {
                $errorMsg = "The course: {$course->getName()} already exists! Course name must be unique!";
                return $this->render('courses/edit.html.twig', [
                    'courseForm' => $form->createView(), 
                    'errorMsg' => $errorMsg, 
                    'course' => $course
                ]);
            }

            try {
                $entityManager->persist($course);
                $entityManager->flush();
                return $this->redirectToRoute("courses");
            } catch (\Exception $e) {
                $errorMsg = 'Something went wrong!';
                return $this->render('courses/edit.html.twig', [
                    'courseForm' => $form->createView(), 
                    'errorMsg' => $errorMsg, 
                    'course' => $course
                ]);
            }
        }
        
        return $this->render('courses/edit.html.twig', [
            'courseForm' => $form->createView(), 
            'errorMsg' => $errorMsg, 
            'course' => $course
        ]);
    }

    #[Route('/courses/{id}', name: 'course_show')]
    public function showOneCourse(int $id, EntityManagerInterface $entityManager): Response
    {
        $course = $entityManager->getRepository(Course::class)->find($id);
        
        if ($course === null) {
            return $this->redirectToRoute('courses');
        }

        $isEmpty = $this->isEmpty($id, $entityManager);
        // Use optimized method with eager loading to avoid N+1 queries
        $evaluations = $entityManager->getRepository(Evaluation::class)->findByCourseWithRelations($id);

        return $this->render('courses/showone.html.twig', [
            'course' => $course,
            'evaluations' => $evaluations,
            'isEmpty' => $isEmpty
        ]);
    }

    #[Route('/courses/{id}/delete', name: 'course_delete')]
    public function deleteCourse(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $errorMsg = '';
        $currentUser = $this->getUser();
        $course = $entityManager->getRepository(Course::class)->find($id);
        
        if ($course === null || !$this->isEmpty($id, $entityManager)) {
            return $this->redirectToRoute('courses');
        }
        
        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('courses');
        }

        $form = $this->createForm(DeleteConfirmationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->remove($course);
                $entityManager->flush();
            } catch (\Exception $e) {
                $errorMsg = 'Възникна грешка при изтриването на курса!';
                return $this->render('courses/delete.html.twig', [
                    'course' => $course,
                    'deleteForm' => $form->createView(),
                    'errorMsg' => $errorMsg
                ]);
            }
            return $this->redirectToRoute('courses');
        }

        return $this->render('courses/delete.html.twig', [
            'course' => $course,
            'deleteForm' => $form->createView(),
            'errorMsg' => $errorMsg
        ]);
    }

    private function isEmpty(int $id, EntityManagerInterface $entityManager): bool
    {
        $evaluations = $entityManager->getRepository(Evaluation::class)->findBy(['courseid' => $id]);
        return count($evaluations) === 0;
    }
}
