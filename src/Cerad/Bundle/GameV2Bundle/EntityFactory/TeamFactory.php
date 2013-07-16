<?php
namespace Cerad\Bundle\GameV2Bundle\EntityFactory;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

/* 
 * I know I need something that will load/create a project, merge in config data and return the project
 * 
 * Also need a plan for creating entities from a fixture array
 */
class TeamFactory
{
    protected $teamManager;
    protected $levelManager;
    
    public function __construct($scheduleManager)
    {
        $this->teamManager  = $scheduleManager->teamManager;
        $this->levelManager = $scheduleManager->levelManager;
    }
    public function createFromFixture($entityFixture,$persist = true)
    {
        // Avoid creating a duplicate entity
        $manager = $this->teamManager;
        $id = IfIsSet::exe($entityFixture,'id');
        
        if ($id)
        {
            $existing = $manager->find($id);
            if ($existing)
            {
                // Maybe check for and apply changes?
                // Or maybe toss an exception?
                return $existing;
            }
        }
        $entity = $manager->newEntity();
        
        // Entity
        $name   = IfIsSet::exe($entityFixture,'name');
        $desc   = IfIsSet::exe($entityFixture,'desc');
        $role   = IfIsSet::exe($entityFixture,'role');
        $status = IfIsSet::exe($entityFixture,'status');
                
        if ($id)     $entity->setId    ($id);
        if ($role)   $entity->setRole  ($id);
        if ($status) $entity->setStatus($status);

        $entity->setName($name);
        $entity->setDesc($desc);
        
        // Need level
        $levelId = IfIsSet::exe($entityFixture,'level_id');
        if ($levelId)
        {
            $level = $this->levelManager->getReference($levelId); // Reference should work as well
            
            // Maybe toss an exception if level is not found?
            $entity->setLevel($level);
        }
        // TODO: Process identifiers
        $identifiersFixture = IfIsSet::exe($entityFixture,'identifiers',array());
        foreach($identifiersFixture as $identifierFixture)
        {
                $value  = isset($identifierFixture['value' ]) ? $identifierFixture['value' ] : null;
                $source = isset($identifierFixture['source']) ? $identifierFixture['source'] : null;
                $status = isset($identifierFixture['status']) ? $identifierFixture['status'] : null;
                
                if (!$value)
                {
                    // More or less arbiter style, name avaiable there
                    $value = $manager->hash(array($source,$sport,$season,$domain,$domainSub));
                }
                $identifier = $manager->newIdentifier();
                
                $identifier->setValue ($value);
                $identifier->setSource($source);
                
                if ($status) $identifier->setStatus($status);
                
                $entity->addIdentifier($identifier);            
        }
        // Persist and return
        if ($persist) $manager->persist($entity);
        return $entity;        
    }
}
?>
