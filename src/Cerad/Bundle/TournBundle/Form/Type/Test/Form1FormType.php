<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Test;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints as Assert;

class Form1FormType extends AbstractType
{
    protected $manager = null;
    
    public function getName() { return 'cerad_tourn_test_form1'; }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('zzz', 'text', array(
            'label'       => 'ZZZ', 
            'constraints' => new Assert\Regex(array('message' => 'Must be zzz', 'pattern' => '/^zzz$/'))
        ));
        $builder->add('xxx', 'text', array('label' => 'XXX'));
        
      //$builder->add('save','submit');
    }
}