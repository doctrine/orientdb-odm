<?php
require_once 'vendor/autoload.php';

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;


define('DB_NAME', 'GratefulDeadConcerts');

class Fixtures
{
    private $client;
    public $dbname;
    public $dbuser;
    public $dbpass;
    public $contentType;

    public function __construct($dbname, $dbuser, $dbpass) {
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->contentType = array('Content-Type' => 'application/json');

        $this->client = new Client('http://127.0.0.1:2480');

    }

    function clean()
    {
        $classes = array('Address', 'Country','City','Profile','Company', 'Post', 'Comment', 'MapPoint');

        //Clean
        foreach ($classes as $class) {
            $query = "DROP CLASS " . $class;
            $this->client->post('/command/' . $this->dbname . '/sql/' .$query,array('Content-Type'=>'application/json'))->setAuth($this->dbuser,$this->dbpass)->send();
        }

        return $this;
    }

    function create_classes()
    {
        $classes = array('Address', 'Country','City','Profile','Company', 'Post','Comment', 'MapPoint');

        foreach ($classes as $class) {
            $query = "CREATE CLASS " . $class;
            $this->client->post('/command/' . $this->dbname . '/sql/' .$query, $this->contentType)->setAuth($this->dbuser,$this->dbpass)->send();
        }

        //create Profile properties
        $this->client->post('/property/'. $this->dbname . '/Profile/name', $this->contentType)->setAuth($this->dbuser,$this->dbpass)->send();
        $this->client->post('/property/'. $this->dbname . '/Profile', $this->contentType, '{"followers": {"propertyType": "LINKMAP","linkedClass": "Profile"}}')->setAuth($this->dbuser,$this->dbpass)->send();

        //create Post properties
        $this->client->post('/property/'. $this->dbname . '/Post', $this->contentType, '{"comments": {"propertyType": "LINKLIST","linkedClass": "Comment"}}')->setAuth($this->dbuser,$this->dbpass)->send();

        //create Address properties
        $this->client->post('/property/'. $this->dbname . '/Address/city', $this->contentType)->setAuth($this->dbuser,$this->dbpass)->send();

        //create MapPoint properties
        $this->client->post('/property/'. $this->dbname . '/MapPoint', $this->contentType, '{"x": {"propertyType": "FLOAT"}, "y":{"propertyType": "FLOAT"} }')->setAuth($this->dbuser,$this->dbpass)->send();

        return $this;

    }

    function init()
    {

        //Insert  City
        $this->client->post('/document/'. $this->dbname , $this->contentType, '{"@class": "City", "name": "Rome" }')->setAuth($this->dbuser,$this->dbpass)->send();

        //Insert  Address
        for ($i = 0 ; $i <= 40 ; $i++) {
            $this->client->post('/document/'. $this->dbname , $this->contentType, sprintf('{"@class": "Address", "street" : "New street %d, Italy", "city":"#18:0"}',$i))->setAuth($this->dbuser,$this->dbpass)->send();
        }

        //Insert countries
        $countries = array('France', 'Italy', 'Spain', 'England', 'Ireland', 'Poland', 'Bulgaria', 'Portogallo', 'Belgium', 'Suisse');
        foreach ($countries as $country) {
            $this->client->post('/document/'. $this->dbname , $this->contentType, '{"@class": "Country", "name": "' . $country . '" }')->setAuth($this->dbuser,$this->dbpass)->send();
        }

        //Insert Profile
        $profiles = array('David','Alex','Luke','Marko','Rexter','Gremlin', 'Thinkerpop', 'Frames');
        foreach ($profiles as $profile) {
            $this->client->post('/document/'. $this->dbname , $this->contentType, '{"@class": "Profile", "name": "' . $profile . '" }')->setAuth($this->dbuser,$this->dbpass)->send();
        }

        //Insert Comment
        $templateComment = '{"@class": "Comment", "body": "comment number %d" }';
        for ($i = 0 ; $i <= 5 ; $i++) {
            $this->client->post('/document/'. $this->dbname , $this->contentType, sprintf($templateComment,$i,$i))->setAuth($this->dbuser,$this->dbpass)->send();
        }

        //Insert Post
        $templatePost = '{"@class": "Post", "id":"%d","title": "Title %d", "body": "Body %d", "comments":["#22:3"] }';
        for ($i = 0 ; $i <= 5 ; $i++) {
            $this->client->post('/document/'. $this->dbname , $this->contentType, sprintf($templatePost,$i,$i,$i))->setAuth($this->dbuser,$this->dbpass)->send();
        }

        //Insert MapPoint
        $this->client->post('/document/'. $this->dbname , $this->contentType, '{"@class": "MapPoint", "x": "42.573968", "y": "13.203125" }')->setAuth($this->dbuser,$this->dbpass)->send();

        return $this;
    }

}

$fixtures = new Fixtures('GratefulDeadConcerts','admin','admin');
$fixtures
    ->clean()
    ->create_classes()
    ->init()
    ;

