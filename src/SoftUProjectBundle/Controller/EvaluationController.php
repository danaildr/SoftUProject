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
        // TODO - да се оправи създаването, като се приема id на ученика и предмета
        $evaluation = new Evaluation();
        $form=$this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $currentStudent=$this->getDoctrine()->getRepository(User::class)->find($id);
            $currentTeacher=$this->getUser();
            $evaluation->setTeacher($currentTeacher);
            $evaluation->setAuthorId($currentTeacher->getId());
            $evaluation->setStudent($currentStudent);
            $evaluation->setRecipient($currentStudent->getId());
            $em=$this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();

            return $this->redirectToRoute("all_evaluation");
        }

        return $this->render('evaluation/create.html.twig', ["evform"=>$form->createView()]);
    }

    /**
     * @Route("/evaluation", name="all_evaluation")
     */
    public function showAllEvalution(){
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
        return $this->render("evaluation/showevaluations.html.twig", ["evaluations"=>$evaluations]);
    }
}
