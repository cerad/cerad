<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Cerad\Bundle\AccountBundle\Validator\Constraints\UsernameExisting;

class UsernameExistingFormType extends AbstractType
{
    public function getName()   { return 'cerad_account_username_existing'; }
    public function getParent() { return 'text'; }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      // $builder->addModelTransformer(new VolunteerIDTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label'           => 'Zayso User Name or Email',
            'attr'            => array('size' => 30),
            'constraints'     => array(
                new Assert\NotNull(array('message' => 'User Name is required')), 
                new UsernameExisting(),
            )
        ));
    }
}

?>
