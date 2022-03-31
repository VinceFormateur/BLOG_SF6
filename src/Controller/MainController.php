<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/', name: 'app_main_')]
class MainController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('main/home.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('main/contact.html.twig');
    }

    #[Route('/blog', name: 'blog')]
    public function blog(PostRepository $postRepository): Response
    {

        return $this->render('main/blog.html.twig', [
            'posts' => $postRepository->findOneByTitle('titre2')
        ]);
    }    

    #[Route('/creer_un_article', name: 'new_post')]
    public function newPost(Request $request, PostRepository $postRepository): Response
    {

        $post = new Post();

        $form = $this->createForm(PostFormType::class, $post);

        $form->handleRequest($request);

        /* Gestion de la soumission du formulaire */
        if ($form->isSubmitted() && $form->isValid()) {

            $postRepository->add($post);       

            $this->addFlash('success', 'Publication ajoutée');
            return $this->redirectToRoute('app_main_blog');
        }

        return $this->renderForm('main/newPost.html.twig', [
            'form_post' => $form
        ]);

    }

}
