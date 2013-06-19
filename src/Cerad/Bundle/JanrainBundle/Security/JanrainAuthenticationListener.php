<?php

namespace Cerad\Bundle\JanrainBundle\Security;

use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

//use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

//use Symfony\Component\Security\Core\SecurityContextInterface;
//use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

use Cerad\Bundle\JanrainBundle\Profile\ProfileFactory;

/* =============================================================
 * This also deals with two cases:
 * 1. New user wants to register using openid profile information
 * 2. Current user wants to add openif information to their account
 */
class JanrainAuthenticationListener extends AbstractAuthenticationListener
{  
    private $securityContext;
    
    // Need this just to get the securityContext
    public function __construct(
            $securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, 
            $successHandler, $failureHandler, $options = array(), $logger = null, $dispatcher = null)
    {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, 
            $successHandler, $failureHandler, $options, $logger, $dispatcher);
        
        $this->securityContext = $securityContext;
    }
    /* ===========================================================
     * Redirect back from the janrain stite
     */
    protected function attemptAuthentication(Request $request)
    {   
        // Verify have token
        $janrainRequestToken = $request->get('token');
        if (!$janrainRequestToken) return;
        
        // Toss an exception if not found
        $janrainKey = $this->options['rpx_api_key'];
        if (!$janrainKey) return;
        
        // Grab the profile
        $post_data = array
        (
            'token'  => $janrainRequestToken,
            'apiKey' => $janrainKey,
            'format' => 'json'
        );
        // Suppose this could be abstracted
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, 'https://rpxnow.com/api/v2/auth_info');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // For non-ssl stuff
        $raw_json = curl_exec($curl);
        curl_close($curl);
        
        $authInfo = json_decode($raw_json, true);
        
        if ($authInfo['stat'] != 'ok')
        {
            // Suppose could toss an exception
            // $error = $authInfo['err']['msg'];
            return;
        }
        // Might want to wrap this in a profile object for decoding
        $profile = ProfileFactory::create($authInfo['profile']);

        // Tuck these away for now, later need to figure out when to remove
        // On logout for sure
        $request->getSession()->set('cerad_janrain_token',  $janrainRequestToken);
        $request->getSession()->set('cerad_janrain_profile',$profile);
      //$request->getSession()->set('cerad_janrain_wtf',    'wtf');
          
        // Security Token
        $token = new JanrainAuthenticationToken($profile);

        try
        {
            $authToken = $this->authenticationManager->authenticate($token);
            
            $currentToken = $this->securityContext->getToken();
            
            if (!$currentToken) { return $authToken; }
            
            // currentToken could be the same as authToken
            
            // currentToken could be different than authToken
            
            return $authToken;
        }
        catch (UsernameNotFoundException $e) 
        {
            // This just means tried to load a user not currently linked
           
            // Already signed in? redirect to add
            
            // Not signed in? redirect to register       
            if ($this->securityContext->getToken() == null)
            {
                $response = $this->httpUtils->createRedirectResponse($request, $this->options['create_path']);
            }
            else
            {
                $response = $this->httpUtils->createRedirectResponse($request, $this->options['add_path']);
            }
            
            return $response;
            
        }
        // Ignore anything else for now
        return;
    }        
}
?>
