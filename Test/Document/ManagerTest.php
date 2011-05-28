<?php

/**
 * QueryTest
 *
 * @package    Orient
 * @subpackage Test
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 * @version
 */

namespace Orient\Test;

use Orient\Test\PHPUnit\TestCase;
use Orient\Document\Manager;

class ManagerTest extends TestCase
{
    public function setup()
    {
        $this->mapper = new Manager();
        
        $this->jsonRecord = '{
          "@type":    "d", 
          "@rid":     "#13:0", 
          "@version": 0, 
          "@class":   "Address",
          "name":     "Rome",
          "country":  "#14:0"
         }';
         
         $this->jsonCollection = '{ 
             "schema": {
                 "id":   6,
                 "name": "Address"
               },
             "result": [{
                "@type":    "d", 
                "@rid":     "#12:0", 
                "@version":  0, 
                "@class":   "Address",
                "street":   "Piazza Navona, 1",
                "type":     "Residence",
                "city":     "#13:0"
              }]
          }';
         
    }
    
    
    public function testAJsonGetsConvertedToAnObject()
    {   
        $json = "..";
        $object = $this->mapper->hydrate($this->jsonRecord);
        
        $this->assertInstanceOf('Orient\Test\Document\Stub\Contact\Address', $object);
    }
}
