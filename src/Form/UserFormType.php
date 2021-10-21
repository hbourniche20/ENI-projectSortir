<?php

    namespace App\Form;

    use App\Entity\User;
    use App\Entity\Ville;
    use Symfony\Bridge\Doctrine\Form\Type\EntityType;
    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\FileType;
    use Symfony\Component\Form\Extension\Core\Type\PasswordType;
    use Symfony\Component\Form\Extension\Core\Type\TelType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolver;
    use Symfony\Component\Validator\Constraints\File;

    class UserFormType extends AbstractType {
        public function buildForm(FormBuilderInterface $builder, array $options): void {
            $builder
                ->add('email')
                //->add('roles')
                ->add('password', PasswordType::class, ['label' => 'Mot de passe', 'mapped' => false])
                ->add('confirmation', PasswordType::class, ['mapped' => false])
                ->add('pseudo')
                ->add('prenom', TextType::class, ['label' => 'Prénom'])
                ->add('nom')
                ->add('tel', TelType::class, ['label' => 'Téléphone'])
                ->add('ville', EntityType::class, ['class' => Ville::class, 'label' => 'Ville de ratachement'])
                ->add('photo', FileType::class, [
                        'label' => 'Photo de profil',
                        'required' => false,
                        'mapped' => false,
                    ]
                )
            ;
        }

        public function configureOptions(OptionsResolver $resolver): void {
            $resolver->setDefaults([
                'data_class' => User::class,
            ]);
        }
    }
