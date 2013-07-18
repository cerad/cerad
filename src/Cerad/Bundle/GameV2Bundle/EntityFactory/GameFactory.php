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
        // Teams
        $gameTeamsFixture = IfIsSet::exe($entityFixture,'game_teams',array());
        foreach($gameTeamsFixture as $gameTeamFixture)
        {
            $this->addGameTeam($game,$gameTeamFixture);
        }
        // Persons
        $gamePersonsFixture = IfIsSet::exe($entityFixture,'game_persons',array());
        $slot = 0;
        foreach($gamePersonsFixture as $gamePersonFixture)
        {
            $slot++;
            $this->addGamePerson($game,$slot,$gamePersonFixture);
        }
        // Persist and return
        if ($persist) $manager->persist($entity);
        return $entity;        
    }
    /* ================================================================
     * This should probably be public and be in a custom import routine
     */
    protected function addGameTeam($game,$entityFixture)
    {
        $name     = IfIsSet::exe($entityFixture,'name');
        $role     = IfIsSet::exe($entityFixture,'role');
        $score    = IfIsSet::exe($entityFixture,'score');
        $status   = IfIsSet::exe($entityFixture,'status');
        $teamId   = IfIsSet::exe($entityFixture,'team_id'  );
        $levelId  = IfIsSet::exe($entityFixture,'level_id' );
        $leagueId = IfIsSet::exe($entityFixture,'league_id');

        $league = null;
        
        // See if have a link to a physical team
        $team = $teamId ? $this->teamManager->find($teamId) : null;
        if ($team)
        {
            if (!$name)    $name    = $team->getName();
            if (!$levelId) $levelId = $team->getLevel()->getId();
            
            $game->getProject()->addTeam($team);
            
        }
        // Check for level
        $level = null;
        if ($levelId) $level = $this->levelManager->find($levelId);
        
        if (!$level && $team) $level = $team->getLevel();
        
        if (!$level) $level = $game->getLevel();
        
        if ($level) $game->getProject()->addLevel($level);
        
        // Few don't have a name
        if (!$name) $name = 'TBD';
        
        $gameTeam = $this->gameManager->newGameTeam();
        $gameTeam->setRole ($role);
        $gameTeam->setName ($name);
        $gameTeam->setScore($score);
        $gameTeam->setLevel($level);
        
        if ($status) $gameTeam->setStatus($status);
                
        // Connect it
        $game->addTeam($gameTeam);
        return $gameTeam;
    }
    /* ================================================================
     * This should probably be public and be in a custom import routine
     */
    protected function addGamePerson($game,$slot,$entityFixture)
    {
        $name     = IfIsSet::exe($entityFixture,'name');
        $role     = IfIsSet::exe($entityFixture,'role');
        $email    = IfIsSet::exe($entityFixture,'role');
        $phone    = IfIsSet::exe($entityFixture,'role');
        $status   = IfIsSet::exe($entityFixture,'status');
        $personId = IfIsSet::exe($entityFixture,'person_id'); // Soft link
        $leagueId = IfIsSet::exe($entityFixture,'league_id');

        $gamePerson = $this->gameManager->newGamePerson();
        $gamePerson->setSlot($slot);
        $gamePerson->setRole($role);
        $gamePerson->setName ($name);
        $gamePerson->setEmail($email);
        $gamePerson->setPhone($phone);
        
        $gamePerson->setPerson($personId);
        
        if ($status) $gamePerson->setStatus($status);
                
        // Connect it
        $game->addPerson($gamePerson);
        return $gamePerson;
    }
}
?>
