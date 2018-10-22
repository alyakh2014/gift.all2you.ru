<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\PartnersRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class AboutController extends AbstractController
{
    /**
     * @Route("/about", name="about_index")
     */
    public function index()
    {
        return $this->render('about/index.html.twig', [
            'controller_name' => 'AboutController',
        ]);
    }

    /**
     * @Route("/your_blog", name="about_blog")
     */
    public function blog(){
        return $this->render('about/blog.html.twig');
    }

    /**
     * @Route("/partners", name="about_partners")
     */
    public function partners(
        PartnersRepository $partnersRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response
    {

        $queryBuilder = $partnersRepository->getWithSearchQueryBuilder();
        $pagination = $paginator->paginate(
            $queryBuilder, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            6/*limit per page*/
        );
        return $this->render('about/partners.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/contacts", name="about_contacts")
     */
    public function contacts(): Response
    {

        //Формируем форму
        $feedback = new Feedback();
       // $feedback->setDate(new \DateTime());
        $form=$this->createFormBuilder($feedback)
            ->setAction($this->generateUrl('feedback_new'))
            ->add('name', TextType::class, array('label'=>'Your name'))
            ->add('email', TextType::class)
            ->add('subject', TextareaType::class)
            ->add('message', TextareaType::class)
            ->add('save', SubmitType::class, array('label'=>'Send new message'))
            ->getForm();
        return $this->render('feedback/new.html.twig', ['form'=> $form->createView()]);
    }

    /**
     * @Route("/admin")
     */
    public function admin(): Response
    {
        return new Response('<html><body>Admin page!</body></html>');
    }

}
