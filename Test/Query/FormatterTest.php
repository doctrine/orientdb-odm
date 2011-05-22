<?php

/**
 * FormatterTest class
 *
 * @package    
 * @subpackage 
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace Orient\Test\Query;

use Orient\Query\Formatter;
use Orient\Test\PHPUnit\TestCase;

class FormatterTest extends TestCase
{
  public function setUp()
  {
    $this->formatter = new Formatter();
  }

  public function testTokenizingAString()
  {
    $this->assertEquals(':Clue', $this->formatter->tokenize('Clue'));
    $this->assertEquals('::Clue', $this->formatter->tokenize(':Clue'));
  }

  public function testUntokenizingAString()
  {
    $this->assertEquals('Clue', $this->formatter->untokenize(':Clue'));
    $this->assertEquals(':Clue', $this->formatter->untokenize('::Clue'));
  }

  public function testFormattingProjections()
  {
    $projections = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatProjections($projections));
    $this->assertEquals('a', $this->formatter->formatProjections(array('a')));
    $this->assertEquals('', $this->formatter->formatProjections(array()));
    $this->assertEquals('a2', $this->formatter->formatProjections(array('a2')));
  }

  public function testFormattingProperty()
  {
    $property = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatProperty($property));
    $this->assertEquals('a', $this->formatter->formatProperty(array('a')));
    $this->assertEquals('', $this->formatter->formatProperty(array()));
    $this->assertEquals('a2', $this->formatter->formatProperty(array('a2')));
  }

  public function testFormattingClass()
  {
    $class = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatClass($class));
    $this->assertEquals('a', $this->formatter->formatClass(array('a')));
    $this->assertEquals('', $this->formatter->formatClass(array()));
    $this->assertEquals('a2', $this->formatter->formatClass(array('a2')));
  }

  public function testFormattingPermission()
  {
    $permission = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatPermission($permission));
    $this->assertEquals('a', $this->formatter->formatPermission(array('a')));
    $this->assertEquals('', $this->formatter->formatPermission(array()));
    $this->assertEquals('a2', $this->formatter->formatPermission(array('a2')));
  }

  public function testFormattingRid()
  {
    $rids = array(
        'a;', 'b--', 'c"', "12:0", "12", "12:2:2", ":2"
    );

    $this->assertEquals('12:0', $this->formatter->formatRid($rids));
  }


  public function testFormattingClassList()
  {
    $classes = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('[a, b, c]', $this->formatter->formatClassList($classes));
    $this->assertEquals('[a]', $this->formatter->formatClassList(array('a')));
    $this->assertEquals(NULL, $this->formatter->formatClassList(array()));
    $this->assertEquals('[a2]', $this->formatter->formatClassList(array('a2')));
  }

  public function testFormattingRole()
  {
    $roles = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatRole($roles));
    $this->assertEquals('a', $this->formatter->formatRole(array('a')));
    $this->assertEquals('', $this->formatter->formatRole(array()));
    $this->assertEquals('a2', $this->formatter->formatRole(array('a2')));
  }

  public function testFormattingType()
  {
    $types = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatType($types));
    $this->assertEquals('a', $this->formatter->formatType(array('a')));
    $this->assertEquals('', $this->formatter->formatType(array()));
    $this->assertEquals('a2', $this->formatter->formatType(array('a2')));
  }

  public function testFormattingLinked()
  {
    $linked = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatLinked($linked));
    $this->assertEquals('a', $this->formatter->formatLinked(array('a')));
    $this->assertEquals('', $this->formatter->formatLinked(array()));
    $this->assertEquals('a2', $this->formatter->formatLinked(array('a2')));
  }

  public function testFormattingInverse()
  {
    $inverse = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatInverse($inverse));
    $this->assertEquals('a', $this->formatter->formatInverse(array('a')));
    $this->assertEquals('', $this->formatter->formatInverse(array()));
    $this->assertEquals('a2', $this->formatter->formatInverse(array('a2')));
  }

  public function testEliminatingInitialAndEndingSpaces()
  {
    $this->assertEquals('', $this->formatter->btrim(' '));
    $this->assertEquals('a', $this->formatter->btrim(' a'));
    $this->assertEquals('a', $this->formatter->btrim('a '));
    $this->assertEquals('a', $this->formatter->btrim(' a '));
  }

  public function testFormattingSourceClass()
  {
    $classes = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatSourceClass($classes));
    $this->assertEquals('a', $this->formatter->formatSourceClass(array('a')));
    $this->assertEquals('', $this->formatter->formatSourceClass(array()));
    $this->assertEquals('a2', $this->formatter->formatSourceClass(array('a2')));
  }

  public function testFormattingSourceProperty()
  {
    $properties = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatSourceProperty($properties));
    $this->assertEquals('a', $this->formatter->formatSourceProperty(array('a')));
    $this->assertEquals('', $this->formatter->formatSourceProperty(array()));
    $this->assertEquals('a2', $this->formatter->formatSourceProperty(array('a2')));
  }

  public function testFormattingDestinationClass()
  {
    $class = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatDestinationClass($class));
    $this->assertEquals('a', $this->formatter->formatDestinationClass(array('a')));
    $this->assertEquals('', $this->formatter->formatDestinationClass(array()));
    $this->assertEquals('a2', $this->formatter->formatDestinationClass(array('a2')));
  }

  public function testFormattingDestinationProperty()
  {
    $property = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatDestinationProperty($property));
    $this->assertEquals('a', $this->formatter->formatDestinationProperty(array('a')));
    $this->assertEquals('', $this->formatter->formatDestinationProperty(array()));
    $this->assertEquals('a2', $this->formatter->formatDestinationProperty(array('a2')));
  }

  public function testFormattingName()
  {
    $names = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->formatName($names));
    $this->assertEquals('a', $this->formatter->formatName(array('a')));
    $this->assertEquals('', $this->formatter->formatName(array()));
    $this->assertEquals('a2', $this->formatter->formatName(array('a2')));
  }

  public function testFormattingTarget()
  {
    $target = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('[a, b, c]', $this->formatter->formatTarget($target));
    $this->assertEquals('[a, 12:0]', $this->formatter->formatTarget(array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->formatTarget(array()));
    $this->assertEquals('a2', $this->formatter->formatTarget(array('a2')));
    $this->assertEquals('a', $this->formatter->formatTarget(array('a;')));
  }

  public function testFormattingWhereConditions()
  {
    $where = array(
        'a = "1"', 'b-- = ";;2"', 'c = "\"ko\""', ', AND 7 = "8"'
    );

    $this->assertEquals('a = "1", b-- = ";;2", c = "\"ko\"",  AND 7 = "8"', $this->formatter->formatWhere($where));
  }
}

