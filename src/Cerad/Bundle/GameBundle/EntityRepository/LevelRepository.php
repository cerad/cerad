<?php
namespace Cerad\Bundle\GameBundle\EntityRepository;

class LevelRepository extends BaseRepository
{ 
    /* -----------------------------------------------------
     * Load a set of level choices
     */
    public function loadLevelChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        $sports     = $qb->getArrayForParam($params,'sports');
        $domains    = $qb->getArrayForParam($params,'domains');    // Arbiter group
        $domainSubs = $qb->getArrayForParam($params,'domainSubs'); // Arbiter 'sport' aka sub-group
        
        // Build query
        $qb->addSelect('distinct level.name');
        
        $qb->andWhereEq('level.sport',    $sports);
        $qb->andWhereEq('level.domain',   $domains);
        $qb->andWhereEq('level.domainSub',$domainSubs);
        
        $qb->addOrderBy('level.name');
        
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['name']] = $item['name'];
        }
        return $choices;
    }
    /* -----------------------------------------------------
     * Load a set of sub domains choices
     * Assume at leat one level for each sub domain of interest
     */
    public function loadDomainSubChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        $sports  = $qb->getArrayForParam($params,'sports');
        $domains = $qb->getArrayForParam($params,'domains');    // Arbiter group
        
        // Build query
        $qb->addSelect('distinct level.domainSub, level.domain');
        
        $qb->andWhereEq('level.sport',    $sports);
        $qb->andWhereEq('level.domain',   $domains);
        
        $qb->addOrderBy('level.domain, level.domainSub');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            // Might have two sub groups in different groups with the same name
            // The group prefix would be misleading if multiple groups are selected
            $choices[$item['domainSub']] = $item['domain'] . ' ' . $item['domainSub'];
        }
        return $choices;       
     }
    /* -----------------------------------------------------
     * Load a set of domains choices
     */
    public function loadDomainChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        $sports = $qb->getArrayForParam($params,'sports');
        
        // Build query
        $qb->addSelect('distinct level.domain');
        
        $qb->andWhereEq('level.sport',    $sports);

        $qb->addOrderBy('level.domain');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['domain']] = $item['domain'];
        }
        return $choices;       
     }
    /* -----------------------------------------------------
     * Load a set of sport choices
     */
    public function loadSportChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        // Build query
        $qb->addSelect('distinct level.sport');
        
        $qb->addOrderBy('level.sport');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['sport']] = $item['sport'];
        }
        return $choices;       
     }
}
?>
