<?php
namespace Cerad\Bundle\AccountBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

/* ====================================================
 * I seemed to be duplicating this code over and over
 */
class AuthenticationInformation
{
    public function get(Request $request)
    {
        $error = null;
        
        // Check request for error
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) 
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        // Then look in session
        $session = $request->getSession();
        if (!$session)
        {
            $info['lastUsername'] = null;
            $info['error'] = $error ? $error->getMessage() : null;
            return $info;
        }
        
        // Pull user name
        $info['lastUsername'] = $session ? $session->get(SecurityContext::LAST_USERNAME) : null;
        
        // Check for error in context
        if (!$error && $session->has(SecurityContext::AUTHENTICATION_ERROR)) 
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove      (SecurityContext::AUTHENTICATION_ERROR);
       }
       $info['error'] = $error ? $error->getMessage() : null;
       return $info; 
    }    
}
?>
