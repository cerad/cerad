<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class ProjectRepository extends BaseRepository
{ 
    public function getProjectClassName()           { return $this->_entityName; }
    public function getProjectFieldClassName()      { return $this->_entityName . 'Field'; }
    public function getProjectLevelClassName()      { return $this->_entityName . 'Level'; }
    public function getProjectIdentifierClassName() { return $this->_entityName . 'Identifier'; }
    
    public function newProject()
    {
        $entityClassName = $this->getProjectClassName();
        return new $entityClassName();
    }
    public function newProjectField()
    {
        $entityClassName = $this->getProjectFieldClassName();
        return new $entityClassName();
    }
    public function newProjectIdentifier()
    {
        $entityClassName = $this->getProjectIdentifierClassName();
        return new $entityClassName();
    }
    /* ========================================================
     * Access to dependent managers
     */
    public function getProjectIdentifierManager() 
    {
        return $this->_em->getRepository($this->getProjectIdentifierClassName());
    }
    public function getProjectLevelManager() 
    {
        return $this->_em->getRepository($this->getProjectLevelClassName());
    }
     public function getProjectFieldManager() 
    {
        return $this->_em->getRepository($this->getProjectFieldClassName());
    }
    /* ========================================================
     * Find stuff
     */
    public function findProject($id) { return $this->find($id); }

    public function findProjectByIdentifierValue($value)
    {
        $manager = $this->getProjectIdentifierManager();
        
        $identifier = $manager->findOneByValue($value);
        
        return $identifier? $identifier->getProject() : null;
    }
    /* -----------------------------------------------------
     * TODO V2: Revisit Later
     * Load a set of season choices
     * Probably want to filter on active projects only?
     */
    public function findDistinceChoices($name,$sortDir = 'ASC')
    {
        $qb = $this->createQueryBuilder('project');
        
        // Build query
        $qb->addSelect('distinct project.' . $name);
        
        $qb->addOrderBy('project.' . $name,$sortDir);
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item[$name]] = $item[$name];
        }
        return $choices;
    }
    public function findSeasonChoices($sortDir = 'DESC')
    {
        return $this->findDistinceChoices('season',$sortDir);
    }
    public function findDomainChoices($sortDir = 'ASC')
    {
        return $this->findDistinceChoices('domain',$sortDir);
    }
    /* ===================================================
     * Grab a distinct list of entities for a list of projects
     * It could easily be argued that does not belong here as
     * it is directly related to form select elements
     * 
     * Maybe we need a "project choice" service
     */
    public function findProjectEntityChoices($projectEntityManager,$projects)
    {
        //$projectEntityManager = $this->getProjectFieldManager();
        
        $searchData = array();
        if (count($projects)) $searchData['entity1'] = $projects;

        $rels = $projectEntityManager->findBy($searchData,array('sort2' =>'ASC','name2' => 'ASC'));
        
        $names   = array();
        $choices = array();
        foreach($rels as $rel)
        {
            $id   = $rel->getEntity2()->getId();
            $name = $rel->getEntity2()->getName();
            
            // This gives a false impression of merging fields across domains
            if (!isset($names[$name]))
            {
                $names  [$name] = true;
                $choices[$id]   = $rel->getName2();
            }
        }
        return $choices;
    }
    /* ===================================================
     * Grab a distinct list of fields for a list of projects
     */
    public function findFieldChoices($projects)
    {
        return $this->findProjectEntityChoices($this->getProjectFieldManager(),$projects);
        
    }
    /* ===================================================
     * Grab a distinct list of levels for a list of projects
     */
    public function findLevelChoices($projects)
    {
        return $this->findProjectEntityChoices($this->getProjectLevelManager(),$projects);
    }
}
?>
