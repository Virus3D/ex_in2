<?php

namespace App\Controller;

use App\Entity\Regular;
use App\Form\RegularType;
use App\Repository\RegularRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/regular')]
final class RegularController extends AbstractController
{
    #[Route(name: 'app_regular_index', methods: ['GET'])]
    public function index(RegularRepository $regularRepository): Response
    {
        return $this->render('regular/index.html.twig', [
            'regulars' => $regularRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_regular_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $regular = new Regular();
        $form = $this->createForm(RegularType::class, $regular);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($regular);
            $entityManager->flush();

            return $this->redirectToRoute('app_regular_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('regular/new.html.twig', [
            'regular' => $regular,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_regular_show', methods: ['GET'])]
    public function show(Regular $regular): Response
    {
        return $this->render('regular/show.html.twig', [
            'regular' => $regular,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_regular_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Regular $regular, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RegularType::class, $regular);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_regular_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('regular/edit.html.twig', [
            'regular' => $regular,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_regular_delete', methods: ['POST'])]
    public function delete(Request $request, Regular $regular, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$regular->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($regular);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_regular_index', [], Response::HTTP_SEE_OTHER);
    }
}
