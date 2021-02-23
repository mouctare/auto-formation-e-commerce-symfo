<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre Prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => new Length([
                    'min' => 2,
                    'max' => 30
                ]),
                'attr' =>[
                    'placeholder' => 'Merci de saisir votre email'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent etre identique',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                   'attr' =>[
                       'placeholder' => 'Merci de saisir votre mot de passe'
                   ]
            ],
                'second_options' => ['label' => 'Confirmez votre mot de passe',
                'attr' =>[
                         'placeholder' => 'Merci confirmez votre mot de passe'
                ]
                ]
            ])
            // ->add('password_confirm', PasswordType::class, [
            //     'label' => 'Confirmez votre mot de passe',
            //     // Cela veut dire doctrine ne doit pas lié cette propriété à mon entité puisqu'elle n'éxiste pas;('mapped' => false,)
            //     'mapped' => false,
            //     'attr' =>[
            //         'placeholder' => 'Merci confirmez votre mot de passe'
            //     ]
            // ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
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
