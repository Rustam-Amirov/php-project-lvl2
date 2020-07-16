<?php

namespace Differ\tests\GendiffTest;

use PHPUnit\Framework\TestCase;

use function Differ\Gendiff\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider additionalProvider
     */
    public function testGendiff($expectedFile, $firstFileName, $secondFileName, $format)
    {
        $expected = file_get_contents(getFixturePath($expectedFile));
        $firstPath = getFixturePath($firstFileName);
        $secondPath = getFixturePath($secondFileName);
        $this->assertEquals($expected, genDiff($firstPath, $secondPath, $format));
    }


    public function additionalProvider()
    {
        return [
            ['expected.json', "before.json", "after.json", 'json'],
            ['expected.plain', "before.json", "after.json", 'plain'],
            ['expected.pretty', "before.json", "after.json", 'pretty'],
            ['expected.pretty', "before.yaml", "after.yaml", 'pretty']
        ];
    }
}

function getFixturePath($fileName)
{
    return "tests/fixtures/$fileName";
}
