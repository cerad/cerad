<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\NotifyPropertyChanged,
    Doctrine\Common\PropertyChangedListener;

class BaseEntity implements NotifyPropertyChanged
{
    /* ========================================================================
     * Property change stuff
     * Benchmarked mass imports with and without change listener
     *   makes a big difference on large imports
     */
    protected $listeners = array();

    public function addPropertyChangedListener(PropertyChangedListener $listener)
    {
        $this->listeners[] = $listener;
    }    
    protected function onPropertyChanged($propName, $oldValue, $newValue)
    {
        foreach ($this->listeners as $listener) 
        {
            $listener->propertyChanged($this, $propName, $oldValue, $newValue);
        }
    }
    protected function onPropertySet($name,$newValue)
    {
        if ($this->$name === $newValue) return $this;
        
        $oldValue = $this->$name;
        
        $this->$name = $newValue;
        
        $this->onPropertyChanged($name,$oldValue,$newValue);
        
        return $this;
    }
    /* =============================================================
     * Probably want to implement array interface, keeps coming in handy
     */
}
?>
