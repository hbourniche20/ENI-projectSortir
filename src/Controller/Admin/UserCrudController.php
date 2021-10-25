<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

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
}
