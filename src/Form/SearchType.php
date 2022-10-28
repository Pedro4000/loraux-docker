<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('query', TextType::class, [
                'attr' => [
                    'class' => 'w-40 beautiful-text-type',
                ]
            ])
            ->add('trouver', SubmitType::class, [
                'attr' => [
                    'class' => 'ml-3 beautiful-button-type',
                    'placeholder' => 'trouver',
                ]
            ]);
        ;
    }
}