<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
class Level extends BaseEntity
{
    protected $id;
    
    protected $name;        // AKA The actual level description
    
    protected $age;         // U12, O30
    protected $gender;      // B/G/C/M/F
    protected $program;     // D1 vs D2, Primeir, AYSO Extra etc
    
    protected $status = 'Active';
    
    protected $identifiers;
    protected $levelLevels;
    protected $projectLevels;
    
    public function getId     () { return $this->id;      }
    public function getAge    () { return $this->age;     }
    public function getName   () { return $this->name;    }
    public function getStatus () { return $this->status;  }
    public function getGender () { return $this->gender;  }
    public function getProgram() { return $this->program; }
    
    public function getIdentifiers  () { return $this->identifiers;    }
    public function getLevelLevels  () { return $this->levelLevels;    }
    public function getProjectLevels() { return $this->projectLevelss; }
   
    public function setId     ($value) { $this->onPropertySet('id',     $value); }
    public function setAge    ($value) { $this->onPropertySet('age',    $value); }
    public function setName   ($value) { $this->onPropertySet('name',   $value); }
    public function setStatus ($value) { $this->onPropertySet('status', $value); }
    public function setGender ($value) { $this->onPropertySet('gender', $value); }
    public function setProgram($value) { $this->onPropertySet('program',$value); }
    
   /* =========================================================
     * 
     */
    public function __construct()
    {
        $this->id            = $this->genGUID();
        $this->identifiers   = new ArrayCollection();
        $this->levelLevels   = new ArrayCollection();
        $this->projectLevels = new ArrayCollection();
    }
    public function addIdentifier(LevelIdentifier $identifier)
    {
        $this->identifiers[] = $identifier;
        $identifier->setLevel($this);
        $this->onPropertyChanged('identifiers');
    }
    
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Level   %-8s %-8s %s\n",
            $this->status,
            $this->name,
            $this->id);
    }
}
?>
