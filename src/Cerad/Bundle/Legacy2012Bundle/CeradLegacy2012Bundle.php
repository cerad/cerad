<?php
namespace Cerad\Bundle\Legacy2012Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\Legacy2012Bundle\DependencyInjection\Legacy2012Extension;

class CeradLegacy2012Bundle extends Bundle
{  
    public function getContainerExtension()
    {
        if ($this->extension) return $this->extension;
        
        $this->extension = new Legacy2012Extension();
        
        return $this->extension;
    }
}   
?>
