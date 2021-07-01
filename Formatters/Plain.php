<?php

namespace Differ\Formatters\Plain;

function plain($ast): string
{
    return implode("\n", formatter($ast));
}

function formatter($ast): array
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

function getObjectFormat($node): array
{
    switch ($node['status']) {
        case 'noChenged':
            return [];
        case 'added':
            $result[] = "Property '{$node['path']}' was added with value: " . displeyVal($node['newValue']);
            break;
        case 'removed':
            $result[] = "Property '{$node['path']}' was removed";
            break;
        case 'updated':
            $result[] = "Property '{$node['path']}' was updated. From "
            . displeyVal($node['oldValue']) . " to " . displeyVal($node['newValue']);
            break;
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Plain format");
    }
    return $result;
}

function displeyVal($val): string
{
    if (is_bool($val)) {
        return ($val === true) ? "true" : "false";
    } elseif ($val === null) {
        return "null";
    } elseif (is_object($val)) {
        return '[complex value]';
    }

    return "'$val'";
}
