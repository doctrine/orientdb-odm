<?php

namespace test\Doctrine\ODM\OrientDB\Document\Stub\Contact;

use Doctrine\ODM\OrientDB\Mapper\Annotations as ODM;

/**
* @ODM\Document(class="Address,ForeignAddress")
*/
class Address
{
    /**
     * @ODM\Property(name="@rid", type="string")
     */
    protected $rid;
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
     * @ODM\Property(name="negative_byte", type="byte")
     */
    protected $negative_byte;

    /**
     * @ODM\Property(name="positive_byte", type="byte")
     */
    protected $positive_byte;

    /**
     * @ODM\Property(name="invalid_byte", type="byte")
     */
    protected $invalid_byte;

    /**
     * @ODM\Property(type="float")
     */
    protected $floating;

    /**
     * @ODM\Property(type="binary")
     */
    protected $image;

    /**
     * @ODM\Property(type="link")
     */
    protected $link;

    /**
     * @ODM\Property(type="embedded")
     */
    protected $embedded;


    /**
     * @ODM\Property(type="embedded_set", cast="link")
     */
    protected $embeddedset;

    /**
     * @ODM\Property(type="embedded_list", cast="link")
     */
    protected $embeddedlist;

    /**
     * @ODM\Property(type="embedded_list", cast="boolean")
     */
    protected $embeddedbooleans;

    /**
     * @ODM\Property(type="embedded_list", cast="string")
     */
    protected $embeddedstrings;

    /**
     * @ODM\Property(type="embedded_list", cast="integer")
     */
    protected $embeddedintegers;

    /**
     * @ODM\Property(type="embedded_set", cast="boolean")
     */
    protected $embeddedsetbooleans;

    /**
     * @ODM\Property(type="embedded_set", cast="string")
     */
    protected $embeddedsetstrings;

    /**
     * @ODM\Property(type="embedded_set", cast="integer")
     */
    protected $embeddedsetintegers;

    /**
     * @ODM\Property(type="link")
     */
    protected $lazy_link;

    /**
     * @ODM\Property(type="linklist")
     */
    protected $lazy_linklist;

    /**
     * @ODM\Property(type="linkset")
     */
    public $lazy_linkset;

    /**
     * @ODM\Property(type="linkmap")
     */
    public $lazy_linkmap;

    /**
     * @ODM\Property(type="linkset")
     */
    protected $linkset;

    /**
     * @ODM\Property(type="linklist")
     */
    protected $linklist;

    /**
     * @ODM\Property(type="linkmap")
     */
    protected $linkmap;

    /**
     * @ODM\Property(type="embedded_map", cast="link")
     */
    protected $embedded_map;

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

    public function setEmbeddedList($list)
    {
        $this->embeddedlist = $list;
    }

    public function getEmbeddedList()
    {
        return $this->embeddedlist;
    }

    public function setEmbeddedSet($set)
    {
        $this->embeddedset = $set;
    }

    public function getEmbeddedSet()
    {
        return $this->embeddedset;
    }

    public function setEmbeddedIntegers($list)
    {
        $this->embeddedintegers = $list;
    }

    public function getEmbeddedIntegers()
    {
        return $this->embeddedintegers;
    }

    public function setEmbeddedBooleans($list)
    {
        $this->embeddedbooleans = $list;
    }

    public function getEmbeddedBooleans()
    {
        return $this->embeddedbooleans;
    }

    public function setEmbeddedStrings($list)
    {
        $this->embeddedstrings = $list;
    }

    public function getEmbeddedStrings()
    {
        return $this->embeddedstrings;
    }

    public function setEmbeddedSetIntegers($list)
    {
        $this->embeddedsetintegers = $list;
    }

    public function getEmbeddedSetIntegers()
    {
        return $this->embeddedsetintegers;
    }

    public function setEmbeddedSetBooleans($list)
    {
        $this->embeddedsetbooleans = $list;
    }

    public function getEmbeddedSetBooleans()
    {
        return $this->embeddedsetbooleans;
    }

    public function setEmbeddedSetStrings($list)
    {
        $this->embeddedsetstrings = $list;
    }

    public function getEmbeddedSetStrings()
    {
        return $this->embeddedsetstrings;
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

    public function getLink()
    {
        return $this->link;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getEmbedded()
    {
        return $this->embedded;
    }

    public function setEmbedded($embedded)
    {
        $this->embedded = $embedded;
    }

    public function getLinkset()
    {
        return $this->linkset;
    }

    public function setLinkset($linkset)
    {
        $this->linkset = $linkset;
    }

    public function getLinklist()
    {
        return $this->linklist;
    }

    public function setLinklist($linklist)
    {
        $this->linklist = $linklist;
    }

    public function getLinkmap()
    {
        return $this->linkmap;
    }

    public function setLinkmap($linkmap)
    {
        $this->linkmap = $linkmap;
    }

    public function getEmbeddedMap()
    {
        return $this->embedded_map;
    }

    public function setEmbeddedMap($embedded_map)
    {
        $this->embedded_map = $embedded_map;
    }

    public function getLazyLink()
    {
        return $this->lazy_link;
    }

    public function setLazyLink($lazy_link)
    {
        $this->lazy_link = $lazy_link;
    }

    public function getLazyLinkList()
    {
        return $this->lazy_linklist;
    }

    public function setLazyLinkList($lazy_linklist)
    {
        $this->lazy_linklist = $lazy_linklist;
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

    public function getNegativeByte()
    {
        return $this->negative_byte;
    }

    public function setNegativeByte($Byte)
    {
        $this->negative_byte = $Byte;
    }

    public function getPositiveByte()
    {
        return $this->positive_byte;
    }

    public function setPositiveByte($Byte)
    {
        $this->positive_byte = $Byte;
    }

    public function getInvalidByte()
    {
        return $this->invalid_byte;
    }

    public function setInvalidByte($Byte)
    {
        $this->invalid_byte = $Byte;
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

    public function testCustomMethod($k1, $k2)
    {
        return $k1 + $k2;
    }

    public static function testStaticMethod($k1, $k2)
    {
        return $k1 + $k2;
    }
}
