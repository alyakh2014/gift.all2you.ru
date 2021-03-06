<?php

namespace App\Controller;

use App\Entity\Blogresponse;
use App\Entity\Blog;
use App\Entity\User;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use App\Repository\BlogresponseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
     * @Route("/blog_admin", name="blog_index_admin", methods="GET")
     */
    public function index_admin(
        BlogRepository $blogRepository,
        Request $request,
        PaginatorInterface $paginator,
        AuthorizationCheckerInterface $authChecker): Response
    {

        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('blog_index');
        }
        return $this->render('blog/blog_admin.html.twig', ['blogs' => $blogRepository->findAll()]);
    }

    /**
     * @Route("/new", name="blog_new", methods="GET|POST")
     */
    public function new(Request $request, AuthorizationCheckerInterface $authChecker): Response
    {
        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('blog_index');
        }
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
    public function show(
        Blog $blog,
        BlogresponseRepository $brr
        ): Response
    {

        //Получаем все отзывы по данному блогу
        $otzivy = $this->getDoctrine()
                    ->getRepository(Blogresponse::class)
                    ->findBy(["blog_id" => $blog, "isActive"=>1]);

        $arrOtzivy = [];
        foreach($otzivy as $key=>$item){
            $arrOtzivy[$key]["id"] = $item->getId();
            $arrOtzivy[$key]["blog_id"] = $item->getBlogId();
            $arrOtzivy[$key]["email"] = $item->getEmail();
            $arrOtzivy[$key]["text"] = $item->getText();
            $arrOtzivy[$key]["date"] = $item->getDate();
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['id'=>$item->getUser()]);
           if(count($user) > 0 && $user[0]->getId() > 0){
             $arrOtzivy[$key]["user"] = $user[0]->getUsername();
           }else{
               unset($arrOtzivy[$key]);
           }
        }
        //Формируем форму
        $blog_response = new Blogresponse();

        $blog_response->setDate(new \DateTime());
        $form = $this->createFormBuilder($blog_response)
                ->setAction($this->generateUrl('blogresponse_new'))
                ->add('blog_id', HiddenType::class)
                ->add('text', TextareaType::class)
                ->add('user', HiddenType::class)
                ->add('email', HiddenType::class)
                ->add('isActive', HiddenType::class)
                ->add('save', SubmitType::class, array('label'=>'Оставить отзыв'))
                ->getForm();

        return $this->render('blog/show.html.twig', ['blog' => $blog, 'otzivy'=> $arrOtzivy, 'form'=>$form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", methods="GET|POST")
     */
    public function edit(Request $request, Blog $blog, AuthorizationCheckerInterface $authChecker): Response
    {
        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('blog_index');
        }
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
