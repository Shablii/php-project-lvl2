<?php

namespace Differ\Formatters\Stylish;

function stylish($ast): string
{
    return "{\n" . implode("\n", formatter($ast)) . "\n}";
}

function formatter($ast, $sep = ''): array
{
    $sep .= "    ";
    return collect($ast)
    ->map(function ($node) use ($sep) {
        var_dump($node);
        switch ($node['type']) {
            case 'OBJECT':
                $result = getObjectFormat($node, $sep);
                break;
            case 'ARRAY':
                $result = getArrayFormat($node, $sep);
                break;
            case 'ARRAY/OBJECT':
                $result = getArrayObjectFormat($node, $sep);
                break;
            default:
                throw new \Exception("unknown type: " . $node['type'] . " for AST in Stylish formatter");
        }

        return $result;
    })
    ->flatten()
    ->all();
}

function getObjectFormat($node, $sep): array
{
    switch ($node['status']) {
        case 'noChenged':
            $result[] = newSep($sep) . $node['key'] . ": " . displeyValue($node['value1']);
            break;
        case 'added':
            $result[] = newSep($sep, "+") . $node['key'] . ": " . displeyValue($node['value2']);
            break;
        case 'removed':
            $result[] = newSep($sep, "-") . $node['key'] . ": " . displeyValue($node['value1']);
            break;
        case 'updated':
            $result[] = newSep($sep, "-") . $node['key'] . ": " . displeyValue($node['value1']);
            $result[] = newSep($sep, "+") . $node['key'] . ": " . displeyValue($node['value2']);
            break;
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Stylish format");
    }
    return $result;
}

function getArrayFormat($node, $sep): array
{
    switch ($node['status']) {
        case 'noChenged':
            $result = [
                newSep($sep) . $node['key'] . ": {",
                formatter($node['children'], $sep),
                $sep . "}"
            ];
            break;
        case 'added':
            $result = [
                newSep($sep, "+") . $node['key'] . ": {",
                recursivMap($sep, $node['children']),
                $sep . "}"
            ];
            break;
        case 'removed':
            $result = [
                newSep($sep, "-") . $node['key'] . ": {",
                recursivMap($sep, $node['children']),
                $sep . "}"
            ];
            break;
        case 'updated':
            $result[] = newSep($sep, "-") . $node['key'] . ": " . displeyValue($node['value1']);
            $result[] = newSep($sep, "+") . $node['key'] . ": " . displeyValue($node['value2']);
            break;
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for ARRAY in Stylish format");
    }
    return $result;
}

function getArrayObjectFormat($node, $sep): array
{
    if (is_object($node['value1'])) {
        $result = [
            newSep($sep, "-") . $node['key'] . ": {",
            recursivMap($sep, $node['value1']),
            $sep . "}"
        ];
        $result[] = newSep($sep, "+") . $node['key'] . ": " . $node['value2'];
    }

    if (is_object($node['value2'])) {
        $result = [
            newSep($sep, "+") . $node['key'] . ": {",
            recursivMap($sep, $node['value2']),
            $sep . "}"
        ];
        $result[] = newSep($sep, "-") . $node['key'] . ": " . $node['value1'];
    }
    return $result;
}

function recursivMap($sep, $node): object
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

function newSep($sep, $value = " "): string
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
