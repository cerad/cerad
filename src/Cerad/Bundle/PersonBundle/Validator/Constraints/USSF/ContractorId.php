<?php
namespace Cerad\Bundle\PersonBundle\Validator\Constraints\USSF;

use Symfony\Component\Validator\Constraint;

class ContractorId extends Constraint
{
    public $message = 'Must be 16-digits';
    public $pattern = '/^(USSFC)?\d{16}$/';
    public $match   = true;
    
    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\RegexValidator';
    }
}

?>
