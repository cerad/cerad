<?php
namespace Cerad\Bundle\GameBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LevelManagerTest extends WebTestCase
{
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.level.manager');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Manager\LevelManager', get_class($manager));
    }
    public function xestResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.level.manager');
        
        $manager->resetDatabase();
    }
    public function testLevel()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.level.manager');
        
        $level = $manager->createLevel('Soccer','NASOA','MSSL','MS-G');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Level', get_class($level));
        $this->assertEquals('SOCCERNASOAMSSLMSG', $level->getHash());
    }
}

?>
