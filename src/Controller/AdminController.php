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
    public function userIndex(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/user.index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }


    #[Route('/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
