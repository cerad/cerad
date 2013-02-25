<?php
namespace Cerad\Bundle\GameBundle\EntityRepository;

class ProjectRepository extends BaseRepository
{ 
    /* -----------------------------------------------------
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
