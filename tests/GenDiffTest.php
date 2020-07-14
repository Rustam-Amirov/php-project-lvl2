<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Gendiff\genDiff;

class GenDiffTest extends TestCase
{
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
            [getDatafile('expected.json'), "tests/fixtures/before.json", "tests/fixtures/after.json", 'json'],
            [getDatafile('expected2.txt'), "tests/fixtures/before.json", "tests/fixtures/after.json", 'plain'],
            [getDatafile('expected1.txt'), "tests/fixtures/before.json", "tests/fixtures/after.json", 'pretty'],
            [getDatafile('expected1.txt'), "tests/fixtures/before.yaml", "tests/fixtures/after.yaml", 'pretty']
        ];
    }
}

function getDatafile($fileName)
{
    return file_get_contents("tests/fixtures/$fileName");
}
