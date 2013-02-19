<?php
namespace Cerad\Bundle\GameBundle\Manager;

use Cerad\Bundle\GameBundle\Entity\Project;
use Cerad\Bundle\GameBundle\Entity\Level;
use Cerad\Bundle\GameBundle\Entity\Field;

use Cerad\Bundle\GameBundle\Entity\Game;
use Cerad\Bundle\GameBundle\Entity\GameTeam;
use Cerad\Bundle\GameBundle\Entity\GamePerson;

class GameManager extends BaseManager
{  
    protected $gameClassName;
    protected $gameTeamClassName;
    protected $gamePersonClassName;
    
    public function __construct($em, $emName = 'default', $itemClassName = null)
    {
        parent::__construct($em,$emName,$itemClassName);
        
        $this->gameClassName       = $itemClassName;
        $this->gameTeamClassName   = $itemClassName . 'Team';
        $this->gamePersonClassName = $itemClassName . 'Person';
    }

    /* ===================================================
     * Game Team Functions
     */
    public function createGameTeamHome($status = 'Normal')
    {
        $gameTeamClassName = $this->gameTeamClassName;
        
        return $gameTeamClassName::createHome($status);
    }
    public function createGameTeamAway($status = 'Normal')
    {
        $gameTeamClassName = $this->gameTeamClassName;
        return $gameTeamClassName::createAway($status);
    }
    public function newGameTeam()
    {
        return new $this->gameTeamClassName();
    }
    /* ===================================================
     * Game Person Functions
     */
    public function createGamePerson($slot,$role,$name,$status = 'Created')
    {
        $gamePersonClassName = $this->gamePersonClassName;
        return $gamePersonClassName::create($slot,$role,$name,$status);
    }
    public function newGamePerson()
    {
        return new $this->gamePersonClassName();
    }
    /* ===================================================
     * Game Person Functions
     */
    public function createGame($role = Game::RoleGame, $status = 'Normal')
    {
        $gameClassName = $this->gameClassName;
        return $gameClassName::create($role,$status);
    }
    public function newGame()
    {
        return new $this->gameClassName();
    }
}
?>
