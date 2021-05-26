<?php

require __DIR__ . '/../vendor/autoload.php';

use function Differ\GetDiff\getDiff;
use function Differ\GetDiff\pars;
use PHPUnit\Framework\TestCase;

class getDiffTest extends TestCase
{
    public function testgetDiff(): void
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

        $file1 = "/Users/mef4ik/LEARNING-PHP/Differ/tests/filepath1.json";
        $file2 = "/Users/mef4ik/LEARNING-PHP/Differ/tests/filepath2.json";
        $this->assertEquals($expectant, getDiff($file1, $file2));
    }
}