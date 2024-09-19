<?php

namespace App\Controller;

use App\Form\CreatorType;
use App\Repository\NoteRepository;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class CreatorController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET'])]
    public function profile(NoteRepository $nr): Response
    {
        return $this->render('creator/profile.html.twig', [
            'notes' => $nr->findByCreator(['creator', $this->getUser()], ['created_at' => 'DESC']),
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UploaderService $uploader): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(CreatorType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('image')->getData()) {
                if ($user->getImage()) {
                    $uploader->deleteImage($user->getImage());
                }
                $newFileName = $uploader->uploadImage($form->get('image')->getData());
                $user->setImage($newFileName);
            }
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Your profile has been updated');
            return $this->redirectToRoute('app_profile');
        }
        
        return $this->render('creator/edit.html.twig', [
            'creatorForm' => $form,
        ]);
    }
}