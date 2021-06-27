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

        $file1 = __DIR__ . "/fixtures/file1.yaml";
        $file2 = __DIR__ . "/fixtures/file2.yaml";
        $this->assertEquals($expectant, genDiff($file1, $file2));
    }


    public function testgenDiffPlain(): void
    {
      $expectant = <<<DOC
      Property 'common.follow' was added with value: false
      Property 'common.setting2' was removed
      Property 'common.setting3' was updated. From true to null
      Property 'common.setting4' was added with value: 'blah blah'
      Property 'common.setting5' was added with value: [complex value]
      Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
      Property 'common.setting6.ops' was added with value: 'vops'
      Property 'group1.baz' was updated. From 'bas' to 'bars'
      Property 'group1.nest' was updated. From [complex value] to 'str'
      Property 'group2' was removed
      Property 'group3' was added with value: [complex value]
      DOC;

          $file1 = __DIR__ . "/fixtures/file1.json";
          $file2 = __DIR__ . "/fixtures/file2.json";
          $this->assertEquals($expectant, genDiff($file1, $file2, "plain"));

          $file1 = __DIR__ . "/fixtures/file1.yaml";
          $file2 = __DIR__ . "/fixtures/file2.yaml";
          $this->assertEquals($expectant, genDiff($file1, $file2, "plain"));
    }
}