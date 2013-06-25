<?php
namespace Cerad\Bundle\GameV2Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\GameV2Bundle\DependencyInjection\GameExtension;

class CeradGameV2Bundle extends Bundle
{   
    public function getContainerExtension()
    {
        return new GameExtension();
    }
}   
?>
