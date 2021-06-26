<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parsers($file)
{
    $typeFile = strpbrk($file, ".");

    $parsFiles = $typeFile === ".json" ?
        json_decode(file_get_contents($file), false) :
        Yaml::parse(file_get_contents($file), Yaml::PARSE_OBJECT_FOR_MAP);

    return boolToStr($parsFiles);
}

function boolToStr($files)
{
    $result = collect($files)
    ->map(function ($item, $key) {
        if (is_bool($item)) {
            $item = (true === $item) ? "true" : "false";
        }

        if ($item === null) {
            $item = "null";
        }

        if (is_object($item)) {
            return boolToStr($item);
        }

        return $item;
    })->all();
    return (object) $result;
}
