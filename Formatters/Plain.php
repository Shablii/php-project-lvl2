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
        if ($node['type'] === "OBJECT" && $node['status'] === 'noChenged') {
            return;
        }
        switch ($node['type']) {
            case 'OBJECT':
                return getObjectFormat($node);
            case 'ARRAY':
                return getArrayFormat($node);
            case 'ARRAY/OBJECT':
                return getArrayObjectFormat($node);
            default:
                throw new \Exception("unknown type: " . $node['type'] . " for AST in Plain formatter");
        }
    })
    ->flatten()
    ->reject(function ($name) {
        return $name == "";
    })
    ->all();
}

function displeyVal($val): string
{
    if (is_bool($val)) {
        return ($val === true) ? "true" : "false";
    } elseif ($val === null) {
        return "null";
    }

    return "'$val'";
}

function getObjectFormat($node): array
{
    switch ($node['status']) {
        case 'noChenged':
            return [];
        case 'added':
            $result[] = "Property '{$node['path']}' was added with value: " . displeyVal($node['value2']);
            break;
        case 'removed':
            $result[] = "Property '{$node['path']}' was removed";
            break;
        case 'updated':
            $result[] = "Property '{$node['path']}' was updated. From "
            . displeyVal($node['value1']) . " to " . displeyVal($node['value2']);
            break;
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Plain format");
    }
    return $result;
}

function getArrayFormat($node): array
{
    switch ($node['status']) {
        case 'noChenged':
            $result[] = formatter($node['children']);
            break;
        case 'added':
            $result[] = "Property '{$node['path']}' was added with value: [complex value]";
            break;
        case 'removed':
            $result[] = "Property '{$node['path']}' was removed";
            break;
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for ARRAY in Plain format");
    }
    return $result;
}

function getArrayObjectFormat($node): string
{
    return (is_object($node['value1']))
    ? "Property '{$node['path']}' was updated. From [complex value] to " . displeyVal($node['value2'])
    : "Property '{$node['path']}' was updated. From " . displeyVal($node['value1']) . " to [complex value]";
}
