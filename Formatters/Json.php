<?php

namespace Differ\Formatters\Json;

function json($ast): string
{
    $formatter = formatter($ast);

    return json_encode($formatter);
}

function formatter($ast): array
{
    return collect($ast)
    ->reduce(function ($acc, $node) {
        if ($node['type'] === "OBJECT") {
            switch ($node['status']) {
                case 'noChenged':
                    $acc[$node['key']] = $node['value1'];
                    break;
                case 'added':
                    $acc["+ " . $node['key']] = $node['value2'];
                    break;
                case 'removed':
                    $acc["- " . $node['key']] = $node['value1'];
                    break;
                case 'updated':
                    $acc["- " . $node['key']] = $node['value1'];
                    $acc["+ " . $node['key']] = $node['value2'];
                    break;
                default:
                    throw new \Exception("unknown status: " . $node['status'] . " for OBJECT in Json format");
            }
        }

        if ($node['type'] === "ARRAY") {
            switch ($node['status']) {
                case 'noChenged':
                    $key = $node['key'];
                    break;
                case 'added':
                    $key = "+ " . $node['key'];
                    break;
                case 'removed':
                    $key = "- " . $node['key'];
                    break;
                default:
                    throw new \Exception("unknown status: " . $node['status'] . " for ARRAY in Json format");
            }
            $acc[$key] = ($node['status'] === 'noChenged') ? formatter($node['children']) : $node['children'];
        }

        if ($node['type'] === "ARRAY/OBJECT") {
            if (is_object($node['value1'])) {
                $acc["- " . $node['key']] = $node['value1'];
                $acc["+ " . $node['key']] = $node['value2'];
            }

            if (is_object($node['value2'])) {
                $acc["- " . $node['key']] = $node['value2'];
                $acc["+ " . $node['key']] = $node['value1'];
            }
        }
        return $acc;
    });
}
