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

    private const MAX_NOMBRE_POST = 50;
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
                ->setEmail( $faker->email )
                ->setUserName( $faker->userName )
                ->setPassword($this->hasher->hashPassword($user, 'Password1*'))            
            ;        
            $manager->persist($user);
        }

        // Création de 100 Publications avec des données aléatoires et des commentaires (entre 0 et 10 par publication)
        for ($i=0; $i<100; $i++) {

            // Création d'un Post
            $post = new Post();
            // Définir les données de ce post
            $post
                ->setTitle( $faker->realText($maxNbChars = 100) )
                ->setContent( $faker->realText($maxNbChars = 1000) )
                ->setAuthor( $blogger )
            ; 
            // On persiste la publication
            $manager->persist($post);

            // Boucle de création des commentaires (entre 0 et 10)
            $rand = rand(0, 10);
            for ($j=0; $j < $rand; $j++) {
                // Création d'un commentaire
                $comment = new Comment();
                // Définir les données de ce commentaire
                $comment
                    ->setContent( $faker->realText($maxNbChars = 500) )
                    ->setPost($post)
                    ->setAuthor($admin)
                ;   
                // On persiste le commentaires
                $manager->persist($comment);                 
            }
        }

        $manager->flush();
    }
}
