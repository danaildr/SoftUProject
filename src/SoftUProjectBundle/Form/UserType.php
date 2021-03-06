<?php

namespace SoftUProjectBundle\Form;

use SoftUProjectBundle\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', TextType::class)
            ->add('password', TextType::class)
            ->add('fullName', TextType::class)
            ->add('city', TextType::class)
            ->add('address', TextType::class)
            ->add('phone', TextType::class)
            ->add('birthday', DateType::class, array('widget' => 'single_text'))
//  TODO: changete generate checkbox
//            ->add('roles', EntityType::class , array(
//                'class'=>Role::class,
//                    'choice_label' => 'name',
//                    'multiple' => true,
//                )
//            )
            ->add("Save", SubmitType::class);
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SoftUProjectBundle\Entity\User'
        ));
    }

}
