<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class ProjectRepository extends BaseRepository
{ 
    public function getProjectClassName()           { return $this->_entityName; }
    public function getProjectFieldClassName()      { return $this->_entityName . 'Field'; }
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
    /* ===================================================
     * Grab a distinct list of fields for a list of projects
     */
    public function loadFieldChoices($projects)
    {
        $projectFieldRepo = $this->_em->getRepository($this->getProjectFieldClassName());
        
        $searchData = array();
        if (count($projects)) $searchData['project'] = $projects;
        foreach($projects as $project)
        {
            //echo sprintf("Project %s %s<br />",$project->getId(),$project->getName());
        }
        $projectFields = $projectFieldRepo->findBy($searchData,array('sort' =>'ASC','name' => 'ASC'));
        
        $names = array();
        $choices = array();
        foreach($projectFields as $projectField)
        {
            $fieldId   = $projectField->getField()->getId();
            $fieldName = $projectField->getField()->getName();
            
          //if (!isset($choices[$fieldId]) || 1) $choices[$fieldId] = $projectField->getName();
            
            // This gives a false impression of merging fields across domains
            if (!isset($names[$fieldName]))
            {
                $names[$fieldName] = true;
                $choices[$fieldId] = $projectField->getName();
            }
        }
        return $choices;
    }
}
?>
