<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('display_name', TextType::class, [
                'attr' => [
                    'placeholder' => "Full name",
                    'class' => "form-input"
                ]
            ])
            ->add('username', TextType::class, [
                'attr' => [
                    'placeholder' => "Username",
                    'class' => "form-input"
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => "Email",
                    'class' => "form-input"
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => "Password",
                    'class' => "form-input"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
