<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request, RoleRepository $roleRepository): Response
    {
        $user = new User();
        $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
        $user->addUserRole($userRole);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();


            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function show(User $user, CommentRepository $commentRepository, ArticleRepository $articleRepository): Response
    {
        $comments =[];
        $articlesLikes =[];
        $articlesShared =[];
        if (array_search("ROLE_USER", $user->getRoles()) == 0){
            $comments = $commentRepository->findBy(['author' => $user->getId()], ['created_date' => 'DESC']);
            $articlesLikes = [];
            if ($user->getArticleLiked() != null) {
                foreach (explode(",", $user->getArticleLiked()) as $index => $liked) {
                    $articlesLikes[$index] = $articleRepository->find($liked);
                }
            }
            $articlesShared = [];
            if ($user->getArticleShared() != null) {
                foreach (explode(",", $user->getArticleShared()) as $index => $shared) {
                    $articlesShared[$index] = $articleRepository->find($shared);
                }
            }
            dump($articlesLikes);
            dump(count($articlesShared));
            dump(($articlesShared));
            dump($user);
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'comments' => $comments,
            'likes' => $articlesLikes,
            'shares' => $articlesShared
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('default');
    }
}
