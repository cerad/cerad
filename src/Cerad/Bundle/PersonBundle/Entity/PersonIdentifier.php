<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* ===============================================================
 * Each person will have one or more unique identifiers
 * AYSOV12345678
 * USSFC1234567812345678
 * NFHSC123456
 * NISOACxxxxx
 * 
 * Keep it real simple for now
 */
class PersonIdentifier extends BaseEntity
{
    protected $id;
    
    protected $key;
    
    protected $person;

    protected $status = 'Active'; // Active means all is well, Checking for needs to be checked
    
    /* =================================================================
     * Accessors
     */
    public function getId    () { return $this->id;      }
    public function getKey   () { return $this->key; }
    public function getPerson() { return $this->person;  }
    public function getStatus() { return $this->status;   }
    
    public function setKey   ($value) { $this->onPropertySet('key',   $value); }    
    public function setStatus($value) { $this->onPropertySet('status',$value); }
    public function setPerson($value) { $this->onPropertySet('person',$value); } 
    
    /* ================================================================
     * person <- person_identifier
     *        <- person_league (region #, cvpa etc)     -> person_identifier
     *        <- person_cert (referee, instructor etc)  -> person_identifier  
     * 
     * Add FED and ROLE to identifier, work for league, not for cert
     * FED applies to both league and cert 
     */
}
?>
