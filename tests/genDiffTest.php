<?php

require __DIR__ . '/../vendor/autoload.php';

use function Differ\GenDiff\genDiff;
use function Differ\GenDiff\pars;
use function Differ\Parsers\parsers;
use PHPUnit\Framework\TestCase;

class genDiffTest extends TestCase
{
/*
    public function testgenDiff(): void
    {
        $expectant = <<<DOC
        {
          - follow: false
            host: hexlet.io
          - proxy: 123.234.53.22
          - timeout: 50
          + timeout: 20
          + verbose: true
        }
        DOC;

        $file1 = __DIR__ . "/fixtures/filepath1.json";
        $file2 = __DIR__ . "/fixtures/filepath2.json";
        $this->assertEquals($expectant, genDiff($file1, $file2));

        $file1 = __DIR__ . "/fixtures/filepath1.yaml";
        $file2 = __DIR__ . "/fixtures/filepath2.yml";
        //$this->assertEquals($expectant, genDiff($file1, $file2));
    }
*/
    public function testgenDiff1(): void
    {
      $expectant = <<<DOC
      {
          common: {
            + follow: false
              setting1: Value 1
            - setting2: 200
            - setting3: true
            + setting3: null
            + setting4: blah blah
            + setting5: {
                  key5: value5
              }
              setting6: {
                  doge: {
                    - wow: 
                    + wow: so much
                  }
                  key: value
                + ops: vops
              }
          }
          group1: {
            - baz: bas
            + baz: bars
              foo: bar
            - nest: {
                  key: value
              }
            + nest: str
          }
        - group2: {
              abc: 12345
              deep: {
                  id: 45
              }
          }
        + group3: {
              deep: {
                  id: {
                      number: 45
                  }
              }
              fee: 100500
          }
      }
      DOC;

        $file1 = __DIR__ . "/fixtures/file1.json";
        $file2 = __DIR__ . "/fixtures/file2.json";
        $this->assertEquals($expectant, genDiff($file1, $file2));

        //$file1 = __DIR__ . "/fixtures/file1.yaml";
        $file2 = __DIR__ . "/fixtures/file2.yaml";
        $this->assertEquals($expectant, genDiff($file1, $file2));
    }
}
