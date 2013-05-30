<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints\Email;

use Cerad\Bundle\AccountBundle\Validator\Constraints\EmailUnique;

class EmailUniqueFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      // $builder->addModelTransformer(new VolunteerIDTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label'           => 'User Email (unique)',
            'attr'            => array('size' => 30),
            'constraints'     => array(
                new Email(array('message' => 'Invalid email')), 
                new EmailUnique(),
            )
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_account_email_unique'; }
}

?>
