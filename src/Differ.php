<?php

namespace Differ\Differ;

use function Differ\Parsers\parsers;
use function Differ\Formatters\formatters;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{

    [$contentFirstFile, $typeFirstFile] = getData($firstFile);
    [$contentSecondFile, $typeSecondFile] = getData($secondFile);

    $oldData = parsers($contentFirstFile, $typeFirstFile);
    $newData = parsers($contentSecondFile, $typeSecondFile);

    $ast = ast($oldData, $newData);
    return formatters($ast, $format);
}

function getData(string $file): array
{
    $content = file_get_contents($file);
    if (!$content) {
        throw new \Exception("Can't read file: $file");
    }
    $type = pathinfo($file, PATHINFO_EXTENSION);
    return [$content, $type];
}

function ast(array $oldData, array $newData, string $path = ""): array
{
    $marge = array_merge($oldData, $newData);

    ksort($marge);

    return array_map(function ($key) use ($oldData, $newData, $path) {

        $oldValue = array_key_exists($key, $oldData) ? $oldData[$key] : 'not exist';
        $newValue = array_key_exists($key, $newData) ? $newData[$key] : 'not exist';

        if (is_array($oldValue) && is_array($newValue)) {
            $children = ast($oldValue, $newValue, $path);
            return [
                'key' => $key,
                'status' => 'parent',
                'children' => $children
            ];
        } else {
            return[
                'key' => $key,
                'oldValue' => $oldValue,
                'newValue' => $newValue,
                'status' => getStatusObject($oldValue, $newValue)
            ];
        }
    }, array_keys($marge));
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
}
