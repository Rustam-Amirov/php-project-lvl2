<?php

use PHPUnit\Framework\TestCase;

use function Differ\Gendiff\genDiff;

class GenDiffTest extends TestCase
{

    protected function setUp(): void
    {
        $this->relativePathToFile1json = __DIR__ . '/' . 'fixtures/' . 'before.json';
        $this->relativePathToFile2json = __DIR__ . '/' . 'fixtures/' . 'after.json';
        $this->absolutePathToFile1json = '../tests/fixtures/before.json';
        $this->absolutePathToFile2json = '../tests/fixtures/after.json';
        
        $this->relativePathToFile1yaml = __DIR__ . '/' . 'fixtures/' . 'before.yaml';
        $this->relativePathToFile2yaml = __DIR__ . '/' . 'fixtures/' . 'after.yaml';
        $this->absolutePathToFile1yaml = '../tests/fixtures/before.yaml';
        $this->absolutePathToFile2yaml = '../tests/fixtures/after.yaml';

        $this->bigFilejson1 = '../tests/fixtures/before1.json';
        $this->bigFilejson2 = '../tests/fixtures/after1.json';
    }

    public function testgenDiffjson()
    {
        $result = <<<'EOD'
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}

EOD;
        $this->assertEquals($result, genDiff($this->relativePathToFile1json, $this->relativePathToFile2json));
        $this->assertEquals($result, genDiff($this->absolutePathToFile1json, $this->absolutePathToFile2json));
    }


    public function testgenDiffyaml()
    {
        $result = <<<'EOD'
{
    host: hexlet.io
  + timeout: 20
  - timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}

EOD;
        $this->assertEquals($result, genDiff($this->relativePathToFile1yaml, $this->relativePathToFile2yaml));
        $this->assertEquals($result, genDiff($this->absolutePathToFile1yaml, $this->absolutePathToFile2yaml));
    }
    


    public function testgenDiffdepthJson()
    {
        $result = <<<'EOD'
{
    common: {
        setting1: Value 1
      - setting2: 200
        setting3: true
      - setting6: {
            key: value
        }
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
    }
    group1: {
      + baz: bars
      - baz: bas
        foo: bar
    }
  - group2: {
        abc: 12345
    }
  + group3: {
        fee: 100500
    }
}

EOD;

        $this->assertEquals($result, genDiff($this->bigFilejson1, $this->bigFilejson2));
    }


    public function testgendiffFormatPlain()
    {
        $result = "Property 'timeout' was changed. From '50' to '20'
Property 'proxy' was removed
Property 'verbose' was added with value: 'true'
";

        $this->assertEquals($result, genDiff($this->relativePathToFile1json, $this->relativePathToFile2json, 'plain'));
        $this->assertEquals($result, genDiff($this->absolutePathToFile1json, $this->absolutePathToFile2json, 'plain'));
    }


    public function testgendiffFormatPlainDepth()
    {
        $result = "Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group2' was removed
Property 'group3' was added with value: 'complex value'
";

        $this->assertEquals($result, genDiff($this->bigFilejson1, $this->bigFilejson2, 'plain'));
    }
}
