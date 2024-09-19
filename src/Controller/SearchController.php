<?php
namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\NoteRepository;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function search(Request $request, NoteRepository $nr, PaginatorInterface $paginator): Response
    {
		$searchQuery = $request->get('q');
		if($searchQuery=== null){
			return $this->render('search/results.html.twig');
		}
		$pagination =$paginator->paginate(
		$nr->findByQuery($searchQuery),
		$request->query->getInt('page', 1),
		24
		);
        return $this->render('search/results.html.twig', [
            'allNotes' => $pagination,
			'searchQuery' => $searchQuery->get('q')
        ]);
    }
}
