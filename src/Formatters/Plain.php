<?php

namespace Differ\Formatters\Plain;

function plain(object $ast): string
{
    return implode("\n", formatter($ast));
}

function formatter(object $ast, string $path = ''): array
{
    return collect($ast)
    ->map(fn ($node) => getFormat($node, $path))
    ->flatten()
    ->reject(fn ($name) => $name == "")
    ->all();
}

function getFormat(array $node, string $path): array
{
    $newPath = $path == "" ? $node['key'] : "{$path}.{$node['key']}";
    switch ($node['status']) {
        case 'noChenged':
            return [];
        case 'added':
            return ["Property '{$newPath}' was added with value: " . displeyValue($node['newValue'])];
        case 'removed':
            return ["Property '{$newPath}' was removed"];
        case 'updated':
            return ["Property '{$newPath}' was updated. From "
            . displeyValue($node['oldValue']) . " to " . displeyValue($node['newValue'])];
        case "parent":
            return formatter($node['children'], $newPath);
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for getFormat in Plain format");
    }
}

function displeyValue(mixed $value): string | int | float
{
    if (is_bool($value)) {
        return ($value === true) ? "true" : "false";
    } elseif (is_numeric($value)) {
        return $value;
    } elseif ($value === null) {
        return "null";
    } elseif (is_object($value)) {
        return '[complex value]';
    }
    return "'$value'";
}
