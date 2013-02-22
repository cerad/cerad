<?php

namespace Cerad\ArbiterBundle\Doctrine;

use Doctrine\Common\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnClearEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class Subscriber implements EventSubscriber
{
    protected $classNameMap = array
    (
        'Cerad\ArbiterBundle\Entity\V3Group'      => array('alias' => 'Group'),
        'Cerad\ArbiterBundle\Entity\V3GroupSub'   => array('alias' => 'GroupSub'),
        'Cerad\ArbiterBundle\Entity\V3GroupSite'  => array('alias' => 'Site'),
        'Cerad\ArbiterBundle\Entity\V3GroupLevel' => array('alias' => 'Level'),
        'Cerad\ArbiterBundle\Entity\V3Team'       => array('alias' => 'Team'),
        'Cerad\ArbiterBundle\Entity\V3Game'       => array('alias' => 'Game'),
        'Cerad\ArbiterBundle\Entity\V3GameTeam'   => array('alias' => 'GameTeam'),
        'Cerad\ArbiterBundle\Entity\V3GamePerson' => array('alias' => 'GamePerson'),
        
    );
    public function getSubscribedEvents()
    {
        return array(Events::preFlush, Events::onFlush);
    }
    protected $counts;
    
    protected function resetCounts() 
    {
        $this->counts = array('insert' => array(),'update' => array(), 'delete' => array());
    }
    protected function addCount($type,$entity)
    {
        $className = get_class($entity);
        if (isset($this->classNameMap[$className]))
        {
            $alias = $this->classNameMap[$className]['alias'];
            if (isset($this->counts[$type][$alias])) $this->counts[$type][$alias] = $this->counts[$type][$alias] + 1;
            else                                     $this->counts[$type][$alias] = 1;
        }
        else
        {
            // Missing class name
        }
    }
    public function getCounts() { return $this->counts; }
    
    /* =================================================
     * This should give me counts for insertions etc
     * 
     * Yes but updating stuff here means recalculating change sets
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {   
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        
        foreach ($uow->getScheduledEntityInsertions() AS $entity) 
        {
            $this->addCount('insert',$entity);
            
            // $entityClassName = getclass($entity);
        }
        foreach ($uow->getScheduledEntityUpdates() AS $entity) 
        {
            $this->addCount('update',$entity);
        }
        foreach ($uow->getScheduledEntityDeletions() AS $entity) 
        {
            $this->addCount('delete',$entity);
        }
        foreach ($uow->getScheduledCollectionDeletions() AS $col) 
        {

        }
        foreach ($uow->getScheduledCollectionUpdates() AS $col) 
        {

        }    
    }
    /* =========================================
     * Could use this to reset counters I suppose
     * 
     * Certainly use this to sync game-game_team
     * 
     * Currently use this to ensure keys(hashes) are set properly
     * 
     * $em->flush calls $uom->commit when sends out preFlush
     * 
     * $uom->computeChangeSets
     * 
     * Then send onFlush
     * 
     * $em->persist ends up $uow->scheduleForInsert
     * 
     */
    public function preFlush(PreFlushEventArgs $eventArgs)
    {
        // So each flush gets a new set of counts
        $this->resetCounts();
        return;
        
       $em = $eventArgs->getEntityManager();
       $uow = $em->getUnitOfWork();
       
       // This will not get new objects even if persisted
       foreach($uow->getIdentityMap() as $className => $entities)
       {
           echo sprintf("Class Name %s\n",$className);
           continue;
           
           if (isset($this->classNameMap[$className]))
           {   
               $alias = $this->classNameMap[$className]['alias'];
               if ($alias == 'Game') die('preFlush ' . $alias);
               switch($alias)
               {
                   case 'Game':
                 //case 'Team':
                 //case 'Level': 
                 //case 'GameTeam':
                       die('preFlush ' . $alias);
                       foreach($entities as $entity)
                       {
                           $entity->setKeys();
                       }
                       break;
               }
           }
       }
    }
}
?>
