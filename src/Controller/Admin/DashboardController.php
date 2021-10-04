<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/", name="admin")
     */
    public function index(): Response
    {
        return $this->render('Admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tools')

            // by default, all backend URLs include a signature hash. If a user changes any
            // query parameter (to "hack" the backend) the signature won't match and EasyAdmin
            // triggers an error. If this causes any issue in your backend, call this method
            // to disable this feature and remove all URL signature checks
            ->disableUrlSignatures()
            ;
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getFullName())
            // use this method if you don't want to display the name of the user
            ->displayUserName(true)

            // you can return an URL with the avatar image
//            ->setAvatarUrl('https://...')
//            ->setAvatarUrl($user->getProfileImageUrl())
            // use this method if you don't want to display the user image
//            ->displayUserAvatar(false)
            // you can also pass an email address to use gravatar's service
            ->setGravatarEmail($user->getUsername())

            // you can use any type of menu item, except submenus
//            ->addMenuItems([
//                MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
//                MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
//                MenuItem::section(),
//                MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
//            ])
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');

        // Tools
        yield MenuItem::section('Tools');
        yield MenuItem::linkToRoute('Word Density', 'fab fa-home', 'admin_word_density')
            ->setPermission('ROLE_WORD_DENSITY_ADMIN');

        // Settings
        yield MenuItem::section('Settings')
            ->setPermission('ROLE_SUPER_ADMIN');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class)
            ->setPermission('ROLE_SUPER_ADMIN');
    }
}
