<?php

namespace Differ\Formatters\Plain;

function plain($ast)
{
    return implode("\n", formatterMap($ast));
}

function formatterMap($ast)
{
    return collect($ast)
    ->map(function ($node) {
        return formatter($node);
    })
    ->flatten()
    ->reject(function ($name) {
        return $name == "";
    })
    ->all();
}

function displeyVal($val)
{
    if (is_bool($val)) {
        $val = ($val === true) ? "true" : "false";
    } elseif ($val === null) {
        $val = "null";
    } else {
        $val = "'$val'";
    }
    return $val;
}


function formatter($node)
{
    if ($node['type'] === "OBJECT") {
        if ($node['status'] === "add") {
            $result = "Property '{$node['path']}' was added with value: " . displeyVal($node['value2']);
        } elseif ($node['status'] === "del") {
            $result = "Property '{$node['path']}' was removed";
        } elseif ($node['status'] === "chenged") {
            $result = "Property '{$node['path']}' was updated. From "
            . displeyVal($node['value1']) . " to " . displeyVal($node['value2']);
        } else {
            $result = "";
        }
    }

    if ($node['type'] === "ARRAY") {
        if ($node['status'] === 'noChenged') {
            return formatterMap($node['children']);
        }
        if ($node['status'] === "add") {
            $result = "Property '{$node['path']}' was added with value: [complex value]";
        } elseif ($node['status'] === "del") {
            $result = "Property '{$node['path']}' was removed";
        }
    }

    if ($node['type'] === "ARRAY/OBJECT") {
        if (is_object($node['value1'])) {
            $result = "Property '{$node['path']}' was updated. From [complex value] to " . displeyVal($node['value2']);
        } else {
            $$result = "Property '{$node['path']}' was updated. From " . displeyVal($node['value1'])
            . " to [complex value]";
        }
    }

    return $result;
}
