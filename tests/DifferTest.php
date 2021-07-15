<?php

namespace Differ;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function testGenDiffYamlFormat(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffYamlFormat";
        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.yaml";
        $pathToSecondFile = __DIR__ . "/fixtures/secondFile.yml";

        $this->assertStringEqualsFile($expectant, genDiff($pathToFirstFile, $pathToSecondFile));
    }

    public function testGenDiffJsonFormat(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffJsonFormat";
        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.json";
        $pathToSecondFile = __DIR__ . "/fixtures/secondFile.json";
        $this->assertStringEqualsFile($expectant, genDiff($pathToFirstFile, $pathToSecondFile, 'stylish'));
    }

    public function testGenDiffPlain(): void
    {
        $expectant = __DIR__ . "/fixtures/testGenDiffPlain";
        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.yaml";
        $pathToSecondFile = __DIR__ . "/fixtures/secondFile.yml";
        $this->assertStringEqualsFile($expectant, genDiff($pathToFirstFile, $pathToSecondFile, "plain"));
    }

    public function testGenDiffJson(): void
    {
        $fileJson = __DIR__ . "/fixtures/testGenDiffJson";
        $expectant = file_get_contents($fileJson);

        $pathToFirstFile = __DIR__ . "/fixtures/firstFile.yaml";
        $pathToSecondFile = __DIR__ . "/fixtures/secondFile.yml";
        $this->assertEquals($expectant, genDiff($pathToFirstFile, $pathToSecondFile, "json"));
    }
}
