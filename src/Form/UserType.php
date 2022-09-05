<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [ 'attr' =>
                ['placeholder'=>'Prénom']
                ])
            ->add('familyName', TextType::class, [ 'attr' =>
                ['placeholder'=>'Nom de famille']])
            ->add('emailAddress', TextType::class, [ 'attr' =>
                ['placeholder'=>'Adresse mail']])
            ->add('password', PasswordType::class, [ 'attr' =>
                ['class'=>'password',
                    'placeholder'=>'mot de passe'],

                ])
            ->add('birthDate', DateType::class, [
                'label'=>'Date de naissance',
                'widget'=>'single_text'
                ])
            ->add('sex', ChoiceType::class, [
                'choices' => [
                    'Masculin'=>1,
                    'Féminin'=>2,
                    'Autre'=>3,
                    'Ne souhaite pas préciser'=>4
                ],
                'label'=> 'Sexe',
                'attr' =>
                ['placeholder'=> true
                ]])
            ->add('save', SubmitType::class, [ 'attr' =>
                ['placeholder'=>'Créer'],
                'label'=>'Créer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}