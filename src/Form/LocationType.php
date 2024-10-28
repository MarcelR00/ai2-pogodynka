<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Location;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('city', null, [
                'attr' => [
                    'placeholder' => 'Enter city name'
                ]
            ])
            ->add('country', ChoiceType::class,
                ["choices" => [
                'Poland' => 'PL',
                'Germany' => 'DE',
                'France' => 'FR',
                'United Kingdom' => 'UK',
                'United States' => 'US',
                'Russia' => 'RU',
                'China' => 'CN',
                'Japan' => 'JP',
            ],
            ])
            ->add('latitude')
            ->add('longitude')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
