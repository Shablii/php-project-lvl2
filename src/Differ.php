<?php

namespace Differ\Differ;

use function Differ\Parsers\parsers;
use function Differ\Formatters\formatters;

function genDiff(string $firstFile, string $secondFile, string $format = 'stylish'): string
{

    [$contentFirstFile, $typeFirstFile] = fileParser($firstFile);
    [$contentSecondFile, $typeSecondFile] = fileParser($secondFile);

    $oldData = parsers($contentFirstFile, $typeFirstFile);
    $newData = parsers($contentSecondFile, $typeSecondFile);

    $ast = ast($oldData, $newData);

    return formatters($ast, $format);
}

function fileParser(string $file): array
{
    $content = file_get_contents($file);
    $type = strpbrk($file, ".");
    return [$content, $type];
}

function ast(object $oldData, object $newData, string $path = ""): object
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

function getStatusObject(mixed $oldData, mixed $newData): string
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
