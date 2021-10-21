<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateSortie', DateTimeType::class, ['date_format' => 'dd MMMM yyyy'])
            ->add('dateLimiteInscription', DateType::class, ['format' => 'dd MMMM yyyy'])
            ->add('nbPlaces')
            ->add('duree')
            ->add('description')
            ->add('villeOrganisatrice', EntityType::class, [
                'class' => Ville::class,
                'disabled' => true,
                'choice_label' => 'nom',
                'label'=>'Ville Organisatrice'
            ])
            ->add('villeAccueil', EntityType::class, [
                'class' => Ville::class,
                'placeholder' => '-- selectionner une ville --',
                'choice_label' => 'nom',
                'label'=>'Destination'
            ])
            // TODO MAKE SITE TYPE DYNAMIC
            ->add('site', EntityType::class,[
                'class' => Site::class,
                'placeholder' => '-- selectionner un lieu --',
                'choice_label' => 'nom',
                'label'=>'Lieu'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
