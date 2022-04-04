<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
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

    #[Route('/{slug}', name: 'show_post', methods: ['GET'])]
    public function showPost(Post $post): Response
    {
        return $this->render('blog/show.post.html.twig', [
            'post' => $post,
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
