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
        '@class = "1"', '_b-- = ";;2"', 'c = "\"ko\""', ', AND 7 = "8"'
    );

    $this->assertEquals('@class = "1", _b-- = ";;2", c = "\"ko\"",  AND 7 = "8"', $this->formatter->formatWhere($where));
  }

  public function testFormattingOrderBy()
  {
    $orderBy = array(
        'a ASC', 'b DESC', 'c PRESF"'
    );

    $this->assertEquals('ORDER BY a ASC, b DESC, c PRESF', $this->formatter->formatOrderBy($orderBy));
    $this->assertEquals('ORDER BY a, 12:0', $this->formatter->formatOrderBy(array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->formatOrderBy(array()));
    $this->assertEquals('ORDER BY a2, @rid', $this->formatter->formatOrderBy(array('a2', '@rid')));
    $this->assertEquals('ORDER BY a#', $this->formatter->formatOrderBy(array('a#;')));
  }

  public function testFormattingLimit()
  {
    $limits = array(
        '@d', '0"', 'a', 2
    );

    $this->assertEquals('LIMIT 2', $this->formatter->formatLimit($limits));
    $this->assertEquals(NULL, $this->formatter->formatLimit(array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->formatLimit(array()));
    $this->assertEquals(NULL, $this->formatter->formatLimit(array('a2', '@rid')));
    $this->assertEquals(NULL, $this->formatter->formatLimit(array('a#;')));
  }

  public function testFormattingRange()
  {
    $ranges = array(
        '@d', '0"', '11', '12:1', '12:2', '12:3', '12:2:2', '12::2'
    );

    $this->assertEquals('RANGE 12:1, 12:2', $this->formatter->formatRange($ranges));
    $this->assertEquals('RANGE 12:0', $this->formatter->formatRange(array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->formatRange(array()));
    $this->assertEquals(NULL, $this->formatter->formatRange(array('a2', '@rid')));
    $this->assertEquals(NULL, $this->formatter->formatRange(array('a#;')));
  }

  public function testFormattingFields()
  {
    $fields = array(
        12, '0', '"\\', '@class\"', '@@rid', 'prop'
    );

    $this->assertEquals('12, 0, @class, @@rid, prop', $this->formatter->formatFields($fields));
    $this->assertEquals('a, 12:0', $this->formatter->formatFields(array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->formatFields(array()));
    $this->assertEquals('a2, @rid', $this->formatter->formatFields(array('a2;', '@rid\'')));
    $this->assertEquals('a#', $this->formatter->formatFields(array('a#;')));
  }

  public function testFormattingValues()
  {
    $values = array(
        12,
        '0',
        '"',
        '@class',
        '@@rid',
        'prop',
        array(1, 2),
        '\'',
        "\\",
        "<a href=\"http://ciao.com\">ciao</a>",
        "!@#$%^&*()",
    );
    $expected = '"12", "0", "\"", "@class", "@@rid", "prop", [1, 2], "\\\'", "\\\", "<a href=\"http://ciao.com\">ciao</a>", "!@#$%^&*()"';

    $this->assertEquals($expected, $this->formatter->formatValues($values));
  }

  public function testFormattingUpdates()
  {
    $fields = array(
        1       => 1,
        '@rid'  => '12:0',
        '"'     => '"',
        '\''    => '\'',
        'carl'  => '""',
        '#1'    => '#13',
        '44'    => '#13',
        'html'  => '<a href="http://ciao.com">ciao</a>\\',
    );
    $updates = ' 1 = "1", @rid = "12:0", carl = "\"\"", #1 = "#13", 44 = "#13", html = "<a href=\"http://ciao.com\">ciao</a>\\\"';

    $this->assertEquals($updates, $this->formatter->formatUpdates($fields));
  }

  public function testFormattingRidUpdates()
  {
    $fields = array(
        1       => 1,
        '@rid'  => '12:0',
        '"'     => '"',
        '\''    => '\'',
        'carl'  => '""',
        '#1'    => '#13',
        '44'    => '#13',
        'html'  => '<a href="http://ciao.com">ciao</a>\\',
    );
    $updates = '@rid = 12:0';

    $this->assertEquals($updates, $this->formatter->formatRidUpdates($fields));
  }
}

