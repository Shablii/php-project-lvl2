<?php

namespace Differ\Stylish;

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
        if ($node['status'] === 'noChenged') {
            $result = newSep($sep) . $node['key'] . ": " . $node['value1'];
        } elseif ($node['status'] === "add") {
            $result = newSep($sep, "+") . $node['key'] . ": " . $node['value2'];
        } elseif ($node['status'] === "del") {
            $result = newSep($sep, "-") . $node['key'] . ": " . $node['value1'];
        } else {
            $result[] = newSep($sep, "-") . $node['key'] . ": " . $node['value1'];
            $result[] = newSep($sep, "+") . $node['key'] . ": " . $node['value2'];
        }
    }

    if ($node['type'] === "ARRAY") {
        if ($node['status'] === 'noChenged') {
            $children = formatterMap($node['children'], $sep);
        } else {
            $children = recursivMap($sep, $node['children']);
        }

        if ($node['status'] === "add") {
            $result = [
                newSep($sep, "+") . $node['key'] . ": {",
                $children,
                $sep . "}"
            ];
        } elseif ($node['status'] === "del") {
            $result = [
                newSep($sep, "-") . $node['key'] . ": {",
                $children,
                $sep . "}"
            ];
        } elseif ($node['status'] === "noChenged") {
            $result = [
                newSep($sep) . $node['key'] . ": {",
                $children,
                $sep . "}"
            ];
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
