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

        $keyCacheName = 'FPC_'.$event->getRequest()->getUri();
        
        if ($this->container->get('memcache.default')->get($keyCacheName)){
            
            $response = $this->container->get('memcache.default')->get($keyCacheName);
            $event->setResponse($response);
            return; 

        } else {

            // $response = new Response( 'not FPC' );
            // $response->headers->set('Content-Type', 'text/html');
            // $event->setResponse($response);
            return;

        }

        
        // $response = new Response( json_encode( $html ) );
        // $response->headers->set('Content-Type', 'text/html');

        // $event->setResponse($response);




        // return;



    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        $keyCacheName = 'FPC_'.$event->getRequest()->getUri();
        
        if ($this->container->get('memcache.default')->get($keyCacheName)){
            
            return;

        } else {

            $response = $event->getResponse();

            // save to memcached if response content has {entityModelLinks}
            $contentTypesAllowedInCache = array('application/json', 'text/html');
            if (in_array($response->headers->get('content-type'), $contentTypesAllowedInCache)){

                $this->container->get('memcache.default')->set($keyCacheName, $response, 0);
            }

            return $response;

        }

    }


}
