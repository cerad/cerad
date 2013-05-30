<?php
namespace Cerad\Bundle\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class EmailUnique extends Constraint
{
  //public $message = 'The string "%string%" contains an illegal character: it can only contain letters or numbers.';

    public $message = 'Email already in use.';
    
    public function validatedBy()
    {
        return 'cerad_account_email_unique';
    }
}

?>
