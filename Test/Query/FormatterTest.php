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

    $this->assertEquals('a, b, c', $this->formatter->format('Projections', $projections));
    $this->assertEquals('a', $this->formatter->format('Projections', array('a')));
    $this->assertEquals('', $this->formatter->format('Projections', array()));
    $this->assertEquals('a2', $this->formatter->format('Projections', array('a2')));
  }

  public function testFormattingProperty()
  {
    $property = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Property', $property));
    $this->assertEquals('a', $this->formatter->format('Property', array('a')));
    $this->assertEquals('', $this->formatter->format('Property', array()));
    $this->assertEquals('a2', $this->formatter->format('Property', array('a2')));
  }

  public function testFormattingClass()
  {
    $class = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Class', $class));
    $this->assertEquals('a', $this->formatter->format('Class', array('a')));
    $this->assertEquals('', $this->formatter->format('Class', array()));
    $this->assertEquals('a2', $this->formatter->format('Class', array('a2')));
  }

  public function testFormattingPermission()
  {
    $permission = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Permission', $permission));
    $this->assertEquals('a', $this->formatter->format('Permission', array('a')));
    $this->assertEquals('', $this->formatter->format('Permission', array()));
    $this->assertEquals('a2', $this->formatter->format('Permission', array('a2')));
  }

  public function testFormattingRid()
  {
    $rids = array(
        'a;', 'b--', 'c"', "12:0", "12", "12:2:2", ":2"
    );

    $this->assertEquals('12:0', $this->formatter->format('Rid', $rids));
  }


  public function testFormattingClassList()
  {
    $classes = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('[a, b, c]', $this->formatter->format('ClassList', $classes));
    $this->assertEquals('[a]', $this->formatter->format('ClassList', array('a')));
    $this->assertEquals(NULL, $this->formatter->format('ClassList', array()));
    $this->assertEquals('[a2]', $this->formatter->format('ClassList', array('a2')));
  }

  public function testFormattingRole()
  {
    $roles = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Role', $roles));
    $this->assertEquals('a', $this->formatter->format('Role', array('a')));
    $this->assertEquals('', $this->formatter->format('Role', array()));
    $this->assertEquals('a2', $this->formatter->format('Role', array('a2')));
  }

  public function testFormattingType()
  {
    $types = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Type', $types));
    $this->assertEquals('a', $this->formatter->format('Type', array('a')));
    $this->assertEquals('', $this->formatter->format('Type', array()));
    $this->assertEquals('a2', $this->formatter->format('Type', array('a2')));
  }

  public function testFormattingLinked()
  {
    $linked = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Linked', $linked));
    $this->assertEquals('a', $this->formatter->format('Linked', array('a')));
    $this->assertEquals('', $this->formatter->format('Linked', array()));
    $this->assertEquals('a2', $this->formatter->format('Linked', array('a2')));
  }

  public function testFormattingInverse()
  {
    $inverse = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Inverse', $inverse));
    $this->assertEquals('a', $this->formatter->format('Inverse', array('a')));
    $this->assertEquals('', $this->formatter->format('Inverse', array()));
    $this->assertEquals('a2', $this->formatter->format('Inverse', array('a2')));
  }

//  public function testEliminatingInitialAndEndingSpaces()
//  {
//    $this->assertEquals('', $this->formatter->btrim(' '));
//    $this->assertEquals('a', $this->formatter->btrim(' a'));
//    $this->assertEquals('a', $this->formatter->btrim('a '));
//    $this->assertEquals('a', $this->formatter->btrim(' a '));
//  }

  public function testFormattingSourceClass()
  {
    $classes = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('SourceClass', $classes));
    $this->assertEquals('a', $this->formatter->format('SourceClass', array('a')));
    $this->assertEquals('', $this->formatter->format('SourceClass', array()));
    $this->assertEquals('a2', $this->formatter->format('SourceClass', array('a2')));
  }

  public function testFormattingSourceProperty()
  {
    $properties = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('SourceProperty', $properties));
    $this->assertEquals('a', $this->formatter->format('SourceProperty', array('a')));
    $this->assertEquals('', $this->formatter->format('SourceProperty', array()));
    $this->assertEquals('a2', $this->formatter->format('SourceProperty', array('a2')));
  }

  public function testFormattingDestinationClass()
  {
    $class = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('DestinationClass', $class));
    $this->assertEquals('a', $this->formatter->format('DestinationClass', array('a')));
    $this->assertEquals('', $this->formatter->format('DestinationClass', array()));
    $this->assertEquals('a2', $this->formatter->format('DestinationClass', array('a2')));
  }

  public function testFormattingDestinationProperty()
  {
    $property = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('DestinationClass', $property));
    $this->assertEquals('a', $this->formatter->format('DestinationClass', array('a')));
    $this->assertEquals('', $this->formatter->format('DestinationClass', array()));
    $this->assertEquals('a2', $this->formatter->format('DestinationProperty', array('a2')));
  }

  public function testFormattingName()
  {
    $names = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('a, b, c', $this->formatter->format('Name', $names));
    $this->assertEquals('a', $this->formatter->format('Name', array('a')));
    $this->assertEquals('', $this->formatter->format('Name', array()));
    $this->assertEquals('a2', $this->formatter->format('Name', array('a2')));
  }

  public function testFormattingTarget()
  {
    $target = array(
        'a;', 'b--', 'c"'
    );

    $this->assertEquals('[a, b, c]', $this->formatter->format('Target', $target));
    $this->assertEquals('[a, 12:0]', $this->formatter->format('Target', array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->format('Target', array()));
    $this->assertEquals('a2', $this->formatter->format('Target', array('a2')));
    $this->assertEquals('a', $this->formatter->format('Target', array('a;')));
  }

  public function testFormattingWhereConditions()
  {
    $where = array(
        '@class = "1"', '_b-- = ";;2"', 'c = "\"ko\""', ', AND 7 = "8"'
    );

    $this->assertEquals('@class = "1", _b-- = ";;2", c = "\"ko\"",  AND 7 = "8"', $this->formatter->format('Where', $where));
  }

  public function testFormattingOrderBy()
  {
    $orderBy = array(
        'a ASC', 'b DESC', 'c PRESF"'
    );

    $this->assertEquals('ORDER BY a ASC, b DESC, c PRESF', $this->formatter->format('OrderBy', $orderBy));
    $this->assertEquals('ORDER BY a, 12:0', $this->formatter->format('OrderBy', array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->format('OrderBy', array()));
    $this->assertEquals('ORDER BY a2, @rid', $this->formatter->format('OrderBy', array('a2', '@rid')));
    $this->assertEquals('ORDER BY a#', $this->formatter->format('OrderBy', array('a#;')));
  }

  public function testFormattingLimit()
  {
    $limits = array(
        '@d', '0"', 'a', 2
    );

    $this->assertEquals('LIMIT 2', $this->formatter->format('Limit', $limits));
    $this->assertEquals(NULL, $this->formatter->format('Limit', array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->format('Limit', array()));
    $this->assertEquals(NULL, $this->formatter->format('Limit', array('a2', '@rid')));
    $this->assertEquals(NULL, $this->formatter->format('Limit', array('a#;')));
  }

  public function testFormattingRange()
  {
    $ranges = array(
        '@d', '0"', '11', '12:1', '12:2', '12:3', '12:2:2', '12::2'
    );

    $this->assertEquals('RANGE 12:1, 12:2', $this->formatter->format('Range', $ranges));
    $this->assertEquals('RANGE 12:0', $this->formatter->format('Range', array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->format('Range', array()));
    $this->assertEquals(NULL, $this->formatter->format('Range', array('a2', '@rid')));
    $this->assertEquals(NULL, $this->formatter->format('Range', array('a#;')));
  }

  public function testFormattingFields()
  {
    $fields = array(
        12, '0', '"\\', '@class\"', '@@rid', 'prop'
    );

    $this->assertEquals('12, 0, @class, @@rid, prop', $this->formatter->format('Fields', $fields));
    $this->assertEquals('a, 12:0', $this->formatter->format('Fields', array('a', '12:0')));
    $this->assertEquals(NULL, $this->formatter->format('Fields', array()));
    $this->assertEquals('a2, @rid', $this->formatter->format('Fields', array('a2;', '@rid\'')));
    $this->assertEquals('a#', $this->formatter->format('Fields', array('a#;')));
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

    $this->assertEquals($expected, $this->formatter->format('Values', $values));
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

    $this->assertEquals($updates, $this->formatter->format('Updates', $fields));
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

    $this->assertEquals($updates, $this->formatter->format('RidUpdates', $fields));
  }
}

