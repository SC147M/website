<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/table")
 */
class TableController extends AbstractController
{
    /**
     * @Route("/", name="table_index", methods={"GET"})
     */
    public function index(TableRepository $tableRepository): Response
    {
        return $this->render('table/index.html.twig', [
            'tables' => $tableRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="table_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $table = new Table();
        $form = $this->createForm(TableType::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($table);
            $entityManager->flush();

            return $this->redirectToRoute('table_index');
        }

        return $this->render('table/new.html.twig', [
            'table' => $table,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="table_show", methods={"GET"})
     */
    public function show(Table $table): Response
    {
        return $this->render('table/show.html.twig', [
            'table' => $table,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="table_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Table $table): Response
    {
        $form = $this->createForm(TableType::class, $table);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('table_index', [
                'id' => $table->getId(),
            ]);
        }

        return $this->render('table/edit.html.twig', [
            'table' => $table,
            'form'  => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="table_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Table $table): Response
    {
        if ($this->isCsrfTokenValid('delete' . $table->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($table);
            $entityManager->flush();
        }

        return $this->redirectToRoute('table_index');
    }
}
