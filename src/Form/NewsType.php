<?php

namespace App\Form;

use App\Entity\News;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,[
                'label'=>'Titre',
                'attr'=>['class'=>'browser-default']
            ])
            ->add('content',TextareaType::class,[
                'required'=>false,
                'label'=>'Contenu',
                ])

            ->add('categories',EntityType::class,[
                'label'=>'Catégories',
                'class' => Category::class,
                // 'expanded'  => true,
                'multiple'  => true,
                'attr'=>['class'=>'browser-default']
            ])
            ->add('isPublished',CheckboxType::class,[
                'label'=>'Est Publier',
                'required'=>false,
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
