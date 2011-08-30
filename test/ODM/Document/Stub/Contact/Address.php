<?php

namespace test\ODM\Document\Stub\Contact;

use Congow\Orient\ODM\Mapper\Annotations as ODM;

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
    
    /**
     * @ODM\Property(name="capital", type="double")
     */
    protected $capital;
    
    /**
     * @ODM\Property(name="negative_short", type="short")
     */
    protected $negative_short;
    
    /**
     * @ODM\Property(name="positive_short", type="short")
     */
    protected $positive_short;
    
    /**
     * @ODM\Property(name="invalid_short", type="short")
     */
    protected $invalid_short;
    
    /**
     * @ODM\Property(name="negative_long", type="long")
     */
    protected $negative_long;
    
    /**
     * @ODM\Property(name="positive_long", type="long")
     */
    protected $positive_long;
    
    /**
     * @ODM\Property(name="invalid_long", type="long")
     */
    protected $invalid_long;
    
    /**
     * @ODM\Property(type="float")
     */
    protected $floating;
    
    /**
     * @ODM\Property(type="binary")
     */
    protected $image;
    
    /**
     * @ODM\Property(type="integer")
     */
    protected $number;
    
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

    public function getCapital()
    {
        return $this->capital;
    }

    public function setCapital($capital)
    {
        $this->capital = $capital;
    }

    public function getNegativeShort()
    {
        return $this->negative_short;
    }

    public function setNegativeShort($short)
    {
        $this->negative_short = $short;
    }

    public function getPositiveShort()
    {
        return $this->positive_short;
    }

    public function setPositiveShort($short)
    {
        $this->positive_short = $short;
    }

    public function getInvalidShort()
    {
        return $this->invalid_short;
    }

    public function setInvalidShort($short)
    {
        $this->invalid_short = $short;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getNegativeLong()
    {
        return $this->negative_long;
    }

    public function setNegativeLong($long)
    {
        $this->negative_long = $long;
    }

    public function getPositiveLong()
    {
        return $this->positive_long;
    }

    public function setPositiveLong($long)
    {
        $this->positive_long = $long;
    }

    public function getInvalidLong()
    {
        return $this->invalid_long;
    }

    public function setInvalidLong($long)
    {
        $this->invalid_long = $long;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getFloating()
    {
        return $this->floating;
    }

    public function setFloating($floating)
    {
        $this->floating = $floating;
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
