<?php
namespace Cerad\Bundle\GameBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LevelRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad.level.repository';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\EntityRepository\LevelRepository', get_class($manager));
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Level',           $manager->getClassName());
    }
    public function sestResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $manager->resetDatabase();
    }
    public function testCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
         
        $params = array('sport' => 'Soccer', 'domain' => 'NASOA','domainSub' => 'MSSL', 'name' => 'MS-G');
        
        $level = $manager->createEntity($params);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Level', get_class($level));
        $this->assertEquals('SOCCERNASOAMSSLMSG', $level->getHash());       
    }
}
?>
