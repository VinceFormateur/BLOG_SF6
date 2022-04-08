<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
// Import Login
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
// Import Post
use App\Repository\PostRepository;
// Imports Register
use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Form\ContactType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
// PAGINATOR
use Knp\Component\Pager\PaginatorInterface;
// EMAIL
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


#[Route('/', name: 'app_main_')]
class MainController extends AbstractController
{

    /****  ACCUEIL  ****/
    #[Route(path: '/', name: 'home')]
    public function home(PostRepository $postRepository): Response
    {
        return $this->render('main/home.html.twig', [
            'posts' => $postRepository->findBy([], ['createdAt' => 'desc'], 5),
        ]);
    }


    /****  FORMULAIRE DE CONTACT  ****/
    #[Route(path: '/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            // Récupération des données dans le formulaire sous-forme de tableau
            $contactFormData = $form->getData(); 

            // Test sur le champ phone (optionnel)
            $phone = $contactFormData['phone'] ? $contactFormData['phone'] : 'non renseigné';

            // Création du message (Email Text)
            $message = (new Email())
                    ->from('vince.symfony@gmail.com')
                    ->to('vince.symfony@gmail.com')
                    ->subject('vous avez reçu un email de Contact de ' . $contactFormData['fullname'])
                    ->text('Son nom : ' . $contactFormData['fullname'] 
                        . \PHP_EOL 
                        . 'Son adresse email : ' . $contactFormData['email'] 
                        . \PHP_EOL 
                        . 'Son téléphone : ' . $phone
                        . \PHP_EOL
                        . 'Son message : ' . $contactFormData['message'], 'text/plain');

            // Envoi de l'email
            $mailer->send($message);            
           
            $this->addFlash('success', 'Votre message a bien été envoyé');

            return $this->redirectToRoute('app_main_home');
        }

        return $this->renderForm('main/contact.html.twig', [
            'form' => $form]);
    }


    /****  CONNEXION  ****/
    #[Route(path: '/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà connecté');            
            return $this->redirectToRoute('app_main_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('main/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    /****  DECONNEXION  ****/
    #[Route(path: '/deconnexion', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    /****  INSCRIPTION  ****/
    #[Route(path: '/inscription', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            $this->addFlash('warning', 'Vous êtes déjà inscrit');
            return $this->redirectToRoute('app_main_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
            );
            $userRepository->add($user);
            $this->addFlash('success', 'Vous êtes bien inscrit');

            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_main_login');
        }

        return $this->renderForm('main/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /****  RECHERCHE  ****/
    #[Route(path: '/recherche', name: 'search', methods: ['GET'])]
    public function search(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response 
    {
        // Récupération du numéro de la page demangée
        $requestedPage = $request->query->getInt('page', 1);
        // Vérification que le numéro est positif
        if ($requestedPage < 1) { throw new NotFoundHttpException(); }        
        // On récupère le contenu du champ de recherche
        $search = $request->query->get('search', '');

        // Utilisation de la méthode présente dans le repository pour rechercher l'élément (dans le titre et le contenu)
        $posts = $postRepository->findBySearch($search);

        // Récupération des publications paginées
        $posts_paginate = $paginator->paginate(
            $posts, // Requête de récupération des publications 
            $requestedPage, // Numéro de la page demandée dans $request
            4 // Nombre de publications par page
        );        

        // Réponse -> envoyer une page contenant les éléments à afficher
        return $this->render('blog/search.post.html.twig', [
            'posts' => $posts_paginate,
        ]);
    }

}