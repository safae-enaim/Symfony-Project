<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
    * @Route("/", name="default", methods={"GET"})
    */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('default/home.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }
}
