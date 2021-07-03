<?php

namespace Differ\Formatters\Plain;

function plain(object $ast): string
{
    return implode("\n", formatter($ast));
}

function formatter(object $ast, array $path = []): array
{
    return collect($ast)
    ->map(function ($node) use ($path): array {
        $path[] = $node['key'];
        if (array_key_exists("type", $node)) {
            $node['path'] = $path;
            return formatter($node['children'], $path);
        }
        $node['path'] = $path;

        return getObjectFormat($node);
    })
    ->flatten()
    ->reject(function ($name): bool {
        return $name == "";
    })
    ->all();
}

function getObjectFormat(array $node): array
{
    $path = implode('.', $node['path']);
    switch ($node['status']) {
        case 'noChenged':
            return [];
        case 'added':
            return ["Property '{$path}' was added with value: " . displeyValue($node['newValue'])];
        case 'removed':
            return ["Property '{$path}' was removed"];
        case 'updated':
            return ["Property '{$path}' was updated. From "
            . displeyValue($node['oldValue']) . " to " . displeyValue($node['newValue'])];
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Plain format");
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

function newAst($ast, $path = "")
{
    return collect($ast)
    ->map(function ($node) use ($path) {
        $path .= $path == "" ? $node['key'] : "." . $node['key'];
        //$oldValue = property_exists($oldData, $key) ? $oldData->$key : 'not exist';
        //if (property_exists($node, "type")) {
        if (array_key_exists("type", $node)) {
            $node['path'] = $path;
            return newAst($node['children'], $path);
        }
        $node['path'] = $path;
        return $node;
    })->all();
}
