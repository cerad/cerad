<?php
/* ==================================================
 * Documents some thoughts on MVC
 */
class GameController
{
    /* ================================================
     * Your basic controller pulling in model and form
     */
    public function editAction1($request)
    {
        $model = $this->createModel($request);
        if ($model instanceof Response) return $model;
        
        $form = $this->createForm($request,$model);
        if ($form instanceof Response) return $form;
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $model = $this->persistModel($model);
            
            return $this->redirectResponse();
        }
        
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradGameBundle\Game\Edit\index.html.twig',$tplData);        
        
    }
    /* ================================================
     * Here the model is injected
     * The model was created based on the request by a listener
     * If problems were encountered in building the model 
     * Or 
     * The user does not have permission to edit the mode
     * Then
     * A redirect will prevent this action from being called
     */
    public function editAction2($request,$model)
    {
        $form = $this->createForm($request,$model);
        
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $model = $this->persistModel($model);
            
            return $this->redirect;
        }
        
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradGameBundle\Game\Edit\index.html.twig',$tplData);                
    }
    /* ================================================
     * Inject both model and form
     */
    public function editAction3($request,$model,$form)
    {   
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $model = $this->persistModel($model);
            
            return $this->redirect;
        }
        
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradGameBundle\Game\Edit\index.html.twig',$tplData);        
    }
    /* ================================================
     * Do the isValid in a listener and call two different actions
     */
    public function editAction4IsValid($request,$model)
    {   
        $model = $this->persistModel($model);
            
        return $this->redirect();        
    }
    public function editAction4GetOrNotValid($request,$model,$form)
    {   
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradGameBundle\Game\Edit\index.html.twig',$tplData);        
    }
    /* ====================================
     * Inject the template here
     * Or what the heck, just do the rendering without getting here
     */
    public function editAction5GetOrNotValid($request,$model,$form,$template)
    {   
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render($template,$tplData);        
    }
    /* ====================================
     * So at this point we might not need a controller as such at all
     * Individual listeners can handle it
     * Want to prevent an exploding number of listeners
     * 
     * onRequestGameEdit_CreateModel
     * onRequestGameEdit_CheckSecurity
     * onRequestGameEdit_CreateForm
     * onRequestGameEdit_ValidateForm
     * 
     *     onRequestGameEdit_FormIsValid
     *         onRequestGameEdit_ReCheckSecurity
     *         onRequestGameEdit_PersistModel
     *         onRequestGameEdit_ModelPersisted
     * 
     *     onRequestGameEdit_FormNotValid
     *         onRequestGameEdit_PickTemplate
     *         onRequestGameEdit_RenderTemplate
     *
     * onRequestGameShow_CreateModel
     * onRequestGameShow_CheckSecurity
     * onRequestGameShow_PickTemplate
     * onRequestGameShow_RenderTemplate
     *
     * GameEdit and GameShow might use the same model
     */

    /* ===================================================
     * The template required a read only project object
     * 
     * 1. Create the project in the controller
     * 2. Create the project in the model
     *    Implies model need to know about something it doesn't need
     * 3. Listener created the project and injects into action
     */
    public function editAction9($request,$model,$form)
    {   
        $form->handleRequest($request);
        
        if ($form->isValid())
        {
            $model = $this->persistModel($model);
            
            return $this->redirect;
        }
        /* ==============================
         * The game's project contains read only info needed by the template
         * 
         */
        $project = $model->project;
        
        $tplData = array();
        $tplData['form']    = $form->createView();
        $tplData['project'] = $project;
        return $this->render('@CeradGameBundle\Game\Edit\index.html.twig',$tplData);
    }
}
?>
