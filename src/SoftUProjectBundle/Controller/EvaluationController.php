<?php

namespace SoftUProjectBundle\Controller;

use SoftUProjectBundle\Entity\Evaluation;
use SoftUProjectBundle\Form\EvaluationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EvaluationController extends Controller
{

    /**
     * @Route("/evaluation/create", name="evaluation_create")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $evaluation = new Evaluation();
        $form=$this->createForm(EvaluationType::class, $evaluation);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $currentTeacher=$this->getUser();
            $evaluation->setTeacher($currentTeacher);
            $evaluation->setAuthorId($currentTeacher->getId());
            $em=$this->getDoctrine()->getManager();
            $em->persist($evaluation);
            $em->flush();

            return $this->redirectToRoute("homepage");
        }

        return $this->render('evaluation/create.html.twig', ["form"=>$form->createView()]);
    }

    /**
     * @Route("/evaluation", name="all_evaluation")
     */
    public function showAllEvalution(){
        $evaluations = $this->getDoctrine()->getRepository(Evaluation::class)->findAll();
        return $this->render("evaluation/showevaluations.html.twig", ["evaluations"=>$evaluations]);
    }
}
