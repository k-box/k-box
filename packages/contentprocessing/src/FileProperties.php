<?php

namespace KBox\Documents;

/**
 *
 */
class FileProperties
{
    private $title;

    private $creator;

    private $description;

    private $subject;

    private $createdAt;

    private $modifiedAt;

    private $lastModifiedBy;

    private $keywords;

    private $category;

    private $company;

    public function title()
    {
        return $this->title;
    }

    public function creator()
    {
        return $this->creator;
    }

    public function description()
    {
        return $this->description;
    }

    public function subject()
    {
        return $this->subject;
    }

    public function createdAt()
    {
        return $this->createdAt;
    }

    public function modifiedAt()
    {
        return $this->modifiedAt;
    }

    public function lastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    public function keywords()
    {
        return $this->keywords;
    }

    public function category()
    {
        return $this->category;
    }

    public function company()
    {
        return $this->company;
    }

    public function setTitle($value)
    {
        $this->title = $value;
        return $this;
    }

    public function setCreator($value)
    {
        $this->creator = $value;
        return $this;
    }

    public function setDescription($value)
    {
        $this->description = $value;
        return $this;
    }

    public function setSubject($value)
    {
        $this->subject = $value;
        return $this;
    }

    public function setCreatedAt($value)
    {
        $this->createdAt = $value;
        return $this;
    }

    public function setModifiedAt($value)
    {
        $this->modifiedAt = $value;
        return $this;
    }

    public function setLastModifiedBy($value)
    {
        $this->lastModifiedBy = $value;
        return $this;
    }

    public function setKeywords($value)
    {
        $this->keywords = $value;
        return $this;
    }

    public function setCategory($value)
    {
        $this->category = $value;
        return $this;
    }

    public function setCompany($value)
    {
        $this->company = $value;
        return $this;
    }
}
