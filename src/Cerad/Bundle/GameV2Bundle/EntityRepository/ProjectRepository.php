<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class ProjectRepository extends BaseRepository
{ 
    public function getProjectClassName()           { return $this->_entityName; }
    public function getProjectIdentifierClassName() { return $this->_entityName . 'Identifier'; }
    
    public function newProject()
    {
        $entityClassName = $this->getProjectClassName();
        return new $entityClassName();
    }
    public function newProjectIdentifier()
    {
        $entityClassName = $this->getProjectIdentifierClassName();
        return new $entityClassName();
    }
    public function findProjectByIdentifierValue($value)
    {
        $repo = $this->_em->getRepository($this->getProjectIdentifierClassName());
        
        $identifier = $repo->findOneByValue($value);
        
        return $identifier? $identifier->getProject() : null;
    }
    /* -----------------------------------------------------
     * TODO V2: Revisit Later
     * Load a set of season choices
     * Probably want to filter on active projects only?
     */
    public function loadChoices($name,$sortDir = 'ASC')
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
    public function loadSeasonChoices($sortDir = 'DESC')
    {
        return $this->loadChoices('season',$sortDir);
    }
    public function loadDomainChoices($sortDir = 'ASC')
    {
        return $this->loadChoices('domain',$sortDir);
    }
}
?>
