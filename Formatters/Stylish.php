<?php

namespace Differ\Formatters\Stylish;

function stylish($ast)
{
    return "{\n" . implode("\n", formatterMap($ast)) . "\n}";
}

function formatterMap($ast, $sep = '')
{
    $sep .= "    ";
    return collect($ast)
    ->map(function ($node) use ($sep) {
        return formatter($sep, $node);
    })
    ->flatten()
    ->all();
}

function formatter($sep, $node)
{
    if ($node['type'] === "OBJECT") {
        switch ($node['status']) {
            case 'noChenged':
                $result = newSep($sep) . $node['key'] . ": " . displeyVal($node['value1']);
                break;
            case 'added':
                $result = newSep($sep, "+") . $node['key'] . ": " . displeyVal($node['value2']);
                break;
            case 'removed':
                $result = newSep($sep, "-") . $node['key'] . ": " . displeyVal($node['value1']);
                break;
            case 'updated':
                $result[] = newSep($sep, "-") . $node['key'] . ": " . displeyVal($node['value1']);
                $result[] = newSep($sep, "+") . $node['key'] . ": " . displeyVal($node['value2']);
                break;
            default:
                throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Stylish format");
        }
    }

    if ($node['type'] === "ARRAY") {
        switch ($node['status']) {
            case 'noChenged':
                $result = [
                    newSep($sep) . $node['key'] . ": {",
                    formatterMap($node['children'], $sep),
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
                $result[] = newSep($sep, "-") . $node['key'] . ": " . displeyVal($node['value1']);
                $result[] = newSep($sep, "+") . $node['key'] . ": " . displeyVal($node['value2']);
                break;
            default:
                throw new \Exception("unknown status: " . $node['status'] . " for ARRAY in Stylish format");
        }
    }

    if ($node['type'] === "ARRAY/OBJECT") {
        if (is_object($node['value1'])) {
            $result = [
                newSep($sep, "-") . $node['key'] . ": {",
                recursivMap($sep, $node['value1']),
                $sep . "}"
            ];
            $result[] = newSep($sep, "+") . $node['key'] . ": " . $node['value2'];
        } else {
            $result = [
                newSep($sep, "+") . $node['key'] . ": {",
                recursivMap($sep, $node['value2']),
                $sep . "}"
            ];
            $result[] = newSep($sep, "-") . $node['key'] . ": " . $node['value1'];
        }
    }
    return $result;
}

function recursivMap($sep, $node)
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

function newSep($sep, $value = " ")
{
    return substr($sep, 0, strlen($sep) - 2) . $value . " ";
}

function displeyVal($val)
{
    if (is_bool($val)) {
        $val = ($val === true) ? "true" : "false";
    }

    if ($val === null) {
        $val = "null";
    }
    return $val;
}
