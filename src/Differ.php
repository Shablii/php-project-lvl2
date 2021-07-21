<?php

namespace Differ\Differ;

use function Differ\Parsers\parsers;
use function Differ\Formatters\formatters;
use function Functional\sort;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{

    [$contentFirstFile, $typeFirstFile] = getData($firstFile);
    [$contentSecondFile, $typeSecondFile] = getData($secondFile);

    $oldData = parsers($contentFirstFile, $typeFirstFile);
    $newData = parsers($contentSecondFile, $typeSecondFile);

    $ast = getAst($oldData, $newData);
    return formatters($ast, $format);
}

function getData(string $file): array
{
    $content = file_get_contents($file);
    if ($content === false) {
        throw new \Exception("Can't read file: {$file}");
    }
    $type = pathinfo($file, PATHINFO_EXTENSION);
    return [$content, $type];
}

function getAst(array $oldData, array $newData): array
{
    $keys = array_keys(array_merge($oldData, $newData));
    $sortKeys = sort($keys, fn ($oldData, $newData) => $oldData <=> $newData);

    return array_map(function ($key) use ($oldData, $newData) {

        $oldValue = array_key_exists($key, $oldData) ? $oldData[$key] : 'not exist';
        $newValue = array_key_exists($key, $newData) ? $newData[$key] : 'not exist';

        if (is_array($oldValue) && is_array($newValue)) {
            $children = getAst($oldValue, $newValue);
            return [
                'key' => $key,
                'status' => 'parent',
                'children' => $children
            ];
        }
        return[
            'key' => $key,
            'oldValue' => $oldValue,
            'newValue' => $newValue,
            'status' => getStatusObject($oldValue, $newValue)
        ];
    }, $sortKeys);
}

function getStatusObject(mixed $oldData, mixed $newData): string
{
    if ($oldData === $newData) {
        return "unchanged";
    }

    if ($oldData === "not exist" && $newData !== "not exist") {
        return "added";
    }

    if ($oldData !== "not exist" && $newData === "not exist") {
        return "removed";
    }

    if ($oldData !== "not exist" && $newData !== "not exist") {
        return "updated";
    }

    return throw new \Exception("unknown status for: {$oldData} and {$newData} for getStatusObject in Differ");
}
