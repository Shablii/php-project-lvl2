<?php

namespace Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiff";
        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.yaml";
        $pathToSecondFile = __DIR__ . "/fixtures/file2.yaml";

        $this->assertStringEqualsFile($expectant, genDiff($pathToFirstFile, $pathToSecondFile));
    }

    public function testGenDiffStylish(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffStylish";
        $pathToFirstFile = __DIR__ . "/fixtures/test1.json";
        $pathToSecondFile = __DIR__ . "/fixtures/test2.json";
        $this->assertStringEqualsFile($expectant, genDiff($pathToFirstFile, $pathToSecondFile, 'stylish'));
    }

    public function testGenDiffPlain(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffPlain";
        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.yaml";
        $pathToSecondFile = __DIR__ . "/fixtures/file2.yaml";
        $this->assertStringEqualsFile($expectant, genDiff($pathToFirstFile, $pathToSecondFile, "plain"));
    }

    public function testGenDiffJson(): void
    {
        $fileJson = __DIR__ . "/fixtures/json.json";
        $expectant = file_get_contents($fileJson);

        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.json";
        $pathToSecondFile = __DIR__ . "/fixtures/file2.json";
        $this->assertEquals($expectant, genDiff($pathToFirstFile, $pathToSecondFile, "json"));
    }
}
