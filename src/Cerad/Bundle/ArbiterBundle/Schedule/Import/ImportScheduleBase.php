<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Import;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\PropertyChangedListener;

use Doctrine\ORM\Events;
use Doctrine\ORM\Event\OnFlushEventArgs;

class ImportScheduleBase implements PropertyChangedListener, EventSubscriber
{
  //protected $manager;
    protected $gameManager;
    protected $fieldManager;
    protected $levelManager;
    protected $projectManager;
    
    protected $results;
    protected $gameHasChanged;
    
    protected $persistFlag = false; // Allows a dry run with updating most things
    protected $flushCount  = 0;
 
    public function __construct($manager)
    {
      //$this->manager        = $manager;
        $this->gameManager    = $manager->gameManager;
        $this->fieldManager   = $manager->fieldManager;
        $this->levelManager   = $manager->levelManager;
        $this->projectManager = $manager->projectManager;
        
        $this->gameClassName = $this->gameManager->getGameClassName();
        
    }
    public function getSubscribedEvents()
    {
        return array(Events::onFlush);
    }
    
    protected function persist($item) 
    { 
        if ($this->persistFlag) $this->gameManager->persist($item);     
    }
    protected function flush($always = false)
    { 
        if (!$this->persistFlag) return;
        
        if ($this->flushCount < 100 && !$always) $this->flushCount++;
        else
        {
            // Clearing after flushing reduces memory consumption
            //$this->levelManager->flush();
            //$this->projectManager->flush();
            
            $this->gameManager->flush();
            
            /* ===================================
             * Need to revisit, slight decrease in memory but increase in time
             * For clean reload 
             * With teams the memory decrease is better
             */
            $this->gameManager->clear(); // This causes issues with my cache when creating new projects
            
            $this->fieldManager  ->clearCache();
            $this->levelManager  ->clearCache();
            $this->projectManager->clearCache();
            
            $this->flushCount = 0;
        }
    }
    
    /* =================================================
     * Listen to the flush and update results
     * Entity names should come from the managers
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {   
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
    
        $results = $this->results;
        
        foreach ($uow->getScheduledEntityInsertions() AS $entity) 
        {
            $className = get_class($entity);
            switch($className)
            {
                case $this->gameClassName: $results->totalGamesInserted++; break;
            }
        }
        foreach ($uow->getScheduledEntityUpdates() AS $entity) 
        {
            $className = get_class($entity);
            switch($className)
            {
                case 'Cerad\Bundle\GameBundle\Entity\Game':       $results->totalGamesUpdated++;       break;
                case 'Cerad\Bundle\GameBundle\Entity\GameTeam':   $results->totalGameTeamsUpdated++;   break;
                case 'Cerad\Bundle\GameBundle\Entity\GamePerson': $results->totalGamePersonsUpdated++; break;
            }
        }
        foreach ($uow->getScheduledEntityDeletions() AS $entity) 
        {
        }
        foreach ($uow->getScheduledCollectionDeletions() AS $col) 
        {

        }
        foreach ($uow->getScheduledCollectionUpdates() AS $col) 
        {

        }    
    }    
    /* =====================================================================
     * Property change listener for debugging
     */
    public function propertyChanged($item, $propName, $oldValue, $newValue)
    {
        echo sprintf(" Prop: %s %s %s\n",$propName,$oldValue,$newValue);
        return;
        
        $this->gameHasChanged = true;
        switch($propName)
        {
            case 'dtBeg':
            case 'dtEnd':
            case 'score':
            case 'name':
            case 'field':
            case 'status':
            case 'level': // NasoaSlots20130201.xml
            case 'role':
                return;
        }
        echo $item;
        echo sprintf(" Prop: %s\n",$propName);;
        
        die();
    }
}
?>
