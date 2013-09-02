<?php
namespace Cerad\Bundle\TournBundle\Controller\Person;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PlanController extends Controller
{
    /* ================================================
     * Concert the plan id into a properly linked plan object
     */
    public function findPlan($project,$planId)
    {
        // Make sure sign in
        $user = $this->getUser();
        if (!is_object($user)) return null;
        
        // Load the person mostly for security
        $personRepo = $this->container->get('cerad_person.repository');
        $userPerson = $personRepo->find($user->getPersonId());
        if (!$userPerson) return null;
        
        // Load plan id if have one
        $plan = $personRepo->findPlan($planId);
        
        // Otherwise pull from default account person
        if (!$plan) $plan = $userPerson->getPlan($project->getId());
        
        // Should never happen
        if (!$plan) return null;
        
        // Security check if necessary
        
        // Inject parameters
        $plan->setPlanProperties($project->getPlan());

        return $plan;
    }
    /* ================================================
     * $id === $planId
     */
    public function planAction(Request $request, $id)
    {  
        // Need current project
        $project =  $this->container->get('cerad_tourn.project');

        // Get the plan object
        $plan = $this->findPlan($project,$id);
        if (!$plan) return $this->redirect($this->generateUrl('cerad_tourn_welcome'));
        
        // Build the form
        $formType = $this->get('cerad_tourn.person_plan.formtype');
        $formType->setMetaData($project->getPlan());
        
        $form = $this->createForm($formType,$plan);
        $form->handleRequest($request);

        if ($form->isValid()) 
        {
            if (1) {
            $personRepo = $this->container->get('cerad_person.repository');
            $personRepo->flush();
            
            // return $this->redirect($this->generateUrl('cerad_tourn_home'));
            }
        }
        
        $tplData = array();
        $tplData['form']   = $form->createView();
        $tplData['person'] = $plan->getPerson();
        return $this->render('@CeradTourn/Person/plan.html.twig', $tplData);
    }
}
?>
