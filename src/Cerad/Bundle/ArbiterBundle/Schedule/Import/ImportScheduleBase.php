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
    protected $teamManager;
    protected $fieldManager;
    protected $levelManager;
    protected $projectManager;

    // Caching
    protected $projects;
    protected $fields;
    protected $levels;
    protected $teams;
    protected $venues;
    
    protected $results;
    protected $gameHasChanged;
    
    protected $persistFlag = false; // Allows a dry run with updating most things
    protected $flushCount  = 0;
 
    public function __construct($manager)
    {
      //$this->manager        = $manager;
        $this->gameManager    = $manager->gameManager;
        $this->teamManager    = $manager->teamManager;
        $this->fieldManager   = $manager->fieldManager;
        $this->levelManager   = $manager->levelManager;
        $this->projectManager = $manager->projectManager;
        
        $this->gameClassName       = $this->gameManager->getGameClassName();
        $this->gameTeamClassName   = $this->gameManager->getGameTeamClassName();
        $this->gamePersonClassName = $this->gameManager->getGamePersonClassName();
        
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
            
            $this->projects = null;
            $this->fields   = null;
            $this->levels   = null;
            $this->teams    = null;
            
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
                case $this->gameClassName:       $results->totalGamesUpdated++;       break;
                case $this->gameTeamClassName:   $results->totalGameTeamsUpdated++;   break;
                case $this->gamePersonClassName: $results->totalGamePersonsUpdated++; break;
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
    /* ========================================
     * Project Caching
     */
    protected function getProject($source,$sport,$season,$domain,$domainSub)
    {
        $manager = $this->projectManager;
        $hash = $manager->hash(array($source,$sport,$season,$domain,$domainSub));
        
        if (isset($this->projects[$hash])) return $this->projects[$hash];
        
        $project = $manager->findByIdentifierValue($hash);
        if ($project)
        {
            $this->projects[$hash] = $project;
            return $project;
        }
        $project = $manager->newEntity();
        
        $project->setSport    ($sport);
        $project->setSource   ($source);
        $project->setSeason   ($season);
        $project->setDomain   ($domain);
        $project->setDomainSub($domainSub);
        
        $name = sprintf('%s %s %s %-8s %s',$source,$sport,$season,$domain,$domainSub);
        $project->setName($name);
        
        $identifier = $manager->newIdentifier();
        $identifier->setSource ($source);
        $identifier->setValue  ($hash);
        $project->addIdentifier($identifier);
        
        $manager->persist($project);
        $this->projects[$hash] = $project;
        
        return $project;
    }
    /* ========================================
     * Field Caching
     */
    protected function getField($project,$name)
    {
        $manager = $this->fieldManager;
        $hash = $manager->hash(array($project->getSource(),$project->getDomain(),$name));
        
        if (isset($this->fields[$hash])) 
        {   
            $item = $this->fields[$hash];
            $project->addField($item);
            return $item;
        }
        $item = $manager->findFieldByIdentifierValue($hash);
        if ($item)
        {   
            $this->fields[$hash] = $item;
            $project->addField($item);
            return $item;
        }
        $item = $manager->newField();
                
        $item->setName($name);
        
        $identifier = $manager->newFieldIdentifier();
        $identifier->setSource ($project->getSource());
        $identifier->setValue  ($hash);
        $item->addIdentifier($identifier);
        
        $manager->persist($item);
        $this->fields[$hash] = $item;
        $project->addField($item);
         
        return $item;
    }
    /* ========================================
     * Team Caching
     */
    protected function getTeam($project,$name,$level,$role = 'Physical')
    {
        // Do some filtering and try to limit to "real" teams
        switch($name)
        {
            case null: case '':
            case '1A': case '1B': case '1SEED':
            case '2A': case '2B': case '2SEED':
            case '3A': case '3B': case '3SEED':
            case '4A': case '4B': case '4SEED':
            case 'A1': case 'A2': case 'A3': case 'A4': case 'A5': case 'A6': 
            case 'B1': case 'B2': case 'B3': case 'B4': case 'B5': case 'B6': 
            case 'C1': case 'C2': case 'C3': case 'C4': case 'C5': case 'C6': 
            case 'D1': case 'D2': case 'D3': case 'D4': case 'D5': case 'D6': 
                
            case 'Bracket I 1st':  case 'Bracket I 2nd':
            case 'Bracket II 1st': case 'Bracket II 2nd':
                
            case 'G1L': case 'G2L': case 'G3L': case 'G4L': case 'G5L': case 'G6L': case 'G7L': case 'G8L':
            case 'G1W': case 'G2W': case 'G3W': case 'G4W': case 'G5W': case 'G6W': case 'G7W': case 'G8W':
            
            case 'F1W': case 'F2W': case 'F3W': case 'F4W':
                
            case 'TBD': case 'TBD1': case 'TBD2': case 'TBD3': case 'TBD4': case 'TOC':
                
            case 'Place 1': case 'Place 2': case 'Place 3': case 'Place 4': case 'Place 5': 
            case 'Place 6': case 'Place 7': case 'Place 8': case 'Place 9':
                
            case 'team1' : case 'team2' : case 'team3' : case 'team4' : 
            case 'Team 1': case 'Team 2': case 'Team 3': case 'Team 4': 
            case 'Team 5': case 'Team 6': case 'Team 7': case 'Team 8':
                
            case 'Wild Card 1': case 'Wild Card 2': case 'Wild Card 3': case 'Wild Card 4': 
            case 'Wild Card 5': case 'Wild Card 6': case 'Wild Card 7': case 'Wild Card 8': 
                                
                return;  
        }
      //if (strpos($name,'Wild Card ')   === 0) return;
        if (strpos($name,'Winner Game ') === 0) return;
        
        // Could almost go by size
        // LFC is valid
        if (strlen($name) < 5) 
        {
            switch($name)
            {
                case 'LFC':
                case '120': case '226': case '919': case '926':
                    break;
                default:
                    echo sprintf("Team Name %s\n",$name);
           }
        }
        // Onwards
        $manager = $this->teamManager;
        $hash = $manager->hash(array($project->getSource(),$project->getDomain(),$name));
        
        if (isset($this->teams[$hash])) 
        {   
            $item = $this->teams[$hash];
            $project->addTeam($item);
            return $item;
        }
        $item = $manager->findByIdentifierValue($hash);
        if ($item)
        {   
            $this->teams[$hash] = $item;
            $project->addTeam($item);
            return $item;
        }
        $item = $manager->newEntity();
                
        $item->setName ($name);
        $item->setRole ($role);
        $item->setLevel($level);
        
        $identifier = $manager->newIdentifier();
        $identifier->setSource ($project->getSource());
        $identifier->setValue  ($hash);
        $item->addIdentifier($identifier);
        
        $manager->persist($item);
        $this->teams[$hash] = $item;
        $project->addTeam($item);
         
        return $item;
    }
    /* ========================================
     * Level Caching
     */
    protected function getLevel($project,$name)
    {
        $manager = $this->levelManager;
        $hash = $manager->hash(array($project->getSource(),$project->getDomain(),$name));
        
        if (isset($this->levels[$hash])) 
        {   
            $item = $this->levels[$hash];
            $project->addLevel($item);
            return $item;
        }
        $item = $manager->findLevelByIdentifierValue($hash);
        if ($item)
        {   
            $this->levels[$hash] = $item;
            $project->addLevel($item);
            return $item;
        }
        $item = $manager->newLevel();
        
        $item->setName($name);
        
        $identifier = $manager->newLevelIdentifier();
        $identifier->setSource ($project->getSource());
        $identifier->setValue  ($hash);
        $item->addIdentifier($identifier);
        
        $manager->persist($item);
        $this->levels[$hash] = $item;
        $project->addLevel($item);
         
        return $item;
    }
}
?>
