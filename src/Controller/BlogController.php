<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\PostType;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog', name: 'app_blog_')]
class BlogController extends AbstractController
{

    /*********** Gestion des POSTS ***********/

    #[Route('/', name: 'index', methods: ['GET'])]
    public function indexBlog(PostRepository $postRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/publier', name: 'new_post', methods: ['GET', 'POST'])]
    public function newPost(Request $request, PostRepository $postRepository): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // On affecte l'utilisateur connecté comme Auteur de la publication
            $post->setAuthor($this->getUser());
            $postRepository->add($post);

            $this->addFlash('success', 'Publication ajoutée');

            return $this->redirectToRoute('app_blog_index');
        }

        return $this->renderForm('blog/new.post.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'show_post', methods: ['GET', 'POST'])]
    public function showPost(Post $post, Request $request, CommentRepository $commentRepository): Response
    {
        // Si l'utilisateur n'est pas connecté, on envoie directement la vue sans le formulaire
        // Pour optimiser le fonctionnement et la sécurité
        if (!$this->getUser()) {
            return $this->render('blog/show.post.html.twig', [
                'post' => $post,
            ]);
        }

        // Sinon on traite la vue avec le formulaire de commentaire et l'utilisateur connecté
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            // On renseigne les infos du commentaire (auteur et publication)
            // $comment
            //    ->setAuthor($this->getUser())
            //    ->setPost($post);

            // Ou en utilisant le méthode présente dans Post (modifiée)
            $post->addComment($comment, $this->getUser());

            // On ajoute le commentaire en BDD
            $commentRepository->add($comment);

            // message Flash
            $this->addFlash('success', 'merci pour votre commentaire');

            //Option 1 en renvoyant la vue avec le slug du Post
            return $this->redirectToRoute('app_blog_show_post', ['slug' => $post->getSlug()]);

            // Option 2 en recréant les données Comment et Form 
            // unset($comment);
            // unset($form);
            // $comment = new Comment();
            // $form = $this->createForm(CommentType::class, $comment);            
        }        

        return $this->renderForm('blog/show.post.html.twig', [
            'post' => $post,
            'form' => $form,            
        ]);
    }


    #[Route('/{slug}/modifier-publication', name: 'edit_post', methods: ['GET', 'POST'])]
    public function editPost(Request $request, Post $post, PostRepository $postRepository): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $postRepository->add($post);

            $this->addFlash('success', 'Publication bien modifiée');

            return $this->redirectToRoute('app_blog_index');
        }

        return $this->renderForm('blog/edit.post.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete_post', methods: ['POST'])]
    public function deletePost(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('mon_joli_blog'.$post->getId(), $request->request->get('_token'))) {
            $postRepository->remove($post);
            $this->addFlash('success', 'Publication bien effacée');
        }

        return $this->redirectToRoute('app_blog_index');
    }
}
