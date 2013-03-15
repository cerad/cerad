<?php
namespace Zayso\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class PersonRegUSSF extends PersonReg
{
    const REGTYPE = 'USSF';
    
    protected $regType = self::REGTYPE;
    
    public function isUSSF()   { return true; }
    
    public function getRegId() { return substr($this->regKey,4); }
    
    /**
     * This is not fine grained enough,  Need some other method to force
     * an id when appropiate
     * 
     *  Assert\NotBlank(message="USSF ID cannot be blank")
     * 
     * This works as expected
     * @Assert\Regex(
     *     pattern="/^(USSF)?\d{16}$/",
     *     message="USSF ID must be 16-digit number")
     * 
     *     groups={"create","edit","add"},
     */
    public function getRegKey() { return $this->regKey; }
}
?>
