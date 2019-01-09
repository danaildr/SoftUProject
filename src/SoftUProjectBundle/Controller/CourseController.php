<?php

namespace SoftUProjectBundle\Controller;

use SoftUProjectBundle\Entity\Course;
use SoftUProjectBundle\Entity\Evaluation;
use SoftUProjectBundle\Form\CourseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends Controller
{
    /**
     * @Route("/courses", name="courses")
     */
    public function showCourses(){
        $courses=$this->getDoctrine()->getRepository(Course::class)->findAll();
        return $this->render('courses/showall.html.twig', array('courses'=>$courses));
    }

    /**
     * @Route("/courses/create", name="course_create")
     * @param $name
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createCourse(Request $request)
    {
        $errorMsg='';
        $course=new Course();
        $courses = $this->getDoctrine()->getRepository(Course::class)->findAll();
        $form= $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        foreach($courses as $value){
            if($value->getName()== $course->getName()){
                $existCourse = $course->getName();
                $errorMsg ="Tne course: $existCourse exist! Please fill in all fields correctly! Course name must be uniqe!";
                return $this->render('courses/create.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg));
            }
        }
        if($form->isSubmitted()){
            try{
                $em= $this->getDoctrine()->getManager();
                $em->persist($course);
                $em->flush();
                return $this->redirectToRoute("courses");
            }catch (\Exception $e){
                $errorMsg ="Please fill in all fields correctly! Course name must be uniqe!";
                return $this->render('courses/create.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg));
            };
        }

        return $this->render('courses/create.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg));
    }

    /**
     * @Route("/courses/{id}/edit")
     *
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editCourse(Request $request, int $id){
        $errorMsg='';
        $course=$this
            ->getDoctrine()
            ->getRepository(Course::class)
            ->find($id);
        if($course == null){
            return $this->redirectToRoute('courses');
        }
        $currentUser= $this->getUser();
        $courses = $this->getDoctrine()->getRepository(Course::class)->findAll();
        if(!$currentUser->isAdmin() && !$currentUser->getId() === $id){
            return $this->redirectToRoute('courses');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        foreach($courses as $value){
            if($value->getId() !=$course->getId()){
                if($value->getName()== $course->getName()){
                    $existCourse = $course->getName();
                    $errorMsg ="Tne course: $existCourse exist! Please fill in all fields correctly! Course name must be uniqe!";
                    return $this->render('courses/edit.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg, 'course'=>$course));
                }
            }

        }
        if($form->isSubmitted()){
            try{
                $em= $this->getDoctrine()->getManager();
                $em->persist($course);
                $em->flush();
                return $this->redirectToRoute("courses");
            }catch (\Exception $e){
                $errorMsg='Something went wrong!';
                return $this->render('courses/edit.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg, 'course'=>$course));
            }
        }
        return $this->render('courses/edit.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg, 'course'=>$course));
    }

    /**
     * @Route("/courses/{id}")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showOneCourse(int $id){
        $course=$this->getDoctrine()->getRepository(Course::class)->find($id);
        $isEmpty=$this->isEmpty($id);
        $evaluations=$this->getDoctrine()->getRepository(Evaluation::class)->findBy(array("courseid"=>$id));
        if($course===null){
            return $this->redirectToRoute('courses');
        }

        return $this->render('courses/showone.html.twig', array('course'=>$course, 'evaluations'=>$evaluations, 'isEmpty'=>$isEmpty));
    }

    /**
     * @Route("/courses/{id}/delete")
     * @param int $id
     */
    public function deleteCourse(Request $request, int $id){
        $errorMsg='';
        $currentUser=$this->getUser();
        $course = $this->getDoctrine()->getRepository(Course::class)->find($id);
        if($course==null){
            return $this->redirectToRoute('courses');
        }
        if(!$currentUser->isAdmin() ){
            return $this->redirectToRoute('courses');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            try{
                $em=$this->getDoctrine()->getManager();
                $em->remove($course);
                $em->flush();
            }catch (\Exception $e){
                $errorMsg = 'Something went wrong!';
                return $this->render('course/delete.html.twig', array('course'=>$course, 'deleteForm'=>$form->createView(), 'errorMsg'=>$errorMsg));
            }
            return $this->redirectToRoute('courses');
        }
        return $this->render('course/delete.html.twig', array('course'=>$course, 'deleteForm'=>$form->createView(), 'errorMsg'=>$errorMsg));

    }



    public function isEmpty($id){
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findBy(array('courseid'=>$id));
        if(count($evaluations)>0){
            return false;
        }
        return true;
    }
}
