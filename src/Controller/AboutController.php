<?php

namespace App\Controller;

use App\Entity\Partners;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\PartnersRepository;

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
    public function contacts(){
        return $this->render('about/contacts.html.twig');
    }


}
