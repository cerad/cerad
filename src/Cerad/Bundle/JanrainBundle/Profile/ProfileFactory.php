<?php
namespace Cerad\Bundle\JanrainBundle\Profile;

/* 
 * Your basic factory interface to provide a provider specific wrapper for janrain profiles
 */
class ProfileFactory
{
    static public function create($profileData)
    {
        $profileClassName = 'Cerad\Bundle\JanrainBundle\Profile\\' . $profileData['providerName'] . 'Profile';
        
        if (class_exists($profileClassName))
        {
            return new $profileClassName($profileData);
        }
        print_r($profileData);
        die();
        // Could probably get by with a generic class?
        return new GenericProfile($profileData);
    }
}

?>
