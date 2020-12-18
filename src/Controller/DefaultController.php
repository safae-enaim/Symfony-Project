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
    public function index(Request $request, ArticleRepository $repo, PaginatorInterface $paginator) {

        $searchForm = $this->createForm(SearchType::class);
        $searchForm->handleRequest($request);
        
        $donnees = $repo->findAll();
 
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
 
            $title = $searchForm->getData()->getTitle();

            $donnees = $repo->search($title);

            if ($donnees == null) {
                $this->addFlash('warning', 'Aucun article contenant ce mot clé n\'a été trouvé, essayez en un autre.');
            } elseif (count($donnees) > 0 && $title != "") {
                $this->addFlash('', count($donnees) . ' articles trouvés');
            }
        }

        $filteredArticles = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            10,
            ['pageParameterName' => 'page']

        );
        $filteredArticles->setCustomParameters([
            'align' => 'center',
            'size' => 'small',
        ]);
        return $this->render('default/home.html.twig', [
            'articles' => $filteredArticles,
            'current_user' => $repo->getCurrentUser(),
            'searchForm' => $searchForm->createView()
        ]);
    }
}
