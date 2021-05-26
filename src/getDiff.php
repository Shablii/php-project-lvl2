<?php

namespace Differ\GetDiff;

function getDiff($file1, $file2): string
{
    $result = [];
    $flow1 = json_decode(file_get_contents($file1), true);
    $flow2 = json_decode(file_get_contents($file2), true);

    $resultFlow = collect(array_merge($flow1, $flow2))->sortKeys();

    $result = $resultFlow->map(function ($item, $key) use ($flow1, $flow2) {
        if (array_key_exists($key, $flow2) && array_key_exists($key, $flow1)) {
            if ($flow1[$key] === $flow2[$key]) {
                $item = parser($item);
                return "  $key: $item";
            } else {
                return "- $key: $flow1[$key]\n+ $key: $item";
            }
        } elseif (array_key_exists($key, $flow2) && !array_key_exists($key, $flow1)) {
            $item = parser($item);
            return "+ $key: $item";
        } elseif (!array_key_exists($key, $flow2) && array_key_exists($key, $flow1)) {
            $item = parser($item);
            return  "- $key: $item";
        }
    })->all();

    return "{\n" . implode("\n", $result) . "\n}";
}

function parser($str): string
{
    if (is_bool($str)) {
        $str = $str = (true === $str) ? "true" : "false";
    }
    return $str;
}
