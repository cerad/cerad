<?php
namespace Cerad\Bundle\GameBundle\EntityRepository;

class FieldRepository extends BaseRepository
{
    /* -----------------------------------------------------
     * Load a set of field names
     */
    public function loadFieldChoices($params = array())
    {
        $qb = $this->createQueryBuilder('field');
        
        // Not sure how much to filter here
        // Fields are unique within an arbiter group
        $seasons    = $qb->getArrayForParam($params,'seasons');
        $domains    = $qb->getArrayForParam($params,'domains');    // Arbiter group
        $domainSubs = $qb->getArrayForParam($params,'domainSubs'); // Arbiter 'sport' aka sub-group
        
        // Build query
        $qb->addSelect('distinct field.name');
        
        $qb->andWhereEq('field.season',   $seasons);
        $qb->andWhereEq('field.domain',   $domains);
        $qb->andWhereEq('field.domainSub',$domainSubs);
        
        $qb->addOrderBy('field.name');
        
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['name']] = $item['name'];
        }
        return $choices;
    }
}
?>
