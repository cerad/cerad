<?php
namespace Cerad\Bundle\GameBundle\Entity;

/* =============================================================
 * For now, this entity is never persisted
 * Individual game teams will store the report data as an array
 * 
 * Also used for pool teams for summary information
 */
class TeamReport
{
    protected $data = array();
    
    protected function setReportProp($name,$value)
    {
        $this->data[$name] = $value;
    }
    protected function addReportProp($name,$value)
    {
        $value += $this->getReportProp($name);
        $this->setReportProp($name,$value); 
    }
    protected function getReportProp($name)
    {
        if (isset($this->data[$name])) return $this->data[$name];
        return null;
    }
    
    public function setPointsEarned($value) { $this->setReportProp('pointsEarned',$value); }
    public function addPointsEarned($value) { $this->addReportProp('pointsEarned',$value); }
    public function getPointsEarned(){ return $this->getReportProp('pointsEarned'); }
    
    public function setPointsMinus($value) { $this->setReportProp('pointsMinus',$value); }
    public function addPointsMinus($value) { $this->addReportProp('pointsMinus',$value); }
    public function getPointsMinus(){ return $this->getReportProp('pointsMinus'); }
    
    public function setGoalsScored($value) { $this->setReportProp('goalsScored',$value); }
    public function addGoalsScored($value) { $this->addReportProp('goalsScored',$value); }
    public function getGoalsScored(){ return $this->getReportProp('goalsScored'); }
    
    public function setGoalsAllowed($value) { $this->setReportProp('goalsAllowed',$value); }
    public function addGoalsAllowed($value) { $this->addReportProp('goalsAllowed',$value); }
    public function getGoalsAllowed(){ return $this->getReportProp('goalsAllowed'); }
    
    public function setCautions($value) { $this->setReportProp('cautions',$value); }
    public function addCautions($value) { $this->addReportProp('cautions',$value); }
    public function getCautions() {return $this->getReportProp('cautions'); }
    
    public function setSendoffs($value) { $this->setReportProp('sendoffs',$value); }
    public function addSendoffs($value) { $this->addReportProp('sendoffs',$value); }
    public function getSendoffs(){ return $this->getReportProp('sendoffs'); }
    
    public function setCoachTossed($value) { $this->setReportProp('coachTossed',$value); }
    public function addCoachTossed($value) { $this->addReportProp('coachTossed',$value); }
    public function getCoachTossed(){ return $this->getReportProp('coachTossed'); }
    
    public function setSpecTossed($value) { $this->setReportProp('specTossed',$value); }
    public function addSpecTossed($value) { $this->addReportProp('specTossed',$value); }
    public function getSpecTossed(){ return $this->getReportProp('specTossed'); }
    
    public function setFudgeFactor($value) { $this->setReportProp('fudgeFactor',$value); }
    public function addFudgeFactor($value) { $this->addReportProp('fudgeFactor',$value); }
    public function getFudgeFactor(){ return $this->getReportProp('fudgeFactor'); }
    
    public function setSportsmanship($value) { $this->setReportProp('sportsmanship',$value); }
    public function addSportsmanship($value) { $this->addReportProp('sportsmanship',$value); }
    public function getSportsmanship(){ return $this->getReportProp('sportsmanship'); }
    
    public function setGamesPlayed($value) { $this->setReportProp('gamesPlayed',$value); }
    public function addGamesPlayed($value) { $this->addReportProp('gamesPlayed',$value); }
    public function getGamesPlayed(){ return $this->getReportProp('gamesPlayed'); }
    
    public function setGamesWon($value) { $this->setReportProp('gamesWon',$value); }
    public function addGamesWon($value) { $this->addReportProp('gamesWon',$value); }
    public function getGamesWon(){ return $this->getReportProp('gamesWon'); }

    public function setGamesTotal($value) { $this->setReportProp('gamesTotal',$value); }
    public function addGamesTotal($value) { $this->addReportProp('gamesTotal',$value); }
    public function getGamesTotal(){ return $this->getReportProp('gamesTotal'); }

    public function setWinPercent($value) { $this->setReportProp('winPercent',$value); }
    public function addWinPercent($value) { $this->addReportProp('winPercent',$value); }
    public function getWinPercent(){ return $this->getReportProp('winPercent'); }

    public function clrData()      { $this->data = array(); }
    public function setData($data) { $this->data = $data; }
    public function getData()      { return $this->data; }
}
?>
