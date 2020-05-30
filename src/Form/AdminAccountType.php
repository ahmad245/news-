<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
            ->add('lastName',TextType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
            ->add('email',EmailType::class,[
            'attr' => [
                'class' => 'browser-default'
            ],
            
        ])
            ->add('password',PasswordType::class,[
                'attr' => [
                    'class' => 'browser-default'
                ],
                
            ])
            ->add('enabled',CheckboxType::class)
            ->add('userRoles',EntityType::class,[
                'attr' => [
                    'class' => 'browser-default'
                ],
                'class'=>Role::class,
                'multiple'  => true
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
