<?php

namespace Differ\Differ;

use function Differ\Parsers\parsers;
use function Differ\Formatters\formatters;

function genDiff($file1, $file2, $format = 'stylish')
{
    $flow1 = parsers($file1);
    $flow2 = parsers($file2);

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
            $status = getStatusArray($key, $flow1, $flow2);
            $children = ($status === 'noChenged') ? ast($valueFlow1, $valueFlow2, $path) : $node;
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
                'status' => getStatusObject($key, $valueFlow1, $valueFlow2),
                'path' => $path
            ];
        }
    });
}

function getStatusArray($key, $flow1, $flow2): string
{
    $noChenged = (property_exists($flow1, $key) && property_exists($flow2, $key));
    $add = (!property_exists($flow1, $key) && property_exists($flow2, $key));
    $del = (property_exists($flow1, $key) && !property_exists($flow2, $key));

    if ($noChenged) {
        $result = "noChenged";
    } elseif ($add) {
        $result = "added";
    } elseif ($del) {
        $result = "removed";
    }

    return $result;
}

function getStatusObject($key, $flow1, $flow2): string
{
    if ($flow1 === $flow2) {
        $result = "noChenged";
    } elseif ($flow1 === "not exist" && $flow2 !== "not exist") {
        $result = "added";
    } elseif ($flow1 !== "not exist" && $flow2 === "not exist") {
        $result = "removed";
    } else {
        $result = "updated";
    }

    return $result;
}
