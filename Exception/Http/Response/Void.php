<?php

/**
 * An exception raised when an HTTP response is empty.
 *
 * @package    Orient
 * @subpackage Exception
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */
namespace Orient\Exception\Http\Response;

use \Orient\Exception;

class Void extends Exception
{
  const MESSAGE = 'The %s client has been unable to retrieve a response for the resource at %s';

  public function __construct($client, $location)
  {
    $this->message = sprintf(self::MESSAGE, $client, $location);
  }
}

