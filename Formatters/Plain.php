<?php

namespace Differ\Formatters\Plain;

function plain(object $ast): string
{
    return implode("\n", formatter($ast));
}

function formatter(object $ast): array
{
    return collect($ast)
    ->map(function ($node) {
        if (array_key_exists("type", $node)) {
            return formatter($node['children']);
        }
        return getObjectFormat($node);
    })
    ->flatten()
    ->reject(function ($name) {
        return $name == "";
    })
    ->all();
}

function getObjectFormat(array $node): array
{
    switch ($node['status']) {
        case 'noChenged':
            return [];
        case 'added':
            return ["Property '{$node['path']}' was added with value: " . displeyVal($node['newValue'])];
        case 'removed':
            return ["Property '{$node['path']}' was removed"];
        case 'updated':
            return ["Property '{$node['path']}' was updated. From "
            . displeyVal($node['oldValue']) . " to " . displeyVal($node['newValue'])];
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Plain format");
    }
}

function displeyVal($value): string
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
