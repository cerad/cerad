<?php
namespace Cerad\Bundle\GameBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\GameBundle\DependencyInjection\GameExtension;

class CeradGameBundle extends Bundle
{   
    public function getContainerExtension()
    {
        return new GameExtension();
    }
}   
?>
