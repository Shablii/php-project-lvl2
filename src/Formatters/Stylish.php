<?php

namespace Differ\Formatters\Stylish;

function getStylishFormat(array $data): string
{
    return "{\n" . implode("\n", getDiff($data)) . "\n}";
}

function getDiff(array $data, string $space = ""): array
{
    return array_map(fn ($node) => getFormat($node, $space), $data);
}

function getFormat(array $node, string $space): string
{
    $ident = ['unchanged' => '    ', 'added' => '  + ', 'removed' => '  - '];
    $newSpace = $space . "    ";
    switch ($node['status']) {
        case 'unchanged':
        case 'added':
        case 'removed':
            $value = $node['status'] === 'removed' ? $node['oldValue'] : $node['newValue'];
            $status = $space . $ident[$node['status']];
            $ident = ['unchanged' => '    ', 'added' => '  + ', 'removed' => '  - '];
            return is_array($value)
            ? getArrayFormat($value, $node['key'], $status, $newSpace)
            : "{$status}{$node['key']}: " . displayValue($value);

        case 'updated':
            $oldValue = is_array($node['oldValue'])
            ? getArrayFormat($node['oldValue'], $node['key'], $space . $ident['removed'], $newSpace)
            : $space . $ident['removed'] . "{$node['key']}: " . displayValue($node['oldValue']);

            $newValue = is_array($node['newValue'])
            ? getArrayFormat($node['newValue'], $node['key'], $space . $ident['added'], $newSpace)
            : $space . $ident['added'] . "{$node['key']}: " . displayValue($node['newValue']);

            return $oldValue . "\n" . $newValue;

        case "parent":
            $children = getDiff($node['children'], $newSpace);
            return $newSpace . $node['key'] . ": {\n" . implode("\n", $children) . "\n" . $newSpace . "}";
        default:
            throw new \Exception("unknown status: " . $node['status'] . " for getFormat in Plain format");
    }
}

function getArrayFormat(array $value, string $key, string $status, string $space): string
{
    $newSpace = $space . "    ";
    $children = array_map(fn ($key) => is_array($value[$key])
        ? getArrayFormat($value[$key], $key, $newSpace, $newSpace)
        : $newSpace . $key . ": " . $value[$key], array_keys($value));

    return $status . $key . ": {\n" . implode("\n", $children) . "\n" . $space . "}";
}

function displayValue(mixed $value): string
{
    if (is_bool($value)) {
        return ($value === true) ? "true" : "false";
    }

    return is_null($value) ? "null" : $value;
}
