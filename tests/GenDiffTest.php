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
        $expected = getDatafile($expectedFile);
        $firstPath = "tests/fixtures/".$firstFileName;
        $secondPath = "tests/fixtures/".$secondFileName;
        $this->assertEquals($expected, genDiff($firstPath, $secondPath, $format));
    }


    public function additionalProvider()
    {
        return [
            ['expected.json', "before.json", "after.json", 'json'],
            ['expected2.txt', "before.json", "after.json", 'plain'],
            ['expected1.txt', "before.json", "after.json", 'pretty'],
            ['expected1.txt', "before.yaml", "after.yaml", 'pretty']
        ];
    }
}

function getDatafile($fileName)
{
    return file_get_contents("tests/fixtures/$fileName");
}
