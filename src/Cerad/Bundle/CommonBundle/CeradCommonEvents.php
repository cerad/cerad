<?php

namespace Cerad\Bundle\CommonBundle;

/* ===============================================================
 * Trying to avoid making bundles overly dependent on each other
 * Instead make them dependent on Common?
 */
final class CeradCommonEvents
{
    // Needless to say, these came from FOSUserBundle
    const REGISTRATION_INITIALIZE = 'fos_user.registration.initialize';
    const REGISTRATION_SUCCESS    = 'fos_user.registration.success';
    const REGISTRATION_COMPLETED  = 'fos_user.registration.completed';
    const REGISTRATION_CONFIRM    = 'fos_user.registration.confirm';
    const REGISTRATION_CONFIRMED  = 'fos_user.registration.confirmed';
    
    // Inteneded to be processed by Janrain?
    const USER_IDENTIFIER_ADD = 'cerad_account.identifier.add';
    
    // Returns any addtional user identifiers?
    const USER_IDENTIFIER_LOAD = 'cerad_common.user_identifier.load';
    
    const PERSON_LOAD = 'cerad_common.person.load'; // Guid or identifier value


}
?>
