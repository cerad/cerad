<?php

namespace Cerad\Bundle\AccountBundle\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/* =============================================================
 * Defaults to sha512
 * Then tries legacy md5
 * Also supports master password
 */
class AccountUserEncoder extends MessageDigestPasswordEncoder
{
    public function __construct($master, $algorithm = 'sha512', $encodeHashAsBase64 = true, $iterations = 5000)
    {
        parent::__construct($algorithm,$encodeHashAsBase64,$iterations);
        
        $this->master = $master;
    }
    public function isPasswordValid($encoded, $raw, $salt)
    {
        // Master Password
        if ($raw == $this->master) return true;
        
        // sha12
        if ($this->comparePasswords($encoded, $this->encodePassword($raw, $salt))) return true;

        // Legacy, be nice to force an update
        if ($encoded == md5($raw)) return true;
        
        // Oops
        return false;
        
        die('isPasswordValid ' . $raw . ' ' . $encoded);
    }
}

?>
