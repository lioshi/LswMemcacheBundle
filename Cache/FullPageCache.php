<?php

namespace Lsw\MemcacheBundle\Cache;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use Symfony\Component\HttpKernel\Exception\HttpException;

use Symfony\Component\HttpFoundation\Response;

class FullPageCache
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {

 return;

$html = 
'
'.$this->container->get('kernel')->getEnvironment().'   
zaeaezae'.
$event->getRequest()->getUri()


;

        
        $response = new Response( json_encode( $html ) );
        $response->headers->set('Content-Type', 'text/html');

        $event->setResponse($response);




        return;



    }


    public function onKernelResponse(FilterResponseEvent $event)
    {

    return;

    $html = 
    '
    '.$this->container->get('kernel')->getEnvironment().'   
    ssdd '


    ;

            
            $response = new Response( json_encode( $html ) );
            $response->headers->set('Content-Type', 'text/html');

            $event->setResponse($response);




            return;



        }


}
