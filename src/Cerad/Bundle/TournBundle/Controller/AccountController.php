<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{
    public function createAction(Request $request)
    {   
        $manager = $this->get('cerad_tourn.schedule.manager');
        
        $item = array
        (
            'userName'  => null,
            'userPass'  => null,
            'aysoid'    => null,
            'region'    => null,
            'firstName' => null,
            'lastName'  => null,
            'nickName'  => null,
            'phone'     => null,
            'email'     => null,
        );
                
        // Build the form
        $formType = $this->get('cerad_tourn.account.create.formtype');
        
        // The form itself
        $form = $this->createForm($formType,$item);
        
        // Check post
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $item = $form->getData(); print_r($item); //die( 'POSTED');
                
                //return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list'));
            }
            //else die("Not valid");
        }
        $tplData = array();
        $tplData['form'] = $form->createView();
        
        return $this->render('@CeradTourn/account/create.html.twig', $tplData);
    }
    public function editAction(Request $request, $id = 0)
    {
        return $this->redirect($this->generateUrl('cerad_tourn_home'));
    }
}
?>
