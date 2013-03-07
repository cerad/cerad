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
        
        return new $profileClassName($profileData);
    }
}

?>
