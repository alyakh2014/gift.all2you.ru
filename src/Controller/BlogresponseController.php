<?php

namespace App\Controller;

use App\Entity\Blogresponse;
use App\Form\BlogresponseType;
use App\Repository\BlogresponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
        $blogresponse->setDate(new \DateTime());
        $form = $this->createFormBuilder($blogresponse)
            ->add('blog_id', HiddenType::class)
            ->add('text', TextareaType::class)
            ->add('user_id', HiddenType::class)
            ->add('email', HiddenType::class)
            ->add('save', SubmitType::class, array('label'=>'Leave response'))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($blogresponse);
            $em->flush();
            $this->addFlash(
                'notice',
                'Отзыв успешно отправлен!'
            );
            return $this->redirectToRoute('blog_show', ['id' => $blogresponse->getBlogId()]);
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
