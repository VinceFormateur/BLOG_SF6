<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Post;
use App\Entity\Comment;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// CONTROLE DES ROLES
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
// PAGINATOR
use Knp\Component\Pager\PaginatorInterface;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    // Injection des différents Repository directement par le Construct
    private UserRepository $userRepo;
    private PostRepository $postRepo;
    private CommentRepository $commentRepo;

    public function __construct(UserRepository $userRepository,
                                PostRepository $postRepository,
                                CommentRepository $commentRepository)
    {
        $this->userRepo = $userRepository;
        $this->postRepo = $postRepository;
        $this->commentRepo = $commentRepository;
    }

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('admin/home.admin.html.twig', [
            'Nb_Users' => $this->userRepo->countNumberUsers(),
            'Nb_Posts' => $this->postRepo->countNumberPosts(),
            'Nb_Comments' => $this->commentRepo->countNumberComments(),
        ]);
    }

    /**********  USERS  ***********/
    #[Route('/users', name: 'user_index')]
    public function userIndex(Request $request, PaginatorInterface $paginator): Response
    {

        $requestedPage = $request->query->getInt('page', 1);
        if ($requestedPage < 1) { throw new NotFoundHttpException(); } 
        $users = $this->userRepo->findBy([], ['username' => 'ASC']);

        $users_paginate = $paginator->paginate( $users, $requestedPage, $this->getParameter('app_admin.user_number') );        

        return $this->render('admin/users/user.index.html.twig', [
            'users' => $users_paginate,
        ]);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/users/user.show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepo->add($user);
            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepo->remove($user);
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
