<?php
namespace Cerad\Bundle\AccountBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\AccountBundle\DependencyInjection\AccountExtension;

class CeradAccountBundle extends Bundle
{  
    // Mostly for the templates I think
    public function getParent() { return 'FOSUserBundle'; }

    public function getContainerExtension()
    {
        return new AccountExtension();
    }
}   
?>
