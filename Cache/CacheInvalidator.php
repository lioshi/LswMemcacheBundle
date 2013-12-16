<?php

/**
 * Better than using
 * $this->getDoctrine()->getManager()->getConfiguration()->getResultCacheImpl()->delete(...);
 * in your controllers
 */

namespace Lsw\MemcacheBundle\Cache;

use Doctrine\ORM\Event\OnFlushEventArgs;

use \Exception;

class CacheInvalidator 
{
  
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        
$fp = fopen("/data/www/testa/web/logInvalidatorCache.txt","w"); 

        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        // $uow->getScheduledCollectionDeletions()
        // $uow->getScheduledCollectionUpdates()

        $scheduledEntityChanges = array(
            'insert' => $uow->getScheduledEntityInsertions(),
            'update' => $uow->getScheduledEntityUpdates(),
            'delete' => $uow->getScheduledEntityDeletions()
        );

        $cacheIds = array();

        $cachelogs = date('Y-m-d h:i:s')."\n";
        foreach ($scheduledEntityChanges as $change => $entities) {
            foreach($entities as $entity) {
                if (method_exists($entity, 'getId')){
                    $id = $entity->getId();
                } else {
                    $id = '-';
                }
                $cachelogs .= get_class($entity).' '.$id.' '.$change."\n";

                // on récupère la classe de l'objet
                $class = get_class($entity);
                if (strrpos($class, "\\")===false){
                    $classesToDelete[] = $class;
                } else {
                    $classesToDelete[] = strtolower(substr($class, strrpos($class, "\\")+1));
                }
            }
        }

        foreach ($classesToDelete as $class) {
            $cachelogs .= "to delete =".$class."\n";
        }


        foreach ($this->getMemcacheKeys() as $key) {
            $cachelogs .= $key."\n";
            // extract class from key
            $classesFromKey = $this->getClassFromKey($key);

            if (count($classesFromKey)){
                foreach ($classesFromKey as $classFromkey) {

                    $cachelogs .= "=".$classFromkey."\n";



                    if (in_array($classFromkey, $classesToDelete)){
                        $cachelogs .= "->>>>> to delete\n";

                        // $memcache = new \Memcached;
                        // $memcache->addServers($servers); // connect to those servers
                        // $memcache->delete();
                    }
                }
            }
        }

fputs($fp, $cachelogs); 
return;

    }

    /**
     * get all keys from memecahced servers hosts in parameters
     * @return [type] [description]
     */
    public function getMemcacheKeys() {
        $return = array();

        $paramMemcachehosts = $this->container->getParameter('memcachedhosts');  // get parameters hosts for memcached 
        // $servers = array(
        //     array('mem1.domain.com', 11211),
        //     array('mem2.domain.com', 11211)
        // );
        foreach ($paramMemcachehosts as $paramMemcachehost) {
            $servers[] = array($paramMemcachehost['dsn'],$paramMemcachehost['port']);
        }

        $memcache = new \Memcached;
        $memcache->addServers($servers); // connect to those servers

        return $memcache->getAllKeys();
    } 

    private function getClassFromKey($key) {

        preg_match('/#.*#/', $key, $matches);
        if (count($matches)){
            $matches[0] = str_replace('#', '', $matches[0]);
            return explode('-', $matches[0]);
        } else {
            return array();
        }
        

    }


}
