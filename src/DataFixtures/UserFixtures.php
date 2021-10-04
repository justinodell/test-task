<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setFirstName('Super')
            ->setLastName('Admin')
            ->setEmail('admin@example.com')
            ->setRoles(['ROLE_SUPER_ADMIN']);

        $password = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
