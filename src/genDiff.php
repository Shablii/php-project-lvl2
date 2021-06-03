<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Differ\Parsers\parsers;

function genDiff($file1, $file2): string
{
    $result = [];

    $flow1 = parsers($file1);
    $flow2 = parsers($file2);

    $result = collect(array_merge($flow1, $flow2))
    ->sortKeys()
    ->map(function ($item, $key) use ($flow1, $flow2) {
        if (array_key_exists($key, $flow2) && array_key_exists($key, $flow1)) {
            if ($flow1[$key] === $flow2[$key]) {
                return "  $key: $item";
            } else {
                return "- $key: $flow1[$key]\n+ $key: $item";
            }
        } elseif (array_key_exists($key, $flow2) && !array_key_exists($key, $flow1)) {
            return "+ $key: $item";
        } elseif (!array_key_exists($key, $flow2) && array_key_exists($key, $flow1)) {
            return  "- $key: $item";
        }
    })->all();

    return "{\n" . implode("\n", $result) . "\n}";
}
