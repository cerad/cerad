<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PersonController extends Controller
{
    public function genPlanForm(Request $request, $personId)
    { 
        // Must have a person
        $account = $this->getUser();
        if (!is_object($account)) return null;
        
        $person = $account->getPerson();
        if (!is_object($person)) return null;
        
        // See if different person
        if ($personId)
        {
            $personManager  = $this->get('cerad_person.manager');
            $personx = $personManager->find($personId);
            if (!$personx) return null;
            
            // Verify allowed to edit
            $slave = $person->getPerson($personx);
            if (!$slave) return null;
            
            // Use it
            $person = $personx;
        }
        // Plan template
        $project = $this->get('cerad_tourn.project');
        
        // Does the person already have a plan?
        $personPlan = $person->getPlan($project);
        if (!$personPlan)
        {
            $personManager  = $this->get('cerad_person.manager');
            $personPlan = $personManager->createPersonPlan($project,$person,false);
        }
                
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
        $formType->setMetaData($project->getPlan());
        
        $form = $this->createForm($formType,$personPlan);

        return array('form' => $form, 'person' => $person);
    }    
    public function planFormAction(Request $request, $id)
    {
        // Must be signed in
        $data = $this->genPlanForm($request,$id);
        if (!$data) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        $form   = $data['form'];
        $person = $data['person'];
        
        $tplData = array();
        $tplData['form']   = $form->createView();
        $tplData['person'] = $person;
        return $this->render('@CeradTourn/person/plan_form.html.twig', $tplData);
    }
    public function planAction(Request $request, $id)
    {   
        // The form itself
        $data = $this->genPlanForm($request,$id);
        if (!$data) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        $form   = $data['form'];
        $person = $data['person'];
        
        // Check post
        if ($request->isMethod('POST'))
        {
            // Submit with a response will be depreciated in 3.x
            // $form->submit($request->request->get($form->getName()));
            $form->submit($request);

            if ($form->isValid())
            {
                // Consider checking if "Will Attend" have been answered.
                // Maybe a constraint?
                $personPlan = $form->getData();
                
                // Persist
                $personManager  = $this->get('cerad_person.manager');   
                $personManager->persist($personPlan);
                $personManager->flush();

                // Clear session
                $request->getSession()->remove('cerad_tourn_person_plan');
                
                // Redirect
                return $this->redirect($this->generateUrl('cerad_tourn_home'));
            }
            else 
            {
                // Really should not happen
                $personPlan = $form->getData();
              //$item['invalid'] = true;
                $request->getSession()->set('cerad_tourn_person_plan',$personPlan->getPlan());
                return $this->redirect($this->generateUrl('cerad_tourn_account_create_failure'));
            }
        }
        $tplData = array();
        $tplData['form']   = $form->createView();
        $tplData['person'] = $person;
        return $this->render('@CeradTourn/person/plan.html.twig', $tplData);
    }
}
?>
