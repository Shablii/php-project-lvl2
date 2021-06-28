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
        if ($node['type'] === "OBJECT" && $node['status'] === 'noChenged') {
            return;
        }
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
        switch ($node['status']) {
            case 'noChenged':
                return;
            case 'added':
                $result = "Property '{$node['path']}' was added with value: " . displeyVal($node['value2']);
                break;
            case 'removed':
                $result = "Property '{$node['path']}' was removed";
                break;
            case 'updated':
                $result = "Property '{$node['path']}' was updated. From "
                . displeyVal($node['value1']) . " to " . displeyVal($node['value2']);
                break;
            default:
                throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Plain format");
        }
    }

    if ($node['type'] === "ARRAY") {
        switch ($node['status']) {
            case 'noChenged':
                $result = formatterMap($node['children']);
                break;
            case 'added':
                $result = $result = "Property '{$node['path']}' was added with value: [complex value]";
                break;
            case 'removed':
                $result = "Property '{$node['path']}' was removed";
                break;
            default:
                throw new \Exception("unknown status: " . $node['status'] . " for ARRAY in Plain format");
        }
    }

    if ($node['type'] === "ARRAY/OBJECT") {
        $result = (is_object($node['value1']))
        ? "Property '{$node['path']}' was updated. From [complex value] to " . displeyVal($node['value2'])
        : "Property '{$node['path']}' was updated. From " . displeyVal($node['value1']) . " to [complex value]";
    }
    return $result;
}
