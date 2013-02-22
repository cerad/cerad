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
    /* -----------------------------------------------------
     * Load for a number
     */
    public function loadGameForProjectNum($project,$num)
    {
        /* =======================================================
         * For an empty game table, this is 6 seconds faster than the query build
         * Run some tests with data loaded, full data set takes 12s vs 16s
         * 
         * With the repo, only takes 2s but of course the data is not being processed
         * Processing with repo 5s, with qb takes 12s with no changes
         * 
         * Drop out project, level and field, 12s => 7.6s
         */
        return $this->getRepository()->findOneBy(array('project' => $project, 'num' => $num));

        // Build query
        $qb = $this->createQueryBuilder($this->gameClassName,'game');

        $qb->addSelect('game'); //, gameProject, gameLevel, gameField');
        
        $qb->addSelect('gameTeam, gamePerson'); //gameTeamLevel');

      //$qb->leftJoin('game.project',  'gameProject');
      //$qb->leftJoin('game.level',    'gameLevel');
      //$qb->leftJoin('game.field',    'gameField');
        $qb->leftJoin('game.teams',    'gameTeam');
        $qb->leftJoin('game.persons',  'gamePerson');
      //$qb->leftJoin('gameTeam.level','gameTeamLevel');
        
        $qb->andWhereEq('game.project',$project);
        $qb->andWhereEq('game.num',    $num);
        
        return $qb->getQuery()->getOneOrNullResult();       
    }
}
?>
