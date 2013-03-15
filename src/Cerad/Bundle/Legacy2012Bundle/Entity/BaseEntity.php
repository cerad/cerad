<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

use 
    Doctrine\Common\NotifyPropertyChanged,
    Doctrine\Common\PropertyChangedListener;

/* ============================================================
 * Implements property changes stuff
 * and blob handling
 * ORM\ChangeTrackingPolicy("NOTIFY")
 */
class BaseEntity implements NotifyPropertyChanged
{
    /* ========================================================
     * Blob routines
     * Assume mostly readonly
     */
    /**  ORM\Column(type="text", name="datax", nullable=true) */
    protected $datax = null;
    protected $data  = null;
    
    public function get($name, $default = null)
    {
        // First time called after loading
        if (!$this->data) $this->data = unserialize($this->datax);

        if (isset($this->data[$name])) return $this->data[$name];
        
        return $default;
    }
    public function set($name,$value)
    {
        // First time called after loading
        if (!$this->data) $this->data = unserialize($this->datax);
        
        // Special for unsetting
        if ($value === null)
        {
            // Do nothing if have nothing
            if (!isset($this->data[$name])) return;
            
            unset($this->data[$name]);
            $this->datax = serialize($this->data);
            
            // Still need to test
            $this->onPropertyChanged('datax',null,$this->datax);
 
            return;
        }
        
        // Only if changed
        if (isset($this->data[$name])) 
        {
            $oldValue = $this->data[$name];
            if ($oldValue === $value) return;
        }
        else $oldValue = null;
        
        $this->data[$name] = $value;
        $this->datax = serialize($this->data);
    
        // Need to specify datax for this to work
        $this->onPropertyChanged('datax',null,$this->datax);
    }
    /* ========================================================================
     * Property change stuff
     */
    protected $listeners = array();
    protected $changed   = false;
    
    public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        $this->listeners[] = $listener;
    }
    public function isChanged() { return $changed; }
    
    protected function onPropertyChanged($propName, $oldValue, $newValue)
    {
        $this->changed = true;
        foreach ($this->listeners as $listener) 
        {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }
    protected function onObjectPropertySet($name,$newObject)
    {
        $oldObject = $this->$name;
        
        if ($oldObject && $newObject)
        {
            if ($oldObject->getId() == $newObject->getId()) return;
        }
        $this->onPropertyChanged($name,$oldObject,$newObject);
        
        $this->$name = $newObject;
    }
    protected function onObjectPropertySetx($name,$newObject)
    {
        $oldObject = $this->$name;
        
        if ($oldObject && $newObject)
        {
            // if ($oldObject->getId() == $newObject->getId()) return;
        }
        $this->onPropertyChanged($name,$oldObject,$newObject);
        
        $this->$name = $newObject;
    }
    protected function onScalerPropertySet($name,$value)
    {
        if ($this->$name === $value) return;
        $this->onPropertyChanged($name,$this->$name,$value);
        $this->$name = $value;
    }
    protected function onDataPropertySet($name,$value)
    {
        if ($this->get($name) === $value) return;
        $this->onPropertyChanged($name,$this->get($name),$value);
        $this->set($name,$value);
    }
}
?>
