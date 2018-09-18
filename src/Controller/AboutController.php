<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
    public function partners(){
        return $this->render('about/partners.html.twig');
    }

    /**
     * @Route("/contacts", name="about_contacts")
     */
    public function contacts(){
        return $this->render('about/contacts.html.twig');
    }


}
