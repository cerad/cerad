29 Aug 2013

Trying to keep dependencies straight.

App
    AccountBundle
        FOSUserBundle
    JanrainBundle
        FOSUserBundle

The user bundle is just a library at this point.  
Not actually using it as a bundle.
No entry in AppKernel

* FOSUserBundle/Model/User
* FOSUserBundle/Resources/config/doctrine/model/User.orm.xml (just copied to AccountBundle)
* FOSUserBundle/Doctrine/UserManager
* FOSUserBundle/Security/UserProvider
* FOSUserBundle/FOSUserEvents
  - REGISTRATION_INITIALIZE Dispatched in RegistrationController, Caught by JanrainListener
  - REGISTRATION_SUCCESS    Dispatched in RegistrationController, Caught by JanrainListener
* FOSUserBundle/Events
  - UserEvent
  - FormEvent

JanrainBundle

JanrainListener
    Needs CeradAccount:UserManager
        createIdentifier
        addIdentifierToUser
   Listens
       REGISTRATION_INITIALIZE
       REGISTRATION_SUCCESS

JanrainAuthenticationListener - check_path - cerad_janrain_check
    attemptAuthentication
        curls for auth info
        session.cerad_janrain_token
        session.cerad_janrain_profile
        authenticationProvider->authenticate
            if successful return logged in user token
        catch UsernameNotFoundException
            no security token - register_path cerad_account_register
            else              - add_path      cerad_account_identifier_add

CeradAccountBundle is dependent on FOSUserBundle

CeradAccountBundle is not dependent on CeradJanrainBundle

CeradJanrainBundle is dependent on FOSUserBundle for registration events

CeradJanrainBundle is dependent on CeradAccountBundle for userManager identifiers

Want JanrainEventListener to process USER_IDENTIFIER_ADD

CeradCommonEvents::USER_IDENTIFIER_ADD
CeradCommonEvents::USER_LOADED

CeradAccount::UserManagerInterface

CeradAccountEvents::USER_IDENTIFIER_ADD

CeradAccountEvents::USER_PERSON_LINK

CeradAccountEvents::USER_PERSON_LOAD   passes guid or identifier, returns a person if found
CeradAccountEvents::USER_PERSON_SEARCH

CeradAccountEvents::USER_AUTHENTICATED

CeradAccountEvents::REGISTRATION_INITIALIZE
CeradAccountEvents::REGISTRATION_SUCCESS
CeradAccountEvents::REGISTRATION_COMPLETED

CeradAccountEvent::UserEvent
CeradAccountEvent::FormEvent
CeradAccountEvent::FilterUserResponseEvent




