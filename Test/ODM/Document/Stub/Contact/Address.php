<?php

namespace Orient\Test\ODM\Document\Stub\Contact;

use Orient\ODM\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Address,ForeignAddress")
*/
class Address
{
    /**
     * @ODM\Property(name="nojson", type="nojson")
     */
    protected $annotatedNotInJSON;
    /**
     * @ODM\Property(name="date", type="date")
     */
    protected $date;
    /**
     * @ODM\Property(name="datetime", type="datetime")
     */
    protected $date_time;
    /**
     * @ODM\Property(name="type", type="string")
     */
    protected $type;
    /**
     * @ODM\Property(name="is_true", type="boolean")
     */
    protected $is__true;
    /**
     * @ODM\Property(name="is_false", type="boolean")
     */
    protected $is__false;
    
    /**
     * @ODM\Property(name="sample")
     */
    protected $example_property;
    
    protected $street;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getAnnotatedButNotInJSON()
    {
        return $this->annotatedNotInJSON;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDateTime($date)
    {
        $this->date_time = $date;
    }

    public function getDateTime()
    {
        return $this->date_time;
    }

    public function setStreet($street)
    {
        $this->street = $street;
    }

    public function getStreet()
    {
        return $this->street;
    }

    public function getIsTrue()
    {
        return $this->is__true;
    }

    public function getisFalse()
    {
        return $this->is__false;
    }

    public function setIsTrue($val)
    {
        $this->is__true = $val;
    }

    public function setisFalse($val)
    {
        $this->is__false = $val;
    }
    
    public function getExampleProperty()
    {
        return $this->example_property;
    }
    
    public function setExampleProperty($value)
    {
        $this->example_property = $value;
    }
}
