<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\Ville;
use App\Repository\VilleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('dateSortie', DateTimeType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nbPlaces', NumberType::class, [
                'label' => 'Nombre de places',
                'invalid_message' => 'Votre nombre de places n\'est pas valide'
            ])
            ->add('duree', NumberType::class, [
                'label' => 'Durée en minutes',
                'invalid_message' => 'Votre durée n\'est pas valide'
            ])
            ->add('description', TextareaType::class)
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
                'label'=>'Destination',
                'query_builder' => function (VilleRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.nom', 'ASC');
                }
            ])
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
