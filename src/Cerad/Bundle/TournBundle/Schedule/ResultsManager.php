<?php
/* =========================================================
 * Focuses on calculating pool play results
 */
namespace Cerad\Bundle\TournBundle\Schedule;

class ResultsManager
{
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
    protected $gameTeams;
    
    protected function processPoolGame($game,$pool,$poolFilter)
    {
        // Never have a filter for now
        if ($poolFilter && $poolFilter != substr($pool,8,1)) return;

        $this->pools[$pool]['games'][$game->getId()] = $game;
        
        return;
        
        $homeGameTeam = $game->getHomeTeam();
        $awayGameTeam = $game->getAwayTeam();
        
        $homeTeamRelReport = $game->getHomeTeam()->getReport();
        $awayTeamRelReport = $game->getAwayTeam()->getReport();
                    
        $homePoolTeam = $game->getHomeTeam()->getTeam();
        $awayPoolTeam = $game->getAwayTeam()->getTeam();

        $homePoolTeamReport = $homePoolTeam->getReport();
        $awayPoolTeamReport = $awayPoolTeam->getReport();
                    
        if ($game->isPointsApplied())
        {
            $this->calcPoolTeamPoints($game,$homePoolTeam,$homePoolTeamReport,$homeTeamRelReport);
            $this->calcPoolTeamPoints($game,$awayPoolTeam,$awayPoolTeamReport,$awayTeamRelReport);
        }
        if ($pool == substr($homePoolTeam->getKey(),0,strlen($pool)))
        {
            $this->pools[$pool]['teams'][$homePoolTeam->getId()] = $homePoolTeam;
        }
         if ($pool == substr($awayPoolTeam->getKey(),0,strlen($pool)))
         {
            $this->pools[$pool]['teams'][$awayPoolTeam->getId()] = $awayPoolTeam;
         }
    }
    /* ================================================================
     * Given a list of games, pull the pool information from them
     */
    public function getPools($games, $poolFilter = null)
    {
        $this->pools = array();
        $this->gameTeams = array();
        
        foreach($games as $game)
        {
            $pool = $game->getLevel()->getName();
            
            $this->processPoolGame($game,$pool,$poolFilter);
        } // die();
        
        $pools = $this->pools;
        ksort($pools);
        
        return $pools;
        
        // Sort the teams by standing within each pool
        foreach($pools as $poolKey => $pool)
        {
            $teams = $pool['teams'];
            
            //sort
            usort($teams,array($this,'compareTeamStandings'));
            
            $pools[$poolKey]['teams'] = $teams;
        }
        return $pools;
    }
    // Passed in report objects, gameTeam is actually gameTeamRelReport
    protected function calcPoolTeamPoints($game,$team,$poolTeam,$gameTeam)
    {
        // Avoid processing the same team twice for cross bracket play
        if (isset($this->gameTeams[$game->getId()][$team->getId()])) return;
        $this->gameTeams[$game->getId()][$team->getId()] = true;
        
        $poolTeam->addPointsEarned($gameTeam->getPointsEarned());   
        $poolTeam->addPointsMinus ($gameTeam->getPointsMinus());
        
        $poolTeam->addGoalsScored ($gameTeam->getGoalsScored());
        
        $goalsAllowed = $gameTeam->getGoalsAllowed();
        if ($goalsAllowed > 5) $goalsAllowed = 5;
        $poolTeam->addGoalsAllowed($goalsAllowed);
        
        $poolTeam->addCautions($gameTeam->getCautions());
        $poolTeam->addSendoffs($gameTeam->getSendoffs());
        
        $poolTeam->addCoachTossed($gameTeam->getCoachTossed());
        $poolTeam->addSpecTossed ($gameTeam->getSpecTossed());
        
        $poolTeam->addSportsmanship($gameTeam->getSportsmanship());
        
        if ($gameTeam->getGoalsScored() !== null)
        {
            $poolTeam->addGamesPlayed(1);
            if ($gameTeam->getGoalsScored() > $gameTeam->getGoalsAllowed()) $poolTeam->addGamesWon(1);
        }
    }
    protected function compareTeamStandings($team1x,$team2x)
    {
        $team1 = $team1x->getReport();
        $team2 = $team2x->getReport();
        
        // Points earned
        $pe1 = $team1->getPointsEarned();
        $pe2 = $team2->getPointsEarned();
        if ($pe1 < $pe2) return  1;
        if ($pe1 > $pe2) return -1;
        
        // Head to head
        
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
        $ga1 = $team1->getGoalsAllowed();
        $ga2 = $team2->getGoalsAllowed();
        if ($ga1 < $ga2) return -1;
        if ($ga1 > $ga2) return  1;
        
        // Just the key
        $key1 = $team1x->getKey();
        $key2 = $team2x->getKey();
        
        if ($key1 < $key2) return -1;
        if ($key1 > $key2) return  1;
         
        return 0;
    }
}
?>
