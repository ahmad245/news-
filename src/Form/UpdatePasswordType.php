<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('oldPassword',PasswordType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
        ->add('newPassword',PasswordType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
        ->add('confirmPassword',PasswordType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
    ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
