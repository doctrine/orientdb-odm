<?php

namespace Doctrine\OrientDB\Proxy\test\Integration\Document;

class Post extends \test\Integration\Document\Post
{
      public function getRid() {
        $parent = parent::getRid();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setRid($rid) {
        $parent = parent::setRid($rid);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function getComments() {
        $parent = parent::getComments();

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }
    public function setComments($city) {
        $parent = parent::setComments($city);

        if (!is_null($parent)) {
            if ($parent instanceOf \Doctrine\ODM\OrientDB\Proxy\AbstractProxy) {
                return $parent();
            }

            return $parent;
        }
    }

}