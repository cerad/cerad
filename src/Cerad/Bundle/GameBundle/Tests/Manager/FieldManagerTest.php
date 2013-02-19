<?php
namespace Cerad\Bundle\GameBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FieldManagerTest extends WebTestCase
{
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.field.manager');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Manager\FieldManager', get_class($manager));
    }
    public function xestResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.field.manager');
        
        $manager->resetDatabase();
    }
    public function testField()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.field.manager');
        
        $field = $manager->createField('SP2013','NASOA','MSSL','Whitesburg CA');
      
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Field', get_class($field));
        $this->assertEquals('SP2013NASOAMSSLWHITESBURGCA', $field->getHash());
    }
}

?>
