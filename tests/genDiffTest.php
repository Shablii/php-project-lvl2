<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiff";
        $file1 = __DIR__ . "/fixtures/file1.yaml";
        $file2 = __DIR__ . "/fixtures/file2.yaml";

        $this->assertStringEqualsFile($expectant, genDiff($file1, $file2));
    }

    public function testGenDiffStylish(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffStylish";
        $file1 = __DIR__ . "/fixtures/test1.json";
        $file2 = __DIR__ . "/fixtures/test2.json";
        $this->assertStringEqualsFile($expectant, genDiff($file1, $file2, 'stylish'));
    }

    public function testGenDiffPlain(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffPlain";
        $file1 = __DIR__ . "/fixtures/file1.yaml";
        $file2 = __DIR__ . "/fixtures/file2.yaml";
        $this->assertStringEqualsFile($expectant, genDiff($file1, $file2, "plain"));
    }

    public function testGenDiffJson(): void
    {
        $fileJson = __DIR__ . "/fixtures/json.json";
        $expectant = file_get_contents($fileJson);

        $file1 = __DIR__ . "/fixtures/file1.json";
        $file2 = __DIR__ . "/fixtures/file2.json";
        $this->assertEquals($expectant, genDiff($file1, $file2, "json"));
    }
}
