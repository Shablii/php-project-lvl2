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

        $file1 = __DIR__ . "/fixtures/file1.yaml";
        $file2 = __DIR__ . "/fixtures/file2.yaml";
        $this->assertEquals($expectant, genDiff($file1, $file2, "plain"));
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
