<?php

/**
 * FormatterTest class
 *
 * @package
 * @subpackage
 * @author     Alessandro Nadalin <alessandro.nadalin@gmail.com>
 */

namespace test\Doctrine\OrientDB\Query;

use Doctrine\OrientDB\Query\Formatter;
use test\PHPUnit\TestCase;

class FormatterTest extends TestCase
{
    public function testTokenizingAString()
    {
        $formatter = new Formatter\Query();

        $this->assertEquals(':Clue', $formatter::tokenize('Clue'));
        $this->assertEquals('::Clue', $formatter::tokenize(':Clue'));
    }

    public function testUntokenizingAString()
    {
        $formatter = new Formatter\Query();

        $this->assertEquals('Clue', $formatter::untokenize(':Clue'));
        $this->assertEquals(':Clue', $formatter::untokenize('::Clue'));
    }

    public function testFormattingProjections()
    {
        $projections = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($projections));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingProperty()
    {
        $property = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($property));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingClass()
    {
        $class = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($class));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingPermission()
    {
        $permission = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($permission));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingRid()
    {
        $rids = array('a;', 'b--', 'c"', "12:0", "12", "12:2:2", ":2");
        $formatter = new Formatter\Query\Rid();

        $this->assertEquals('12:0', $formatter::format($rids));
    }

    public function testFormattingClassList()
    {
        $classes = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\ClassList();

        $this->assertEquals('[a, b, c]', $formatter::format($classes));
        $this->assertEquals('[a]', $formatter::format(array('a')));
        $this->assertEquals(null, $formatter::format(array()));
        $this->assertEquals('[a2]', $formatter::format(array('a2')));
    }

    public function testFormattingRole()
    {
        $roles = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($roles));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingType()
    {
        $types = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($types));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingLinked()
    {
        $linked = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($linked));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingInverse()
    {
        $inverse = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($inverse));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingSourceClass()
    {
        $classes = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($classes));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingSourceProperty()
    {
        $properties = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($properties));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingDestinationClass()
    {
        $class = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($class));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingDestinationProperty()
    {
        $property = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($property));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingName()
    {
        $names = array('a;', 'b--', 'c"');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('a, b, c', $formatter::format($names));
        $this->assertEquals('a', $formatter::format(array('a')));
        $this->assertEquals('', $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
    }

    public function testFormattingTarget()
    {
        $target = array(
            'a;', 'b--', 'c"'
        );
        $formatter = new Formatter\Query\Target();

        $this->assertEquals('[a, b, c]', $formatter::format($target));
        $this->assertEquals('[a, 12:0]', $formatter::format(array('a', '12:0')));
        $this->assertEquals(null, $formatter::format(array()));
        $this->assertEquals('a2', $formatter::format(array('a2')));
        $this->assertEquals('a', $formatter::format(array('a;')));
    }

    public function testFormattingWhereConditions()
    {
        $where = array('@class = "1"', '_b-- = ";;2"', 'c = "\"ko\""', ', AND 7 = "8"');
        $formatter = new Formatter\Query\Where();

        $this->assertEquals('@class = "1" _b-- = ";;2" c = "\"ko\"" , AND 7 = "8"', $formatter::format($where));
    }

    public function testFormattingOrderBy()
    {
        $orderBy = array(
            'a ASC', 'b DESC', 'c PRESF"'
        );
        $formatter = new Formatter\Query\OrderBy();

        $this->assertEquals('ORDER BY a ASC, b DESC, c PRESF', $formatter::format($orderBy));
        $this->assertEquals('ORDER BY a, 12:0', $formatter::format(array('a', '12:0')));
        $this->assertEquals(null, $formatter::format(array()));
        $this->assertEquals('ORDER BY a2, @rid', $formatter::format(array('a2', '@rid')));
        $this->assertEquals('ORDER BY a#', $formatter::format(array('a#;')));
    }

    public function testFormattingLimit()
    {
        $limits = array('@d', '0"', 'a', 2);
        $formatter = new Formatter\Query\Limit();

        $this->assertEquals('LIMIT 2', $formatter::format($limits));
        $this->assertEquals(null, $formatter::format(array('a', '12:0')));
        $this->assertEquals(null, $formatter::format(array()));
        $this->assertEquals(null, $formatter::format(array('a2', '@rid')));
        $this->assertEquals(null, $formatter::format(array('a#;')));
    }

    public function testFormattingSkip()
    {
        $limits = array('@d', '0"', 'a', 2);
        $formatter = new Formatter\Query\Skip();

        $this->assertEquals('SKIP 10', $formatter::format(array('10')));
        $this->assertEquals('SKIP 10', $formatter::format(array(10)));
        $this->assertEquals('SKIP 0', $formatter::format(array(0)));
        $this->assertNull($formatter::format(array(-1)));
    }

    public function testFormattingFields()
    {
        $fields = array(12, '0', '"\\', '@class\"', '@@rid', 'prop');
        $formatter = new Formatter\Query\Regular();

        $this->assertEquals('12, 0, @class, @@rid, prop', $formatter::format($fields));
        $this->assertEquals('a, 12:0', $formatter::format(array('a', '12:0')));
        $this->assertEquals(null, $formatter::format(array()));
        $this->assertEquals('a2, @rid', $formatter::format(array('a2;', '@rid\'')));
        $this->assertEquals('a#', $formatter::format(array('a#;')));
    }

    public function testFormattingValues()
    {
        $values = array(
            true,
            false,
            null,
            "12",
            12,
            '0',
            0,
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

        $expected = 'TRUE, FALSE, NULL, "12", 12, "0", 0, "\"", "@class", "@@rid", "prop", [1, 2], "\\\'", "\\\", "<a href=\"http://ciao.com\">ciao</a>", "!@#$%^&*()"';
        $formatter = new Formatter\Query\Values();

        $this->assertEquals($expected, $formatter::format($values));
    }

    public function testFormattingUpdates()
    {
        $fields = array(
            1 => 1,
            'int0' => 0,
            'str0' => '0',
            'false' => false,
            'true' => true,
            'null' => null,
            '@rid' => '12:0',
            '"' => '"',
            '\'' => '\'',
            'carl' => '""',
            '#1' => '#13',
            '44' => '#13',
            'html' => '<a href="http://ciao.com">ciao</a>\\',
        );

        $updates = ' 1 = 1, int0 = 0, str0 = "0", false = FALSE, true = TRUE, null = NULL, @rid = "12:0", carl = "\"\"", #1 = "#13", 44 = "#13", html = "<a href=\"http://ciao.com\">ciao</a>\\\"';
        $formatter = new Formatter\Query\Updates();

        $this->assertEquals($updates, $formatter::format($fields));
    }

    public function testFormattingRidUpdates()
    {
        $fields = array(
            1 => 1,
            '@rid' => '12:0',
            '"' => '"',
            '\'' => '\'',
            'carl' => '""',
            '#1' => '#13',
            '44' => '#13',
            'html' => '<a href="http://ciao.com">ciao</a>\\',
        );

        $updates = '@rid = #12:0';
        $formatter = new Formatter\Query\RidUpdates();

        $this->assertEquals($updates, $formatter::format($fields));
    }
}
