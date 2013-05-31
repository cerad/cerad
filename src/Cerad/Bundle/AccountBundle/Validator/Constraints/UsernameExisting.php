<?php
namespace Cerad\Bundle\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UsernameExisting extends Constraint
{
  //public $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';

    public $message = 'User name does not exist.';
    
    public function validatedBy()
    {
        return 'cerad_account_username_existing';
    }
}

?>
