<?php
namespace Cerad\Bundle\JanrainBundle\Profile;

class GoogleProfile extends AbstractProfile
{
    // OAuth2 has it's own field
    public function getIdentifier2() 
    { 
        // Just to be safe
        if (!isset($this->data['googleUserId']) || !$this->data['googleUserId']) return;
        
        // Append google just to help make sure it stays unique
        return 'google' . $this->data['googleUserId']; 
    }
}
/* 
  [providerName]      => Google 
  [identifier]        => https://www.google.com/accounts/o8/id?id=AItOawlw690VK5sxrrejWazT_iCy_cMC6Xs2fv4 
  [googleUserId]      => 113055156735633728525
  [verifiedEmail]     => ahundiak@gmail.com 
  [email]             => ahundiak@gmail.com 
  [preferredUsername] => ahundiak 
  [displayName]       => Art Hundiak 
  [name] => 
    [formatted]  => Art Hundiak 
    [givenName]  => Art 
    [familyName] => Hundiak
  [url]   => https://www.google.com/profiles/113055156735633728525 
 */
?>
