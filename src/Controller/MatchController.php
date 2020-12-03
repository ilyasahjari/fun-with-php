<?php

namespace App\Controller;

use App\Entity\match;
use App\Form\matchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/match")
 */
class MatchController extends AbstractController
{
    /**
     * @Route("/", name="match_index", methods={"GET"})
     */
    public function index(): Response
    {
        $matchs = $this->getDoctrine()
            ->getRepository(match::class)
            ->findAll();

        return $this->render('match/index.html.twig', [
            'matchs' => $matchs,
        ]);
    }

    /**
     * @Route("/new", name="match_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $match = new match();
        $form = $this->createForm(matchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($match);
            $entityManager->flush();

            return $this->redirectToRoute('match_index');
        }

        return $this->render('match/new.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="match_show", methods={"GET"})
     */
    public function show(match $match): Response
    {
        return $this->render('match/show.html.twig', [
            'match' => $match,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="match_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, match $match): Response
    {
        $form = $this->createForm(matchType::class, $match);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('match_index');
        }

        return $this->render('match/edit.html.twig', [
            'match' => $match,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="match_delete", methods={"DELETE"})
     */
    public function delete(Request $request, match $match): Response
    {
        if ($this->isCsrfTokenValid('delete'.$match->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($match);
            $entityManager->flush();
        }

        return $this->redirectToRoute('match_index');
    }
}
