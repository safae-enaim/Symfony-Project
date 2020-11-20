<?php

namespace App\Controller;
use DateTime;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apropos")
 */
class AProposController extends AbstractController
{
    /**
    * @Route("/", name="apropos", methods={"GET"})
    */
    public function index(): Response
    {
        return $this->render('default/apropos.html.twig');
    }
}
