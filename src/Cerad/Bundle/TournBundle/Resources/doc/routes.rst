=======================================
/welcome   
    Routes: 
        cerad_tourn_welcome
        cerad_account_welcome
        cerad_person_welcome

   @CeradTourn/MainController/welcomeAction
    cerad_account.authentication_information
    cerad_account.login.formtype
   @CeradTourn/Welcome/index.html.twig

=======================================
/home
    Routes:
        cerad_tourn_home
        cerad_account_home
        cerad_person_home

   @CeradTourn/HomeController/homeAction
        No account       => redirect cerad_tourn_welcome
        No accountPerson => cerad_person_ayso_referee_create

======================================
/person/edit
    Routes:
        cerad_tourn_person_edit
        cerad_person_edit

   @CeradTourn/PersonEditController/editAction
        Edit passed personId or user.PersonId
            No personId => redirect cerad_person_create *** Should not happen
        cerad_person.repository
       $person = $personRepo->find($personId);
            No person => redirect cerad_tourn_welcome

       $person->getFedAYSOV
       $personFed->getCertReferee
       $personFed->getOrgRegion

    Form:
        person: cerad_tourn.person_edit.form_type
        fedId:  cerad_person.ayso_volunteer_id.form_type  readonly
        orgId:  cerad_person.ayso_region_id.form_type
        badge:  cerad_person.ayso_referee_badge.form_type
        vgs:    update

    Template
        @CeradTourn/Person/Edit/index.html.twig

======================================
/person/plan
    Routes: 
        cerad_tourn_person_plan 
        cerad_person_plan

   @CeradTourn/Person/PlanController/planAction
       cerad_tourn.project

      $plan = $userPerson->getPlan($project->getId()
      $plan->setPlanProperties($project->getPlan());

      $formType = $this->get('cerad_tourn.person_plan.formtype');
      $formType->setMetaData($project->getPlan());

      @CeradTourn/Person/plan.html.twig

======================================
/person-person/edit/id
    Routes: 
        cerad_tourn_person_person_edit
        cerad_person_person_edit 

======================================
/account/register 
    Routes: 
        cerad_tourn_account_register 
        cerad_account_register

   @CeradTourn/Account/RegistrationController/registerAction
        cerad_account.create.formtype
        cerad_person.ayso_volunteer_id.form_type
        cerad_person.repository
       $personRepo->findFed
       $person->getFedAYSOV();
        cerad_account.user_manager
        $userManager->createUser
       @CeradAccount/Registration/Register/index.html.twig
        
        FOSUserEvents::REGISTRATION_INITIALIZE User Request
        FOSUserEvents::REGISTRATION_SUCCESS    Form Request
        FOSUserEvents::REGISTRATION_COMPLETED  User Request Response

        Persist:  account person personFed
        Redirect: cerad_person_edit (/person/edit)