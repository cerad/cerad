<?php
namespace Cerad\Bundle\AccountBundle\Functions;

/* ==================================
 * Copied from the php manual which in turn copied from some package
 * Returns 40 char string
 * 5BBD0493-8C84-4036-829F-046E230F7225
 */
class Guid
{
    static public function gen()
    {        
        if (function_exists('com_create_guid') === true)
        {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', 
                mt_rand(0, 65535),     mt_rand(0, 65535),     mt_rand(0, 65535), 
                mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), 
                mt_rand(0, 65535),     mt_rand(0, 65535));
    }
}
?>
