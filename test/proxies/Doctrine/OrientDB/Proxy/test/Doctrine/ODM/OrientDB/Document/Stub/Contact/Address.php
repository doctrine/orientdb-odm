<?php

namespace Doctrine\OrientDB\Proxy\test\Doctrine\ODM\OrientDB\Document\Stub\Contact;

class Address extends \test\Doctrine\ODM\OrientDB\Document\Stub\Contact\Address
{
      public function setType($type) {
        $parent = parent::setType($type);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getType() {
        $parent = parent::getType();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getAnnotatedButNotInJSON() {
        $parent = parent::getAnnotatedButNotInJSON();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setDate($date) {
        $parent = parent::setDate($date);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getDate() {
        $parent = parent::getDate();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedList($list) {
        $parent = parent::setEmbeddedList($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedList() {
        $parent = parent::getEmbeddedList();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedSet($set) {
        $parent = parent::setEmbeddedSet($set);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedSet() {
        $parent = parent::getEmbeddedSet();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedIntegers($list) {
        $parent = parent::setEmbeddedIntegers($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedIntegers() {
        $parent = parent::getEmbeddedIntegers();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedBooleans($list) {
        $parent = parent::setEmbeddedBooleans($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedBooleans() {
        $parent = parent::getEmbeddedBooleans();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedStrings($list) {
        $parent = parent::setEmbeddedStrings($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedStrings() {
        $parent = parent::getEmbeddedStrings();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedSetIntegers($list) {
        $parent = parent::setEmbeddedSetIntegers($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedSetIntegers() {
        $parent = parent::getEmbeddedSetIntegers();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedSetBooleans($list) {
        $parent = parent::setEmbeddedSetBooleans($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedSetBooleans() {
        $parent = parent::getEmbeddedSetBooleans();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedSetStrings($list) {
        $parent = parent::setEmbeddedSetStrings($list);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedSetStrings() {
        $parent = parent::getEmbeddedSetStrings();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setDateTime($date) {
        $parent = parent::setDateTime($date);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getDateTime() {
        $parent = parent::getDateTime();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getCapital() {
        $parent = parent::getCapital();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setCapital($capital) {
        $parent = parent::setCapital($capital);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getNegativeShort() {
        $parent = parent::getNegativeShort();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setNegativeShort($short) {
        $parent = parent::setNegativeShort($short);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getPositiveShort() {
        $parent = parent::getPositiveShort();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setPositiveShort($short) {
        $parent = parent::setPositiveShort($short);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getLink() {
        $parent = parent::getLink();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setLink($link) {
        $parent = parent::setLink($link);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbedded() {
        $parent = parent::getEmbedded();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbedded($embedded) {
        $parent = parent::setEmbedded($embedded);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getLinkset() {
        $parent = parent::getLinkset();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setLinkset($linkset) {
        $parent = parent::setLinkset($linkset);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getLinklist() {
        $parent = parent::getLinklist();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setLinklist($linklist) {
        $parent = parent::setLinklist($linklist);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getLinkmap() {
        $parent = parent::getLinkmap();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setLinkmap($linkmap) {
        $parent = parent::setLinkmap($linkmap);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getEmbeddedMap() {
        $parent = parent::getEmbeddedMap();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setEmbeddedMap($embedded_map) {
        $parent = parent::setEmbeddedMap($embedded_map);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getLazyLink() {
        $parent = parent::getLazyLink();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setLazyLink($lazy_link) {
        $parent = parent::setLazyLink($lazy_link);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getLazyLinkList() {
        $parent = parent::getLazyLinkList();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setLazyLinkList($lazy_linklist) {
        $parent = parent::setLazyLinkList($lazy_linklist);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getInvalidShort() {
        $parent = parent::getInvalidShort();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setInvalidShort($short) {
        $parent = parent::setInvalidShort($short);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getImage() {
        $parent = parent::getImage();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setImage($image) {
        $parent = parent::setImage($image);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getNegativeLong() {
        $parent = parent::getNegativeLong();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setNegativeLong($long) {
        $parent = parent::setNegativeLong($long);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getPositiveLong() {
        $parent = parent::getPositiveLong();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setPositiveLong($long) {
        $parent = parent::setPositiveLong($long);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getInvalidLong() {
        $parent = parent::getInvalidLong();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setInvalidLong($long) {
        $parent = parent::setInvalidLong($long);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getNegativeByte() {
        $parent = parent::getNegativeByte();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setNegativeByte($Byte) {
        $parent = parent::setNegativeByte($Byte);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getPositiveByte() {
        $parent = parent::getPositiveByte();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setPositiveByte($Byte) {
        $parent = parent::setPositiveByte($Byte);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getInvalidByte() {
        $parent = parent::getInvalidByte();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setInvalidByte($Byte) {
        $parent = parent::setInvalidByte($Byte);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getNumber() {
        $parent = parent::getNumber();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setNumber($number) {
        $parent = parent::setNumber($number);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getFloating() {
        $parent = parent::getFloating();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setFloating($floating) {
        $parent = parent::setFloating($floating);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setStreet($street) {
        $parent = parent::setStreet($street);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getStreet() {
        $parent = parent::getStreet();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getIsTrue() {
        $parent = parent::getIsTrue();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getisFalse() {
        $parent = parent::getisFalse();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setIsTrue($val) {
        $parent = parent::setIsTrue($val);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setisFalse($val) {
        $parent = parent::setisFalse($val);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getExampleProperty() {
        $parent = parent::getExampleProperty();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setExampleProperty($value) {
        $parent = parent::setExampleProperty($value);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function testCustomMethod($k1, $k2) {
        $parent = parent::testCustomMethod($k1, $k2);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }

}