<?php
namespace Cerad\Bundle\AccountBundle\Functions;

/* ==================================
 * Aliasing/Importing a function is not supported for ssome reason
 * Hence the static function isdie of a class
 */
class Hash
{
    static public function exe($value, $crc = false)
    {
        if (is_array($value))
        {
            $array = $value;
            $value = null;
            
            // Trim and cat
            array_walk($array, function($val) use (&$value) { $value .= trim($val); });
        }
        // Probably just get rid of everything but letters/numbers,undersocres
        $value = strtoupper(preg_replace("/[^a-zA-Z0-9_]/", '', $value));

        if (!$crc) return $value;
        
        return hash('crc32',$value);       
    }    
}
?>
