<?php
namespace Cerad\Bundle\AccountBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UsernameExistingValidator extends ConstraintValidator
{
    protected $provider;
    
    public function __construct($provider)
    {
        $this->provider = $provider;
    }
    public function validate($username, Constraint $constraint)
    {
        // Takes care of all the can nonsense
        try 
        {
            // Easiest way to see if have one
            $this->provider->loadUserByUsername($username);
            return;
        }
        catch (\Exception $e)
        {
        }
        $this->context->addViolation($constraint->message, array('%string%' => $username));
    }

}

?>
