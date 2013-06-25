<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

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
    
    protected $sport;
    protected $domain;
    protected $domainSub;
    protected $name;        // AKA The actual level description
    
    protected $program;     // D1 vs D2, Primeir, AYSO Extra etc
    protected $age;         // U12, O30
    protected $sex;         // B/G/C/M/F
    
    protected $status;
    
    protected $link;   // Future, allow linking the same level across multiple domains
    
    public function getId()        { return $this->id;      }
    public function getAge ()      { return $this->age;     }
    public function getSex ()      { return $this->sex;     }
    public function getLink()      { return $this->link;    }
    public function getStatus()    { return $this->status;  }
    public function getProgram()   { return $this->program; }
    
    public function getName()      { return $this->name;    }
    public function getSport ()    { return $this->sport;   }
    public function getDomain()    { return $this->domain;  }
    public function getDomainSub() { return $this->domainSub; }

    public function setId       ($value) { $this->onPropertySet('id',       $value); }
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
    public function setSport    ($value) { $this->onPropertySet('sport',    $value); }
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    
    public function setAge      ($value) { $this->onPropertySet('age',     $value); }
    public function setSex      ($value) { $this->onPropertySet('sex',     $value); }
    public function setProgram  ($value) { $this->onPropertySet('program', $value); }
    
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    public function setLink     ($value) { $this->onPropertySet('link',     $value); }
    
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Level   %-8s %-8s %-8s %-10s %s %s\n",
            $this->status,
            $this->sport,
            $this->domain,
            $this->domainSub,
            $this->name,
            $this->id);
    }
}
?>
