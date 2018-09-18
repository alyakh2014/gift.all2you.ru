<?php

namespace App\Controller;

use App\Entity\Blogresponse;
use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog_index", methods="GET")
     */
    public function index(
        BlogRepository $blogRepository,
        Request $request,
        PaginatorInterface $paginator): Response
    {

        //$q = $request->query->get('q');
        //$queryBuilder = $blogRepository->getWithSearchQueryBuilder($q);
        $queryBuilder = $blogRepository->getWithSearchQueryBuilder();
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );
       // return $this->render('blog/index.html.twig', ['blogs' => $blogRepository->findAll()]);
        return $this->render('blog/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/new", name="blog_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($blog);
            $em->flush();

            return $this->redirectToRoute('blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_show", methods="GET")
     */
    public function show(Blog $blog): Response
    {

        $response = new Blogresponse();
        $response->setDate(new \DateTime());
        $form = $this->createFormBuilder($response)
                ->add('text', TextType::class)
                ->add('date', DateType::class)
                ->add('username', TextType::class)
                ->add('blog_id', IntegerType::class)
                ->add('save', SubmitType::class, array('label'=>'Оставить отзыв'))
                ->getForm();

        return $this->render('blog/show.html.twig', ['blog' => $blog, 'form'=>$form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", methods="GET|POST")
     */
    public function edit(Request $request, Blog $blog): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('blog_edit', ['id' => $blog->getId()]);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog_delete", methods="DELETE")
     */
    public function delete(Request $request, Blog $blog): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($blog);
            $em->flush();
        }
        return $this->redirectToRoute('blog_index');
    }
}
