<?php

namespace Differ\Formatters\Stylish;

function stylish(object $ast): string
{
    return "{\n" . implode("\n", formatter($ast)) . "\n}";
}

function formatter(object $ast, string $sep = ''): array
{
    return collect($ast)
    ->map(fn ($node) => getFormat($node, $sep))
    ->flatten()
    ->all();
}

function getArray(string $sep, array $node, callable $children, string $status = ' ', string $key = ""): array
{
    $newKey = $key === "" ? $node['key'] : $key;
    return [
        newSep($sep, $status) . $newKey . ": {",
        $children(),
        $sep . "}"
        ];
}
function getObject(string $sep, array $node, string $status = ' '): string
{
    $val = $status === "+" || $status === " " ? $node['newValue'] : $node['oldValue'];
    return newSep($sep, $status) . $node['key'] . ": " . displeyValue($val);
}

function getFormat(array $node, string $sep): array
{
    $newSep = "    {$sep}";
    switch ($node['status']) {
        case 'noChenged':
            return [getObject($newSep, $node)];
        case 'added':
            return [is_object($node['newValue'])
            ? getArray($newSep, $node, fn() => arrayFormater($node['newValue'], $newSep), "+")
            : getObject($newSep, $node, '+')];
        case 'removed':
            return [is_object($node['oldValue'])
            ? getArray($newSep, $node, fn() => arrayFormater($node['oldValue'], $newSep), "-")
            : getObject($newSep, $node, '-')];
        case 'updated':
            return [
                is_object($node['oldValue'])
            ? getArray($newSep, $node, fn() => arrayFormater($node['oldValue'], $newSep), "-")
            : getObject($newSep, $node, '-'),
                 is_object($node['newValue'])
            ? getArray($newSep, $node, fn() => arrayFormater($node['newValue'], $newSep), "+")
            : getObject($newSep, $node, '+')
            ];
        case 'parent':
            return getArray($newSep, $node, fn() => formatter($node['children'], $newSep));
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for getFormat in Stylish format");
    }
}

function arrayFormater(object $node, string $sep): object
{
    $newSep = "    {$sep}";
    return collect($node)
    ->map(function ($node, $key) use ($newSep) {
        if (is_object($node)) {
            return getArray($newSep, (array) $node, fn() => arrayFormater($node, $newSep), " ", $key);
        }
        return $newSep . $key . ": " . $node;
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
