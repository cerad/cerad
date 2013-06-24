<?php
namespace Cerad\Bundle\GameBundle\EntityRepository;

class LeagueRepository extends BaseRepository
{ 
    // Nice to make this configurable
    public function getLeagueClassName() { return $this->getClassName(); }
    
    public function newLeague() 
    { 
        $className = $this->getLeagueClassName();
        return new $className(); 
    }

    /* ==============================================================
     * Empties the League table for debugging
     * Assumes no one is actually linked to it
     */
    public function deleteLeagues()
    {
        $conn = $this->_em->getConnection();
        $conn->executeUpdate('DELETE FROM league;');
        
        $conn->executeUpdate('ALTER TABLE league AUTO_INCREMENT = 1;');
    }
    /* ==============================================================
     * Connect an actual league object to a itemLeague relation
     */
    public function connectLeagues($items)
    {
        foreach($items as $item)
        {
            foreach($item->getLeagues() as $itemLeague)
            {
                // Confusing notation?
                $leagueId = $itemLeague->getLeagueId();
                
                // League Object
                $league = null;
                if ( $leagueId) $league = $this->find($leagueId);
                if (!$league)   $league = $this->newLeague();
            
                $itemLeague->setLeaguex($league);
            }
        }
    }
}
?>
