<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(NoteRepository $nr): Response
    {
        $lastNotes = $nr->findBy(
            ['is_public' => true], //Filtre les notes publiques
            ['created_at' => 'DESC'], //Trie les notes par dates de création
            6 //Limite à notes
        );
        return $this->render('home/index.html.twig', [
            'lastNotes' =>$lastNotes, // Envoie des notes à la vue Twig
        ]);
    }
}
