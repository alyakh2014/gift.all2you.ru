<?php


namespace App\Controller\Api;


use App\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiController
 * @package App\Controller\Api
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/all_articles", name="all_articles", methods="GET")
     * @param EntityManager $em
     * @return false|string
     */
    public function showAllArticles(EntityManagerInterface $em){
        $articles = $em->getRepository(Blog::class)->findAll();
        //todo remove the prepare procedure to service
        foreach ($articles as $item):
            $arr[] = [
                $item->getId(),
                $item->getTitle(),
                $item->getAuthor(),
                $item->getDateCreate()
                ];
        endforeach;
        return new Response(json_encode($arr));
    }
}