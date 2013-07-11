<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    protected function getSecurityAuthenticationInfomation(Request $request)
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
    public function loginAction(Request $request)
    {
        // Majic to get any previous errors
        $authInfo = $this->get('cerad_account.authentication_information');
        $info = $authInfo->get($request);
        
        $item = array(
            'error'    => $info['error'],
            'username' => $info['lastUsername'],
            'password' => null,
        );
        $loginForm         = $this->createForm($this->get('cerad_account.signin.formtype'),        $item);
        $passwordResetForm = $this->createForm($this->get('cerad_account.password_reset.formtype'),$item);
        
        // Render
        $tplData = array();
        $tplData['loginForm']         = $loginForm        ->createView();
        $tplData['passwordResetForm'] = $passwordResetForm->createView();
        return $this->render('@CeradAccount/Security/login.html.twig', $tplData);      
    }
    /* ================================================
     * In case the firewall is not configured correctly
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

}
?>
