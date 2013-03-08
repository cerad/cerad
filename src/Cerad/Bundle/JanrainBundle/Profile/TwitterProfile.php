<?php
namespace Cerad\Bundle\JanrainBundle\Profile;

class TwitterProfile extends AbstractProfile
{
    // OAuth2 is tacked on to the end of the identifier
    // TODO: Verify for Twitter
    public function getIdentifier2() 
    { 
        $identifier = $this->data['identifier'];
        
        $userId = substr($identifier,strrpos($identifier,'=')+1);
        
        if (!$userId) return null;
        
        return 'twitter' . $userId;
    }
}
/* ==========================================
 * [providerName]      => Twitter 
 * [identifier]        => http://twitter.com/account/profile?user_id=49477179 
 * [preferredUsername] => ahundiak 
 * [displayName]       => Art Hundiak 
 * [name] 
 *     [formatted] => Art Hundiak 
 * [url]   => http://twitter.com/ahundiak 
 * [photo] => http://a0.twimg.com/sticky/default_profile_images/default_profile_3_normal.png [utcOffset] => -06:00 ) 
 */
?>
