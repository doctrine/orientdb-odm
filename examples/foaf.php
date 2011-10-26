<?php

namespace Congow\Orient;

require 'test/PHPUnit/bootstrap.php';

$client = new Http\Client\Curl();
$binding = new Foundation\Binding($client, '127.0.0.1', 2480, 'admin', 'admin', 'friends');

$httpResponse = $binding->query('select from friends where any() traverse(0,1) ( @rid = #5:3 ) and @rid <> #5:3');

$friends = json_decode($httpResponse->getBody())->result;

foreach ($friends as $friend)
{
    echo $friend->name . "\n";
}
