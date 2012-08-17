<?php

namespace Congow\Orient\Proxy\test\Integration\Document;

class Address extends \test\Integration\Document\Address
{
      public function getCity() {
        $parent = parent::getCity();

        if (!is_null($parent)) {
            if ($parent instanceOf \Congow\Orient\ODM\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setCity($city) {
        $parent = parent::setCity($city);

        if (!is_null($parent)) {
            if ($parent instanceOf \Congow\Orient\ODM\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }

}