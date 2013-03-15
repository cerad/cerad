<?php
namespace Cerad\Bundle\PersonBundle\DataTransformer\AYSO;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use Zayso\CoreBundle\Entity\Org;

class RegionEntityTransformer implements DataTransformerInterface
{
    protected $manager = null;
    
    public function __construct($manager = null) { $this->manager = $manager; }
            
    public function transform($org)
    {
        if (!$org) return null;

        return (int)substr($org->getId(),5);
    }
    public function reverseTransform($value)
    {
        $region = (int)preg_replace('/\D/','',$value);
        if (!$region) return null;
        
        $orgId = sprintf('AYSOR%04u',$region);
        
        $em  = $this->manager->getEntityManager();
        $org = $em->find('ZaysoCoreBundle:Org',$orgId);
        if ($org) return $org;
        
        throw new TransformationFailedException(sprintf(
            'An issue with number "%s" does not exist!',
            $region
        ));
        
        // Probably want an org manager?
        $org = new Org();
        $org->setId($orgId);
        $em->persist($org);
        return $org;
    }
}
?>
