<?php

use PHPUnit\Framework\TestCase;

use function Differ\GenDiff\stringify;
use function Differ\Gendiff\genDiff;

class GenDiffTest extends TestCase
{
    public $relativePathToFile1;
    public $relativePathToFile2;
    public $absolutePathToFile1;
    public $absolutePathToFile2;

    protected function setUp(): void
    {
        $this->relativePathToFile1 = __DIR__ . '/' . 'fixtures/' . 'before.json';
        $this->relativePathToFile2 = __DIR__ . '/' . 'fixtures/' . 'after.json';
        $this->absolutePathToFile1 = '../tests/fixtures/before.json';
        $this->absolutePathToFile2 = '../tests/fixtures/after.json';
    }
    public function teststringify()
    {
        $this->assertEquals("  key: value\n", stringify('key', 'value'));
        $this->assertEquals("  true: false\n", stringify(true, false));
        $this->assertEquals("+ key: true\n", stringify('key', true, '+'));
    }
    public function testgenDiff()
    {
        $this->assertNull(genDiff($this->relativePathToFile1, $this->relativePathToFile2));
        $this->assertNull(genDiff($this->absolutePathToFile1, $this->absolutePathToFile2));
        $this->assertNull(genDiff(['<firstFile>' => $this->relativePathToFile1, '<secondFile>' => $this->relativePathToFile2]));
        $this->assertNull(genDiff(['<firstFile>' => $this->absolutePathToFile1, '<secondFile>' => $this->absolutePathToFile2]));
    }
}
