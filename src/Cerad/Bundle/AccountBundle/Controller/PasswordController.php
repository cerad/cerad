<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

// Used by AuthenticationListener to log user in with LoginManager
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;

/* ====================================================================
 * It would be nice to have this logic tucked away here but 
 * Problem is integration, how to get the header information when this is part of
 * a Tourn app or a different app class altogether
 * 
 * This could be a base class which is then extended by the tournament class
 * Things like extended template name would then be a parameter
 * 
 */
class PasswordController extends Controller
{
    const SESSION_EMAIL = 'fos_user_send_resetting_email/email';
    
    public function resetResetAction(Request $request)
    {
        die('reset reset');
    }
    public function resetRequestAction(Request $request)
    {
        $item = array(
            'username' => $request->getSession()->get(SecurityContext::LAST_USERNAME),
        );
        $form = $this->createForm($this->get('cerad_account.password_reset.formtype'),$item);
        
        if ($request->isMethod('POST'))
        {
            // Submit with a response will be depreciated in 3.x
            // $form->submit($request->request->get($form->getName()));
            $form->submit($request);

            if ($form->isValid())
            {
                // Grab the user name
                $item = $form->getData();
                $username = $item['username'];
                
                // Grab the user
                $userProvider = $this->get('cerad_account.user_provider');
                try
                {
                    $user = $userProvider->loadUserByUsername($username);
                    
                    if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) 
                    {
                        //return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.'.$this->getEngine());
                    }
                    if (null === $user->getConfirmationToken()) 
                    {
                        $tokenGenerator = $this->get('fos_user.util.token_generator');
                        $user->setConfirmationToken($tokenGenerator->generateToken());
                    }
                    $this->get('session')->set(static::SESSION_EMAIL, $this->getObfuscatedEmail($user));
                    $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
                    $user->setPasswordRequestedAt(new \DateTime());
                    $this->get('fos_user.user_manager')->updateUser($user);

                    return $this->redirect($this->generateUrl('cerad_account_password_reset_email_sent'));
                }
                catch (\Exception $e)
                {
                    // This should not happen as the username was already verified to exist
                    // die('User not found');
                }
            }
            // else { die('Invalid'); }
        }
        $tplData = array();
        $tplData['passwordResetForm'] = $form->createView();
        return $this->render('@CeradAccount/password_reset.html.twig', $tplData);
    }
    public function emailSentAction(Request $request)
    {
        $session = $request->getSession();
        $email = $session->get(static::SESSION_EMAIL);
        $session->remove(static::SESSION_EMAIL);

        if (empty($email)) 
        {
            return $this->redirect($this->generateUrl('cerad_account_password_reset_request'));
        }
        $tplData = array();
        $tplData['email'] = $email;
        return $this->render('@CeradAccount/password_reset_email_sent.html.twig', $tplData);
    }
    protected function getObfuscatedEmail(UserInterface $user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }  
}
?>
