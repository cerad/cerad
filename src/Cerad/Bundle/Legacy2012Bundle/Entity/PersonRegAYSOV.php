<?php
namespace Zayso\CoreBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PersonRegAYSOV extends PersonReg
{
    const REGTYPE = 'AYSOV';
    
    protected $regType = self::REGTYPE; // 'AYSOV';
    
    public function isAYSOV()   { return true; }
    
    public function getAysoid() { return substr($this->regKey,5); }
    public function getRegId()  { return substr($this->regKey,5); }
    
    /**
     * This is not fine grained enough,  Need some other method to force
     * an id when appropiate
     * 
     *  Assert\NotBlank(message="USSF ID cannot be blank")
     * 
     * This works as expected
     * @Assert\Regex(
     *     pattern="/^(AYSOV)?\d{8}$/",
     *     message="AYSO Vol ID must be 8-digit number")
     * 
     *     groups={"create","edit","add"},
     */
    public function getRegKey() { return $this->regKey; }

    public function getRegion()
    {
        if (!$this->org) return null;
        return substr($this->org->getId(),4); 
    }
    public function setSafeHaven($safeHaven) { return $this->set('safe_haven',$safeHaven); }
    public function getSafeHaven()           { return $this->get('safe_haven'); }
    public function hasSafeHaven()
    {
        $safeHaven = $this->get('safe_haven'); // What about no?
        if ($safeHaven && $safeHaven != 'No') return true;
        return false;
    }

}
?>
