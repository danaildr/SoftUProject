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
        $errorMsg='';
        $evaluation = new Evaluation();
        $currentUser=$this->getUser();
        if($currentUser->isTeacher()){
            $form=$this->createForm(EvaluationType::class, $evaluation);
            $form->handleRequest($request);
            $currentStudent=$this->getDoctrine()->getRepository(User::class)->find($id);
            if($form->isSubmitted() && $form->isValid()){

                try{
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
                }catch (\Exception $exception){
                    $errorMsg = "Please fill in all fields correctly! Evaluation value must be number, decimal separator is '.' not ',' ! Content is text!";
                    return $this->render('evaluation/create.html.twig', ["evform"=>$form->createView(), 'currentStudent'=>$currentStudent, 'errorMsg'=>$errorMsg]);
                }
            }

            return $this->render('evaluation/create.html.twig', ["evform"=>$form->createView(), 'currentStudent'=>$currentStudent, 'errorMsg'=>$errorMsg]);
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
        if($evaluation === null){
            return $this->redirectToRoute('all_evaluation');
        }
        return $this->render('evaluation/evalluation.html.twig', array('evaluation'=>$evaluation));

    }

    /**
     * @Route("/evaluation/{id}/edit", name="edit_evaluation")
     *
     *
     */
    public function editEvaluation(Request $request, int $id)
    {
        $errorMsg='';
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class)->find($id);
        if($evaluation === null){
            return $this->redirectToRoute('all_evaluation');
        }
        $currentUser=$this->getUser();
        if($currentUser->isTeacher()){
            $form=$this->createForm(EvaluationType::class, $evaluation);
            $form->handleRequest($request);
            $currentStudent=$this->getDoctrine()->getRepository(User::class)->find($id);
            if($form->isSubmitted() && $form->isValid()){
                try{
                    $evaluation->setCourseid($evaluation->getCourse()->getId());
                    $em=$this->getDoctrine()->getManager();
                    $em->persist($evaluation);
                    $em->flush();

                    return $this->redirectToRoute("all_evaluation");
                }catch (\Exception $e){
                    $errorMsg ="Please fill in all fields correctly! Evaluation value must be number, decimal separator is '.' not ',' ! Content is text!";
                    return $this->render('evaluation/edit.html.twig', ['evaluation'=>$evaluation,"evform"=>$form->createView(), 'currentStudent'=>$currentStudent, 'errorMsg'=>$errorMsg]);
                }
            }

            return $this->render('evaluation/edit.html.twig', ['evaluation'=>$evaluation,"evform"=>$form->createView(), 'currentStudent'=>$currentStudent,  'errorMsg'=>$errorMsg]);
        }

        return $this->render('evaluation/edit.html.twig');
    }
    /**
     * @Route("/evaluation/{id}/delete", name="delete_evaluation")
     *
     *
     */
    public function deleteEvaluation(Request $request, int $id)
    {
        $evaluation = $this->getDoctrine()->getRepository(Evaluation::class)->find($id);
        if($evaluation === null){
            return $this->redirectToRoute('all_evaluation');
        }
        $currentUser=$this->getUser();
        if($currentUser->isAdmin()){
            $form=$this->createForm(EvaluationType::class, $evaluation);
            $form->handleRequest($request);
            $currentStudent=$this->getDoctrine()->getRepository(User::class)->find($id);
            if($form->isSubmitted() && $form->isValid()){

                $evaluation->setCourseid($evaluation->getCourse()->getId());


                $em=$this->getDoctrine()->getManager();
                $em->remove($evaluation);
                $em->flush();

                return $this->redirectToRoute("all_evaluation");
            }

            return $this->render('evaluation/delete.html.twig', ['evaluation'=>$evaluation,"evform"=>$form->createView(), 'currentStudent'=>$currentStudent]);
        }

        return $this->render('evaluation/delete.html.twig');
    }
}
