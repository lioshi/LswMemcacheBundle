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

return; // do deactivate the listenner action

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
        
return $event->getResponse(); // do deactivate the listenner action


        $keyCacheName = 'FPC_'.$event->getRequest()->getUri();
        
        if ($this->container->get('memcache.default')->get($keyCacheName)){
            
            return;

        } else {

            $response = $event->getResponse();

            // put this in page candidate to FPC
            // $response->headers->add(array('linked-entities' => '{sqdsqdsqdsqd}'));

            // save to memcached if response content has {entityModelLinks}
            $contentTypesAllowedInCache = array('application/json', 'text/html');
            
            if (array_key_exists('linked-entities', $response->headers)){
                // IMPORTANT : Enlever les valeurs de linked-entities avant de la renvoyer, pas de visiilité sur le client pour la var linked-entities
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
