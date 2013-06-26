<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class GameRepository extends BaseRepository
{
    // Nice to make this configurable
    public function getGameClassName      () { return $this->_entityName; }
    public function getGameReportClassName() { return $this->_entityName . 'Report'; }

    public function getGamePersonClassName() { return $this->_entityName . 'Person'; }
    
    public function getGameTeamClassName      () { return $this->_entityName . 'Team';   }
    public function getGameTeamReportClassName() { return $this->_entityName . 'TeamReport'; }
    
    // Might want team repository
    public function getTeamClassName      () { return 'Cerad\Bundle\GameBundle\Entity\Team';   }
    public function getTeamReportClassName() { return 'Cerad\Bundle\GameBundle\Entity\TeamReport'; }
   
    // Simple object new
    public function newGame()       
    { 
        $className = $this->getGameClassName();
        return new $className;
    }
    
    public function createGamePerson($game = null, $role = null, $slot = null, $name = null)       
    { 
        $className = $this->getGamePersonClassName();
        $gamePerson= new $className();
        
        $gamePerson->setRole($role);
        $gamePerson->setSlot($slot);
        $gamePerson->setName($name);
        
        if ($game) $game->addPerson($gamePerson);
        
        return $gamePerson;
    }
   public function createGameTeam($game = null, $role = null)
    { 
        $className = $this->getGameTeamClassName();
        $gameTeam  = new $className();
        
        $gameTeam->setRole($role);
        
        if ($game) $game->addTeam($gameTeam);
        
        return $gameTeam;
    }

    /* ======================================================
     * Load for a number
     * Look at the benchmark to see why I'm not loading any relations here
     */
    public function loadGameForProjectNum($project,$num)
    {
        return $this->findOneBy(array('project' => $project, 'num' => $num));
    }
    /* -----------------------------------------------------
     * Load Team Choices
     * 
     * Use GameTeam because there won't always be a team
     * 
     * Would be nice to filter tournament type TBD teams
     * 
     * Spend some time thinking about requireing a team again
     */
    public function loadTeamChoices($params = array())
    {
        $qb = $this->createQueryBuilder('game');
 
        // Need index on game_team.name
        $seasons    = $qb->getArrayForParam($params,'seasons');
        $sports     = $qb->getArrayForParam($params,'sports');
        $domains    = $qb->getArrayForParam($params,'domains');
        $domainSubs = $qb->getArrayForParam($params,'domainSubs');
        $levels     = $qb->getArrayForParam($params,'levels');
        
        // Build query
        $qb->addSelect('distinct team.name');
        
        $qb->leftJoin('game.project','project'); // No playing across projects
        $qb->leftJoin('game.teams',  'team');
        $qb->leftJoin('team.level',  'level');   // Teams can play across levels
        
        $qb->andWhereEq('project.season', $seasons);
        $qb->andWhereEq('level.sport',    $sports);
        $qb->andWhereEq('level.domain',   $domains);
        $qb->andWhereEq('level.domainSub',$domainSubs);
        $qb->andWhereEq('level.name',     $levels);
        
        $qb->addOrderBy('team.name');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['name']] = $item['name'];
        }
        return $choices;
    }   
    /* =========================================================================
     * Kick off one or more queries to get ids
     */
    public function loadGameIds($params)
    {  
        $qb = $this->createQueryBuilder('game');
        
        $seasons    = $qb->getArrayForParam($params,'seasons');
        $sports     = $qb->getArrayForParam($params,'sports');
        $domains    = $qb->getArrayForParam($params,'domains');
        $domainSubs = $qb->getArrayForParam($params,'domainSubs');
        $levels     = $qb->getArrayForParam($params,'levels');
        
        $projects   = $qb->getArrayForParam($params,'projects');
        $gameTypes  = $qb->getArrayForParam($params,'gameTypes');

        $ages      = $qb->getArrayForParam($params,'ages');
        $teams     = $qb->getArrayForParam($params,'teams');
        $fields    = $qb->getArrayForParam($params,'fields');
        $venues    = $qb->getArrayForParam($params,'venues');
        $genders   = $qb->getArrayForParam($params,'genders');
        $statuses  = $qb->getArrayForParam($params,'statuses');
        
        $dates     = $qb->getArrayForParam($params,'dates');
        
        $date1       = isset($params['date1' ])      ? $params['date1' ]:      null; // 2013-01-20
        $date2       = isset($params['date2' ])      ? $params['date2' ]:      null;
        $date1On     = isset($params['date1On'])     ? $params['date1On']:     null;
        $date2On     = isset($params['date2On'])     ? $params['date2On']:     null;
        $date1After  = isset($params['date1After'])  ? $params['date1After']:  null;
        $date2Before = isset($params['date2Before']) ? $params['date2Before']: null;
   
        $time1 = ' 00:00:00';
        $time2 = ' 23:59:59';
       
        /* =========================================================
         * TODO:  Date before and Date after should be limited by season dates
         */
        if ($date1After)
        {
            $date1On = $date2On = $date2Before = null;
            $date1  .= $time1;
            $date2   = null;
        }
        if ($date2Before)
        {
            $date1On = $date2On = $date1After = null;
            $date2  .= $time2;
            $date1   = null;
        }
        // See if ON check is selected
        if ($date1On || $date2On)
        {
            if ($date1On && $date1) $dates[] = $date1 . $time1;
            if ($date2On && $date2) $dates[] = $date2 . $time1; // time2 does not work here
            $date1 = null;
            $date2 = null;
        }
        // Should probably always have a value unless checked
        if ($date1 && $date2)
        {
            // Swap if necessary
            if ($date1 > $date2)
            {
                $tmp   = $date1;
                $date1 = $date2;
                $date2 = $tmp;
            }
            $date1 .= $time1;
            $date2 .= $time2;
        }
        
        // Build query
        
        $qb->addSelect('distinct game.id');
        
        $qb->leftJoin('game.project',  'gameProject');
        $qb->leftJoin('game.level',    'gameLevel');
        $qb->leftJoin('game.field',    'gameField');
        
        $qb->leftJoin('game.teams',    'gameTeam');
        $qb->leftJoin('gameTeam.level','gameTeamLevel');
        
        $qb->andWhereGTE('date(game.dtBeg)',$date1);
        $qb->andWhereLTE('date(game.dtBeg)',$date2);
        
        $qb->andWhereEq('date(game.dtBeg)',$dates);
        
        $qb->andWhereEq('gameField.name', $fields);
        $qb->andWhereEq('gameField.venue',$venues);
        $qb->andWhereEq('game.status',    $statuses);
        $qb->andWhereEq('game.pool',      $gameTypes); // Hack, PP SF etc
        
        $qb->andWhereEq('gameProject.id',    $projects);
        $qb->andWhereEq('gameProject.season',$seasons);
        $qb->andWhereEq('gameProject.domain',$domains);
        
        $qb->andWhereEq('gameTeamLevel.sport',    $sports);
      //$qb->andWhereEq('gameTeamLevel.domain',   $domains);
        $qb->andWhereEq('gameTeamLevel.domainSub',$domainSubs);
        $qb->andWhereEq('gameTeamLevel.name',     $levels);
        $qb->andWhereEq('gameTeamLevel.age',      $ages);
        $qb->andWhereEq('gameTeamLevel.sex',      $genders);
        $qb->andWhereEq('gameTeam.name',          $teams);
        
        $items = $qb->getQuery()->getArrayResult(); // print_r($items); die('loadGameIds');
        $ids = array();
        foreach($items as $item) $ids[] = $item['id'];  // Maybe a map command?
        
        return $ids;
    }
    /* ======================================================================
     * Need to do some bench marking to verify is just returning the game suffices
     * Just loading the game takes ~ 500 ms
     * Drops to 485ms with all the joins on development machine
     * 
     * Wider if pure dql will make much difference
     * A straight findBy is only a tiny bit faster than the query.  Only order by game props
     * 
     * If js is used for sorting then don't care what order we get.
     * 
     * Afain, need to do some production server bench marks.
     */
    public function loadGamesForIds($gameIds = array())
    {
        print_r($gameIds); die('gameId');
        if (!count($gameIds)) return array();
        
        return $this->findBy(array('id' => $gameIds),array('dtBeg' => 'ASC'));
        
        // return $this->findBy(array('id' => $gameIds), array('dtBeg' => 'ASC')); //,'game.field.name' => 'ASC'));
        
        // Build query
        $qb = $this->createQueryBuilder('game');

        $qb->addSelect('game');
        
        //$qb->addSelect('game, gameProject, gameLevel, gameField');
        //$qb->addSelect('gameTeam, gameTeamLevel, gamePerson');

        //$qb->leftJoin('game.project',  'gameProject');
        //$qb->leftJoin('game.level',    'gameLevel');
        //$qb->leftJoin('game.field',    'gameField');
        //$qb->leftJoin('game.teams',    'gameTeam');
        //$qb->leftJoin('game.persons',  'gamePerson');
        //$qb->leftJoin('gameTeam.level','gameTeamLevel');

        $qb->andWhereEq('game.id',$gameIds);
        
        $qb->addOrderBy('game.dtBeg');  // DATE and TIME should be usable here
        $qb->addOrderBy('game.field');
       
        $games = $qb->getQuery()->getResult();
        
        return $games;        
    }

    public function loadGames($params = array(),$limit = null,$offset=null)
    {
        // Grab games of interest
        $gameIds = $this->loadGameIds($params);
        if (!count($gameIds)) return array();
        
      //$gameIds = array(1,2,3,4,5);
      //
        // For the moment, can only sort on game fields
        // Using qb will be easy enough but want to do some more research
        $sortBy = array('dtBeg' => 'ASC','gameField.name' => 'ASC');
        $sortBy = array('dtBeg' => 'ASC');
       
        return $this->findBy(array('id' => $gameIds),$sortBy,$limit,$offset);
    }
}
?>
