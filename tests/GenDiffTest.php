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
        $this->relativePathToFile1json = __DIR__ . '/' . 'fixtures/' . 'before.json';
        $this->relativePathToFile2json = __DIR__ . '/' . 'fixtures/' . 'after.json';
        $this->absolutePathToFile1json= '../tests/fixtures/before.json';
        $this->absolutePathToFile2json = '../tests/fixtures/after.json';
        
        $this->relativePathToFile1yaml = __DIR__ . '/' . 'fixtures/' . 'before.yaml';
        $this->relativePathToFile2yaml = __DIR__ . '/' . 'fixtures/' . 'after.yaml';
        $this->absolutePathToFile1yaml = '../tests/fixtures/before.yaml';
        $this->absolutePathToFile2yaml = '../tests/fixtures/after.yaml';
    }


    public function teststringify()
    {
        $this->assertEquals("  key: value\n", stringify('key', 'value'));
        $this->assertEquals("  true: false\n", stringify(true, false));
        $this->assertEquals("+ key: true\n", stringify('key', true, '+'));
    }


    public function testgenDiff()
    {
        $result = <<<'EOD'
{
- timeout: 50
+ timeout: 20
+ verbose: true
  host: hexlet.io
- proxy: 123.234.53.22
}

EOD;
        $this->assertEquals($result, genDiff($this->relativePathToFile1json, $this->relativePathToFile2json));
        $this->assertEquals($result, genDiff($this->absolutePathToFile1json, $this->absolutePathToFile2json));
        $this->assertEquals($result, genDiff(['<firstFile>' => $this->relativePathToFile1json, '<secondFile>' => $this->relativePathToFile2json]));
        $this->assertEquals($result, genDiff(['<firstFile>' => $this->absolutePathToFile1json, '<secondFile>' => $this->absolutePathToFile2json]));
    
        $this->assertEquals($result, genDiff($this->relativePathToFile1yaml, $this->relativePathToFile2yaml));
        $this->assertEquals($result, genDiff($this->absolutePathToFile1yaml, $this->absolutePathToFile2yaml));
        $this->assertEquals($result, genDiff(['<firstFile>' => $this->relativePathToFile1yaml, '<secondFile>' => $this->relativePathToFile2yaml]));
        $this->assertEquals($result, genDiff(['<firstFile>' => $this->absolutePathToFile1yaml, '<secondFile>' => $this->absolutePathToFile2yaml]));
    }
}
