<?php
namespace Cerad\Bundle\GameV2Bundle\EntityFactory;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

/* 
 * I know I need something that will load/create a project, merge in config data and return the project
 * 
 * Also need a plan for creating entities from a fixture array
 */
class GameFactory
{
    protected $gameManager;
    protected $teamManager;
    protected $fieldManager;
    protected $levelManager;
    protected $projectManager;
    
    public function __construct($scheduleManager)
    {
        $this->gameManager    = $scheduleManager->gameManager;
        $this->teamManager    = $scheduleManager->teamManager;
        $this->fieldManager   = $scheduleManager->fieldManager;
        $this->levelManager   = $scheduleManager->levelManager;
        $this->projectManager = $scheduleManager->projectManager;
    }
    public function createFromFixture($entityFixture,$persist = true)
    {
        // Avoid creating a duplicate entity
        $manager = $this->gameManager;
        $id = IfIsSet::exe($entityFixture,'id');
        
        if ($id)
        {
            $existing = $manager->find($id);
            if ($existing)
            {
                // Maybe check for and apply changes?
                // Or maybe toss an exception?
                return $existing;
            }
        }
        $game = $entity = $manager->newGame();
        
        // Entity
        $num    = IfIsSet::exe($entityFixture,'num');
        $role   = IfIsSet::exe($entityFixture,'role');
        $status = IfIsSet::exe($entityFixture,'status');
        $dtBeg  = IfIsSet::exe($entityFixture,'dt_beg');
        $dtEnd  = IfIsSet::exe($entityFixture,'dt_end');
        
        $entity->setNum((int)$num);
       
        if ($id)     $entity->setStatus($id);
        if ($role)   $entity->setStatus($role);
        if ($status) $entity->setStatus($status);
        
        if ($dtBeg) $entity->setDtBeg(new \DateTime($dtBeg));
        if ($dtEnd) $entity->setDtEnd(new \DateTime($dtEnd));
        
        // Need project
        $projectId = IfIsSet::exe($entityFixture,'project_id');
        if ($projectId)
        {
            $project = $this->projectManager->find($projectId); // Reference should work as well
            
            // Maybe toss an exception if level is not found?
            $entity->setProject($project);
        }
        
        // Need level
        $levelId = IfIsSet::exe($entityFixture,'level_id');
        if ($levelId)
        {
            $level = $this->levelManager->getReference($levelId); // Reference should work as well
            
            // Maybe toss an exception if level is not found?
            $entity->setLevel($level);
            
            // Track project levels
            $project->addLevel($level);
        }
        // Need field
        $fieldId = IfIsSet::exe($entityFixture,'field_id');
        if ($fieldId)
        {
            $field = $this->fieldManager->getReference($fieldId); // Reference should work as well
            
            // Maybe toss an exception if level is not found?
            $entity->setField($field);
            
           // Track project fields
            $project->addField($field);
        }
        // Home team
        $this->addTeam($game,'Home',$entityFixture['team1_id'],$entityFixture['score1']);
        $this->addTeam($game,'Away',$entityFixture['team2_id'],$entityFixture['score2']);
        
        // Persist and return
        if ($persist) $manager->persist($entity);
        return $entity;        
    }
    /* ================================================================
     * This should probably be public and be in a custom import routine
     */
    protected function addTeam($game,$role,$teamId,$score)
    {
        $gameTeam = $this->gameManager->newGameTeam();
        $gameTeam->setRole($role);
        $gameTeam->setScore($score);
        $gameTeam->setLevel($game->getLevel());
        
        if ($teamId)
        {
            $team = $this->teamManager->find($teamId);
            if ($team)
            {
                $gameTeam->setName ($team->getName());
                $gameTeam->setLevel($team->getLevel());
                $game->getProject()->addTeam($team);
            }
        }
        else $gameTeam->setName('TBD');
        
        $game->addTeam($gameTeam);
        
        
        return $gameTeam;
    }
}
?>
