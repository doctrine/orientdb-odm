<?php

/*
 * This file is part of the Congow\Orient package.
 *
 * (c) Alessandro Nadalin <alessandro.nadalin@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class Record
 *
 * @package    Congow\Orient
 * @subpackage Query
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Congow\Orient\Query\Command\Truncate;

use Congow\Orient\Query\Command;

class Record extends Command
{
    public function __construct($rid)
    {
        parent::__construct();

        $this->setToken('Rid', $rid);
    }

    /**
     * @inheritdoc
     */
    protected function getSchema()
    {
        return "TRUNCATE RECORD :Rid";
    }

    /**
     * @inheritdoc
     */
    protected function getTokenFormatters()
    {
        return array_merge(parent::getTokenFormatters(), array(
          'Rid'     => "Congow\Orient\Formatter\Query\Rid",
        ));
    }
}
