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
        $form= $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $em= $this->getDoctrine()->getManager();
            $em->persist($course);
            $em->flush();
            return $this->redirectToRoute("courses");
        }

        return $this->render('courses/create.html.twig', array('courseForm' => $form->createView(), 'errorMsg'=>$errorMsg));
    }

    /**
     * @Route("/courses/{id}")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showOneCourse(int $id){
        $course=$this->getDoctrine()->getRepository(Course::class)->find($id);
        $evaluations=$this->getDoctrine()->getRepository(Evaluation::class)->findBy(array("courseid"=>$id));
        if($course===null){
            $errorMsg = "Not found this course!";
            return $this->render('default/dashboard.html.twig', array('errorMsg'=>$errorMsg));
        }

        return $this->render('courses/showone.html.twig', array('course'=>$course, 'evaluations'=>$evaluations));
    }
}
