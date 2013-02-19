<?php
namespace Cerad\Bundle\GameBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameManagerTest extends WebTestCase
{
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $this->assertEquals('games', $manager->getEntityManagerName());
    }
    public function testResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $manager->resetDatabase();
        
        $this->assertEquals('games', $manager->getEntityManagerName());
    }
    public function testGameTeam()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $team = $manager->createGameTeamAway();
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\GameTeam', get_class($team));
        $this->assertEquals('Away', $team->getRole());
    }
    public function testGamePerson()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $person = $manager->createGamePerson(1,'Referee','John Price');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\GamePerson', get_class($person));
        $this->assertEquals('John Price', $person->getName());
    }
    public function testGame()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $game = $manager->createGame();
        
        $game->setNum(100);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Game', get_class($game));
        $this->assertEquals(100, $game->getNum());
    }
}

?>
