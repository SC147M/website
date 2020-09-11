<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     * @param ReservationRepository $reservationRepository
     * @return Response
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC'], 5);


        return $this->render(
            'reservation/index.html.twig',
            [
                'reservations' => $reservations,
            ]
        );
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request): Response
    {
        $reservation = new Reservation();
        $reservation->setUser($this->getUser());
        $reservation->setStart((new DateTime));
        $reservation->setEnd((new DateTime));
        $reservation->setType(Reservation::TYPE_TABLE);
        $reservation->addParticipant($this->getUser());
        $form = $this->createForm(
            ReservationType::class,
            $reservation,
            [
                'validation_groups' => ['create'],
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reservation);
            $entityManager->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render(
            'reservation/new.html.twig',
            [
                'reservation' => $reservation,
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     * @param Reservation $reservation
     * @return Response
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render(
            'reservation/show.html.twig',
            [
                'reservation' => $reservation,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     * @param Request     $request
     * @param Reservation $reservation
     * @return Response
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $user = $this->getUser();
        if ($reservation->getUser()->getId() != $user->getId()) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute(
                'reservation_index',
                [
                    'id' => $reservation->getId(),
                ]
            );
        }

        return $this->render(
            'reservation/edit.html.twig',
            [
                'reservation' => $reservation,
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"DELETE"})
     * @param Request     $request
     * @param Reservation $reservation
     * @return Response
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        $user = $this->getUser();
        if ($reservation->getUser()->getId() != $user->getId()) {
            throw new AccessDeniedHttpException();
        }

        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
}
