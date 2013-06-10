<?php

namespace Cerad\Bundle\GameBundle\Entity;

use Doctrine\Common\NotifyPropertyChanged,
    Doctrine\Common\PropertyChangedListener;

class BaseEntity implements NotifyPropertyChanged
{
    /* ========================================================================
     * Standard hashing
     * Either value or array
     * Remove some punctuation
     * Return actual hash or canocial string
     */
    static public function hash($value, $crc = false)
    {
        if (is_array($value))
        {
            $array = $value;
            $value = null;
            
            // Trim and cat
            array_walk($array, function($val) use (&$value) { $value .= trim($val); });
        }
        // Probably just get rid of everything but letters/numbers
        // Maybe allow under scores?
        $value = strtoupper(preg_replace("/[^a-zA-Z0-9]/", '', $value));
        
        // $value = strtoupper(str_replace(array(' ','~','-','#',',','"',"'"),'',$value));
        
        if (!$crc) return $value;
        
        return hash('crc32',$value);       
    }
    /* ========================================================================
     * Property change stuff
     * TODO: Benchmark mass imports with and without change listener
     *       Just to see if there is any real value
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
