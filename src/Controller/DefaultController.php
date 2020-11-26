<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default", methods={"GET"})
     */
    public function index(Request $request, ArticleRepository $repo) {

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repo->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $title = $searchForm->getData()->getTitle();

            $donnees = $repo->search($title);

            if ($donnees == null) {
                $this->addFlash('erreur', 'Aucun article contenant ce mot clé dans le titre n\'a été trouvé, essayez en un autre.');
           
            }
        }

        return $this->render('default/home.html.twig', [
            'articles' => $donnees,
            'current_user' => $repo->getCurrentUser(),
            'searchForm' => $searchForm->createView()
        ]);
    }
}
