<?php
namespace Cerad\Bundle\JanrainBundle\Profile;

class FacebookProfile extends AbstractProfile
{
    // OAuth2 is tacked on to the end of the identifier
    public function getIdentifier2() 
    { 
        $identifier = $this->data['identifier'];
        
        $facebookId = substr($identifier,strrpos($identifier,'=')+1);
        
        if (!$facebookId) return null;
        
        // Tack on facebook to keep it unique
        return 'facebook' . $facebookId;
    }
}
/*
 * [providerName]      => Facebook 
 * [identifier]        => http://www.facebook.com/profile.php?id=1530263836 
 * [verifiedEmail]     => ahundiak@gmail.com 
 * [preferredUsername] => ArthurHundiak 
 * [displayName]       => Arthur Hundiak 
 * [name] => Array ( 
 *     [formatted]  => Arthur Hundiak 
 *     [givenName]  => Arthur 
 *     [familyName] => Hundiak 
 * [email]     => ahundiak@gmail.com 
 * [url]       => http://www.facebook.com/arthur.hundiak 
 * [photo]     => https://graph.facebook.com/1530263836/picture?type=large 
 * [utcOffset] => -05:00 
 * [address]
 *     [formatted] => Huntsville, Alabama 
 *     [type]      => currentLocation 
 * [gender] => male
 * 
 */

?>
