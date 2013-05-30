<?php
namespace Cerad\Bundle\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UsernameUnique extends Constraint
{
  //public $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';

    public $message = 'User name must be unique.';
    
    public function validatedBy()
    {
        return 'cerad_account_username_unique';
    }
}

?>
