<?php

namespace App\Controller;

use App\Entity\SnookerBreak;
use App\Form\SnookerBreakType;
use App\Repository\SnookerBreakRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/break")
 */
class SnookerBreakController extends AbstractController
{
    /**
     * @Route("/", name="snooker_break_index", methods={"GET"})
     * @param SnookerBreakRepository $snookerBreakRepository
     * @return Response
     */
    public function index(SnookerBreakRepository $snookerBreakRepository): Response
    {
        return $this->render('snooker_break/index.html.twig', [
            'snooker_breaks' => $snookerBreakRepository->findLatestHighBreaks(),
            'highest_breaks' => $snookerBreakRepository->findBy([], ['score' => 'desc'], 20),
        ]);
    }

    /**
     * @Route("/new", name="snooker_break_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $snookerBreak = new SnookerBreak();
        $snookerBreak->setUser($this->getUser());

        $form = $this->createForm(SnookerBreakType::class, $snookerBreak);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($snookerBreak);
            $entityManager->flush();

            return $this->redirectToRoute('snooker_break_index');
        }

        return $this->render('snooker_break/new.html.twig', [
            'snooker_break' => $snookerBreak,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="snooker_break_show", methods={"GET"})
     */
    public function show(SnookerBreak $snookerBreak): Response
    {
        return $this->render('snooker_break/show.html.twig', [
            'snooker_break' => $snookerBreak,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="snooker_break_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, SnookerBreak $snookerBreak): Response
    {
        $form = $this->createForm(SnookerBreakType::class, $snookerBreak);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('snooker_break_index');
        }

        return $this->render('snooker_break/edit.html.twig', [
            'snooker_break' => $snookerBreak,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="snooker_break_delete", methods={"DELETE"})
     */
    public function delete(Request $request, SnookerBreak $snookerBreak): Response
    {
        if ($this->isCsrfTokenValid('delete'.$snookerBreak->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($snookerBreak);
            $entityManager->flush();
        }

        return $this->redirectToRoute('snooker_break_index');
    }
}