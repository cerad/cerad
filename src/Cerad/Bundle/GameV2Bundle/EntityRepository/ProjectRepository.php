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
    /* ------------------------------------
     * Your basic creator
     * Don't think I want this
     */
    public function createProject($sport,$season,$domain,$domainSub,$id = null)
    {
        $projectClassName = $this->getClassName();
        $project = new $projectClassName();
        $project->setSport    ($sport);
        $project->setSeason   ($season);
        $project->setDomain   ($domain);
        $project->setDomainSub($domainSub);
        
        // Was ID Passed?
        if (!$id) $id = $this->hash(array($sport,$domain,$domainSub,$season));
        $project->setId($id);
        
        return $project;
    }
    /* ------------------------------------
     * Loads existing project or optionally creates a new one
     * Pretty sure I don't want this either
     */
    protected $cache;
    
    public function loadProject($sport,$season,$domain,$domainSub,$autoCreate = true)
    {
        /* ==============================================
         * A cache is required to avoid having to flush
         */
        $id = $this->hash(array($sport,$domain,$domainSub,$season));
        if (isset($this->cache[$id])) return $this->cache[$id];
        
        $params = array
        (
            'sport'     => $sport,
            'season'    => $season,
            'domain'    => $domain,
            'domainSub' => $domainSub,
        );
        $project = $this->findOneBy($params);
        if ($project) 
        {
            $this->cache[$id] = $project;
            return $project;
        }
        if (!$autoCreate) return null;
        
        // Create a new one
        $project = $this->createProject($sport,$season,$domain,$domainSub,$id);
        $this->cache[$id] = $project;
        $this->persist($project);
        return $project;
    }
    /* -----------------------------------------------------
     * TODO V2: Revisit Later
     * Load a set of season choices
     * Probably want to filter on active projects only?
     */
    public function loadSeasonChoices($params = array())
    {
        $qb = $this->createQueryBuilder('project');
        
        // Build query
        $qb->addSelect('distinct project.season');
        
        $qb->addOrderBy('project.season');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['season']] = $item['season'];
        }
        return $choices;
    }
}
?>
