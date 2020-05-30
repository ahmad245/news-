<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Filter;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

        ->add('title',TextType::class,[
            'attr' => [
                'class' => 'browser-default autocomplete',
                'autocomplete'=>"off"
            ],
            'required' => false,
        ])

        ->add('category',EntityType::class, [
            'attr' => [
                'class' => 'browser-default autocomplete'
            ],
            'class' => Category::class,
            'required'   => false,
        ])
        ->add('user',EntityType::class, [
            'attr' => [
                'class' => 'browser-default autocomplete'
            ],
            'class' => User::class,
            'required'   => false,
        ])
        ->add('startDate', TextType::class, [

            'attr' => [
                'class' => 'datepicker',
                'autocomplete'=>"off"
            ],
            'required' => false,
            'empty_data' => null,
            // 'widget' => 'single_text',
            // 'html5' => false,

        ])
        ->add('endDate', TextType::class, [

            'attr' => [
                'class' => 'datepicker',
                'autocomplete'=>"off"
            ],
            'required' => false,
            'empty_data' => null,
            // 'widget' => 'single_text',
            // 'html5' => false,

        ])


        ->add('submit',SubmitType::class,[
            'attr' => [
                'class' => 'btn waves-effect waves-light'
            ],
            'label'=>'Search'
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'=>Filter::class,
            'method'=>'GET',
            'csrf_protection'=>false
        ]);
    }
    public function getBlockPrefix()
    {
        return  '';
    }
}
