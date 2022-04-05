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
    // configuration des constantes pour la création des éléments
    private const MAX_USER = 47;
    private const MAX_POST = 100;
    private const MAX_COMMENT = 20;

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
        // On persiste l'admin        
        $manager->persist($admin);
        // On stocke l'admin dans un tableau pour l'attribuer aux publications        
        $user_post[] = $admin;   
        // On stocke l'admin dans un tableau pour l'utiliser pour les commentaires
        $users[] = $admin;
        
        // Création d'un compte ROLE_BLOGGER
        $blogger = new User();
        $blogger
            ->setEmail('b@b.fr')
            ->setUserName('blogger')
            ->setPassword($this->hasher->hashPassword($blogger, 'Password1*'))            
            ->setRoles(["ROLE_BLOGGER"])
        ;   
        // On persiste le Blogger        
        $manager->persist($blogger);
        // On stocke le blogger dans un tableau pour l'attribuer aux publications         
        $user_post[] = $blogger;  
        // On stocke le blogger dans un tableau pour l'utiliser pour les commentaires
        $users[] = $blogger;

        // Création d'un compte ROLE_USER
        $user = new User();
        $user
            ->setEmail('c@c.fr')
            ->setUserName('user')
            ->setPassword($this->hasher->hashPassword($user, 'Password1*'))            
        ;      
        // On persiste l'utilisateur
        $manager->persist($user); 

        // Création de MAX_USER comptes aléatoires ROLE_USER
        for ($i=0; $i<self::MAX_USER; $i++) {
            $user = new User();
            $user
                ->setEmail( $faker->email )
                ->setUserName( $faker->userName )
                ->setPassword($this->hasher->hashPassword($user, 'Password1*'))            
            ;      
            // On persiste l'utilisateur
            $manager->persist($user);
            // On stocke l'utilisateur dans un tableau pour l'utiliser pour les commentaires
            $users[] = $user;
        }

        // Création de MAX_POST Publications avec des données aléatoires et des commentaires (entre 0 et MAX_COMMENT par publication)
        for ($i=0; $i<self::MAX_POST; $i++) {

            // Création d'un Post
            $post = new Post();
            // Définir les données de ce post
            $post
                ->setTitle( $faker->realText($maxNbChars = 100) )
                ->setContent( $faker->realText($maxNbChars = 2000) )
                ->setAuthor( $faker->randomElement($user_post) )
            ; 
            // On persiste la publication
            $manager->persist($post);

            // Boucle de création des commentaires (entre 0 et MAX_COMMENT)
            $rand = rand(0, self::MAX_COMMENT);
            for ($j=0; $j < $rand; $j++) {
                // Création d'un commentaire
                $comment = new Comment();
                // Définir les données de ce commentaire
                $comment
                    ->setContent( $faker->realText($maxNbChars = 500) )
                    ->setPost($post)
                    // On récupère aléatoirement un utilisateur dans le tableau précédent
                    ->setAuthor( $faker->randomElement($users) )
                ;   
                // On persiste le commentaire
                $manager->persist($comment);                 
            }
        }

        $manager->flush();
    }
}
