<?php

namespace SoftUProjectBundle\Controller;

use SoftUProjectBundle\Entity\Evaluation;
use SoftUProjectBundle\Entity\User;
use SoftUProjectBundle\Form\EvaluationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends Controller
{

    /**
     * @Route("/evaluation/create/{id}", name="evaluation_create")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, int $id)
    {
        $evaluation = new Evaluation();
        $currentUser=$this->getUser();
        if($currentUser->isTeacher()){
            $form=$this->createForm(EvaluationType::class, $evaluation);
            $form->handleRequest($request);
            $currentStudent=$this->getDoctrine()->getRepository(User::class)->find($id);
            if($form->isSubmitted() && $form->isValid()){

                $currentTeacher=$this->getUser();
                $evaluation->setCourseid($evaluation->getCourse()->getId());
                // $evaluation->setCourse();
                $evaluation->setTeacher($currentTeacher);
                $evaluation->setAuthorId($currentTeacher->getId());
                $evaluation->setStudent($currentStudent);
                $evaluation->setRecipient($currentStudent->getId());

                $em=$this->getDoctrine()->getManager();
                $em->persist($evaluation);
                $em->flush();

                return $this->redirectToRoute("all_evaluation");
            }

            return $this->render('evaluation/create.html.twig', ["evform"=>$form->createView(), 'currentStudent'=>$currentStudent]);
        }
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/evaluation", name="all_evaluation")
     */
    public function showAllEvalution(){
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findBy(array(),array("dateAdded"=>'DESC', 'course'=>'DESC'));
        return $this->render("evaluation/showevaluations.html.twig", ["evaluations"=>$evaluations]);
    }

    /**
     * @Route("/evaluation/{id}")
     * @param int $id
     */
    public function showOneEvaluation(int $id){
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class)->find($id);

        return $this->render('evaluation/evalluation.html.twig', array('evaluation'=>$evaluation));

    }

    /**
     * @Route("/evaluation/{id}/edit", name="edit_evaluation")
     *
     *
     */
    public function editEvaluation(Request $request, int $id)
    {
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class)->find($id);
        $currentUser=$this->getUser();
        if($currentUser->isTeacher()){
            $form=$this->createForm(EvaluationType::class, $evaluation);
            $form->handleRequest($request);
            $currentStudent=$this->getDoctrine()->getRepository(User::class)->find($id);
            if($form->isSubmitted() && $form->isValid()){

                $currentTeacher=$this->getUser();
                $evaluation->setCourseid($evaluation->getCourse()->getId());
                // $evaluation->setCourse();
//                $evaluation->setTeacher($currentTeacher);
//                $evaluation->setAuthorId($currentTeacher->getId());
//                $evaluation->setStudent($currentStudent);
//                $evaluation->setRecipient($currentStudent->getId());

                $em=$this->getDoctrine()->getManager();
                $em->persist($evaluation);
                $em->flush();

                return $this->redirectToRoute("all_evaluation");
            }

            return $this->render('evaluation/edit.html.twig', ['evaluation'=>$evaluation,"evform"=>$form->createView(), 'currentStudent'=>$currentStudent]);
        }

        return $this->render('evaluation/edit.html.twig');
    }
}
