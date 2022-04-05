<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;
// ENTITIES
use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        // Instance de Faker en localisation Française
        $faker = Faker\Factory::create('fr_FR');

        // Création d'un compte ROLE_ADMIN
        $admin = new User();
        $admin
            ->setEmail('a@a.fr')
            ->setUserName('admin')
            ->setPassword($this->hasher->hashPassword($admin, 'Password1*'))            
            ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($admin);         
        
        // Création d'un compte ROLE_BLOGGER
        $blogger = new User();
        $blogger
            ->setEmail('b@b.fr')
            ->setUserName('blogger')
            ->setPassword($this->hasher->hashPassword($blogger, 'Password1*'))            
            ->setRoles(["ROLE_BLOGGER"])
        ;   
        $manager->persist($blogger);       

        // Création d'un compte ROLE_USER
        $user = new User();
        $user
            ->setEmail('c@c.fr')
            ->setUserName('user')
            ->setPassword($this->hasher->hashPassword($user, 'Password1*'))            
        ;        
        $manager->persist($user);

        // Création de 10 comptes aléatoires ROLE_USER
        for ($i=0; $i<10; $i++) {
            $user = new User();
            $user
                ->setEmail('user'.$i.'@user.fr')
                ->setUserName('user'.$i)
                ->setPassword($this->hasher->hashPassword($user, 'Password1*'))            
            ;        
            $manager->persist($user);
        }

        $manager->flush();
    }
}
