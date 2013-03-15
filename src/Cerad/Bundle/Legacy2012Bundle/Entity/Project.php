<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

class Project extends BaseEntity
{
    protected $id;

    protected $description = null;

    protected $status = null;

    protected $parent = null;
    protected $projectGroup = null;

    // Getters/setters
    public function setProjectGroup($group)      { $this->onObjectPropertySet('projectGroup', $group); }
    public function getProjectGroup()            { return $this->projectGroup; }

    public function setParent($project)          { $this->onObjectPropertySet('parent', $project); }
    public function getParent()                  { return $this->parent; }

    public function setId($id)                   { $this->onScalerPropertySet('id', $id); }
    public function getId()                      { return $this->id; }

    public function setDescription($description) { $this->onScalerPropertySet('description', $description); }
    public function getDescription()             { return $this->description; }

    public function setStatus($status)           { $this->onScalerPropertySet('status', $status); }
    public function getStatus()                  { return $this->status; }

}