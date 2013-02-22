<?php
namespace Cerad\Bundle\GameBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad.game.repository';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\EntityRepository\GameRepository', get_class($manager));
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Game',      $manager->getClassName());
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Game',      $manager->getGameClassName());
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\GameTeam',  $manager->getGameTeamClassName());
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\GamePerson',$manager->getGamePersonClassName());
    }
    public function testCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
         
        $game = $manager->createEntity();
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Game', get_class($game));
        $this->assertEquals('Game', $game->getRole());
        
        $game = $manager->createGame(array('num' => 42));
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Game', get_class($game));
        $this->assertEquals('Game', $game->getRole());
        $this->assertEquals(42,     $game->getNum());
        
        $gameTeam = $manager->createGameTeamAway();
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\GameTeam', get_class($gameTeam));
        $this->assertEquals('Away', $gameTeam->getRole());
        
        $params = array('slot' => 1, 'role' => 'Referee', 'name' => 'John Price', 'game' => $game);
        $gamePerson = $manager->createGamePerson($params);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\GamePerson', get_class($gamePerson));
        $this->assertEquals('Referee',   $gamePerson->getRole());
        $this->assertEquals(42,          $gamePerson->getGame()->getNum());
        $this->assertEquals('John Price',$game->getPersonForSlot(1)->getName());
       
        $manager->persist($game);
        $manager->flush();
        
        return;
        
    }
    public function testLoad()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
         
        $game = $manager->loadGameForProjectNum(null,42);
        $this->assertEquals(42,     $game->getNum());
        $this->assertEquals('John Price',$game->getPersonForSlot(1)->getName());
        
        // Even with nullable=false on the relation, games without project can be created?
        $project = $game->getProject();
        $this->assertFalse(is_object($project));
    }
}
?>
