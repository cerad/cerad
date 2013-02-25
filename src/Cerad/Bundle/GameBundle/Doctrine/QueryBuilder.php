<?php
namespace Cerad\Bundle\GameBundle\Doctrine;

use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;

class QueryBuilder extends DoctrineQueryBuilder
{
    // No need to worry about the constructor, just needs an entity manager
    public function andWhereEq($name,$value)
    {
        $qb = $this;
        
        // Handle array
        if (is_array($value))
        {
            // Filter empty
            if (!count($value)) return;
            
            // Use IN, assume for now array contains scaler values
            if (count($value) > 1)
            {
                return $qb->andWhere($qb->expr()->in($name, $value));
            }
            $value = $value[0];
        }
        // Just a plain old scaler
        if (is_object($value)) $value = $value->getId();
        
        return $qb->andWhere($qb->expr()->eq($name,$qb->expr()->literal($value)));
    }
    public function andWhereGTE($name,$value)
    {
        // Blanks don't really make sense here
        if (!$value) return;
        
        // Just a plain old scaler
        return $this->andWhere($this->expr()->gte($name,$this->expr()->literal($value)));
    }
    public function andWhereLTE($name,$value)
    {
        // Blanks don't really make sense here
        if (!$value) return;
        
        // Just a plain old scaler
        return $this->andWhere($this->expr()->lte($name,$this->expr()->literal($value)));
    }
    // Useful for pulling parameters
    public function getArrayForParam($params,$name)
    {
        if (!isset($params[$name])) return array();
        
        $items = $params[$name];
        
        if (isset($items[0]) && !$items[0]) return array();
        
        return $items;
    }
}
?>
