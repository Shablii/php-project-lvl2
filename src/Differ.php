<?php

namespace Differ\Differ;

use function Differ\Parsers\parsers;
use function Differ\Formatters\formatters;

function genDiff($file1, $file2, $format = 'stylish'): string
{
    $flow1 = parsers($file1);
    $flow2 = parsers($file2);
    var_dump($flow1);
    var_dump($flow2);
    $ast = ast($flow1, $flow2);
    return formatters($ast, $format);
}

function ast($flow1, $flow2, $path = ""): object
{
    return collect($flow1)->merge($flow2)
    ->sortKeys()
    ->map(function ($node, $key) use ($flow1, $flow2, $path) {
        $valueFlow1 = property_exists($flow1, $key) ? $flow1->$key : 'not exist';
        $valueFlow2 = property_exists($flow2, $key) ? $flow2->$key : 'not exist';

        $path .= $path == "" ? $key : "." . $key;
        if (is_object($node)) {
            $status = getStatusArray($flow1, $flow2, $key);
            $children = (is_object($valueFlow1) && is_object($valueFlow2))
            ? ast($valueFlow1, $valueFlow2, $path) : $node;
            return [
                'key' => $key,
                'type' => "ARRAY",
                'children' => $children,
                'status' => $status,
                'path' => $path
            ];
        } else {
            $type = (is_object($valueFlow1) || is_object($valueFlow2)) ? "ARRAY/OBJECT" : 'OBJECT';
            return[
                'key' => $key,
                'type' => $type,
                'value1' => $valueFlow1,
                'value2' => $valueFlow2,
                'status' => getStatusObject($valueFlow1, $valueFlow2),
                'path' => $path
            ];
        }
    });
}

function getStatusArray($flow1, $flow2, $key): string
{
    $noChenged = (property_exists($flow1, $key) && property_exists($flow2, $key));
    $added = (!property_exists($flow1, $key) && property_exists($flow2, $key));
    $removed = (property_exists($flow1, $key) && !property_exists($flow2, $key));

    if ($noChenged) {
        $result = "noChenged";
    } elseif ($added) {
        $result = "added";
    } elseif ($removed) {
        $result = "removed";
    }

    return $result;
}

function getStatusObject($flow1, $flow2): string
{
    if ($flow1 === $flow2) {
        $result = "noChenged";
    } elseif ($flow1 === "not exist" && $flow2 !== "not exist") {
        $result = "added";
    } elseif ($flow1 !== "not exist" && $flow2 === "not exist") {
        $result = "removed";
    } elseif ($flow1 !== "not exist" && $flow2 !== "not exist") {
        $result = "updated";
    }
    return $result;
}
