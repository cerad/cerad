<?php
namespace Cerad\Bundle\PersonBundle\Validator\Constraints\AYSO;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VolunteerIdValidator extends ConstraintValidator
{
    protected $manager;
    
    public function validate($value, Constraint $constraint)
    {
        // Takes care of all the can nonsense
        if (!$this->manager->findUserByUsernameOrEmail($value)) return;
       
        $this->context->addViolation($constraint->message, array('%string%' => $value));
    }

}

?>
