<?php
namespace Cerad\Bundle\CommonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/* ==================================================
 * For lack of a better term
 * Primary elements have their own manager
 * Examples: Team Game Project
 * They all have identifiers
 */
abstract class BaseEntityPrimary extends BaseEntity
{
    protected $id; // All have a guid id

    public function getId()    { return $this->id; }
    public function setId($id) { return $this->onPropertySet('id',$id); }
    
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
        if (!$identifier->getName()) $identifier->setname($this->getName());
        
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
}
?>
