<?php

namespace App\EventSubscriber;

use App\Controller\UserController;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ControllerSubscriber implements EventSubscriberInterface
{

//    /**
//     * @var Logger
//     */
//    private $logger;

    public function __construct(ContainerInterface $container)
    {
        //$this->logger = $container->get('monolog.logger.controller');
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::CONTROLLER => [
               ['changeController', 10]
           ]
       ];
    }

    /**
     * @param ControllerEvent $event
     */
    public function changeController(ControllerEvent  $event){

        if(!$event->isMasterRequest()){
            return;
        }
/*        $controller = $event->getController();
        $this->logger->info(get_class($controller[0]));
        foreach (get_class_methods($controller[0]) as $get_class_method) {
            $this->logger->info($get_class_method);
        }*/
    }
}