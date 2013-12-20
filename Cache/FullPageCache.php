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
            $response->headers->add(array('fpc' => true ));
            $event->setResponse($response);
            return; 

        } else {

            return;
        }
    }


    public function onKernelResponse(FilterResponseEvent $event)
    {
        $keyCacheName = 'FPC_'.$event->getRequest()->getUri();
        
        if ($this->container->get('memcache.default')->get($keyCacheName)){
            
            return;

        } else {

            $response = $event->getResponse();

            // put this in page candidate to FPC
            // $response->headers->add(array('models-entities' => '{sqdsqdsqdsqd}'));

            // save to memcached if response content has {entityModelLinks}
            $contentTypesAllowedInCache = array('application/json', 'text/html');
            
            if (array_key_exists('models-entities', $response->headers)){
                $modelsEntities = true; 
            } else {
                $modelsEntities = false; 
            }

            if (
                in_array($response->headers->get('content-type'), $contentTypesAllowedInCache) &&
                $modelsEntities
                ){

                $this->container->get('memcache.default')->set($keyCacheName, $response, 0, array(
                        // how get the models and entities id link to this page?
                        // how get via response?

                    ));
            }

            return $response;

        }

    }


}
