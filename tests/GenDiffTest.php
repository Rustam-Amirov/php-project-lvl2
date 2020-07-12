<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Gendiff\genDiff;

class GenDiffTest extends TestCase
{
    public function helper($fileName)
    {
        return file_get_contents(__DIR__ . "/fixtures/$fileName");
    }


    /**
     * @dataProvider additionalProvider
     */
    public function testGendiff($expected, $first, $second, $format)
    {
        $this->assertEquals($expected, genDiff($first, $second, $format));
    }


    public function additionalProvider()
    {
        return [
            [$this->helper('expected.json'), "tests/fixtures/before.json", "tests/fixtures/after.json", 'json'],
            [$this->helper('expected2.txt'), "tests/fixtures/before.json", "tests/fixtures/after.json", 'plain'],
            [$this->helper('expected1.txt'), "tests/fixtures/before.json", "tests/fixtures/after.json", 'pretty'],
            [$this->helper('expected1.txt'), "tests/fixtures/before.yaml", "tests/fixtures/after.yaml", 'pretty']
        ];
    }
}
