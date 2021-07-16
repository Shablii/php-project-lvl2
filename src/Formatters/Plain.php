<?php

namespace Differ\Formatters\Plain;

function plain(array $ast): string
{
    return implode("\n", formatter($ast));
}

function formatter(array $ast, string $path = ''): array
{
    $result = array_map(fn ($node) => getFormat($node, $path), $ast);

    return array_filter($result, fn ($name) => $name !== "");
}

function getFormat(array $node, string $path): string
{
    $newPath = $path == "" ? $node['key'] : "{$path}.{$node['key']}";
    switch ($node['status']) {
        case 'unchanged':
            return '';
        case 'added':
            return "Property '{$newPath}' was added with value: " . displayValue($node['newValue']);
        case 'removed':
            return "Property '{$newPath}' was removed";
        case 'updated':
            return "Property '{$newPath}' was updated. From "
            . displayValue($node['oldValue']) . " to " . displayValue($node['newValue']);
        case "parent":
            return implode("\n", formatter($node['children'], $newPath));
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for getFormat in Plain format");
    }
}

function displayValue(mixed $value): string | int | float
{
    if (is_bool($value)) {
        return ($value === true) ? "true" : "false";
    }

    if (is_numeric($value)) {
        return $value;
    }

    if (is_null($value)) {
        return "null";
    }

    return is_array($value) ? '[complex value]' : "'$value'";
}
