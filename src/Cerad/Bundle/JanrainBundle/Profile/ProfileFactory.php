<?php
namespace Cerad\Bundle\JanrainBundle\Profile;

/* 
 * Your basic factory interface to provide a provider specific wrapper for janrain profiles
 */
class ProfileFactory
{
    static public function create($profileData)
    {
        // Mess a bit with provider name
        $providerName = $profileData['providerName'];
        switch($providerName)
        {
            case 'Yahoo!':            $providerName = 'Yahoo';     break; // Bet this bites me later
            case 'Microsoft Account': $providerName = 'Microsoft'; break; // Bet this bites me later
        }
        $profileClassName = 'Cerad\Bundle\JanrainBundle\Profile\\' . $providerName. 'Profile';
        
        if (class_exists($profileClassName))
        {
            return new $profileClassName($profileData);
        }

        // Could probably get by with a generic class?
        return new GenericProfile($profileData);
    }
}

?>
