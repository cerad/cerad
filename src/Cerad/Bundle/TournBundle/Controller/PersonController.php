<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{
    public function genPlanForm(Request $request)
    { 
        // Must have a person
        $account = $this->getUser();
        if (!is_object($account)) return null;
        
        $person = $account->getPerson();
        if (!is_object($person)) return null;
        
        die(get_class($person));
        
        // Plan template
        $project = $this->container->getParameter('cerad_tourn_project');
        
        $personManager  = $this->get('cerad_person.manager');
        $personPlan = $personManager->newPersonPlan();
        
        $personPlan->setPlanProperties($project['plan']);
                
        // Restore from session
        if ($request->getSession()->has('cerad_tourn_person_plan'))
        {
            $plan = $request->getSession()->get('cerad_tourn_person_plan');
            if (is_array($plan))
            {
                $plan = array_merge($personPlan->getPlan(),$plan);
                $personPlan->setPlan($plan);
            }
        }
        // Build the form
        $formType = $this->get('cerad_tourn.person_plan.formtype');
        $formType->setMetaData($project['plan']);
        
        $form = $this->createForm($formType,$personPlan);

        return $form;
        
    }    
    public function planFormAction(Request $request)
    {
        $form = $this->genPlanForm($request);
        if (!$form) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradTourn/person/plan_form.html.twig', $tplData);
    }
    public function planAction(Request $request)
    {   
        // The form itself
        $form = $this->genPlanForm($request);
        if (!$form) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        // Check post
        if ($request->isMethod('POST'))
        {
            // Submit with a response will be depreciated in 3.x
            // $form->submit($request->request->get($form->getName()));
            $form->submit($request);

            if ($form->isValid())
            {
                $personPlan = $form->getData();
              //$item['invalid'] = false;
                
                $request->getSession()->set('cerad_tourn_person_plan',$personPlan->getPlan());
                
                // Persist
          
                // Redirect
                return $this->redirect($this->generateUrl('cerad_tourn_person_plan_success'));
                 
            }
            else 
            {
                // Really should not happen
                $item = $form->getData();
              //$item['invalid'] = true;
                $request->getSession()->set('cerad_tourn_person_plan',$personPlan->getPlan());
                return $this->redirect($this->generateUrl('cerad_tourn_account_create_failure'));
            }
        }
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradTourn/person/plan.html.twig', $tplData);
    }
}
?>
