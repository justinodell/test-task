<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @IsGranted("ROLE_SUPER_ADMIN")
 */
class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;
    private array $roleHierarchy;

    /**
     * UserCrudController constructor.
     *
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param array $roleHierarchy
     */
    public function __construct(UserPasswordHasherInterface $passwordEncoder, array $roleHierarchy = [])
    {
        $this->passwordHasher = $passwordEncoder;
        $this->roleHierarchy = $roleHierarchy;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('fullName')->onlyOnIndex(),
            TextField::new('firstName')->onlyOnForms(),
            TextField::new('lastName')->onlyOnForms(),
            TextField::new('email'),
            ChoiceField::new('roles')
                ->autocomplete()
                ->allowMultipleChoices()
                ->setChoices($this->getRoles()),

            FormField::addPanel('Change password')->setIcon('fa fa-key'),
            Field::new('plainPassword', 'New password')
                ->onlyOnForms()
                ->setFormType(RepeatedType::class)
                ->setFormTypeOptions([
                    'type' => PasswordType::class,
                    'first_options' => ['label' => 'New password'],
                    'second_options' => ['label' => 'Repeat password'],
                ]),
        ];
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        $this->addEncodePasswordEventListener($formBuilder);

        return $formBuilder;
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        $this->addEncodePasswordEventListener($formBuilder);

        return $formBuilder;
    }

    protected function addEncodePasswordEventListener(FormBuilderInterface $formBuilder): void
    {
        $formBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            if ($user->getPlainPassword()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            }
        });
    }

    public function configureActions(Actions $actions): Actions
    {
        $impersonate = Action::new('impersonate', false, 'fa fa-fw fa-user-lock')
            ->linkToUrl(function (User $entity) {
                return $this->generateUrl('admin', ['_switch_user' => $entity->getUsername()]);
            });

        $actions = parent::configureActions($actions);
        $actions->add(Crud::PAGE_INDEX, $impersonate);

        return $actions;
    }

    /**
     * @return array
     */
    private function getRoles(): array
    {
        $roles = [];

        foreach ($this->roleHierarchy as $key => $value) {
            $roles[$key] = $key;
            foreach ($value as $role) {
                $roles[$role] = $role;
            }
        }

        return $roles;
    }
}
