<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\NotifyPropertyChanged,
    Doctrine\Common\PropertyChangedListener;

class BaseEntity implements NotifyPropertyChanged
{
    protected function genGUID()
    {
        // Simple 32 char string, not really a guid but oh well
        // return strtoupper(md5(uniqid('zayso',true)));
        
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                mt_rand(0, 65535),     mt_rand(0, 65535),     mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), 
                mt_rand(0, 65535),     mt_rand(0, 65535));
    }
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
    protected function onPropertyChanged($propName, $oldValue = null, $newValue = null)
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
