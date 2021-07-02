<?php

namespace Differ\Formatters\Stylish;

function stylish(object $ast): string
{
    return "{\n" . implode("\n", formatter($ast)) . "\n}";
}

function formatter(object $ast, string $sep = ''): array
{
    $sep .= "    ";
    return collect($ast)
    ->map(function ($node) use ($sep): array {
        if (array_key_exists("type", $node)) {
            return [
                newSep($sep) . $node['key'] . ": {",
                formatter($node['children'], $sep),
                $sep . "}"
                ];
        }
        return getObjectFormat($node, $sep);
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
    $result = [];
    switch ($node['status']) {
        case 'noChenged':
            $result[] = getObject($sep, $node);
            break;
        case 'added':
            $result[] = is_object($node['newValue'])
            ? getArray($sep, $node, "+")
            : getObject($sep, $node, '+');
            break;
        case 'removed':
            $result[] = is_object($node['oldValue'])
            ? getArray($sep, $node, "-")
            : getObject($sep, $node, '-');
            break;
        case 'updated':
            $result[] = is_object($node['oldValue'])
            ? getArray($sep, $node, "-")
            : getObject($sep, $node, '-');
            $result[] = is_object($node['newValue'])
            ? getArray($sep, $node, "+")
            : getObject($sep, $node, '+');
            break;
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Stylish format");
    }
    return $result;
}

function recursivMap(string $sep, object $node): object
{
    $sep .= "    ";
    return collect($node)
    ->map(function ($item, $key) use ($sep) {
        if (is_object($item)) {
            return [
                $sep . $key . ": {",
                recursivMap($sep, $item),
                $sep . "}"
                ];
        }
        return $sep . $key . ": " . $item;
    });
}

function newSep(string $sep, string $value = " "): string
{
    return substr($sep, 0, strlen($sep) - 2) . $value . " ";
}

function displeyValue($val): string
{
    if (is_bool($val)) {
        $val = ($val === true) ? "true" : "false";
    }

    if ($val === null) {
        $val = "null";
    }

    return $val;
}
