<?php

namespace Differ\Formatters\Stylish;

const NOCHEGED = "    ";
const ADDED = "  + ";
const REMOVED = "  - ";

function stylish(array $ast): string
{
    return "{\n" . implode("\n", formatter($ast)) . "\n}";
}

function formatter(array $ast, string $space = ""): array
{
    return array_map(fn ($node) => getFormat($node, $space), $ast);
}

function getFormat(array $node, string $space): string
{
    $newSpace = $space . "    ";
    switch ($node['status']) {
        case 'unchanged':
            $value = $node['newValue'];
            $status = $space . NOCHEGED;
            return is_array($value)
            ? getArrayFormat($value, $node['key'], $status, $newSpace)
            : "$status{$node['key']}: " . displayValue($value);
        case 'added':
            $value = $node['newValue'];
            $status = $space . ADDED;
            return is_array($value)
            ? getArrayFormat($value, $node['key'], $status, $newSpace)
            : "$status{$node['key']}: " . displayValue($value);
        case 'removed':
            $value = $node['oldValue'];
            $status = $space . REMOVED;
            return is_array($value)
            ? getArrayFormat($value, $node['key'], $status, $newSpace)
            : "$status{$node['key']}: " . displayValue($value);
        case 'updated':
            $oldValue = is_array($node['oldValue'])
            ? getArrayFormat($node['oldValue'], $node['key'], $space . REMOVED, $newSpace)
            : $space . REMOVED . "{$node['key']}: " . displayValue($node['oldValue']);

            $newValue = is_array($node['newValue'])
            ? getArrayFormat($node['newValue'], $node['key'], $space . ADDED, $newSpace)
            : $space . ADDED . "{$node['key']}: " . displayValue($node['newValue']);

            return $oldValue . "\n" . $newValue;
        case "parent":
            $status = $space . NOCHEGED;
            $children = formatter($node['children'], $newSpace);
            return $status . $node['key'] . ": {\n" . implode("\n", $children) . "\n" . $status . "}";
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for getFormat in Plain format");
    }
}

function getArrayFormat(array $value, string $key, string $status, string $space): string
{
    $newSpace = $space . "    ";
    $children = array_map(fn ($key) => is_array($value[$key])
        ? getArrayFormat($value[$key], $key, $newSpace, $newSpace)
        : $newSpace . $key . ": " . $value[$key], array_keys($value));

    return $status . $key . ": {\n" . implode("\n", $children) . "\n" . $space . "}";
}

function displayValue(mixed $value): string
{
    if (is_bool($value)) {
        return ($value === true) ? "true" : "false";
    }

    return is_null($value) ? "null" : $value;
}
