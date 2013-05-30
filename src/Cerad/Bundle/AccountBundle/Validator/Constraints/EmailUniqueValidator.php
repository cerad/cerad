<?php
namespace Cerad\Bundle\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EmailUniqueValidator extends ConstraintValidator
{
    protected $manager;
    
    public function __construct($manager)
    {
        $this->manager = $manager;
    }
    public function validate($value, Constraint $constraint)
    {
        // Takes care of all the can nonsense
        if (!$this->manager->findUserByUsernameOrEmail($value)) return;
       
        $this->context->addViolation($constraint->message, array('%string%' => $value));
    }

}

?>
