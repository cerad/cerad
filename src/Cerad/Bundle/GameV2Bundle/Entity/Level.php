<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityPrimary as CommonBaseEntityPrimary;

/* ==============================================
 * In most cases, these should be immutable
 * Or at least the hash fields
 * 
 * Use select distinct to get domans, subs and sports
 * 
 * Have not considered multiple regions under one area domain
 * Different levels for different seasons
 * 
 * Should levels have age/gender?
 * 
 *       Status   levelHash                 sport    domain   domainSub  name aka level
V4Level: Active   SOCCERNASOAAHSAAFRESHMANB Soccer   NASOA    AHSAA      Freshman B
V4Level: Active   SOCCERNASOAAHSAAHSJVB     Soccer   NASOA    AHSAA      HS-JV B
V4Level: Active   SOCCERNASOAAHSAAHSJVG     Soccer   NASOA    AHSAA      HS-JV G
V4Level: Active   SOCCERNASOAAHSAAHSVARB    Soccer   NASOA    AHSAA      HS-Var B
V4Level: Active   SOCCERNASOAAHSAAHSVARG    Soccer   NASOA    AHSAA      HS-Var G
V4Level: Active   SOCCERNASOAAHSAAMSB       Soccer   NASOA    AHSAA      MS-B
V4Level: Active   SOCCERNASOAAHSAAMSG       Soccer   NASOA    AHSAA      MS-G
V4Level: Active   SOCCERNASOAMSSLMSB        Soccer   NASOA    MSSL       MS-B
V4Level: Active   SOCCERNASOAMSSLMSG        Soccer   NASOA    MSSL       MS-G
 */
class Level extends CommonBaseEntityPrimary
{   
    protected $age;         // U12, O30
    protected $gender;      // B/G/C/M/F
    protected $program;     // D1 vs D2, Primeir, AYSO Extra etc
    protected $division;    // Just because
    
    protected $levelLevels1;
    protected $levelLevels2;
    protected $levelProjects;
    
    public function getAge     () { return $this->age;      }
    public function getGender  () { return $this->gender;   }
    public function getProgram () { return $this->program;  }
    public function getDivision() { return $this->division; }
    
    public function getLevelLevels1 () { return $this->levelLevel1s;    }
    public function getLevelLevels2 () { return $this->levelLevel2s;    }
    public function getLevelProjects() { return $this->levelProjects; }
   
    public function setAge     ($value) { $this->onPropertySet('age',     $value); }
    public function setGender  ($value) { $this->onPropertySet('gender',  $value); }
    public function setProgram ($value) { $this->onPropertySet('program', $value); }
    public function setDivision($value) { $this->onPropertySet('division',$value); }
    
    /* =========================================================
     * Standard methods
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->levelLevels1  = new ArrayCollection();
        $this->levelLevels2  = new ArrayCollection();
        $this->levelProjects = new ArrayCollection();
    }
    public function newIdentifier() { return new LevelIdentifier(); }
    
    public function addProject(Project $item, $role = null)
    {
        // Works
        return $this->addRelItem('levelProjects',__NAMESPACE__.'\\ProjectLevel',$item,$role);
    }
    public function addLevel1(Level $item, $role = null)
    {
        // Works
        return $this->addRelItem('levelLevels1',__NAMESPACE__.'\\LevelLevel',$item,$role);
    }
    public function addLevel2(Level $item, $role = null)
    {
        // Works
        return $this->addRelItem('levelLevels2',__NAMESPACE__.'\\LevelLevel',$item,$role);
    }
    
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Level %s %s %s\n",
            $this->status,
            $this->name,
            $this->id);
    }
    /* ===================================
     * Does it make sense to be able to load up an instance here?
     * Project is used to help generate an identifier is necessary
     * And could be used to attache the entity to the project?
     */
    public function loadFromArray($entityFixture,$project = null,$connectToProject = true)
    {
        // Entity
        $id     = IfIsSet::exe($entityFixture,'id');
        $name   = IfIsSet::exe($entityFixture,'name');
        $desc   = IfIsSet::exe($entityFixture,'desc');
        $status = IfIsSet::exe($entityFixture,'status');
                
        if ($id)     $this->setId    ($id);
        if ($status) $this->setStatus($status);

        $this->setName($name);
        $this->setDesc($desc);
        
        $age      = IfIsSet::exe($entityFixture,'age');
        $gender   = IfIsSet::exe($entityFixture,'gender');
        $program  = IfIsSet::exe($entityFixture,'program');
        $division = IfIsSet::exe($entityFixture,'division');
        
        $this->setAge     ($age);
        $this->setGender  ($gender);
        $this->setProgram ($program);
        $this->setDivision($division);
        
        // Idnetifiers
        $identifiersFixture = IfIsSet::exe($entityFixture,'identifiers',array());
        foreach($identifiersFixture as $identifierFixture)
        {
            $entity = $this;
            
            $value  = IfIsSet::exe($identifierFixture,'value' );
            $source = IfIsSet::exe($identifierFixture,'source');
            $status = IfIsSet::exe($identifierFixture,'status');
                
            if (!$value)
            {
                // More or less arbiter style, name avaiable there
                // $value = $manager->hash(array($source,$sport,$season,$domain,$domainSub));
                $values = array();
                $values[] = $source;
 
                if (!$project) $values[] = 'Fake';
                else
                {
                    $values[] = $project->getSeason();
                }
                $values[] = $entity->getName();
                    
                $value = $this->hash($values);
            }
            $identifier = $this->newIdentifier();
            
            $identifier->setValue ($value);
            $identifier->setSource($source);
                
            if ($status) $identifier->setStatus($status);
                
            $this->addIdentifier($identifier);                        
        }
        // Probably going to far
        if ($project && $connectToProject)
        {
            $project->addLevel($this);
        }
        return $this;
    }
}
?>
