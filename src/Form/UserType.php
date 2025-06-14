<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => !$isEdit,
                'mapped' => !$isEdit,
                'help' => $isEdit ? 'Оставете празно, ако не искате да променяте паролата' : null
            ])
            ->add('fullName', TextType::class, [
                'label' => 'Full Name'
            ])
            ->add('city', TextType::class, [
                'label' => 'City',
                'required' => false
            ])
            ->add('address', TextType::class, [
                'label' => 'Address',
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'label' => 'Phone',
                'required' => false
            ])
            ->add('birthday', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Birthday',
                'required' => false
            ])
            ->add('userRoles', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Roles',
                'required' => false,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
