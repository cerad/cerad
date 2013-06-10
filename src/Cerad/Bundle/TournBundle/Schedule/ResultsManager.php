<?php
/* =========================================================
 * Focuses on calculating pool play results
 */
namespace Cerad\Bundle\TournBundle\Schedule;

class ResultsManager
{
    protected $gameManager;
    public function __construct($gameManager)
    {
        $this->gameManager = $gameManager;
    }
    protected function calcPointsEarnedForTeam($game,$team1,$team2)
    {
        // Make scores are set
        $team1Goals = $team1->getGoalsScored();
        $team2Goals = $team2->getGoalsScored();
        if (($team1Goals === null) || ($team2Goals === null)) 
        {
            $team1->clear();
            $team2->clear();
            return;
        }
        $team1->setGoalsAllowed($team2Goals);
        $team2->setGoalsAllowed($team1Goals);
   
        $pointsMinus  = 0;
        $pointsEarned = 0;
        
        if ($team1Goals  > $team2Goals) $pointsEarned = 6;
        if ($team1Goals == $team2Goals) $pointsEarned = 3;
        
        // Shutout
        //if ($team2Goals == 0) $pointsEarned++;
        
        $maxGoals = $team1Goals;
        if ($maxGoals > 3) $maxGoals = 3;
        $pointsEarned += $maxGoals;
      
        $fudgeFactor = $team1->getFudgeFactor();
        $pointsEarned += $fudgeFactor;
         
        $pointsMinus  -= ($team1->getPlayerEjections()* 2);
        $pointsMinus  -= ($team1->getCoachEjections() * 3);
        $pointsMinus  -= ($team1->getSpecEjections()  * 0);
             
        $pointsEarned += $pointsMinus;
              
        $team1->setPointsMinus ($pointsMinus);
        $team1->setPointsEarned($pointsEarned);
        
        return;
        
        echo sprintf("Points: %d %s %d %d",$pointsEarned,$pointsMinus,$team1Goals,$team2Goals);          
    }
    // Points earned during a game
    public function calcPointsEarnedForGame($game)
    {
        $gameReport = $game->getReport();
        
        $homeTeamReport = $game->getHomeTeam()->getReport();
        $awayTeamReport = $game->getAwayTeam()->getReport();
        
        // Might be handy
        if ($gameReport->getStatus() == 'Clear')
        {
            $gameReport->clear();
            $homeTeamReport->clear();
            $awayTeamReport->clear();
            return;
        }
        $this->calcPointsEarnedForTeam($game,$homeTeamReport,$awayTeamReport);
        $this->calcPointsEarnedForTeam($game,$awayTeamReport,$homeTeamReport);
    }
    /* =====================================================
     * The extraction portion
     */
    protected $pools;
    
    protected function getPoolTeamReport($pool,$name)
    {
        if (isset($this->pools[$pool]['teams'][$name])) return $this->pools[$pool]['teams'][$name];
        
        $report = $this->gameManager->newTeamReport();
        $report->setTeamName($name);
        $this->pools[$pool]['teams'][$name] = $report;
        
        return $report;
    }
    protected function processPoolGame($game,$pool,$poolFilter)
    {
        // Never have a filter for now
        if ($poolFilter && $poolFilter != substr($pool,8,1)) return;

        $this->pools[$pool]['games'][$game->getId()] = $game;
        
        $homeGameTeam = $game->getHomeTeam();
        $awayGameTeam = $game->getAwayTeam();
        
        $homeGameTeamReport = $game->getHomeTeam()->getReport();
        $awayGameTeamReport = $game->getAwayTeam()->getReport();
        
        $homePoolTeamReport = $this->getPoolTeamReport($pool,$homeGameTeam->getName());;
        $awayPoolTeamReport = $this->getPoolTeamReport($pool,$awayGameTeam->getName());;

        /* ====================================================
         * Not sure need this, games is scored but points not applied?
        $pointsAppiled = true;
        if ($homeGameTeamReport->getGoalsScored() == null) $pointsApplied = false;
        if ($awayGameTeamReport->getGoalsScored() == null) $pointsApplied = false;
        if (!$pointsApplied) return;
        */
        
        $this->calcPoolTeamPoints($game,$homePoolTeamReport,$homeGameTeamReport);
        $this->calcPoolTeamPoints($game,$awayPoolTeamReport,$awayGameTeamReport);
        
        /* =====================================================
         * The national games had a few other tests here
         */
    }
    /* =============================================================
     * Transfers data from game team to pool team
     */
    protected function calcPoolTeamPoints($game,$poolTeamReport,$gameTeamReport)
    {
        /* ======================================================
         * Look at this later, something abut cross bracket play
         * and not processing the same team twice?
         * team in this case was the pool team not the game team
         */
        //if (isset($this->gameTeams[$game->getId()][$team->getId()])) return;
        //$this->gameTeams[$game->getId()][$team->getId()] = true;
        
        $poolTeamReport->addPointsEarned($gameTeamReport->getPointsEarned());   
        $poolTeamReport->addPointsMinus ($gameTeamReport->getPointsMinus());
        
        $poolTeamReport->addGoalsScored ($gameTeamReport->getGoalsScored());
        $poolTeamReport->addGoalsAllowed($gameTeamReport->getGoalsAllowed());
        
        /* =======================================================
         * Tie breaking rule for goals allowed
         */
        $goalsScored = $gameTeamReport->getGoalsScored();
        if ($goalsScored > 3) $goalsScored = 3;
        $poolTeamReport->addGoalsScoredMax($goalsScored);
        
        $goalsAllowed = $gameTeamReport->getGoalsAllowed();
        if ($goalsAllowed > 5) $goalsAllowed = 5;
        $poolTeamReport->addGoalsAllowedMax($goalsAllowed);
        
        // Conduct
        $poolTeamReport->addPlayerWarnings ($gameTeamReport->getPlayerWarnings());
        $poolTeamReport->addPlayerEjections($gameTeamReport->getPlayerEjections());
        
        $poolTeamReport->addCoachEjections($gameTeamReport->getCoachEjections());
        $poolTeamReport->addSpecEjections ($gameTeamReport->getSpecEjections ());
        
        $poolTeamReport->addSportsmanship($gameTeamReport->getSportsmanship());
        
        // Missing from national?
        $poolTeamReport->addGamesTotal(1);
        
        if ($gameTeamReport->getGoalsScored() !== null)
        {
            // Track games played
            $poolTeamReport->addGamesPlayed(1);
            
            // Track games won
            if ($gameTeamReport->getGoalsScored() > $gameTeamReport->getGoalsAllowed()) $poolTeamReport->addGamesWon(1);
        }
        
        // WPF
        if ($poolTeamReport->getGamesPlayed())
        {
            // The 6 comes from the six soccer fest points
            $spf = 6;
            $wpf = $poolTeamReport->getPointsEarned() / (($poolTeamReport->getGamesPlayed() * 10) + $spf);
            $wpf = sprintf('%.3f',$wpf);
        }
        else $wpf = null;
        
        $poolTeamReport->setWinPercent($wpf);

    }
    /* =====================================================
     * Standings sort
     */
    protected function compareTeamStandings($team1,$team2)
    {
        //$team1 = $team1x->getReport();
        //$team2 = $team2x->getReport();
        
        // Points earned
        $pe1 = $team1->getPointsEarned();
        $pe2 = $team2->getPointsEarned();
        if ($pe1 < $pe2) return  1;
        if ($pe1 > $pe2) return -1;
        
        // Head to head
        $compare = $this->compareHeadToHead($team1,$team2);
        if ($compare) return $compare;
        
        // Games won
        $gw1 = $team1->getGamesWon();
        $gw2 = $team2->getGamesWon();
        if ($gw1 < $gw2) return  1;
        if ($gw1 > $gw2) return -1;
        
        // Sportsmanship deductions
        $pm1 = $team1->getPointsMinus();
        $pm2 = $team2->getPointsMinus();
        if ($pm1 < $pm2) return  1;
        if ($pm1 > $pm2) return -1;
         
        // Goals Allowed
        $ga1 = $team1->getGoalsAllowedMax();
        $ga2 = $team2->getGoalsAllowedMax();
        if ($ga1 < $ga2) return -1;
        if ($ga1 > $ga2) return  1;
        
        // Goal differential
        $gd1 = $team1->getGoalsScoredMax() - $team1->getGoalsAllowed();
        $gd2 = $team2->getGoalsScoredMax() - $team2->getGoalsAllowed();
        if ($gd1 < $gd2) return -1;
        if ($gd1 > $gd2) return  1;
        
        // Just the key
        $key1 = $team1->getTeamName();
        $key2 = $team2->getTeamName();
        
        if ($key1 < $key2) return -1;
        if ($key1 > $key2) return  1;
         
        return 0;
    }
    /* ===============================================
     * Assume for now they only play once
     * Though do have one division with dups
     */
    protected function compareHeadToHead($team1,$team2)
    {
        $team1Wins = 0;
        $team2Wins = 0;
        
        foreach($this->poolGames as $game)
        {
            $homeTeamName = $game->getHomeTeam()->getName();
            $awayTeamName = $game->getAwayTeam()->getName();
            
            $team1Name = $team1->getTeamName();
            $team2Name = $team2->getTeamName();
            
            if ($homeTeamName == $team1Name && ($awayTeamName == $team2Name))
            {
                $score1 = $game->getHomeTeam()->getReport()->getGoalsScored();
                $score2 = $game->getAwayTeam()->getReport()->getGoalsScored();
                if ($score1 > $score2) $team1Wins++;
                if ($score1 < $score2) $team2Wins++;
            }
            if ($homeTeamName == $team2Name && ($awayTeamName == $team1Name))
            {
                $score2 = $game->getHomeTeam()->getReport()->getGoalsScored();
                $score1 = $game->getAwayTeam()->getReport()->getGoalsScored();
                if ($score1 > $score2) $team1Wins++;
                if ($score1 < $score2) $team2Wins++;
            }
        }
        if ($team1Wins < $team2Wins) return  1;
        if ($team1Wins > $team2Wins) return -1;
        return 0;
    }
    /* ================================================================
     * Given a list of games, pull the pool information from them
     */
    public function getPools($games, $poolFilter = null)
    {
        $this->games = $games;
        $this->pools = array();
        
        foreach($games as $game)
        {
            // Recalc? Only if formula changes
            //$this->calcPointsEarnedForGame($game);
            
            // 
            $pool = $game->getLevel()->getName();
            
            $this->processPoolGame($game,$pool,$poolFilter);
        } // die();
        
        $pools = $this->pools;
        ksort($pools);
        
        
        // Sort the teams by standing within each pool
        foreach($pools as $poolKey => $pool)
        {
            // Used for head to head
            $this->poolGames = $pools[$poolKey]['games'];
            
            // The teamReports
            $teams = $pool['teams'];
            
            //sort
            usort($teams,array($this,'compareTeamStandings'));
            
            $pools[$poolKey]['teams'] = $teams;
        }
        return $pools;
    }
}
?>
