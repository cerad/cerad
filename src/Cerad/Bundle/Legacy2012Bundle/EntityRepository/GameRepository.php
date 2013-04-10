<?php
namespace Cerad\Bundle\Legacy2012Bundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository
{
    // Kind of hokay but think of this as an extended EntityManager
    public function clear()        { $this->_em->clear(); }
    public function flush()        { $this->_em->flush(); }
    public function remove ($item) { $this->_em->remove ($item); }
    public function detach ($item) { $this->_em->detach ($item); }
    public function persist($item) { $this->_em->persist($item); }
    public function refresh($item) { $this->_em->refresh($item); }
    
    public function getDatabaseConnection() { return $this->_em->getConnection(); }
    public function getEventManager      () { return $this->_em->getEventManager(); }
    
    public function loadGamesForProject($project)
    {
        return $this->findBy(array('project' => $project));
    }
    public function loadGamesForDate($date)
    {
        return $this->findBy(array('date' => $date),array('time' => 'ASC'),20);
    }

}
?>
