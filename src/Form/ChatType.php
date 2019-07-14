<?php

namespace App\Form;

use App\Entity\ChatConversation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => " Chat title (optional)"
                ],
                'label' => 'Conversation name',
                'required' => false
            ])
            ->add('conv', EntityType::class, [
                'label' => 'Participants',
                'class' => User::class,
                'multiple' => true,
                'choice_label' => 'username',
                'placeholder' => 'Select your correspondant',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChatConversation::class,
        ]);
    }
}
