<?php

namespace App\DataFixtures;

use App\Entity\Acteur;
use App\Entity\Film;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $hasher;
    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $user = new User();
        $user
            ->setEmail('usr@usr.com')
            ->setPassword($this->hasher->hashPAssword($user, 'usr'))
            ->setRoles(['ROLE_USER']);
        $manager->persist($user);
        $admin = new User();
        $admin
            ->setEmail('admin@admin.com')
            ->setPassword($this->hasher->hashPAssword($admin, 'admin'))
            ->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($admin);
        $manager->flush();
    }
}
