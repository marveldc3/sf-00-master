<?php

namespace App\Controller;

use App\Repository\NoteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notes')]

class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_all')]
    public function all(NoteRepository $nr): Response
    {

        return $this->render('note/all.html.twig', [
            'allNotes' =>  $nr->findBy(['is_public' => true], ['created_at' => 'DESC'], 6),
        ]);
    }


    #[Route('/{slug}', name: 'app_note_show')]
    public function show(string $slug, NoteRepository $nr): Response
    {

        return $this->render('note/show.html.twig', [
            'note' =>  $nr->findOneBySlug($slug),
        ]);
    }

    #[Route('/{username}', name: 'app_note_user')]
    public function userNotes(
        string $username,
        UserRepository $user,
    ): Response {
        $creator = $user->findOneByUsername($username); // Recherche de l'utilisateur
        return $this->render('note/user.html.twig', [
            'creator' => $creator, //Envoie les données de l'utilisateur à la vue Twig
            'userNotes' =>  $user->getNotes(), // récupère les notes de l'utilisateur
        ]);
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    public function new(): Response
    {

        return $this->render('note/new.html.twig', []);
    }

    #[Route('/edit/{slug}', name: 'app_note_edit', methods: ['GET', 'POST'])]
    public function edit(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); //Recheche de la note à modifier
        return $this->render('note/edit.html.twig', []);
    }
    #[Route('/delete/{slug}', name: 'app_note_delete', methods: ['POST'])]
    public function delete(string $slug, NoteRepository $nr): Response
    {
        $note = $nr->findOneBySlug($slug); //Recheche de la note à modifier
        $this->addFlash('success', 'La note a bien été supprimée');
        return $this->redirectToRoute('app_note_user');
    }
}
