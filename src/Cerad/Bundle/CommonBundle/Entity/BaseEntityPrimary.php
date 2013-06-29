<?php
namespace Cerad\Bundle\CommonBundle\Entity;

use Cerad\Bundle\CommonBundle\Collections\ArrayCollection;

/* ==================================================
 * For lack of a better term
 * Primary elements have their own manager
 * Examples: Project Field Venue Level Team League
 * 
 * Game might be a bit different
 * 
 * They all have identifiers
 */
abstract class BaseEntityPrimary extends BaseEntity
{
    protected $id;   // All have a guid id
    protected $name; // All have a name
    protected $desc; // Applicable to most
    
    protected $status = 'Active';  // This might be sone step too far
    
    public function getId    () { return $this->id;     }
    public function getName  () { return $this->name;   }
    public function getDesc  () { return $this->desc;   }
    public function getStatus() { return $this->status; }
    
    public function setId    ($value) { return $this->onPropertySet('id',    $value); }
    public function setName  ($value) { return $this->onPropertySet('name',  $value); }
    public function setDesc  ($value) { return $this->onPropertySet('desc',  $value); }
    public function setStatus($value) { return $this->onPropertySet('status',$value); }
    
    /* =============================================================
     * Lots of entities have identifiers
     * Might be a trait?
     * BaseEntityWithIdentifiers
     * BaseEntityPrimary
     */
    protected $identifiers;
    
    public function getIdentifiers () { return $this->identifiers;  }
    
    // Abstract, needs to be overwritten
    abstract public function newIdentifier();
    
    /* =========================================
     * Tempting to have this create an identifier
     * One problem is that need to know the class name
     */
    public function addIdentifier($identifier)
    {
        // Check for dups
        foreach($this->identifiers as $identifierx)
        {
            if ($identifierx->getValue() == $identifier->getValue()) return;
        }
        // Copy name if none existing
        if (!$identifier->getName()) $identifier->setName($this->getName());
        
        // Add it
        $this->identifiers[] = $identifier;
        
        // Establish link
        $identifier->setEntity($this);
        
        // Make sure it persists
        $this->onPropertyChanged('identifiers');
    }
    
    /* =========================================
     * Even a standard constructor
     */
    public function __construct()
    {
        $this->id          = self::genGUID();
        $this->identifiers = new ArrayCollection();        
    }
    
    /* =======================================
     * Try to standardize adding many to many relation
     * 
     * Works except because it is in the common bundle.
     * A FQN needs to be passed, bit of a pain on the calling end
     */
    public function addRelItem($relPropName,$relClassName,$item,$role = null)
    {
        // Relations
        $rels = $this->$relPropName;
        
        // Protect against dups
        foreach($rels as $rel)
        {
            if (($rel->getRole() == $role) && ($rel->getEntity2()->getId() == $item->getId())) return $this;
        }
        // Make new entity
        $rel = new $relClassName();
        $rel->setRole   ($role);
        $rel->setEntity1($this);
        $rel->setEntity2($item);
        $rel->setName1  ($this->getName());
        $rel->setName2  ($item->getName());

        $rels[] = $rel;
        $this->$relPropName = $rels;
        $this->onPropertyChanged($relPropName);
     
        return $this;
    }
}
?>
