<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Gendiff\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionalProviderJSON
     */
    public function testGendiffJSON($first, $second, $format)
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected.json");
        $this->assertEquals($expected, genDiff($first, $second, $format));
    }

    public function additionalProviderJSON()
    {
        return [["tests/fixtures/before1.json", "tests/fixtures/after1.json", 'json']];
    }


    /**
     * @dataProvider additionalProviderPlain
     */
    public function testGendiffPlain($first, $second, $format)
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected2.txt");
        $this->assertEquals($expected, genDiff($first, $second, $format));
    }

    public function additionalProviderPlain()
    {
        return [["tests/fixtures/before1.json", "tests/fixtures/after1.json", 'plain']];
    }


    /**
     * @dataProvider additionalProviderPretty
     */
    public function testGendiffPretty($first, $second, $format)
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected1.txt");
        $this->assertEquals($expected, genDiff($first, $second, $format));
    }

    public function additionalProviderPretty()
    {
        return [["tests/fixtures/before1.json", "tests/fixtures/after1.json", 'pretty']];
    }

    /**
     * @dataProvider additionalProviderYaml
     */
    public function testGendiffYaml($first, $second, $format)
    {
        $expected = file_get_contents(__DIR__ . "/fixtures/expected3.txt");
        $this->assertEquals($expected, genDiff($first, $second, $format));
    }

    public function additionalProviderYaml()
    {
        return [["tests/fixtures/before.yaml", "tests/fixtures/after.yaml", 'pretty']];
    }
}
