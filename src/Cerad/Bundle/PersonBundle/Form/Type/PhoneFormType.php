<?php
namespace Cerad\Bundle\PersonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Cerad\Bundle\PersonBundle\DataTransformer\PhoneTransformer;

class PhoneFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Note the use of View and not Model transformer
        $builder->addViewTransformer(new PhoneTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Cell Phone',
            'attr'  => array('size' => 20),
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_person_phone'; }
}

?>
