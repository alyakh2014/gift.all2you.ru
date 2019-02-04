<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Blogresponse;
use App\Entity\User;
use App\Form\BlogresponseType;
use App\Repository\BlogRepository;
use App\Repository\BlogresponseRepository;
use App\Repository\UserRepository;
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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
        $blogresponses = [];
        foreach($blogresponseRepository->findAll() as $key=>$value){
            $blogresponses[$key]['id'] = $value->getId();
            $blogresponses[$key]['active'] = $value->getIsActive();
            //Получаем название статьи к которой добавлен отзыв
            $blogresponses[$key]['blogId'] = $value->getBlogId();
            $blogresponses[$key]['blogTitle'] = $this->getDoctrine()
                                            ->getRepository(Blog::class)
                                            ->findOneBy(["id" => $value->getBlogId()])->getTitle();
            $blogresponses[$key]['message'] = $value->getText();
            $blogresponses[$key]['date'] = $value->getDate();
            //Получаем имя пользователя отзыва
            $blogresponses[$key]['userId'] = $value->getUser();
            $blogresponses[$key]['user']  = $this->getDoctrine()
                        ->getRepository(User::class)
                        ->findOneBy(["id" => $value->getUser()])
                        ->getUsername();
        }
        return $this->render('blogresponse/index.html.twig', ['blogresponses' => $blogresponses /*$blogresponseRepository->findAll()*/]);
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
            ->add('user', HiddenType::class)
            ->add('email', HiddenType::class)
            ->add('isActive', HiddenType::class)
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
    public function show(BlogresponseRepository $blogresponseRepository, Request $request): Response
    {
        $response = $blogresponseRepository->findOneBy(['id'=>$request->get('id')]);
        $res = [];
        $res['id'] = $response->getId();
        $blogItem = $this->getDoctrine()
                    ->getRepository(Blog::class)
                    ->findOneBy(['id'=>$response->getBlogId()]);
        $res['blogId'] = $blogItem->getId();
        $res['blogName'] = $blogItem->getTitle();

        $res['email'] = $response->getEmail();
        $res['text'] = $response->getText();
        $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(["id" => $response->getUser()]);
        $res['userId'] = $user->getId();
        $res['userName'] = $user->getUsername();
        $res['time'] = $response->getDate();

       return $this->render('blogresponse/show.html.twig', ['blogresponse' => $res]);
    }

    /**
     * @Route("/{id}/edit", name="blogresponse_edit", methods="GET|POST")
     */
    public function edit(
        Request $request,
        Blogresponse $blogresponse,
        AuthorizationCheckerInterface $authChecker): Response
    {

        return $this->redirectToRoute("blog_index");
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

    /**
     * @Route("/activate/{id}", name="blogresponse_activate", methods="GET|POST")
     */
    public function activate(Request $request, Blogresponse $blogresponse): Response
    {
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $blogresp = $em->getRepository(Blogresponse::class)->find($id);

        if (!$blogresp) {
            throw $this->createNotFoundException(
                'No response found for id '.$id
            );
        }

        $blogresp->setIsActive(1);
        $em->flush();
        return $this->redirectToRoute('blogresponse_index');
    }
    /**
     * @Route("/deactivate/{id}", name="blogresponse_deactivate", methods="GET|POST")
     */
    public function deactivate(Request $request, Blogresponse $blogresponse): Response
    {
        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();
        $blogresp = $em->getRepository(Blogresponse::class)->find($id);

        if (!$blogresp) {
            throw $this->createNotFoundException(
                'No response found for id '.$id
            );
        }

        $blogresp->setIsActive(0);
        $em->flush();
        return $this->redirectToRoute('blogresponse_index');
    }
}
