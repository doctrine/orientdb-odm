<?php

use Doctrine\OrientDB\Binding\HttpBinding;
use Doctrine\OrientDB\Binding\BindingParameters;

require __DIR__.'/../autoload.php';

$parameters = BindingParameters::create('http://admin:admin@127.0.0.1:2480/friends');
$binding = new HttpBinding($parameters);

$response = $binding->query('select from friends where any() traverse(0,1) ( @rid = #5:3 ) and @rid <> #5:3');
$friends = $response->getResult();

foreach ($friends as $friend) {
    echo $friend->name, "\n";
}
