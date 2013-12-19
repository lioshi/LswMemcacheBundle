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

        $classesToDelete = array();
        
        foreach ($scheduledEntityChanges as $change => $entities) {
            foreach($entities as $entity) {
                // if (method_exists($entity, 'getId')){
                //     $id = $entity->getId();
                // } else {
                //     $id = '-';
                // }
                // $classesToDelete .= get_class($entity).' '.$id.' '.$change."\n";
                $classesToDelete[] = get_class($entity);
            }
        }

        $loggingMemcache = new LoggingMemcache;
        $memcached = $this->getMemCached();
        
        $LinkedModelsToCachedKeys = $memcached->get($loggingMemcache->getLinkedModelsToCachedKeysName());
        $cachelogs = count($LinkedModelsToCachedKeys)."\n";

        foreach ($classesToDelete as $classToDelete) {

            $cachelogs .= 'Classes to delete : '.$classToDelete."\n";
            if (isset($LinkedModelsToCachedKeys[$classToDelete])){
                foreach ($LinkedModelsToCachedKeys[$classToDelete] as $key) {
                    $memcached->delete($key);
                    $cachelogs .= 'Key deleted : '.$key."\n";
                }
            }
        }


        // $memcache = new \Memcached;
        // $memcache->addServers($servers);



        // foreach ($this->getMemcacheKeys() as $key) {
        //     $cachelogs .= $key."\n";
        //     // extract class from key
        //     $classesFromKey = $this->getClassFromKey($key);

        //     if (count($classesFromKey)){
        //         foreach ($classesFromKey as $classFromkey) {

        //             $cachelogs .= "=".$classFromkey."\n";



        //             if (in_array($classFromkey, $classesToDelete)){
        //                 $cachelogs .= "->>>>> to delete\n";

        //                 // $memcache = new \Memcached;
        //                 // $memcache->addServers($servers); // connect to those servers
        //                 // $memcache->delete();
        //             }
        //         }
        //     }
        // }

fputs($fp, $cachelogs); 
return;

    }

    /**
     * get all keys from memecahced servers hosts in parameters
     * @return [type] [description]
     */
    public function getMemcacheKeys() {

        return $this->getMemCached()->getAllKeys();
    } 

    private function getMemCached() {

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

        return $memcache;
    }

 
    
}
