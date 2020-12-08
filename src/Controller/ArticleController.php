<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\ArticleType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentStateRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->redirectToRoute('default');
    }

    /**
    * @Route("/{id}", name="displayArticle", methods={"GET"})
    */
    public function oneArticle (Article $article): Response
    {
        $article->getDisplayComments();
        return $this->render('article/article.html.twig', [
            'article' => $article,
        ]);
    }


    /**
     * @Route("/{id}/send-comment", name="commentArticle", methods={"GET","POST"})
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function sendComment (Request $request, ArticleRepository $articleRepository, UserRepository $userRepository, CommentStateRepository  $commentStateRepository): Response
    {
        $params = $request->request;
        $comment = new Comment();
        $currentDate = new \DateTimeImmutable;
        $currentDate->getTimestamp();
        $idArcile = $request->attributes->get('id');
        $comment
            ->setAuthor($userRepository->findOneBy(['id' =>  $params->get('user')]))
            ->setArticle($articleRepository->findOneBy(['id' => $idArcile]))
            ->setContent($params->get('comment'))
            ->setState($commentStateRepository->findOneBy(['name' => 'waiting']))
            ->setCreatedDate($currentDate)
        ;
        $article = $articleRepository->findOneBy(['id' => $idArcile]);
        dump($article);
        $article->setNotification($article->getNotification()+1);
        dump($article);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($article);
        $manager->persist($comment);
        $manager->flush();

        $this->addFlash('success', 'Votre commentaire a bien été enregistré. Il doit être approuvé par un auteur avant d\'être affiché');
        return $this->redirectToRoute('displayArticle', ['id' => $idArcile]);
    }

    /**
     * @Route("New", name="article_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $article = new Article();
        $category = new Category();
        $category->setName('Frisson');
        $article->addCategory($category);

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Article $article): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }


    /**
     * @Route("/{id}/send-comment", name="likeArticle", methods={"GET","POST"})
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @param UserRepository $userRepository
     * @return Response
     */
    public function clickLikeArticle (Request $request, UserRepository $userRepository, ArticleRepository $articleRepository): Response
    {
        $params = $request->request;
        $user = $userRepository->findOneBy(['id' =>  $params->get('user')]);
        $idArcile = $request->attributes->get('id');
        $article = $articleRepository->findOneBy(['id' =>  $idArcile]);

        if($user->getArticleLiked()->contains($article)){
            $user->addLike($article);
            $article->addLike();
        }else{
            $user->removeLike($article);
            $article->removeLike();
        }
        
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($article);
        $manager->persist($user);
        $manager->flush();

        return $this->redirectToRoute('displayArticle', ['id' => $idArcile]);
        /*
         $params = $request->request;
        $comment = new Comment();
        $currentDate = new \DateTimeImmutable;
        $currentDate->getTimestamp();
        $idArcile = $request->attributes->get('id');
        $comment
            ->setAuthor($userRepository->findOneBy(['id' =>  $params->get('user')]))
            ->setArticle($articleRepository->findOneBy(['id' => $idArcile]))
            ->setContent($params->get('comment'))
            ->setState($commentStateRepository->findOneBy(['name' => 'waiting']))
            ->setCreatedDate($currentDate)
        ;
        $article = $articleRepository->findOneBy(['id' => $idArcile]);
        dump($article);
        $article->setNotification($article->getNotification()+1);
        dump($article);
        */

        // return $this->redirectToRoute('displayArticle', ['id' => 12]);
    }

}
