<?php

namespace Cerad\Bundle\GameBundle\Entity;

/* ==============================================
 * Try seeing how this works out for reporting
 */
class GameReport extends BaseEntity
{
    protected $id;
       
    protected $game;
    
    protected $text;
    
    protected $status = 'Pending';
    
    public function getId()      { return $this->id;      }
    public function getText()    { return $this->text;    }
    public function getGame()    { return $this->game;    }
    public function getStatus()  { return $this->status;  }
    
    public function setText    ($value) { $this->onPropertySet('text',    $value); }
    public function setGame    ($value) { $this->onPropertySet('game',    $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',  $value); }    
}
?>
