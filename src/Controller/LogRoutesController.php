<?php

namespace App\Controller;

use App\Entity\LogRoutes;
use App\Form\LogRoutesType;
use App\Repository\LogRoutesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/log/routes")
 */
class LogRoutesController extends AbstractController
{
    /**
     * @Route("/", name="log_routes_index", methods={"GET"})
     */
    public function index(LogRoutesRepository $logRoutesRepository): Response
    {
        return $this->render('log_routes/index.html.twig', [
            'log_routes' => $logRoutesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="log_routes_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $logRoute = new LogRoutes();
        $form = $this->createForm(LogRoutesType::class, $logRoute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($logRoute);
            $entityManager->flush();

            return $this->redirectToRoute('log_routes_index');
        }

        return $this->render('log_routes/new.html.twig', [
            'log_route' => $logRoute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="log_routes_show", methods={"GET"})
     */
    public function show(LogRoutes $logRoute): Response
    {
        return $this->render('log_routes/show.html.twig', [
            'log_route' => $logRoute,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="log_routes_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, LogRoutes $logRoute): Response
    {
        $form = $this->createForm(LogRoutesType::class, $logRoute);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('log_routes_index');
        }

        return $this->render('log_routes/edit.html.twig', [
            'log_route' => $logRoute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="log_routes_delete", methods={"DELETE"})
     */
    public function delete(Request $request, LogRoutes $logRoute): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logRoute->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($logRoute);
            $entityManager->flush();
        }

        return $this->redirectToRoute('log_routes_index');
    }
}
