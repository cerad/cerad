<?php
namespace Cerad\Bundle\GameBundle\Manager;

use Cerad\Bundle\GameBundle\Entity\Project;
use Cerad\Bundle\GameBundle\Entity\Level;
use Cerad\Bundle\GameBundle\Entity\Field;

use Cerad\Bundle\GameBundle\Entity\Game;
use Cerad\Bundle\GameBundle\Entity\GameTeam;
use Cerad\Bundle\GameBundle\Entity\GamePerson;

class GameManager
{
    protected $em     = null;
    protected $emName = null; // Entity Manager Name (for form queries)
    protected $emSub  = null; // Subscriber - Not sure if really need this
    
    public function getEntityManager()      { return $this->em; }
    public function getEntityManagerName()  { return $this->emName; }
    public function getDatabaseConnection() { return $this->em->getConnection(); }
    
    public function __construct($em, $emName = 'default', $emSub = null)
    {
        $this->em     = $em;
        $this->emName = $emName;
        $this->emSub  = $emSub;
    }
    
    // Kind of hokay but think of this as an extended EntityManager
    public function clear()        { $this->em->clear(); }
    public function flush()        { $this->em->flush(); }
    public function remove ($item) { $this->em->remove ($item); }
    public function detach ($item) { $this->em->detach ($item); }
    public function persist($item) { $this->em->persist($item); }
    public function refresh($item) { $this->em->refresh($item); }
    
    // Return my own class with some convience methods
    public function createQueryBuilder($entityClassName = null, $alias = null)
    {
        $qb = new QueryBuilder($this->em);
        
        if ($entityClassName)
        {
            if ($alias) $qb->from($entityClassName,$alias);
            else        $qb->from($entityClassName);
        }
        return $qb;
    }
    /* ===================================================
     * Project Functions
     * These may turn out to be more trouble that it is worth
     * But it offers the chance that different project objects could be used
     */
    public function createProject($season,$sport,$domain,$domainSub,$status = 'Active')
    {
        return Project::create($season,$sport,$domain,$domainSub,$status);
    }
    public function newProject()
    {
        return new Project();
    }
    /* ===================================================
     * Level Functions
     */
    public function createLevel($sport,$domain,$domainSub,$name,$status = 'Active')
    {
        return Level::create($sport,$domain,$domainSub,$name,$status);
    }
    public function newLevel()
    {
        return new Level();
    }
    /* ===================================================
     * Field Functions
     */
    public function createField($season,$domain,$domainSub,$name,$status = 'Active')
    {
        return Field::create($season,$domain,$domainSub,$name,$status);
    }
    public function newField()
    {
        return new Field();
    }
    /* ===================================================
     * Game Team Functions
     */
    public function createGameTeamHome($status = 'Normal')
    {
        return GameTeam::createHome($status);
    }
    public function createGameTeamAway($status = 'Normal')
    {
        return GameTeam::createAway($status);
    }
    public function newGameTeam()
    {
        return new GameTeam();
    }
    /* ===================================================
     * Game Person Functions
     */
    public function createGamePerson($slot,$role,$name,$status = 'Created')
    {
        return GamePerson::create($slot,$role,$name,$status);
    }
    public function newGamePerson()
    {
        return new GamePerson();
    }
    /* ===================================================
     * Game Person Functions
     */
    public function createGame($role = Game::RoleGame, $status = 'Normal')
    {
        return Game::create($role,$status);
    }
    public function newGame()
    {
        return new Game();
    }
    /* ===================================================
     * Empties all the game tables and resets their autoindex
     */
    public function truncate()
    {
        $conn = $this->getDatabaseConnection();
        $tables = array(
            'game_person',
            'game_team',
        //  'game',
            'field',
            'level',
            'project',
        );
        foreach($tables as $table)
        {
            $conn->executeUpdate('DELETE FROM ' . $table . ';');
            $conn->executeUpdate('ALTER TABLE ' . $table . ' AUTO_INCREMENT=1;');
        }
    }
}
?>
