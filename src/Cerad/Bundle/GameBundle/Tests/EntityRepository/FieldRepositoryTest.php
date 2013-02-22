<?php
namespace Cerad\Bundle\GameBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FieldRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad.field.repository';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\EntityRepository\FieldRepository', get_class($manager));
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Field',           $manager->getClassName());
    }
    public function testCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
         
        $params = array('season' => 'SP2013', 'domain' => 'NASOA','domainSub' => 'MSSL', 'name' => 'Whitesburg CA');
        
        $field = $manager->createEntity($params);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Field', get_class($field));
        $this->assertEquals('NASOAMSSLSP2013WHITESBURGCA', $field->getHash());   
        
        // Venue
        $params = array('season' => 'SP2013', 'domain' => 'NASOA','domainSub' => 'HASL', 'venue' => 'Merrimack #1');
        
        $field = $manager->createEntity($params);
        
        $this->assertEquals('NASOAHASLSP2013MERRIMACK1',$field->getHash());
        
        // Venue with subsite
        $params = array('season' => 'SP2013', 'domain' => 'NASOA','domainSub' => 'HASL', 'venue' => 'Merrimack', 'venueSub' => '01N');
        
        $field = $manager->createEntity($params);
        
        $this->assertEquals('NASOAHASLSP2013MERRIMACK01N',$field->getHash());
        $this->assertEquals('Merrimack, 01N',$field->getName());
        
        

    }
}
?>
