<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Cerad\Bundle\TournBundle\Form\Type\Test\DynamicFormType;

class TestController extends Controller
{
    /* ==========================================
     * bind has been replaced with submit
     */
    public function form1Action(Request $request)
    {
        // Build the data item
        $item = array(
            'zzz'     => null,
            'xxx'     => null,
            'invalid' => false, // If true then need to validate and get errors
        );
        $session = $request->getSession();
        if ($session->has('cerad_tourn_test_form1'))
        {
            $item = array_merge($item,$session->get('cerad_tourn_test_form1'));
        }
        // Build the form
        $formType = $this->get('cerad_tourn.test.form1.formtype');
        $form = $this->createForm($formType,$item);
      
        // Check post
        if ($request->isMethod('POST'))
        {
            // Submit with a response will be depreciated in 3.x
            // $form->submit($request->request->get($form->getName()));
            $form->submit($request);

            if ($form->isValid())
            {
                $item = $form->getData();
                $item['invalid'] = false;
                
                $session->set('cerad_tourn_test_form1',$item);
                
                return $this->redirect($this->generateUrl('cerad_tourn_test_form1_success'));
            }
            else 
            {
                $item = $form->getData();
                $item['invalid'] = true;
                $session->set('cerad_tourn_test_form1',$item);
                return $this->redirect($this->generateUrl('cerad_tourn_test_form1_failure'));
            }
        }
        else 
        {
            if ($item['invalid']) 
            {
                // Need the bind for isValid to work, CSRF will always generate error
                $form->bind($item);
                $form->isValid(); 
                //print_r($form->getErrorsAsString());
            }
        }

        // And display
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradTourn/test/form1.html.twig', $tplData);
        
    }
    public function simpleAction()
    {
        // And display
        $tplData = array();
        return $this->render('@CeradTourn/test/simple.html.twig', $tplData);
        
    }
    public function dynamicAction(Request $request)
    {
        if ($request->isMethod('POST'))
        {
            $all = $request->request->all();
          //$all['cerad_tourn_test_dynamic']['gender'] = 'X';
            $request->request->replace($all);
        }
        $form = $this->createForm(new DynamicFormType());
        $form->handleRequest($request);
        if ($form->isValid())
        {
            echo "Form is valid<br />";
            $data = $form->getData();
            echo sprintf("Form is valid gender: %s<br />",$data['gender']);
        }
       // And display
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradTourn/test/dynamic.html.twig', $tplData);
        
    }
}
?>
