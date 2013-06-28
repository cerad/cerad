<?php
namespace Cerad\Bundle\GameV2Bundle\EntityFactory;

/* 
 * I know I need something that will load/create a project, merge in config data and return the project
 * 
 * Also need a plan for creating entities from a fixture array
 */
class ProjectFactory
{
    protected $manager;
    
    public function __construct($manager)
    {
        $this->manager = $manager;
    }
    public function createFromFixture($fixture,$persist = true)
    {
        $manager = $this->manager;
        
        $id   = isset($fixture['id'  ]) ? $fixture['id'  ] : null;
        $name = isset($fixture['name']) ? $fixture['name'] : null;
        $desc = isset($fixture['desc']) ? $fixture['desc'] : null;
        
        $status    = isset($fixture['status'])    ? $fixture['status']    : null;
        $sport     = isset($fixture['sport'])     ? $fixture['sport']     : null;
        $source    = isset($fixture['source'])    ? $fixture['source']    : null;
        $season    = isset($fixture['season'])    ? $fixture['season']    : null;
        $domain    = isset($fixture['domain'])    ? $fixture['domain']    : null;
        $domainSub = isset($fixture['domainSub']) ? $fixture['domainSub'] : null;
        $data      = isset($fixture['data'])      ? $fixture['data']      : null;
    
        // Might want to try loading first
        if ($id)
        {
            $existing = $manager->find($id);
            if ($existing)
            {
                // Maybe check for and apply changes?
                return $existing;
            }
        }
        $entity = $manager->newProject();
        
        if ($id)     $entity->setId    ($id);
        if ($status) $entity->setStatus($status);
        
        $entity->setName     ($name);
        $entity->setDesc     ($desc);
        $entity->setSport    ($sport);
        $entity->setSource   ($source);
        $entity->setSeason   ($season);
        $entity->setDomain   ($domain);
        $entity->setDomainSub($domainSub);
        $entity->setData     ($data);
        
        // Process identifiers
        $identifierFixtures = isset($fixture['identifiers']) ? $fixture['identifiers'] : null;
        foreach($identifierFixtures as $identifierFixture)
        {
                $value  = isset($identifierFixture['value' ]) ? $identifierFixture['value' ] : null;
                $source = isset($identifierFixture['source']) ? $identifierFixture['source'] : null;
                $status = isset($identifierFixture['status']) ? $identifierFixture['status'] : null;
                
                if (!$value)
                {
                    // More or less arbiter style, name avaiable there
                    $value = $manager->hash(array($source,$sport,$season,$domain,$domainSub));
                }
                $identifier = $manager->newProjectIdentifier();
                
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
