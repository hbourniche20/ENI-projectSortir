<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('ville', 'Id de la ville'),
            EmailField::new('email', 'Email'),
            ArrayField::new('roles', 'Les roles ROLE_'),
            TextField::new('password', 'Mot de passe (Il doit être encrypter, utiliser "symfony console security:encode-password")'),
            TextField::new('pseudo', 'Pseudo'),
            TextField::new('prenom', 'Prenom'),
            TextField::new('nom', 'Nom'),
            TextField::new('tel', 'Téléphone'),
            BooleanField::new('desactiver', 'Desactiver'),
        ];
    }

    public function configureCrud(Crud $crud): Crud {
        return $crud
            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')

            ->setPageTitle('new', 'Nouveau %entity_label_singular%')
            ->setPageTitle('edit', 'Modifier %entity_label_singular%')
            ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // PAGE INDEX
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('email')
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('tel')
            ->add('ville')
            ->add('roles')
            ->add('desactiver')
            ;
    }
}
