<?php

namespace App\Form;

use App\Entity\Retour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchRetourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numRetour', TextType::class, [
                'label' => 'N° RET, RETSA, NT',
                'attr' => ['class' => 'form-control fs-1 fw-bold border-primary border-3'],
                'required' => false,
            ])
            ->add('prenomClient', TextType::class, [
                'label' => 'Prénom client',
                'attr' => ['class' => 'form-control fs-1 fw-bold border-primary border-3'],
                'required' => false,
            ])
            ->add('nomClient', TextType::class, [
                'label' => 'Nom client',
                'attr' => ['class' => 'form-control fs-1 fw-bold border-primary border-3'],
                'required' => false,
            ])
            ->add('transporteur', ChoiceType::class, [
                'label' => 'Transporteur',
                'choices' => [
                    'shenker' => 'shenker',
                    'dpd' => 'dpd',
                    'mazet' => 'mazet',
                    'geodis' => 'geodis',
                    'gls' => 'gls',
                ],
                'attr' => ['class' => 'form-select fs-1 fw-bold border-primary border-2'],
                'required' => false,
                'placeholder' => 'Sélectionnez',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
