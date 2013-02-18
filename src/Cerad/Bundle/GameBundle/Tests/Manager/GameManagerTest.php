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
    public function testTruncate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $manager->truncate();
        
        $this->assertEquals('games', $manager->getEntityManagerName());
    }
    public function testProject()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $project = $manager->createProject('Soccer','SP2013','NASOA','MSSL');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        $this->assertEquals('SOCCERSP2013NASOAMSSL', $project->getHash());
    }
    public function testLevel()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $level = $manager->createLevel('Soccer','NASOA','MSSL','MS-G');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Level', get_class($level));
        $this->assertEquals('SOCCERNASOAMSSLMSG', $level->getHash());
    }
    public function testField()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.game.manager');
        
        $field = $manager->createField('SP2013','NASOA','MSSL','Whitesburg CA');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Field', get_class($field));
        $this->assertEquals('SP2013NASOAMSSLWHITESBURGCA', $field->getHash());
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
