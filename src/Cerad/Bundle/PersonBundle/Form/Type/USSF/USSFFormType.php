<?php
namespace Zayso\CoreBundle\Component\FormType\Admin\Person;

use Zayso\CoreBundle\Component\DataTransformer\USSFIdTransformer;
use Zayso\CoreBundle\Component\DataTransformer\RegionTransformer;

use Zayso\CoreBundle\Component\FormValidator\RegionValidator;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormValidatorInterface;

class USSFEditFormType extends AbstractType
{
    protected $manager = null;
    
    public function __construct($manager, $required = false)
    {
        $this->manager = $manager;
        $this->required = $required;
    }
    public function getName() { return 'ussfEdit'; }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('regKey','text', array('label' => 'USSF ID (16-digits)','attr' => array('size' => 20),'required' => $this->required));
        $builder->get('regKey')->appendClientTransformer(new USSFIdTransformer());
 
        $builder->add('refBadge', 'choice', array(
            'label'         => 'USSF Referee Badge',
            'required'      => true,
            'choices'       => $this->refBadgePickList,
        ));
        $builder->add('refDate',   'text', array('label' => 'USSF Referee Date',  'attr' => array('size' => 8),'required' => false,));

        $builder->add('memYear', 'choice', array(
            'label'         => 'Recert Year',
            'required'      => true,
            'choices'       => $this->memYearPickList,
        ));

        // Don't worry about organization just yet
        // $builder->add('orgKey','text', array('label' => 'AYSO Region Number', 'attr' => array('size' => 4)));
        // $builder->get('orgKey')->appendClientTransformer(new RegionTransformer());
        // $builder->addValidator(new RegionValidator($this->manager->getEntityManager(),'orgKey'));
 
    }
    protected $refBadgePickList = array
    (
        'None'    => 'None',
        'Grade 9' => 'Grade 9',
        'Grade 8' => 'Grade 8',
        'Grade 7' => 'Grade 7',
        'Grade 6' => 'Grade 6',
        'Grade 5' => 'Grade 5',
        'Grade 4' => 'Grade 4',
   );
    protected $memYearPickList = array
    (
        'None' => 'None',
        '2012' => 'CY2012',
        '2011' => 'CY2011',
        '2010' => 'CY2010',
    );
}
