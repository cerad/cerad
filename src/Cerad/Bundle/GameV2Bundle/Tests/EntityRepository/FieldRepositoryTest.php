<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FieldRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad_gamev2.field.manager';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\EntityRepository\FieldRepository', get_class($manager));
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Field',$manager->getClassName());
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
        
        // domainSub = MSSL
        $field = $manager->createField('NASOA',null,'SP2013','Whitesburg CA');
        
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Field', get_class($field));
        $this->assertEquals('NASOASP2013WHITESBURGCA', $field->getId());       
    }
}
?>
