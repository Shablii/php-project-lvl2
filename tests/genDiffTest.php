<?php

require __DIR__ . '/../vendor/autoload.php';

use function Differ\GenDiff\genDiff;
use function Differ\GenDiff\pars;
use function Differ\Parsers\parsers;
use PHPUnit\Framework\TestCase;

class genDiffTest extends TestCase
{
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
        $this->assertEquals($expectant, genDiff($file1, $file2));
    }
}