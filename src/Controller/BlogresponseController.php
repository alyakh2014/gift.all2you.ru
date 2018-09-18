<?php

namespace App\Controller;

use App\Entity\Blogresponse;
use App\Form\BlogresponseType;
use App\Repository\BlogresponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blogresponse")
 */
class BlogresponseController extends AbstractController
{
    /**
     * @Route("/", name="blogresponse_index", methods="GET")
     */
    public function index(BlogresponseRepository $blogresponseRepository): Response
    {
        return $this->render('blogresponse/index.html.twig', ['blogresponses' => $blogresponseRepository->findAll()]);
    }

    /**
     * @Route("/new", name="blogresponse_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $blogresponse = new Blogresponse();
        $form = $this->createForm(BlogresponseType::class, $blogresponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($blogresponse);
            $em->flush();

            return $this->redirectToRoute('blogresponse_index');
        }

        return $this->render('blogresponse/new.html.twig', [
            'blogresponse' => $blogresponse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blogresponse_show", methods="GET")
     */
    public function show(Blogresponse $blogresponse): Response
    {
        return $this->render('blogresponse/show.html.twig', ['blogresponse' => $blogresponse]);
    }

    /**
     * @Route("/{id}/edit", name="blogresponse_edit", methods="GET|POST")
     */
    public function edit(Request $request, Blogresponse $blogresponse): Response
    {
        $form = $this->createForm(BlogresponseType::class, $blogresponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blogresponse_edit', ['id' => $blogresponse->getId()]);
        }

        return $this->render('blogresponse/edit.html.twig', [
            'blogresponse' => $blogresponse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blogresponse_delete", methods="DELETE")
     */
    public function delete(Request $request, Blogresponse $blogresponse): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blogresponse->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($blogresponse);
            $em->flush();
        }

        return $this->redirectToRoute('blogresponse_index');
    }
}
