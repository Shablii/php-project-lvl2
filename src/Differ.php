<?php

namespace Differ\Differ;

use function Differ\Parsers\parsers;
use function Differ\Formatters\formatters;

function genDiff($file1, $file2, $format = 'stylish'): string
{
    $oldData = parsers($file1);
    $newData = parsers($file2);

    $ast = ast($oldData, $newData);

    return formatters($ast, $format);
}

function ast($oldData, $newData, $path = ""): object
{
    return collect($oldData)->merge($newData)
    ->sortKeys()
    ->map(function ($node, $key) use ($oldData, $newData, $path) {
        $oldValue = property_exists($oldData, $key) ? $oldData->$key : 'not exist';
        $newValue = property_exists($newData, $key) ? $newData->$key : 'not exist';

        $path .= $path == "" ? $key : "." . $key;
        if (is_object($oldValue) && is_object($newValue)) {
            $children = ast($oldValue, $newValue, $path);
            return [
                'key' => $key,
                'type' => 'Parent',
                'path' => $path,
                'children' => $children
            ];
        } else {
            return[
                'key' => $key,
                'oldValue' => $oldValue,
                'newValue' => $newValue,
                'status' => getStatusObject($oldValue, $newValue),
                'path' => $path
            ];
        }
    });
}

function getStatusObject($oldData, $newData): string
{
    if ($oldData === $newData) {
        return "noChenged";
    } elseif ($oldData === "not exist" && $newData !== "not exist") {
        return "added";
    } elseif ($oldData !== "not exist" && $newData === "not exist") {
        return "removed";
    } elseif ($oldData !== "not exist" && $newData !== "not exist") {
        return "updated";
    }
}
