<?php 

namespace App\Controller;

use App\Entity\Article;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ArticleController extends AbstractController
{
    /**
    * @Route("/", name="default", methods={"GET"})
    */
    public function index(ArticleRepository $articleRepository, UserRepository $userRepository): Response
    {

        return $this->render('default/home.html.twig', [
            'articles' => $articleRepository->displayArticles(),

        ]);
    }
    
}