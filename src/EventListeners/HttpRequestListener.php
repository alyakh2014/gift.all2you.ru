<?php


namespace App\EventListeners;

use App\Entity\LogRoutes;
use Doctrine\ORM\EntityManager;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class HttpRequestListener
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * HttpRequestListener constructor.
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
    public function __construct(ContainerInterface $container, $em)
    {
//        $this->logger = $container->get('monolog.logger.http');
//        $this->em = $em;
    }

    public function onKernelRequest(RequestEvent $event){
        $requestType = $event->getRequestType();
        $requestInfo = $event->getRequest()->getRequestUri();
      //  $this->logger->info("Date: ".date("d-m-Y H:i")."; Request type: ".$requestType."; Request URI: ".$requestInfo."\n");
        //Write to the db
      /*  $model = new LogRoutes();
        $model->setDate(new \DateTime())
            ->setRequestType($requestType)
            ->setPage($requestInfo);
        $this->em->persist($model);
        $this->em->flush();*/
    }
}