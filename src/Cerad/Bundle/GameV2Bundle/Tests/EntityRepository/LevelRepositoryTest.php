<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LevelRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad_gamev2.level.manager';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\EntityRepository\LevelRepository', get_class($manager));
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Level',$manager->getClassName());
    }
    public function testResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $manager->resetDatabase();
    }
    public function testCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $level = $manager->createLevel('Soccer','NASOA','MSSL','MS-G');
        
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Level', get_class($level));
        $this->assertEquals('SOCCERNASOAMSSLMSG', $level->getId());       
    }
}
?>
