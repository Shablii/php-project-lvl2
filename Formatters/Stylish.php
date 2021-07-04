<?php

namespace Differ\Formatters\Stylish;

function stylish(object $ast): string
{
    return "{\n" . implode("\n", formatter($ast)) . "\n}";
}

function formatter(object $ast, string $sep = ''): array
{
    return collect($ast)
    ->map(function ($node) use ($sep): array {
        $newSep = "    {$sep}";
        if ($node['status'] === 'Parent') {
            return [
                newSep($newSep) . $node['key'] . ': {',
                formatter($node['children'], $newSep),
                $newSep . '}'
                ];
        }
        return getObjectFormat($node, $newSep);
    })
    ->flatten()
    ->all();
}

function getArray(string $sep, array $node, string $status = ' '): array
{
    $val = $status === "+" || $status === " " ? $node['newValue'] : $node['oldValue'];
    return [
        newSep($sep, $status) . $node['key'] . ": {",
        recursivMap($sep, $val),
        $sep . "}"
        ];
}

function getObject(string $sep, array $node, string $status = ' '): string
{
    $val = $status === "+" || $status === " " ? $node['newValue'] : $node['oldValue'];
    return newSep($sep, $status) . $node['key'] . ": " . displeyValue($val);
}

function getObjectFormat(array $node, string $sep): array
{
    switch ($node['status']) {
        case 'noChenged':
            return [getObject($sep, $node)];
        case 'added':
            return [is_object($node['newValue'])
            ? getArray($sep, $node, "+")
            : getObject($sep, $node, '+')];
        case 'removed':
            return [is_object($node['oldValue'])
            ? getArray($sep, $node, "-")
            : getObject($sep, $node, '-')];
        case 'updated':
            return [
                is_object($node['oldValue'])
            ? getArray($sep, $node, "-")
            : getObject($sep, $node, '-'),
                 is_object($node['newValue'])
            ? getArray($sep, $node, "+")
            : getObject($sep, $node, '+')
            ];
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Stylish format");
    }
}

function recursivMap(string $sep, object $node): object
{
    $newSep = "    {$sep}";
    return collect($node)
    ->map(function ($item, $key) use ($newSep) {
        if (is_object($item)) {
            return [
                $newSep . $key . ": {",
                recursivMap($newSep, $item),
                $newSep . "}"
                ];
        }
        return $newSep . $key . ": " . $item;
    });
}

function newSep(string $sep, string $value = " "): string
{
    return substr($sep, 0, strlen($sep) - 2) . $value . " ";
}

function displeyValue(mixed $value): string
{
    if (is_bool($value)) {
        return ($value === true) ? "true" : "false";
    }

    if ($value === null) {
        return "null";
    }

    return $value;
}
