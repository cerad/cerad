<?php
namespace Cerad\Bundle\GameBundle\EntityRepository;

class GameRepository extends BaseRepository
{
    // Nice to make this configurable
    public function getGameClassName      () { return $this->_entityName; }
    public function getGameTeamClassName  () { return $this->_entityName . 'Team';   }
    public function getGamePersonClassName() { return $this->_entityName . 'Person'; }
    
    public function newGame()       { return new $this->getGameClassName      (); }
    public function newGameTeam()   { return new $this->getGameTeamClassName  (); }
    public function newGamePerson() { return new $this->getGamePersonClassName(); }
    
    public function createGame($params = array())       
    { 
        $entityClassName = $this->_entityName;
        return $entityClassName::create($params); 
    }
    public function createGameTeam($params = array())       
    { 
        $entityClassName = $this->getGameTeamClassName();
        return $entityClassName::create($params); 
    }
    public function createGameTeamHome($params = array())       
    { 
        $entityClassName = $this->getGameTeamClassName();
        
        $params['role'] = $entityClassName::RoleHome;
        $params['slot'] = $entityClassName::SlotHome;
        
        return $entityClassName::create($params); 
    }
    public function createGameTeamAway($params = array())       
    { 
        $entityClassName = $this->getGameTeamClassName();
        
        $params['role'] = $entityClassName::RoleAway;
        $params['slot'] = $entityClassName::SlotAway;
        
        return $entityClassName::create($params); 
    }
    public function createGamePerson($params = array())       
    { 
        $entityClassName = $this->getGamePersonClassName();
        return $entityClassName::create($params); 
    }
    /* ======================================================
     * Load for a number
     * Look at the benchmark to see why I'm not loading any relations here
     */
    public function loadGameForProjectNum($project,$num)
    {
        return $this->findOneBy(array('project' => $project, 'num' => $num));
    }
}
?>
